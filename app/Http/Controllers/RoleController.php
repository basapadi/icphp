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
        $query = RoleMenu::select('role_menus.*')->with(['menu','menu.parent'])->leftJoin('menus', 'role_menus.menu_id', '=', 'menus.id');
        $rows = $query->filter();
        if(isset($request->q) && $request->q != ''){
            $query->whereRaw('LOWER(role_menus.role) LIKE ?', ['%'.strtolower($request->q).'%'])->orWhereRaw('LOWER(menus.label) LIKE ?', ['%'.strtolower($request->q).'%']);
        }
        $rows = $rows->get();
        return Response::ok('Loaded', [
            'rows' => $rows->toArray(), 
            'columns' => Transformer::quasarColumn([
                // ['value' => 'id', 'label'=> 'Id', 'align' => 'left'],
                ['value' => 'role', 'label'=> 'Hak Akses', 'align' => 'left'],
                ['value' => 'menu__module', 'label'=> 'Modul', 'align' => 'left'],
                ['value' => 'menu__route', 'label'=> 'Route', 'align' => 'left'],
                ['value' => 'menu__parent__label', 'label'=> 'Parent Menu', 'align' => 'left'],
                ['value' => 'menu__label', 'label'=> 'Menu', 'align' => 'left'],
                ['value' => 'view', 'label'=> 'Lihat', 'align' => 'left'],
                ['value' => 'create', 'label'=> 'Tambah', 'align' => 'left'],
                ['value' => 'edit', 'label'=> 'Ubah', 'align' => 'left'],
                ['value' => 'delete', 'label'=> 'Hapus', 'align' => 'left'],
                ['value' => 'download', 'label'=> 'Unduh', 'align' => 'left']
            ]),
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
