<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Btx\Http\Response;
use Btx\Http\Libraries\ApiResponse;
use Btx\Query\Transformer;
use App\Models\{
    Menu,
    RoleMenu
};
use Exception;

class RoleController extends BaseController
{
    // override function grid di BaseController
    public function grid(Request $request){
        $this->allowAccessModule('setting.role','view');
        $configMenus = collect(config('ihandcashier.menus'));
        $query = RoleMenu::select('role_menus.*')->with(['menu','menu.parent'])->leftJoin('menus', 'role_menus.menu_id', '=', 'menus.id');
        if(isset($request->q) && $request->q != ''){
            $query->whereRaw('LOWER(role_menus.role) LIKE ?', ['%'.strtolower($request->q).'%'])->orWhereRaw('LOWER(menus.label) LIKE ?', ['%'.strtolower($request->q).'%']);
        }
        $tQuery = clone $query;
        $show = [
            'view'      => 1,
            'create'    => 1,
            'edit'      => 1,
            'update'    => 1,
            'delete'    => 1,
            'download'  => 1
        ];
        $data = $query->filter()->get()->map(function($role) use ($configMenus,$show){
            $roleShow = $show;
            $cm = $configMenus->where('id',$role->menu_id)->first();
            if($cm && isset($cm['hide'])){
                foreach ($cm['hide'] as $hide) {
                    $roleShow[$hide] = 0;
                }
            }
            if($role->menu->route == '#'){
                $roleShow = [
                    'view'      => 1,
                    'create'    => 0,
                    'edit'      => 0,
                    'update'    => 0,
                    'delete'    => 0,
                    'download'  => 0
                ];
            }

            $role->show = $roleShow;
            return $role;
        });
        $total = $tQuery->filter(false)->count();
        return Response::ok('Loaded', [
            'rows' => $data->toArray(), 
            'columns' => Transformer::quasarColumn([
                // ['value' => 'id', 'label'=> 'Id', 'align' => 'left'],
                ['value' => 'role', 'label'=> 'Hak Akses', 'align' => 'left'],
                ['value' => 'menu__module', 'label'=> 'Module', 'align' => 'left'],
                ['value' => 'menu__route', 'label'=> 'Route', 'align' => 'left', 'type' => 'text','class' => 'italic'],
                ['value' => 'menu__parent__label', 'label'=> 'Parent Menu', 'align' => 'left'],
                ['value' => 'menu__label', 'label'=> 'Menu', 'align' => 'left'],
                ['value' => 'view', 'label'=> 'View', 'align' => 'left'],
                ['value' => 'create', 'label'=> 'Add', 'align' => 'left'],
                ['value' => 'edit', 'label'=> 'Edit', 'align' => 'left'],
                ['value' => 'update', 'label'=> 'Update', 'align' => 'left'],
                ['value' => 'delete', 'label'=> 'Delete', 'align' => 'left'],
                ['value' => 'download', 'label'=> 'Download', 'align' => 'left']
            ]),
            'total' => $total,
            'properties' => [
                'multipleSelect' => false 
            ]
        ]);
    }

    public function update(Request $request, $id){
        try {
            $id = $this->decodeId($id);
            if(empty($id)) return Response::badRequest('ID tidak ditemukan');
            $role = RoleMenu::where('id', $id)->first();
            if(empty($role)) return Response::badRequest('Data tidak ditemukan');
            $role->{$request->column} = $request->value;
            $role->save();

            return Response::ok('Data berhasil disimpan');
        }catch(Exception $e){
            return Response::internalServerError($e->getMessage());
        }
        
    }
}
