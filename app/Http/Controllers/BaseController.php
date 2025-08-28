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
    private $_detailSchema = [];

    public function grid(Request $request)
    {
        $this->allowAccessModule($this->_module, 'view');
        $query = $this->_queryBuilder;
        $totalQuery = clone $this->_queryBuilder;
        if (count($this->_filterColumnsLike) > 0 && $this->_filterParamLike != '') {
            foreach ($this->_filterColumnsLike as $key => $column) {
                $param = ['%' . strtolower($this->_filterParamLike) . '%'];
                if ($key == 0) {
                    $query->whereRaw('LOWER(' . $column . ') LIKE ?', $param);
                    $totalQuery->whereRaw('LOWER(' . $column . ') LIKE ?', $param);
                } else {
                    $query->orWhereRaw('LOWER(' . $column . ') LIKE ?', $param);
                    $totalQuery->orWhereRaw('LOWER(' . $column . ') LIKE ?', $param);
                }
            }
        }
        $rows = $query->filter();
        $total = $totalQuery->filter(false);
        // dd($rows->toSql(),$this->_filterParamLike);
        $rows = $rows->get();
        $total = $total->count();
        $this->_gridProperties['filterDateRange'] = $this->_gridProperties['filterDateRange']??false;
        $this->_gridProperties['advanceFilter'] = $this->_gridProperties['advanceFilter']??true;
        $this->_gridProperties['multipleSelect'] = $this->_gridProperties['multipleSelect']??$this->_multipleSelectGrid;

        return Response::ok('Loaded', [
            'rows' => $rows->toArray(),
            'total' => $total,
            'columns' => $this->_columns,
            'properties' => $this->_gridProperties,
            'detail_schemes' => $this->_detailSchema
        ]);
    }

    public function form(Request $request)
    {
        $this->allowAccessModule($this->_module, 'create');
        return Response::ok('Form', [
            'fields' => fractal($this->_form, new FormTransformer(), ArraySerializer::class),
            'data' => [] //data yang dibawa saat pertama kali dibuka, contoh data options pada selecbox
        ]);
    }

    public function create(Request $request) {}

    public function update(Request $request, $id) {}

    public function delete(Request $request, $id) {}


    /**
     * Set the columns for the grid response.
     *
     * @param array $columns Columns in the format required by Btx\Query\Transformer::quasarColumn
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setColumns(array $columns)
    {
        $this->_columns = Transformer::quasarColumn($columns);
    }


    /**
     * Set the model for this controller and initialize its query builder.
     *
     * @param string $model Fully qualified class name of the model
     * @return \Illuminate\Database\Eloquent\Builder<\Btx\Query\Model>
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setModel(string $model)
    {
        $this->_model = app($model);
        $this->_queryBuilder = $this->_model->newQuery();
        return $this->_queryBuilder;
    }


    /**
     * Set custom filter columns and parameter for LIKE queries in the grid.
     *
     * @param array $columns List of column names to apply LIKE filter
     * @param string $param The filter value to use in LIKE queries
     * @return void
     * @see https://btx.basapadi.com/query/request-query
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setFilterColumnsLike(array $columns, string $param)
    {
        $this->_filterColumnsLike = $columns;
        $this->_filterParamLike = $param;
    }


    /**
     * Enable or disable multiple select in the grid view.
     *
     * @param bool $value
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setMultipleSelectGrid(bool $value)
    {
        $this->_multipleSelectGrid = $value;
    }


    /**
     * Decode an encoded ID using Hashids.
     *
     * @param string $encodeId Encoded ID string
     * @return mixed Decoded ID or null on failure
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function decodeId(string $encodeId)
    {
        try {
            $decrypted = Hashids::decode($encodeId);
        } catch (Exception $e) {
            $decrypted = null;
        }
        return $decrypted;
    }


    /**
     * Set the module name for this controller.
     *
     * @param string $module Module name
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setModule(string $module)
    {
        $this->_module = $module;
    }


    /**
     * Check access permission for a specific module action.
     *
     * @param string $module Module name
     * @param string $action Action name (e.g., view, create, update)
     * @param bool $asBoolean If true, return boolean; otherwise abort with 401 on failure
     * @return bool|null
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function allowAccessModule(string $module, string $action, bool $asBoolean = false)
    {
        $auth = auth()->user();
        if (!empty($module) && !empty($action)) {
            $can = Menu::select('menus.id', 'module', 'role_menus.' . $action)
                ->where('module', $module)->join('role_menus', function ($join) use ($auth) {
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
     * Check access for a given action and return the action name if allowed, or an empty string if not.
     *
     * @param string $action Action name (e.g., view, create, edit, update, download)
     * @return string
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function allowAccess(string $action)
    {
        return $this->allowAccessModule($this->_module, $action, true) ? $action : '';
    }


    /**
     * Set the form fields for the controller.
     *
     * @param array $fields
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setForm(array $fields)
    {
        $this->_form = $fields;
    }


    /**
     * Set the detail schema for the view.
     *
     * @param array $schema
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setDetailSchema(array $schema)
    {
        $this->_detailSchema = $schema;
    }


    /**
     * Set grid properties for the controller.
     *
     * Available attributes in $properties:
     *  - multipleSelect (bool, default: true)
     *  - filterDateRange (bool, default: false)
     *  - advanceFilter (bool, default: true)
     *
     * @param array $properties
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setGridProperties(array $properties)
    {
        $this->_gridProperties = $properties;
    }
}
