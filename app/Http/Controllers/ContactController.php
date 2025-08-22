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
            ['value' => 'status_label', 'label'=> 'Status', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]]
        ]);
        $this->setFilterColumnsLike(['nama','telepon'],request('q')??'');
        $this->setForm([
            ['name' => 'type','type' => 'select', 'label' =>'Tipe Kontak','required' => true,'hint' => 'Silahkan Pilih Tipe Kontak'],
            ['name' => 'nama','type' => 'text', 'label' => 'Nama','required' => true,'hint' => 'Nama Kontak'],
            ['name' => 'alamat','type' => 'textarea', 'label' => 'Alamat','required' => true,'hint' => 'Alamat'],
            ['name' => 'telepon','type' => 'phone', 'label' => 'Telepon','required' => true,'hint' => 'Telepon'],
            ['name' => 'email','type' => 'email', 'label' => 'Email','hint' => 'Email'],
            ['name' => 'status','type' => 'radio', 'label' => 'Status','hint' => 'Status Kontak', 'options' => [
                '0' => 'Tidak Aktif',
                '1' => 'Aktif'
            ]],
        ]);
    }
}
