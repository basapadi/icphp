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
class RoleController extends BaseController
{
    public function __construct(){

    }

    public function grid(Request $request){
        $query = RoleMenu::with('menu');
        $rows = $query->filter();
        if(isset($request->q) && $request->q != ''){
            $rows->orWhereRaw('LOWER(role) LIKE ?', ['%'.strtolower($request->q).'%']);
        }
        $rows = $rows->get();
        return Response::ok('Loaded', [
            'rows' => $rows->toArray(), 
            'columns' => Transformer::quasarColumn([
                ['value' => 'role', 'label'=> 'Hak Akses', 'align' => 'left'],
                ['value' => 'menu__label', 'label'=> 'Menu', 'align' => 'left'],
                ['value' => 'view', 'label'=> 'Lihat', 'align' => 'left'],
                ['value' => 'create', 'label'=> 'Tambah', 'align' => 'left'],
                ['value' => 'delete', 'label'=> 'Hapus', 'align' => 'left'],
                ['value' => 'download', 'label'=> 'Unduh', 'align' => 'left']
            ]),
            'properties' => [
                'multipleSelect' => false 
            ]
        ]);
    }
}
