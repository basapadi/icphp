<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends BaseController
{
    public function __construct(){
        $this->setModel(Item::class);
        $this->setModule('master.item');
        $this->setColumns([
            // ['value' => 'id', 'label'=> 'ID', 'align' => 'left', 'show' => false],
            ['value' => 'kode_barang', 'label'=> 'Kode Barang', 'align' => 'left'],
            ['value' => 'nama', 'label'=> 'Nama', 'align' => 'left'],
            // ['value' => 'gambar', 'label'=> 'Gambar', 'align' => 'left'],
            ['value' => 'kategori', 'label'=> 'Kategori', 'align' => 'left'],
            ['value' => 'status_label', 'label'=> 'Status', 'align' => 'left', 'type' => 'badge'],
            ['value' => 'actions', 'label'=> 'Actions', 'align' => 'left','options' => [$this->allowAccess('edit'),$this->allowAccess('delete')]]
        ]);
        $this->setFilterColumnsLike(['kode_barang','nama'],request('q')??'');
        // $this->setMultipleSelectGrid(false);
        $this->setForm([
            ['name' => 'kode_barang','type' => 'text', 'label' =>'Kode Barang','required' => true,'hint' => 'Masukkan Kode Barang'],
            ['name' => 'nama','type' => 'text', 'label' =>'Nama Barang','required' => true,'hint' => 'Masukkan Nama Barang'],
            ['name' => 'gambar','type' => 'text', 'label' =>'Gambar','required' => false,'hint' => 'Gambar'],
            ['name' => 'kategori','type' => 'select', 'label' =>'Kategori','required' => true,'hint' => 'Pilih Kategori', 'options' => [
                '0' => 'ELektronik',
                '1' => 'Alat Rumah Tangga'
            ]],
            ['name' => 'status','type' => 'radio', 'label' => 'Status','hint' => 'Status Kontak', 'options' => [
                '0' => 'Tidak Aktif',
                '1' => 'Aktif'
            ]],
        ]);
    }
}
