<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Response;
use App\Models\Setting;
use App\Http\Upload;
use Exception;
use Illuminate\Support\Facades\Artisan;

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


            if($data['key'] == 'mailing' && isset($data['password'])){
                $data['password'] = encrypt(trim($data['password']));
            } else $data['password'] = $setting->data->password;

            $setting->data = $data;
            $setting->save();

            if($data['key'] == 'mailing'){
                $mailingData = (object) $setting->data;
                applyMailConfig($mailingData->driver);
                updateEnv('MAIL_MAILER', trim($mailingData->driver));
                updateEnv('MAIL_HOST', trim($mailingData->host));
                updateEnv('MAIL_PORT', trim($mailingData->port));
                updateEnv('MAIL_USERNAME', trim($mailingData->username));
                updateEnv('MAIL_PASSWORD', decrypt(trim($mailingData->password)));
                updateEnv('MAIL_FROM_ADDRESS', trim($mailingData->fromAddress));
                updateEnv('MAIL_FROM_NAME', trim("'{$mailingData->fromName}'"));
                Artisan::call('config:clear');
                Artisan::call('view:clear');
            }
            
            return Response::ok('Pengaturan berhasil disimpan');
        }catch(Exception $e){
            return Response::internalServerError($e->getMessage());
        }
    }
}