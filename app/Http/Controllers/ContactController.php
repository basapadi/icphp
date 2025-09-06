<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ContactController extends BaseController
{
    public function __construct(){
        $this->setModel(Contact::class);
        $this->setModule('master.contact');
        $this->setFilterColumnsLike(['nama','telepon'],request('q')??'');
        $this->setForm([
            'main' => [
                'label' => 'Form Satuan Barang',
                'column' => 2,
                'forms' => [
                    ['name' => 'type','type' => 'select', 'label' =>'Tipe Kontak','required' => true,'hint' => 'Silahkan Pilih Tipe Kontak', 'options' => [
                        'pelanggan' => 'Pelanggan',
                        'pemasok' => 'Pemasok'
                    ]],
                    ['name' => 'nama','type' => 'text', 'label' => 'Nama','required' => true,'hint' => 'Nama Kontak'],
                    ['name' => 'telepon','type' => 'phone', 'label' => 'Telepon','required' => true,'hint' => 'Telepon', 'format' => '^(?:\\+62|62|0)8[1-9][0-9]{6,10}$'],
                    ['name' => 'email','type' => 'email', 'label' => 'Email','hint' => 'Email'],
                    ['name' => 'alamat','type' => 'textarea', 'label' => 'Alamat','required' => true,'hint' => 'Alamat'],
                    ['name' => 'status','type' => 'radio','required' => true,'direction'=> 'row', 'label' => 'Status','hint' => 'Status Kontak', 'options' => [
                        '0' => 'Tidak Aktif',
                        '1' => 'Aktif'
                    ]]
                ]
            ]
        ]);
    }
}
