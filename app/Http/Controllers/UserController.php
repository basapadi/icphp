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
        $this->setFilterColumnsLike(['username','name'],request('q')??'');
    }
}
