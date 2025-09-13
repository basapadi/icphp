<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Exception;
use Illuminate\Http\Request;
use App\Http\Upload;
use App\Http\Response;
class ItemController extends BaseController
{
    public function __construct(){
        $this->setModel(Item::class);
        $this->setModule('master.item');
        $this->setFilterColumnsLike(['kode_barang','nama'],request('q')??'');
        // $this->setMultipleSelectGrid(false);
        $form = $this->getResourceForm('item');
        $categories = $this->getResourceStatic('categories');
        injectData($form, [
            'categories' => $categories
        ]);
        // dd($form);
        $this->setForm($form);
    }

    public function store(Request $request) {
        $rules = [
            'kode_barang' => 'required|string',
            'barcode' => 'string',
            'nama' => 'required|string',
            'status' => 'required|numeric|in:0,1',
            'gambar' => 'nullable|file|mimes:jpg,png|max:2048',
            'kategori' => 'nullable|string'
        ];

        $data = $this->validate($rules);
        try {
            
            $path = null;
            if ($request->hasFile('gambar')) {
                if ($request->file('gambar')->isValid()) {
                    $path = Upload::image([
                        'file' => 'gambar',
                        'size' => [500,500],
                        'path' => 'uploads/item'
                    ]);
                }
            }

            if(!isset($request->id)){
                $this->allowAccessModule('master.item', 'create');
                $exist = Item::where('nama',$data['nama'])->orWhere('kode_barang',$data['kode_barang'])->orWhere('barcode',$data['barcode'])->first();
                if($exist) return $this->setAlert('error','Duplikat Data','Data dengan nama atau SKU atau barcode yang anda masukkan sudah ada');
                
                $preInsert = [
                    'kode_barang'   => trim($data['kode_barang']),
                    'barcode'   => trim($data['barcode']),
                    'nama'   => trim($data['nama']),
                    'status'   => trim($data['status']),
                    'kategori'   => trim($data['kategori']),
                ];
                if($path != null){
                    if(!isset($path['status'])) $preInsert['gambar'] = $path['path'];
                    else return $this->setAlert('error','Gagal!', $path['message']);
                }

                Item::insert($preInsert);
                return $this->setAlert('info','Berhasil',$preInsert['nama'].' berhasil disimpan');
            }else{
                $this->allowAccessModule('master.item', 'update');
                $exist = Item::where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');
                $exist->kode_barang = trim($data['kode_barang']);
                $exist->barcode = trim($data['barcode']);
                $exist->nama = trim($data['nama']);
                $exist->status = trim($data['status']);
                $exist->kategori = trim($data['kategori']);

                if($path != null){
                    if(!isset($path['status'])) $exist->gambar = $path['path'];
                    else return $this->setAlert('error','Gagal!', $path['message']);
                }
                
                $exist->save();

                return $this->setAlert('info','Berhasil',$exist->nama.' berhasil disimpan');
            }
        }catch(Exception $e){
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('master.item', 'edit');
        $id = $this->decodeId($id);
        $data = Item::where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');
        $form = $this->getResourceForm('item');
        $categories = $this->getResourceStatic('categories');
        injectData($form, [
            'categories' => $categories
        ]);
        return Response::ok('loaded',[
            'data' => $data,
            'sections' => $form
        ]);

    }
}
