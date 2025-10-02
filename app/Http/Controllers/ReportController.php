<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Response;
use Btx\File\Directory;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicModel;

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
