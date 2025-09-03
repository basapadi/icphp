<?php

namespace App\Http\Controllers;

use App\Models\{
    Trash
};
use Illuminate\Http\Request;

class TrashController extends BaseController
{
    public function __construct(){
        $this->setModel(Trash::class)->where('created_by', auth()->user()->id);
        $this->setModule('trash');
    }
}
