<?php

namespace App\Http\Controllers;

use App\Http\Response;
use Illuminate\Http\Request;
use App\Models\Master;
use Exception;
use App\Objects\Notification;

class UnitController extends BaseController
{
    public function __construct(){
        $this->setModel(Master::class)->whereIn('type',['UNIT']);
        $this->setModule('master.unit');
        $this->setFilterColumnsLike(['kode','nama'],request('q')??'');

        $basicUnits = Master::select('id','nama')->where('type','BASIC_UNIT')->get()->pluck('nama','id')->toArray();
        $form = $this->getResourceForm('unit');
        injectData($form, [
            'basic_units' => $basicUnits
        ]);
        $this->setForm($form);
    }

    public function store(Request $request) {
        $rules = [
            'type' => 'required|string|in:UNIT,BASIC_UNIT',
            'kode' => 'required|string',
            'nama' => 'required|string',
            'status' => 'required|numeric|in:0,1',
            'to' => 'nullable|numeric',
            'conversion' => 'nullable|numeric',
            'id' => 'nullable|numeric',
        ];

        //validasi request
        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;
        $data = $request->all();
        $conversion = [];
        try {
            if(isset($request->to)&& isset($request->conversion)){
                $conversion['to'] = Master::select('id','kode','nama')->where('id',$request->to)->first()->toArray();
                $conversion['conversion'] = (int) $request->conversion;
            }

            if(!isset($request->id)){
                $this->allowAccessModule('master.unit', 'create');
                $exist = Master::where('nama',$data['nama'])->orWhere('kode',$data['kode'])->first();
                if($exist) return $this->setAlert('error','Duplikat Data','Data dengan nama atau kode yang anda masukkan sudah ada');

                $preInsert = [
                    'kode' => trim($data['kode']),
                    'nama' => trim($data['nama']),
                    'type' => trim($data['type']),
                    'status' => trim($data['status']),
                ];
                $preInsert['attributes'] = json_encode($conversion);
            
                Master::insert($preInsert);
                return $this->setAlert('info','Berhasil',$preInsert['nama'].' berhasil disimpan');
            } else {
                $this->allowAccessModule('master.unit', 'update');
                $exist = Master::where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');
                $exist->type = $data['type'];
                $exist->kode = $data['kode'];
                $exist->nama = $data['nama'];
                $exist->status = $data['status'];
                $exist->attributes = $conversion;
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
        $data = Master::where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');
        $form = $this->getResourceForm('unit');
        $basicUnits = Master::select('id','nama')->where('type','BASIC_UNIT')->get()->pluck('nama','id')->toArray();
        injectData($form, [
            'basic_units' => $basicUnits
        ]);
        $data['conversion'] = @$data->attributes->conversion ?? null;
        $data['to'] = @$data->attributes->to->id ?? null;
        return Response::ok('loaded',[
            'data' => $data,
            'sections' => $form
        ]);

    }
}
