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
            ['value' => 'status_label', 'label'=> 'Status Aktif', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'status', 'label'=> 'Status', 'align' => 'left', 'type' => 'select', 'show' => false, 'option_filter' => true,'options' => [
                ['label' => 'Aktif', 'value' => '1'],
                ['label' => 'Tidak Aktif', 'value' => '0']
            ]]
        ]);
        $this->setFilterColumnsLike(['kode','nama'],request('q')??'');
        $this->setForm([
            ['name' => 'type','type' => 'select', 'label' =>'Tipe','required' => true,'hint' => 'Pilih Tipe', 'options' => [
                '0' => 'Unit',
                '1' => 'Basic Unit'
            ]],
            ['name' => 'code','type' => 'text', 'label' =>'Kode','required' => true,'hint' => 'Masukkan Kode Satuan'],
            ['name' => 'nama','type' => 'text', 'label' =>'Nama','required' => false,'hint' => 'Masukkan Nama Satuan'],
            ['name' => 'status','type' => 'radio', 'label' => 'Status','hint' => 'Status Kontak', 'options' => [
                '0' => 'Tidak Aktif',
                '1' => 'Aktif'
            ]],
        ]);
    }
}
