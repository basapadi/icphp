<?php

namespace App\Services;

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Schema;
use OpenAI;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LLMService
{
    private $client;
    private $apiKey;
    private $baseUrl;
    private $model;
    private $messages = [];

    public function __construct()
    {
        $this->apiKey = config('ihandcashier.ai_api_key');
        $this->baseUrl = config('ihandcashier.ai_base_url');
        $this->model  = config('ihandcashier.ai_model');

        $this->client = OpenAI::factory()
            ->withApiKey($this->apiKey)
            ->withBaseUri($this->baseUrl)
            ->make();
    }

    public function ask($prompt)
    {
        $messages = session('chat_history', []);
        if ($this->getLLMResponse() != null) {
            if (!collect($messages)->contains(fn($m) => $m['role'] === 'system')) {
                $schemaText = $this->getFullSchema();
                $systemPrompt = 'Generate sql untuk Postgres dan gunakan tabel berikut: ' . $schemaText . '. Aturan: Jangan minta klarifikasi, Gunakan bahasa indonesia, komentar lucu, Jangan tampilkan query';

                array_unshift($messages, [
                    'role' => 'system',
                    'content' => $systemPrompt
                ]);
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => (string) $prompt
        ];

        session(['chat_history' => $messages]);


        // Susunan parameter untuk function/tool
        $functionDefinition = [
            'name' => 'run_sql_query',
            'description' => 'Eksekusi query SQL (hanya SELECT)',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'query' => ['type' => 'string']
                ],
                'required' => ['query']
            ]
        ];
        $result = null;
        switch (config('ihandcashier.ai_platform')) {

            /*
            =========================================
            ========== 1. GROK (X.AI) =================
            =========================================
            */
            case 'grok':
                $response = $this->client->chat()->create([
                    'model' => $this->model,
                    'messages' => $messages,
                    'tools' => [
                        [
                            'type' => 'function',
                            'function' => $functionDefinition
                        ]
                    ],
                    'tool_choice' => [
                        'type' => 'function',
                        'function' => ['name' => 'run_sql_query']
                    ],
                ]);
                session()->put('llm_id', @$response->id ?? null);
                $result = $response;
                break;
            /*
            =========================================
            ========== 2. DEEPSEEK / OPENAI =========
            =========================================
            */
            default:
                $response = $this->client->chat()->create([
                    'model' => $this->model,
                    'messages' => $messages,
                    'tools' => [
                        [
                            'type' => 'function',
                            'function' => $functionDefinition
                        ]
                    ],
                    'tool_choice' => [
                        'type' => 'function',
                        'function' => ['name' => 'run_sql_query']
                    ]

                ]);
                session()->put('llm_id', @$response->id ?? null);
                $results = $response;
                break;
        }

        session()->put('chat_history', array_slice($messages, -5));
        return $results;
    }

    public function getLLMResponse()
    {
        return @session()->get('llm_id') ?? null;
    }


    public function formatAnswer($data, $messages)
    {
        $messages[] = ['role' => 'user', 'content' => 'Ringkas data berikut: ' . json_encode($data) . ' \n Formatkan output dalam bentuk blok markdown. Jangan menuliskan kata ringkasan.'];
        // dd($this->messages);
        return $this->client->chat()->create([
            'model' => $this->model,
            'messages' => $messages
        ]);
    }

    public function getTableDefinition($table)
    {
        // Pisahkan nama table dan kolom yang diinginkan
        $parts = explode('|', $table);
        $tableName = $parts[0];
        $selectedColumns = isset($parts[1]) ? explode(',', $parts[1]) : [];

        $columns = DB::select("SELECT column_name, data_type, is_nullable
        FROM information_schema.columns
        WHERE table_name = ?
        ORDER BY ordinal_position", [$tableName]);

        $definition = "Table: $tableName\n";

        foreach ($columns as $col) {
            // Jika ada filter kolom, cek dulu
            if ($selectedColumns && !in_array($col->column_name, $selectedColumns)) {
                continue;
            }
            $definition .= "- {$col->column_name} {$col->data_type}\n";
        }

        return $definition . "\n";
    }

    public function getViewDefinition($viewName)
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {

            $columns = DB::select("
                SELECT column_name, data_type
                FROM information_schema.columns
                WHERE table_schema = 'public'
                AND table_name = ?
                ORDER BY ordinal_position
            ", [$viewName]);
        } elseif ($driver === 'sqlite') {
            $columns = DB::select("PRAGMA table_info($viewName)");
            // Samakan format supaya konsisten
            $columns = collect($columns)->map(function ($col) {
                return (object) [
                    'column_name' => $col->name,
                    'data_type' => $col->type,
                ];
            });
        } else {
            throw new \Exception("Unsupported database driver: $driver");
        }

        $definition = "{$viewName}\n";

        foreach ($columns as $col) {
            $definition .= "- {$col->column_name} {$col->data_type}\n";
        }

        return $definition . "\n";
    }


    public function getFullSchema()
    {
        $dir = base_path('resources/data/detail_schemas/db');
        $path = base_path('resources/data/detail_schemas/db/table_schema.txt');

        if (File::exists($path)) {
            return File::get($path);
        }

        File::makeDirectory($dir, 0775, true, true);

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {

            $views = DB::select("
                SELECT table_name
                FROM information_schema.views
                WHERE table_schema = 'public'
                ORDER BY table_name
            ");

            $views = collect($views)->pluck('table_name');
        } elseif ($driver === 'sqlite') {

            $views = DB::select("
                SELECT name
                FROM sqlite_master
                WHERE type = 'view'
                ORDER BY name
            ");

            $views = collect($views)->pluck('name');
        } else {
            throw new \Exception("Unsupported database driver: $driver");
        }

        $schema = "";
        $exceptTables = config('ihandcashier.ai_except_tables') ?? [];

        foreach ($views as $name) {

            if (in_array($name, $exceptTables)) {
                continue;
            }

            $schema .= $this->getViewDefinition($name);
        }

        File::put($path, $schema);

        return $schema;
    }
}
