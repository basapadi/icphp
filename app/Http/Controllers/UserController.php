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
            ['value' => 'actions', 'label'=> 'Aksi', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]],
            ['value' => 'username', 'label'=> 'Username', 'align' => 'left','option_filter' => true],
            ['value' => 'email', 'label'=> 'Email', 'align' => 'left','option_filter' => true],
            ['value' => 'name', 'label'=> 'Fullname', 'align' => 'left','option_filter' => true],
            ['value' => 'role', 'label'=> 'Hak Akses', 'align' => 'left','type' => 'select', 'option_filter' => true, 'options' => roleConfigToOptions()],
            ['value' => 'status_label', 'label'=> 'Status', 'align' => 'left', 'type' => 'badge','styles' => 'width:50px;'],
            ['value' => 'status', 'label'=> 'Status', 'align' => 'left', 'type' => 'select', 'show' => false, 'option_filter' => true,'options' => [
                ['label' => 'Aktif', 'value' => '1'],
                ['label' => 'Tidak Aktif', 'value' => '0']
            ]] 
        ]);
        $this->setFilterColumnsLike(['username','name'],request('q')??'');
    }
}
