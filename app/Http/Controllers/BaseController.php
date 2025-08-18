<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Btx\Query\Model;
use Btx\Http\Response;
use Btx\Http\Libraries\ApiResponse;
use Btx\File\Upload;
use Btx\Query\Transformer;
use Illuminate\Support\Facades\DB;
use Spatie\Fractal\Fractal;

class BaseController extends Controller
{
    private Model $_model;
    private ?Fractal $_columns;
    private ?array $_filterColumnsLike = [];
    private string $_filterParamLike = '';
    private $_queryBuilder;

    public function grid(){
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
        return Response::ok('Loaded', ['rows' => $rows->toArray(), 'total' => $total, 'columns' => $this->_columns]);
    }

    protected function setColumns(array $columns){
        $this->_columns = Transformer::quasarColumn($columns);
    }

    protected function setModel(string $model){
        $this->_model = app($model);
        $this->_queryBuilder = $this->_model->newQuery();
        return $this;
    }

    protected function setFilterColumnsLike($columns,$param){
        $this->_filterColumnsLike = $columns;
        $this->_filterParamLike = $param;
    }

    protected function query(){
        return $this->_queryBuilder;
    }
}
