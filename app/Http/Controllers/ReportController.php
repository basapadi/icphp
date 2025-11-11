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

class ReportController extends BaseController
{
    private $_query = null;
    private $_file = [];
    private $_columns = [];
    public function __construct(){
        $this->setModule('report.report');
        $this->setGridProperties([
            'filterDateRange' => true,
            'filterDateName' => 'nama',
            'multipleSelect' => false
        ]);
        $this->setFilterColumnsLike(['masters.nama'],request('q')??'');
    }

    public function queries(Request $request){
       
        return Response::ok('Loaded', $this->getQueryFiles());
    }

    public function grid(Request $request){
        $this->allowAccessModule($this->_module, 'view');
        $this->_gridProperties['filterDateRange'] = $this->_gridProperties['filterDateRange']??false;
        $this->_gridProperties['advanceFilter'] = $this->_gridProperties['advanceFilter']??true;
        $this->_gridProperties['simpleFilter'] = $this->_gridProperties['simpleFilter']??true;
        $this->_gridProperties['multipleSelect'] = false;
        $this->_gridProperties['contextMenu'] = $this->_contextMenus;

        if(isset($request->path)){
            $file = $this->getResourceQuery(trim($request->path));
            $this->_file = $file;
            $this->_columns = $file['columns'];
            $this->_query = $file['query'];
        }

        if($this->_query == null){
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
            'query' => $file
        ]);
    }

    public function getSchemas(Request $request){
        $result = [];
        $connection = config('database.default');
        $tables = [
            'items',
            'item_stocks',
            'item_prices',
            'item_stock_adjustments',
            'masters',
            'trx_purchase_orders',
            'trx_purchase_order_details',
            'trx_received_items',
            'trx_received_item_details',
            'trx_received_payment_items',
            'trx_sale_items',
            'trx_sale_item_details',
            'trx_sale_orders',
            'trx_sale_order_details',
            'trx_sale_order_payment_items',
            'trx_sale_order_shipments',
            'contacts',
            'trx_purchase_invoices',
            'trx_purchase_invoice_details',
            'trx_purchase_invoice_item_receiveds',
            'trx_purchase_payments',
            'trx_sale_invoices',
            'trx_sale_invoice_details',
            'trx_sale_invoice_item_sales',
            'trx_sale_payments'
        ];

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

    public function preview(Request $request){
        $this->allowAccessModule('report.builder', 'view');
        $q = trim($request->rawQuery);
        if(!$this->protectQuery($q)) return Response::badRequest('Query ini tidak dapat diproses');
        $query = $q. " LIMIT {$request->_limit} OFFSET {$request->_page}";
        $rows = DB::select($query);
        $total = DB::select("SELECT COUNT(*) as count FROM ({$q}) as sub")[0]->count;
        if($total > 0){
            $rawcolumns = array_keys((array)$rows[0]);
        } else {
            $rawcolumns = [];
        }
        $columns = [];

        foreach($rawcolumns as $c){
            array_push($columns, [
                "name"          => $c,
                "required"      => true,
                "label"         => str_replace('_',' ',strtoupper($c)),
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

    public function saveQuery(Request $request){
        $this->allowAccessModule('report.builder', 'create');

        $rules = [
            'name'                   => 'required|string',
            'query'                   => 'required|string'
        ];
        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;

        if(!$this->protectQuery($data['query'])) return Response::badRequest('Query ini tidak dapat diproses');
        try {
            $columns = [];
            $types = [];
            $rows = DB::select($data['query']." limit 1");
            if(count($rows) > 0){
                $rawcolumns = array_keys((array)$rows[0]);
                foreach ($rows[0] as $key => $value) {
                    $types[$key] = gettype($value);
                }
            } else {
                $rawcolumns = [];
            }
            foreach($rawcolumns as $c){
                array_push($columns, [
                    "name"          => $c,
                    "required"      => true,
                    "label"         => str_replace('_',' ',strtoupper($c)),
                    "align"         => $types[$c] === 'integer'?'right': 'left',
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
            $trimmedName = strtolower(trim($data['name'])).'.json';
            $filePath = resource_path("data/queries/reports/user/{$trimmedName}");
            $dir = resource_path("data/queries/reports/user");
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true, true);
            }else {
                File::makeDirectory($dir, 0775, true, true);
            }

            file_put_contents($filePath, json_encode($jsonFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            chmod($filePath, 0664);
            return Response::ok('Query berhasil disimpan');
        }catch(Exception $e){
            rollBack();
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function deleteQuery(Request $request, $name){
        $this->allowAccessModule('report.report', 'delete');
        if(empty($name)) return Response::badRequest('Nama file tidak ditemukan');
        $filePath = resource_path('data/queries/reports/user/'.$name);
        if (File::exists($filePath)) {
            File::delete($filePath);
            return Response::ok('Laporan berhasil dihapus');
        }
        
    }

    private function getQueryFiles($folder = 'reports',$showNav = false){
        $files = [];
        $dirDefault = new Directory();
        $dirDefault->withNavigation = $showNav;
        $default = $dirDefault->scan('data/queries/'.$folder.'/default')->get();

        $dirUser = new Directory();
        $dirUser->withNavigation = $showNav;
        $user = $dirUser->scan('data/queries/'.$folder.'/user')->get()->reject(function($item){
            return $item['name'] == '.gitignore';
        });
        foreach ($default as $k => $d) {
            $exist = $user->where('name',$d['name'])->first();
            if($exist) array_push($files,$exist);
            else array_push($files,$d);
        }
        foreach ($user as $k => $u) {
            $exist = $default->where('name',$u['name'])->first();
            if(empty($exist)) array_push($files,$u);
        }

        $files = collect($files)->map(function($item){
            $item['label'] = strtoupper(str_replace('_',' ',explode('.',$item['name'])[0]));
            return $item;
        });

        return $files;
    }

    private function protectQuery($query)
    {
        // Trim spasi dan newline
        $query = trim($query);

        // Hapus komentar SQL tipe "-- ..." atau "/* ... */"
        $query = preg_replace('/--.*(\n|$)/', '', $query);       // komentar baris tunggal
        $query = preg_replace('/\/\*.*?\*\//s', '', $query);     // komentar blok

        $query = trim($query);

        $upper = strtoupper($query);
        if (!preg_match('/^SELECT\s+/i', $query)) {
            return false;
        }

        $forbidden = ['UPDATE', 'DELETE', 'INSERT', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE', 'REPLACE'];
        foreach ($forbidden as $word) {
            if (str_contains($upper, $word)) {
                return false;
            }
        }

        return true;
    }

}
