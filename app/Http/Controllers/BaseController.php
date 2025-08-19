<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Btx\Query\Model;
use Btx\Http\Response;
use Btx\Http\Libraries\ApiResponse;
use Btx\File\Upload;
use Btx\Query\Transformer;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Fractal\Fractal;
use Vinkla\Hashids\Facades\Hashids;

class BaseController extends Controller
{
    private Model $_model;
    private ?Fractal $_columns;
    private ?array $_filterColumnsLike = [];
    private string $_filterParamLike = '';
    private $_queryBuilder;
    private $_multipleSelectGrid = true;

    public function grid(Request $request){
        $query = $this->_queryBuilder;
        $totalQuery = clone $this->_queryBuilder;
        $rows = $query->filter();
        $total = $totalQuery->filter(false);
        if(count($this->_filterColumnsLike) > 0 && $this->_filterParamLike != ''){
            foreach ($this->_filterColumnsLike as $column) {
               $rows->orWhereRaw('LOWER('.$column.') LIKE ?', ['%'.strtolower($this->_filterParamLike).'%']);
               $total->orWhereRaw('LOWER('.$column.') LIKE ?', ['%'.strtolower($this->_filterParamLike).'%']);
            }
        }
        // dd($rows->toSql(),$this->_filterParamLike);
        $rows = $rows->get();
        $total = $total->count();
        return Response::ok('Loaded', [
            'rows' => $rows->toArray(), 
            'total' => $total, 
            'columns' => $this->_columns,
            'properties' => [
                'multipleSelect' => $this->_multipleSelectGrid 
            ]
        ]);
    }

    public function update(Request $request, $id){
        
    }

    protected function setColumns(array $columns){
        $this->_columns = Transformer::quasarColumn($columns);
    }

    protected function setModel(string $model){
        $this->_model = app($model);
        $this->_queryBuilder = $this->_model->newQuery();
        return $this->_queryBuilder;
    }

    protected function setFilterColumnsLike($columns,$param){
        $this->_filterColumnsLike = $columns;
        $this->_filterParamLike = $param;
    }

    protected function setMultipleSelectGrid(bool $value){
        $this->_multipleSelectGrid = $value;
    }

    protected function query(){
        return $this->_queryBuilder;
    }

    protected function decodeId($encodeId){
        try {
            $decrypted = Hashids::decode($encodeId);
        } catch (Exception $e) {
            $decrypted = null; // atau return default value
        }
        return $decrypted;
    }
}
