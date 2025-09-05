<?php

namespace App\Http\Controllers;

use App\Models\{
    Trash
};
use Illuminate\Http\Request;
use App\Http\Response;
class TrashController extends BaseController
{
    public function __construct(){
        $this->setModel(Trash::class)->where('created_by', auth()->user()->id);
        $this->setModule('trash');
    }

    public function truncate(){
        $this->allowAccessModule('trash', 'delete');
        Trash::truncate();
        return Response::ok('Data berhasil dihapus');
    }
}
