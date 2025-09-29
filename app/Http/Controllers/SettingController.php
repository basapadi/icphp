<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Response;
use App\Models\Setting;
use App\Http\Upload;
use Exception;

class SettingController extends BaseController
{
    public function __construct(){
        $this->setModel(Setting::class);
    }

    public function all(Request $request){
        $this->allowAccessModule('setting.general', 'view');
        $results = Setting::where('status',true)->get();
        $data = [];
        foreach ($results as $key => $r) {
            if($r->name == 'mailing'){
                $dr = (array) $r->data;
                unset($dr['password']);
                $r->data = (object) $dr;
            }
           
            $data[$r->name] = $r;
        }
        return Response::ok('loaded',$data);
    }

    public function save(Request $request){
        $path = null;
        try {
            if ($request->hasFile('logo')) {
                if ($request->file('logo')->isValid()) {
                    $path = Upload::image([
                        'file' => 'logo',
                        'size' => [500,500],
                        'path' => 'uploads/setting',
                        'permission' => 0755
                    ]);

                    if(isset($path['status'])&& !$path['status']) return Response::badRequest($path['message']);

                }
            }

            $data = $request->all();
            if($data['key'] == 'toko' && $request->hasFile('logo')) $data['logo'] = @$path['path']??'';
            $setting = Setting::where('name',trim($request->key))->first();
            if(empty($setting)) return Response::badRequest("Data pengaturan dengan nama {$request->key} tidak ada");


            if($data['key'] == 'mailing' && $data['password'] != ''){
                $data['password'] = encrypt(trim($data['password']));
            }
            $setting->data = $data;
            $setting->save();

            return Response::ok('Pengaturan berhasil disimpan');
        }catch(Exception $e){
            return Response::internalServerError($e->getMessage());
        }
    }
}