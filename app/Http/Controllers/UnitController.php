<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Master;

class UnitController extends BaseController
{
    public function __construct(){
        $this->setModel(Master::class)->whereIn('type',['UNIT']);
        $this->setModule('master.unit');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'actions', 'label'=> 'aksi', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]],
            ['value' => 'type', 'label'=> 'Tipe', 'align' => 'left','option_filter' => true, 'type' => 'select','options' => [
                ['label' => 'Basic Unit', 'value' => 'BASIC_UNIT'],
                ['label' => 'Unit', 'value' => 'UNIT']
            ]],
            ['value' => 'kode', 'label'=> 'Kode', 'align' => 'left'],
            ['value' => 'nama', 'label'=> 'Nama', 'align' => 'left'],
            ['value' => 'status_label', 'label'=> 'Status', 'align' => 'left', 'type' => 'badge','styles' => 'width:50px;'],
            ['value' => 'status', 'label'=> 'Status', 'align' => 'left', 'type' => 'select', 'show' => false, 'option_filter' => true,'options' => [
                ['label' => 'Aktif', 'value' => '1'],
                ['label' => 'Tidak Aktif', 'value' => '0']
            ]]
        ]);
        $this->setFilterColumnsLike(['kode','nama'],request('q')??'');
        $this->setForm([
            'main' => [
                'label' => 'Form Utama',
                'forms' => [
                    ['name' => 'type','type' => 'select', 'label' =>'Tipe','required' => true,'hint' => 'Pilih Tipe', 'options' => [
                        'UNIT' => 'Unit',
                        'BASIC_UNIT' => 'Basic Unit'
                    ]],
                    ['name' => 'code','type' => 'text', 'label' =>'Kode','required' => true,'hint' => 'Masukkan Kode Satuan'],
                    ['name' => 'nama','type' => 'text', 'label' =>'Nama','required' => true,'hint' => 'Masukkan Nama Satuan'],
                    ['name' => 'status','type' => 'radio','required' => true, 'label' => 'Status','hint' => 'Status Kontak', 'options' => [
                        '0' => 'Tidak Aktif',
                        '1' => 'Aktif'
                    ]]
                ]
            ]
        ]);
    }
}
