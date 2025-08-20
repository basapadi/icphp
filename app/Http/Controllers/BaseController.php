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
        if(count($this->_filterColumnsLike) > 0 && $this->_filterParamLike != ''){
            foreach ($this->_filterColumnsLike as $key => $column) {
                $param = ['%'.strtolower($this->_filterParamLike).'%'];
                if($key == 0){
                    $query->whereRaw('LOWER('.$column.') LIKE ?', $param);
                    $totalQuery->whereRaw('LOWER('.$column.') LIKE ?', $param);
                } else {
                    $query->orWhereRaw('LOWER('.$column.') LIKE ?', $param);
                    $totalQuery->orWhereRaw('LOWER('.$column.') LIKE ?', $param);
                }
            }
        }
        $rows = $query->filter();
        $total = $totalQuery->filter(false);
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

    public function create(Request $request){
        
    }

    public function update(Request $request, $id){
        
    }

    public function delete(Request $request,$id){
        
    }

    /**
     * Mengatur column response untuk grid
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setColumns(array $columns){
        $this->_columns = Transformer::quasarColumn($columns);
    }

    /**
     * Mengatur Model untuk controller tertentu
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setModel(string $model){
        $this->_model = app($model);
        $this->_queryBuilder = $this->_model->newQuery();
        return $this->_queryBuilder;
    }

    /**
     * Mengatur query filter untuk grid diluar ketentuan dari request query Btx\Query\Model
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     * @description lihat detail request query di https://btx.basapadi.com/query/request-query
     */
    protected function setFilterColumnsLike(array $columns,string $param){
        $this->_filterColumnsLike = $columns;
        $this->_filterParamLike = $param;
    }

    /**
     * Mengatur view mutipleselect di grid
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setMultipleSelectGrid(bool $value){
        $this->_multipleSelectGrid = $value;
    }

    /**
     * untuk mendecoding id yang sudah di encode secara default dari model
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
    */
    protected function decodeId(string $encodeId){
        try {
            $decrypted = Hashids::decode($encodeId);
        } catch (Exception $e) {
            $decrypted = null; // atau return default value
        }
        return $decrypted;
    }
}
