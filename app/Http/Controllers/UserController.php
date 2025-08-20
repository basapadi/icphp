<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Btx\Query\Model;
use App\Models\User;

class UserController extends BaseController
{
    public function __construct(){
        $this->setModel(User::class);
        $this->setModule('master.user');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'status_label', 'label'=> 'Status', 'align' => 'left'],
            ['value' => 'username', 'label'=> 'Username', 'align' => 'left'],
            ['value' => 'name', 'label'=> 'Fullname', 'align' => 'left'],
            ['value' => 'role', 'label'=> 'Hak Akses', 'align' => 'left'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]]
        ]);
        $this->setFilterColumnsLike(['username','name'],request('q')??'');
    }
}
