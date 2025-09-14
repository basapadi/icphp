<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Exception;
use App\Objects\Notification;
use App\Http\Response;

class ContactController extends BaseController
{
    public function __construct(){
        $this->setModel(Contact::class);
        $this->setModule('master.contact');
        $this->setFilterColumnsLike(['nama','telepon'],request('q')??'');
        $form = $this->getResourceForm('contact');
        $this->setForm($form);
    }

    public function store(Request $request) {
        $rules = [
            'type' => 'required|string|in:pelanggan,pemasok',
            'email' => 'required|string',
            'telepon' => 'required|string',
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'status' => 'required|numeric|in:0,1',
            'id' => 'nullable|numeric',
        ];

        //validasi request
        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;
        try {
            if(!isset($request->id)){
                $this->allowAccessModule('master.contact', 'create');
                $exist = Contact::where('telepon',$data['telepon'])->orWhere('email',$data['email'])->first();
                if($exist) return $this->setAlert('error','Duplikat Data','Data dengan telepon atau email yang anda masukkan sudah ada');

                $preInsert = [
                    'email' => trim($data['email']),
                    'telepon' => trim($data['telepon']),
                    'nama' => trim($data['nama']),
                    'type' => trim($data['type']),
                    'alamat' => trim($data['alamat']),
                    'status' => trim($data['status']),
                ];

                Contact::insert($preInsert);
                return $this->setAlert('info','Berhasil',$preInsert['nama'].' berhasil disimpan');
            } else {
                $this->allowAccessModule('master.contact', 'update');

                $exist = Contact::where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');
                
                $exist->type    = $data['type'];
                $exist->nama    = $data['nama'];
                $exist->email   = $data['email'];
                $exist->telepon = $data['telepon'];
                $exist->alamat  = $data['alamat'];
                $exist->status  = $data['status'];

                $exist->save();
                return $this->setAlert('info','Berhasil',$exist->nama.' berhasil disimpan');
            }
        }catch(Exception $e){
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('master.contact', 'edit');
        $id = $this->decodeId($id);
        $data = Contact::where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');
        $form = $this->getResourceForm('contact');
        return Response::ok('loaded',[
            'data' => $data,
            'sections' => $form
        ]);
    }
}
