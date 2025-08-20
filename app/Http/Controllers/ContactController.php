<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ContactController extends BaseController
{
    public function __construct(){
        $this->setModel(Contact::class);
        $this->setModule('master.contact');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'type', 'label'=> 'Tipe', 'align' => 'left'],
            ['value' => 'nama', 'label'=> 'Nama', 'align' => 'left'],
            ['value' => 'alamat', 'label'=> 'Alamat', 'align' => 'left'],
            ['value' => 'telepon', 'label'=> 'Telepon', 'align' => 'left'],
            ['value' => 'email', 'label'=> 'Email', 'align' => 'left'],
            ['value' => 'status_label', 'label'=> 'Status', 'align' => 'left'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]]
        ]);
        $this->setFilterColumnsLike(['nama','telepon'],request('q')??'');
    }
}
