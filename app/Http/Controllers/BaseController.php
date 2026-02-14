<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Btx\Query\Model;
use Btx\File\Upload;
use Btx\Query\Transformer;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Fractal\Fractal;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\{
    Menu,
    AuditLog
};
use App\Transformers\FormTransformer;
use Spatie\Fractalistic\ArraySerializer;
use App\Http\Response;
use App\Traits\{HasQueryBuilder, QueryHelper, DataBuilder, BaseHelper, Services};
use App\Models\Trash;
use App\Objects\ContextMenu;
use Illuminate\Support\Facades\Storage;
use App\Http\Guard\RoleGuard;

class BaseController extends Controller
{
    use HasQueryBuilder, QueryHelper, DataBuilder, BaseHelper, Services;

    private Model $_model;
    private ?Fractal $_columns;
    private ?array $_filterColumnsLike = [];
    private string $_filterParamLike = '';
    protected $_queryBuilder;
    private bool $_multipleSelectGrid = true;
    protected string $_module = '';
    private ?array $_form = [];
    protected ?array $_gridProperties = [];
    protected ?array $_detailSchema = [];
    private ?array $_createRules = [];
    private ?array $_updateRules = [];
    private ?array $_formData = [];
    protected ?array $_contextMenus = [];
    private ?array $_injectDataColumns = [];
    private ?array $_exceptContextMenu = [];

    public function __construct()
    {
        // RoleGuard::IsAdminUser();
    }

    /**
     * Handle grid request to fetch data with filtering and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
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
        $rows = $rows->get();
        $total = $total->count();

        if (!empty($this->_mergeData)) $this->mergeData($rows);
        $this->defaultContextMenu();
        $this->_gridProperties['filterDateRange'] = $this->_gridProperties['filterDateRange'] ?? false;
        $this->_gridProperties['advanceFilter'] = $this->_gridProperties['advanceFilter'] ?? true;
        $this->_gridProperties['simpleFilter'] = $this->_gridProperties['simpleFilter'] ?? true;
        $this->_gridProperties['multipleSelect'] = $this->_gridProperties['multipleSelect'] ?? $this->_multipleSelectGrid;
        $this->_gridProperties['contextMenu'] = $this->_contextMenus;
        if (empty($this->_detailSchema)) $this->getDetailSchema();
        return Response::ok('Loaded', [
            'rows' => $rows->toArray(),
            'total' => $total,
            'columns' => $this->getColumns(),
            'properties' => $this->_gridProperties,
            'detail_schemes' => $this->_detailSchema,
            'module' => $this->_module
        ]);
    }

    /**
     * Handle form request to fetch form configuration and data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    public function form(Request $request)
    {
        $this->allowAccessModule($this->_module, 'create');
        $forms = [];
        $dialog = [
            'width' => 2
        ];
        foreach ($this->_form as $key => $f) {
            if ($key == 'dialog') {
                $dialog = $f;
                continue;
            }
            $nf = $f;
            $nf['forms'] = fractal($f['forms'], new FormTransformer(), ArraySerializer::class);
            $forms[$key] = $nf;
        }
        return Response::ok('Form', [
            'sections' => $forms,
            'dialog' => $dialog,
            'data' => $this->_formData
        ]);
    }

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
        return $decrypted[0];
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

        $role = (string) auth()->user()->role;
        $configMenu = collect(config('ihandcashier.menus'))->where('module', $module)->first();
        if (empty($configMenu)) return false;
        if (isset($configMenu['hide']) && in_array($action, $configMenu['hide'])) return false;

        if (!empty($module) && !empty($action)) {
            $can = Menu::select('menus.id', 'module', 'role_menus.' . $action)
                ->where('module', $module)
                ->join('role_menus', function ($join) use ($role) {
                    $join->on('role_menus.menu_id', '=', 'menus.id')
                        ->where('role_menus.role', '=', $role); // ← perbaikan
                })
                ->first()
                ->{$action};
            return !$can ? (!$asBoolean ? abort(response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki hak akses.'
            ], 401)) : false) : true;
        }
        return !$asBoolean ? abort(response()->json([
            'status' => false,
            'message' => 'Anda tidak memiliki hak akses.'
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
    protected function setDataDefaultForm(array $fields, array $data = [])
    {
        $this->_form = $fields;
        $this->_formData = $data;
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

    /**
     * Set additional contextmenu on grid.
     * @param array $contextMenu ContextMenu
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setContextMenu(array $contextMenu)
    {
        $ctxMenu = [];
        foreach ($contextMenu as $key => $cm) {
            if (empty($cm)) continue;
            $cm->module = $this->_module;
            array_push($ctxMenu, $cm);
        }
        $this->_contextMenus = $ctxMenu;
    }

    protected function clearContextMenu()
    {
        $this->_contextMenus = [];
    }

    /**
     * Get the detail schema from the JSON file.
     *
     * @return array
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function getDetailSchema($schema = null)
    {
        $schema = $schema ?? $this->_module;
        $path = base_path('resources/data/detail_schemas/' . $schema . '.json');
        $schema = file_get_contents($path);
        $this->_detailSchema = json_decode($schema, true);
    }

    /**
     * Get the resource form configuration from the JSON file.
     *
     * @param string $name Form name
     * @return array
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function getResourceForm($name)
    {
        $path = base_path('resources/data/forms/' . $name . '.json');
        $form = file_get_contents($path);
        return json_decode($form, true);
    }

    protected function getResourceDefaultContent($name)
    {
        $path = base_path('resources/data/defaults/contents/' . $name . '.html');
        if (file_exists($path)) {
            $form = file_get_contents($path);
            return $form;
        }
        return '';
    }

    protected function getResourceEvaluationForm($name)
    {
        $path = base_path('resources/data/forms/evaluasi/' . $name . '.json');
        $form = file_get_contents($path);
        return json_decode($form, true);
    }

    /**
     * Get the resource query configuration from the file.
     *
     * @param string $path Path to the query file
     * @return array
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function getResourceQuery($path)
    {
        $basepath = base_path('resources/' . $path);
        $query = file_get_contents($basepath);
        return json_decode($query, true);
    }

    protected function getResourceSql($path)
    {
        $basepath = base_path('resources/' . $path);
        $query = file_get_contents($basepath);
        return $query;
    }

    /**
     * Get static resource data from the JSON file.
     *
     * @param string $name Static resource name
     * @return array
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function getResourceStatic($name)
    {
        $path = base_path('resources/data/statics/' . $name . '.json');
        $data = file_get_contents($path);
        return json_decode($data, true);
    }

    /**
     * Set data columns to be injected into the grid columns.
     *
     * @param array $data Data to inject
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function setInjectDataColumn(array $data)
    {
        $this->_injectDataColumns = $data;
    }

    /**
     * set exception contextmenu on grid
     * @param array $contextmenukey array of default contextmenu ['view','create','edit','delete']
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     * */
    protected function setExceptContextMenu(array $contextmenukey)
    {
        $this->_exceptContextMenu = $contextmenukey;
    }

    /**
     * Get the columns configuration for the grid, injecting data and filtering actions.
     *
     * @return array
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function getColumns($filename = null)
    {
        $filename = $filename ?? $this->_module;
        $path = base_path('resources/data/columns/' . $filename . '.json');
        $schema = json_decode(file_get_contents($path), true);
        injectDataColumn($schema, $this->_injectDataColumns);
        $schema = collect($schema)->map(function ($col) {
            if ($col['name'] == 'actions') {
                $options = [];
                foreach ($col['options'] as $o) {
                    if (in_array($o, ['view', 'edit', 'update', 'delete'])) {
                        $op = $this->allowAccess($o);
                        if ($op != '') array_push($options, $op);
                    } else array_push($options, $o);

                    if (in_array($o, ['no_view', 'no_create', 'no_edit', 'no_update', 'no_delete'])) {
                        $no =  explode('_', $o);
                        $options = array_diff($options, [explode('_', $o)[1]]);
                    }
                }
                $col['options'] = $options;
            }
            return $col;
        });
        return $schema;
    }

    /**
     * Generate and save the detail schema to a JSON file.
     *
     * @param array $data Schema data
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    private function generateDetailSchemaToJson($data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        $path = base_path('resources/data/detail_schemas/' . $this->_module . '.json');
        file_put_contents($path, $json);
    }

    /**
     * Generate and save the columns configuration to a JSON file.
     *
     * @param array $data Columns data
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    private function generateColumnsToJson($data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        $path = base_path('resources/data/columns/' . $this->_module . '.json');
        file_put_contents($path, $json);
    }

    /**
     * Save deleted data to the trash for potential rollback.
     *
     * @param mixed $data The data model instance being deleted
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    private function saveTrash($data)
    {
        Trash::create([
            'module' => $data->__module,
            'data' => json_encode($data),
            'created_by' => $data->__user,
            'can_rollback' => $data->__can_rollback,
            'schema' => json_encode($this->getDetailSchema())
        ]);
    }

    /**
     * Initialize default context menu items based on permissions.
     *
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    protected function defaultContextMenu()
    {
        $menus = [
            'view' => ['method' => 'viewData', 'label' => 'Detail', 'icon' => 'SquareChartGantt', 'color' => '#009688'],
            'create' => ['method' => 'tambahData', 'label' => 'Tambah', 'icon' => 'SquarePlus', 'color' => '#FF9800'],
            'edit' => ['method' => 'editData', 'label' => 'Ubah', 'icon' => 'SquarePen', 'color' => '#3F51B5'],
            'delete' => ['method' => 'hapusData', 'label' => 'Hapus', 'icon' => 'SquareX', 'color' => '#F44336']
        ];

        foreach (array_keys($menus) as $key) {
            if (!$this->allowAccessModule($this->_module, $key, true)) {
                unset($menus[$key]);
            }
        }

        $menus = array_diff_key($menus, array_flip($this->_exceptContextMenu));

        $menu = new ContextMenu('reload', 'Muat Ulang');
        $menu->onClick = 'load';
        $menu->icon = 'RefreshCcw';
        $menu->color = '#4CAF50';
        array_push($this->_contextMenus, $menu);

        $columnEditor = new ContextMenu('editor', 'Kolom Editor');
        $columnEditor->onClick = 'openColumnEditor';
        $columnEditor->icon = 'Columns3';
        $columnEditor->color = '#009ac0ff';
        array_push($this->_contextMenus, $columnEditor);

        foreach ($menus as $key => $cm) {
            $allowAccess = $this->allowAccess($key);
            if ($allowAccess != '') {
                $menu = new ContextMenu($key, $cm['label']);
                $menu->onClick = $cm['method'];
                $menu->icon = $cm['icon'];
                $menu->color = $cm['color'];
                array_push($this->_contextMenus, $menu);
            }
        }
    }

    /**
     * Store a new resource.
     *
     * @param Request $request
     * @return void
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    public function store(Request $request)
    {
        $this->allowAccessModule($this->_module, 'create');

        //TODO:: Store default

    }

    /**
     * Delete a resource by ID.
     *
     * @param Request $request
     * @param string $id Encoded ID
     * @return \Illuminate\Http\JsonResponse
     * @author bachtiarpanjaitan <bachtiarpanjaitan0@gmail.com>
     */
    public function delete(Request $request, $id)
    {
        $this->allowAccessModule($this->_module, 'delete');
        if (empty($id)) return Response::badRequest('ID tidak boleh kosong');
        $id = $this->decodeId($id);
        if (empty($id)) return Response::badRequest('ID tidak ditemukan');
        try {
            DB::beginTransaction();
            $data = $this->_model->find($id);
            $data->__module = $this->_module;
            $data->__user = auth()->user()->id;
            $data->__can_rollback = true;

            $relations = ['details', 'details.unit', 'details.item', 'payments', 'unit', 'item', 'contact', 'invoice'];
            $data->loadRelationsWithNested($relations);
            $data->delete();
            $this->saveTrash($data);
            $this->saveAuditLog('delete', 'Data deleted', $data->toArray());
            DB::commit();
            return $this->setAlert('info', 'Berhasil', 'Data berhasil dihapus, silahkan periksa keranjang sampah untuk melihat data terhapus');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->setAlert('error', 'Gagal!', $e->getMessage());
        }
    }

    protected function privateUpload($filename, $path = '')
    {

        $file = request()->file($filename);
        $safePath = trim(str_replace(['..', './', '\\'], '', $path), '/');

        if (!Storage::exists($safePath)) {
            Storage::makeDirectory($safePath);
        }
        $originalFilename = $file->getClientOriginalName();
        $filename = uniqid() . '_' . $filename . '.' . $file->getClientOriginalExtension();
        $storedPath = $file->storeAs($safePath, $filename);
        $storedPath = str_replace('\\', '/', $storedPath);
        $url = route('file', ['path' => $storedPath]);
        return [
            'path'  => $storedPath,
            'url'   => $url,
            'filename' => $originalFilename
        ];
    }

    protected function publicUpload(string $fieldName, ?string $subFolder = null)
    {
        $files = request()->file($fieldName);

        if (!$files) {
            return null;
        }

        // pastikan selalu array (single file ikut antre)
        $files = is_array($files) ? $files : [$files];

        // sanitasi nama folder
        $subFolder = $subFolder
            ? trim(str_replace(['..', '/', '\\'], '', $subFolder), '/')
            : '';

        $basePath = public_path('uploads');
        $uploadPath = $subFolder
            ? $basePath . '/' . $subFolder
            : $basePath;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $results = [];

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $originalFilename = $file->getClientOriginalName();

            $filename = uniqid() . '_' . preg_replace(
                '/[^a-zA-Z0-9._-]/',
                '_',
                $originalFilename
            );

            $file->move($uploadPath, $filename);

            $relativePath = $subFolder
                ? 'uploads/' . $subFolder . '/' . $filename
                : 'uploads/' . $filename;

            $results[] = [
                'path'     => $relativePath,
                'url'      => url($relativePath),
                'filename' => $originalFilename,
            ];
        }

        // kalau cuma satu file, kembalikan satu object biar tidak nyusahin caller
        return count($results) === 1 ? $results[0] : $results;
    }

    protected function publicMultipleUpload(string $fieldName, ?string $subFolder = null)
    {
        $files = request()->file($fieldName);

        if (!$files) {
            return null;
        }

        // pastikan selalu array (single file ikut antre)
        $files = is_array($files) ? $files : [$files];

        // sanitasi nama folder
        $subFolder = $subFolder
            ? trim(str_replace(['..', '/', '\\'], '', $subFolder), '/')
            : '';

        $basePath = public_path('uploads');
        $uploadPath = $subFolder
            ? $basePath . '/' . $subFolder
            : $basePath;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $results = [];

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $originalFilename = $file->getClientOriginalName();

            $filename = uniqid() . '_' . preg_replace(
                '/[^a-zA-Z0-9._-]/',
                '_',
                $originalFilename
            );

            $file->move($uploadPath, $filename);

            $relativePath = $subFolder
                ? 'uploads/' . $subFolder . '/' . $filename
                : 'uploads/' . $filename;

            $results[] = [
                'path'     => $relativePath,
                'url'      => url($relativePath),
                'filename' => $originalFilename,
            ];
        }

        return $results;
    }

    protected function saveAuditLog($action, $description = null, array $context = [])
    {
        AuditLog::create([
            'user_id' => !empty(auth()->user()->id) ? auth()->user()->id : null,
            'module' => $this->_module,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => json_encode($context),
        ]);
    }

    protected function extractLevel3WithBreadcrumb(array $items, array $parents = []): array
    {
        $fields = [];

        foreach ($items as $item) {

            $currentParents = $parents;

            if (!empty($item['label'])) {
                $currentParents[] = $item['label'];
            }

            // Ketemu input (level 3 + ada type)
            if (($item['level'] ?? null) === 3 && isset($item['type'])) {

                $field = $item;

                // Override label jadi breadcrumb
                $field['label'] = implode(' ▸ ', $currentParents);

                $fields[] = $field;
            }

            // Rekursif ke child
            if (!empty($item['child']) && is_array($item['child'])) {
                $fields = array_merge(
                    $fields,
                    $this->extractLevel3WithBreadcrumb($item['child'], $currentParents)
                );
            }
        }

        return $fields;
    }

    public function arrayToJson(array $array)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$key] = $value;
        }

        return response()->json(
            $result,
            200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }
}
