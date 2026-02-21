<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Response;
use Btx\File\Directory;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicModel;
use Illuminate\Support\Facades\Schema;
use Exception;
use Illuminate\Support\Facades\File;
use App\Objects\DynamicCollectionExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends BaseController
{
    private $_query = null;
    private $_file = [];
    private $_columns = [];
    public function __construct()
    {
        $this->setModule('report.report');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'nama',
            'multipleSelect' => false
        ]);
        $this->setFilterColumnsLike(['masters.nama'], request('q') ?? '');
    }

    public function queries(Request $request)
    {

        return Response::ok('Loaded', $this->getQueryFiles());
    }

    public function grid(Request $request)
    {
        $this->allowAccessModule($this->_module, 'view');
        $this->_gridProperties['filterDateRange'] = $this->_gridProperties['filterDateRange'] ?? false;
        $this->_gridProperties['advanceFilter'] = $this->_gridProperties['advanceFilter'] ?? true;
        $this->_gridProperties['simpleFilter'] = $this->_gridProperties['simpleFilter'] ?? true;
        $this->_gridProperties['multipleSelect'] = false;
        $this->_gridProperties['contextMenu'] = $this->_contextMenus;

        if (isset($request->path)) {
            $json = $this->getResourceQuery(trim($request->path));
            $pathSql = trim(str_replace('.json', '.sql', $request->path));
            $sql = $this->getResourceSql($pathSql);
            $this->_file = $json;
            $this->_columns = $json['columns'];
            $this->_query = $sql;
        }
        if ($this->_query == null) {
            return Response::ok('Loaded', [
                'rows' => [],
                'total' => 0,
                'columns' => [],
                'properties' => $this->_gridProperties
            ]);
        }

        $model = (new DynamicModel())->setTable(DB::raw("({$this->_query}) as t"));
        $qtotal = clone $model->newQuery()->filter(false);
        $qrows = $model->newQuery()->filter();

        $rows = $qrows->get();
        $total = $qtotal->count();

        return Response::ok('Loaded', [
            'rows' => $rows->toArray(),
            'total' => $total,
            'columns' => $this->_columns,
            'properties' => $this->_gridProperties,
            'query' => [
                'query' => $this->_query
            ]
        ]);
    }

    public function getSchemas(Request $request)
    {
        $result = [];
        $connection = config('database.default');
        $tables = [];

        if ($connection === 'pgsql') {

            $views = DB::select("
            SELECT table_name
            FROM information_schema.views
            WHERE table_schema = 'public'
        ");

            $tables = collect($views)->pluck('table_name')->toArray();
        } elseif ($connection === 'mysql') {

            $views = DB::select("
            SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.VIEWS
            WHERE TABLE_SCHEMA = DATABASE()
        ");

            $tables = collect($views)->pluck('TABLE_NAME')->toArray();
        } elseif ($connection === 'sqlite') {

            $views = DB::select("
            SELECT name
            FROM sqlite_master
            WHERE type = 'view'
        ");

            $tables = collect($views)->pluck('name')->toArray();
        } else {
            throw new \Exception("Driver basis data tidak didukung: $connection");
        }

        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);

            foreach ($columns as $column) {
                // tipe kolom dari Laravel schema builder
                $type = DB::getSchemaBuilder()->getColumnType($table, $column);

                if ($connection === 'sqlite') {
                    // SQLite: gunakan PRAGMA
                    $info = DB::select("PRAGMA table_info($table)");
                    $colInfo = collect($info)->firstWhere('name', $column);

                    $nullable = $colInfo->notnull == 0;
                    $default  = $colInfo->dflt_value;
                    $length   = null; // SQLite gak punya panjang kolom eksplisit
                } elseif ($connection === 'pgsql') {
                    // PostgreSQL: gunakan INFORMATION_SCHEMA
                    $info = DB::select("
                        SELECT 
                            column_name, 
                            column_default, 
                            is_nullable, 
                            character_maximum_length
                        FROM information_schema.columns
                        WHERE table_name = ? AND table_schema = 'public'
                    ", [$table]);

                    $colInfo = collect($info)->firstWhere('column_name', $column);

                    $nullable = $colInfo->is_nullable === 'YES';
                    $default  = $colInfo->column_default;
                    $length   = $colInfo->character_maximum_length;
                } elseif ($connection === 'mysql') {
                    // MySQL: gunakan INFORMATION_SCHEMA
                    $info = DB::select("
                        SELECT COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()
                    ", [$table]);

                    $colInfo = collect($info)->firstWhere('COLUMN_NAME', $column);

                    $nullable = $colInfo->IS_NULLABLE === 'YES';
                    $default  = $colInfo->COLUMN_DEFAULT;
                    $length   = $colInfo->CHARACTER_MAXIMUM_LENGTH;
                } else {
                    throw new \Exception("Driver basis data tidak didukung: $connection");
                }

                $result[$table][] = [
                    'name'     => $column,
                    'type'     => $type,
                    'length'   => $length,
                    'nullable' => $nullable,
                    'default'  => $default,
                ];
            }
        }

        return Response::ok('Loaded', $result);
    }

    public function preview(Request $request)
    {
        $this->allowAccessModule('report.builder', 'view');
        $q = trim($request->rawQuery);
        if (!$this->protectQuery($q)) return Response::badRequest('Query ini tidak dapat diproses');
        $query = $q . " LIMIT {$request->_limit} OFFSET {$request->_page}";
        $rows = DB::select($query);
        $total = DB::select("SELECT COUNT(*) as count FROM ({$q}) as sub")[0]->count;
        if ($total > 0) {
            $rawcolumns = array_keys((array)$rows[0]);
        } else {
            $rawcolumns = [];
        }
        $columns = [];

        foreach ($rawcolumns as $c) {
            array_push($columns, [
                "name"          => $c,
                "required"      => true,
                "label"         => str_replace('_', ' ', strtoupper($c)),
                "align"         => "left",
                "field"         => $c,
                "sortable"      => true,
                "type"          => "text",
                "show"          => true,
                "styles"        => "",
            ]);
        }

        return Response::ok('Loaded', [
            'rows' => $rows,
            'total' => $total,
            'columns' => $columns,
            'properties' => $this->_gridProperties
        ]);
    }

    public function saveQuery(Request $request)
    {
        $this->allowAccessModule('report.builder', 'create');

        $rules = [
            'name'                   => 'required|string',
            'query'                   => 'required|string'
        ];
        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

        if (!$this->protectQuery($data['query'])) return Response::badRequest('Query ini tidak dapat diproses');
        try {
            $columns = [];
            $types = [];
            $rows = DB::select($data['query'] . " limit 1");
            if (count($rows) > 0) {
                $rawcolumns = array_keys((array)$rows[0]);
                foreach ($rows[0] as $key => $value) {
                    $types[$key] = gettype($value);
                }
            } else {
                $rawcolumns = [];
            }
            foreach ($rawcolumns as $c) {
                array_push($columns, [
                    "name"          => $c,
                    "required"      => true,
                    "label"         => str_replace('_', ' ', strtoupper($c)),
                    "align"         => $types[$c] === 'integer' ? 'right' : 'left',
                    "field"         => $c,
                    "type"          => "text",
                    "show"          => true,
                    "styles"        => "",
                    "option_filter" => true
                ]);
            }

            $jsonFile = [];
            $jsonFile['query'] = $data['query'];
            $jsonFile['columns'] = $columns;
            $trimmedNameJson = strtolower(trim($data['name'])) . '.json';
            $trimmedNameSql = strtolower(trim($data['name'])) . '.sql';
            $filePathJson = resource_path("data/queries/reports/user/{$trimmedNameJson}");
            $filePathSql = resource_path("data/queries/reports/user/{$trimmedNameSql}");
            $dir = resource_path("data/queries/reports/user");
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true, true);
            } else {
                File::makeDirectory($dir, 0775, true, true);
            }

            file_put_contents($filePathJson, json_encode($jsonFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($filePathSql, $data['query']);
            chmod($filePathJson, 0664);
            chmod($filePathSql, 0664);
            return Response::ok('Query berhasil disimpan');
        } catch (Exception $e) {
            rollBack();
            return $this->setAlert('error', 'Gagal', $e->getMessage());
        }
    }

    public function deleteQuery(Request $request, $name)
    {
        $this->allowAccessModule('report.report', 'delete');
        if (empty($name)) return Response::badRequest('Nama file tidak ditemukan');
        $filePath = resource_path('data/queries/reports/user/' . $name);
        $filePathSql = resource_path('data/queries/reports/user/' . str_replace('.json', '.sql', $name));

        if (File::exists($filePath)) File::delete($filePath);
        if (File::exists($filePathSql)) File::delete($filePathSql);

        return Response::ok('Laporan berhasil dihapus');
    }

    public function download(Request $request)
    {
        $this->allowAccessModule('report.report', 'download');
        if (empty($request->path)) return Response::badRequest('Path tidak ditemukan');

        $pathSql = trim(str_replace('.json', '.sql', $request->path));
        $sql = $this->getResourceSql($pathSql);
        $this->_query = $sql;

        $model = (new DynamicModel())->setTable(DB::raw("({$this->_query}) as t"));

        $data = $model->get();
        // dd($data->toArray());
        if ($data->isEmpty()) {
            return Response::badRequest('Data kosong');
        }

        $meta = [
            'title' => $request->name,
            'Perusahaan' => 'PT JAKARTA OSES ENERGI',
            'Periode' => Carbon::now()->format('M-Y')
        ];
        return Excel::download(
            new DynamicCollectionExport($data, $meta),
            $request->name . '.xlsx'
        );
    }

    private function getQueryFiles($folder = 'reports', $showNav = false)
    {
        $files = [];
        $dirDefault = new Directory();
        $dirDefault->withNavigation = $showNav;
        $default = $dirDefault->scan('data/queries/' . $folder . '/default')->where('extension', '!=', 'sql')->get();

        $dirUser = new Directory();
        $dirUser->withNavigation = $showNav;
        $user = $dirUser->scan('data/queries/' . $folder . '/user')->where('extension', '!=', 'sql')->get()->reject(function ($item) {
            return $item['name'] == '.gitignore';
        });
        foreach ($default as $k => $d) {
            $exist = $user->where('name', $d['name'])->first();
            if ($exist) array_push($files, $exist);
            else {
                $json = $this->getResourceQuery('data/queries/reports/default/' . $d['name']);
                $d['description'] = @$json['description'] ?? '';
                array_push($files, $d);
            }
        }
        foreach ($user as $k => $u) {
            $exist = $default->where('name', $u['name'])->first();
            if (empty($exist)) array_push($files, $u);
        }

        $files = collect($files)->map(function ($item) {
            $item['label'] = strtoupper(str_replace('_', ' ', explode('.', $item['name'])[0]));
            return $item;
        });

        return $files;
    }

    private function protectQuery($query)
    {
        $query = trim($query);

        // hapus komentar
        $query = preg_replace('/--.*(\n|$)/', '', $query);
        $query = preg_replace('/\/\*.*?\*\//s', '', $query);

        $query = trim($query);

        // wajib SELECT
        if (!preg_match('/^SELECT\s+/i', $query)) {
            return false;
        }

        // forbidden SQL commands (full word only)
        $forbiddenPattern = '/\b(UPDATE|DELETE|INSERT|DROP|ALTER|CREATE|TRUNCATE|REPLACE)\b/i';

        if (preg_match($forbiddenPattern, $query)) {
            return false;
        }

        if (str_contains($query, ';')) {
            return false;
        }

        return true;
    }
}
