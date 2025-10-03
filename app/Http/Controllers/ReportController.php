<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Response;
use Btx\File\Directory;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicModel;
use Illuminate\Support\Facades\Schema;

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
            'properties' => $this->_gridProperties
        ]);
    }

    public function getSchemas(Request $request){
        $result = [];
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
            'trx_sale_order_shipments'
        ];

        foreach ($tables as $table) {
            $columns = Schema::getColumnListing($table);

            foreach ($columns as $column) {
                // tipe kolom dari Laravel schema builder
                $type = DB::getSchemaBuilder()->getColumnType($table, $column);

                // ambil info detail default/nullability langsung dari PRAGMA SQLite
                $info = DB::select("PRAGMA table_info($table)");
                $colInfo = collect($info)->firstWhere('name', $column);

                $result[$table][] = [
                    'name'     => $column,
                    'type'     => $type,
                    'length'   => null, // SQLite nggak expose length
                    'nullable' => $colInfo->notnull == 0,
                    'default'  => $colInfo->dflt_value,
                ];
            }
        }

        return Response::ok('Loaded', $result);
    }

    public function preview(Request $request){
        $q = trim($request->rawQuery);
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
                "label"         => strtoupper($c),
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

    private function getQueryFiles($showNav = false){
        $dir = new Directory();
        $dir->withNavigation = $showNav; //if you want to show navigation folder
        $files = $dir->scan('data/queries/reports')->get();

        $files = $files->map(function($item){
            $item['label'] = strtoupper(str_replace('_',' ',explode('.',$item['name'])[0]));
            return $item;
        });

        return $files;
    }
}
