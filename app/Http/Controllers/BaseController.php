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
use App\Models\Menu;
use App\Transformers\FormTransformer;
use Spatie\Fractalistic\ArraySerializer;

class BaseController extends Controller
{
    private Model $_model;
    private ?Fractal $_columns;
    private ?array $_filterColumnsLike = [];
    private string $_filterParamLike = '';
    private $_queryBuilder;
    private $_multipleSelectGrid = true;
    private $_module = '';
    private ?array $_form = [];
    private $_gridProperties = [];

    public function grid(Request $request){
        $this->allowAccessModule($this->_module,'view');
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
        $this->_gridProperties['filterDateRange'] = $this->_gridProperties['filterDateRange']??false;
        $this->_gridProperties['multipleSelect'] = $this->_multipleSelectGrid;

        return Response::ok('Loaded', [
            'rows' => $rows->toArray(), 
            'total' => $total, 
            'columns' => $this->_columns,
            'properties' => $this->_gridProperties
        ]);
    }

    public function form(Request $request){
        $this->allowAccessModule($this->_module,'create');
        return Response::ok('Form', [
            'fields' => fractal($this->_form, new FormTransformer(), ArraySerializer::class), 
            'data' => [] //data yang dibawa saat pertama kali dibuka, contoh data options pada selecbox
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

    /**
     * untuk mengatur module controller
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
    */
    protected function setModule(string $module){
        $this->_module = $module;
    }

    /**
     * untuk mengecek hak akses ke action tertentu
     * @param $asBoolean type return sebagai boolean?
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
    */
    protected function allowAccessModule(string $module, string $action, bool $asBoolean = false){
        $auth = auth()->user();
        if(!empty($module) && !empty($action)){
            $can = Menu::select('menus.id','module', 'role_menus.'.$action)
                ->where('module',$module)->join('role_menus', function ($join) use ($auth) {
                    $join->on('role_menus.menu_id', '=', 'menus.id')
                        ->on('role_menus.role', '=', $auth->role);
            })->first()->{$action};
            return !$can ? (!$asBoolean ? abort(response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401)) : false) : true;
        }
        return !$asBoolean ? abort(response()->json([
            'status' => false,
            'message' => 'Unauthorized'
        ], 401)) : false;
    }

    /**
     * untuk mengecek hak akses ke action tertentu, apabila true maka return action namenya jika tidak return string kosong
     * @param $action actions antara lain: view,create,edit,update,download
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
    */
    protected function allowAccess(string $action){
        return $this->allowAccessModule($this->_module,$action,true) ? $action : '';
    }

    protected function setForm(array $fields){
       $this->_form = $fields;
    }

    protected function setGridProperties(array $properties){
        $this->_gridProperties = $properties;
    }
}
