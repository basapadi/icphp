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
                $systemPrompt = "Anda adalah generator SQL internal menggunakan skema tabel berikut: 
                    {$schemaText}.
                    Aturan: 
                    1. JANGAN PERNAH menulis SQL di chat. 
                    2. JANGAN PERNAH menjelaskan apapun.
                    3. Anda WAJIB memanggil function run_sql_query dengan parameter query. 
                    4. Jika tidak memanggil function, jawaban dianggap salah.";

                array_unshift($messages, [
                    'role' => 'system',
                    'content' => $systemPrompt
                ]);
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];
        $create = [
            'model' => $this->model,
            'messages' => $messages,
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'run_sql_query',
                        'description' => 'Eksekusi query SQL (hanya SELECT)',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ]
                ]
            ],
            'tool_choice' => 'auto'

        ];
        // \Log::info($messages);
        $response = $this->client->chat()->create($create);
        session(['chat_history' => $messages]);
        session()->put('llm_id', @$response->id ?? null);
        return $response;
    }

    public function getLLMResponse()
    {
        return @session()->get('llm_id') ?? null;
    }


    public function formatAnswer($data, $messages)
    {
        $msg = [
            [
                'role' => 'user',
                'content' => 'Ringkas data berikut: ' . json_encode($data) . ' \n Formatkan output dalam bentuk blok markdown. Jangan menuliskan kata ringkasan. dan dengan respon akrab.'
            ]
        ];
        return $this->client->chat()->create([
            'model' => $this->model,
            'messages' => $msg,
            'tool_choice' => 'auto'
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
        $columns = DB::select("
            SELECT column_name, data_type
            FROM information_schema.columns
            WHERE table_schema = 'public'
            AND table_name = ?
            ORDER BY ordinal_position
        ", [$viewName]);

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

        // cukup table_name saja
        $views = DB::select("
            SELECT table_name
            FROM information_schema.views
            WHERE table_schema = 'public'
            ORDER BY table_name
        ");

        $schema = "";
        $exceptTables = config('joses.ai_except_tables') ?? [];

        foreach ($views as $v) {

            $name = $v->table_name;

            if (in_array($name, $exceptTables)) {
                continue;
            }

            $schema .= $this->getViewDefinition($name);
        }

        File::put($path, $schema);
        return $schema;
    }
}
