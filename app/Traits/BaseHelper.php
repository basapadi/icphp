<?php

namespace App\Traits;
use Native\Laravel\Facades\{Alert,Notification};
use App\Http\Response;
use App\Objects\Notification as ObjectsNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait BaseHelper
{
    public function setAlert($type = 'info',$title = '', $message = ''){
        if(config('nativephp.nativephp_running')){
            Notification::title(strtoupper($type).' : '.$title)->message($message)->show($message);
            return response()->json([
                'status' => $type == 'info'?true: false,
                'isnativephp'  => config('nativephp.nativephp_running'),
                'message' => $message
            ],$type == 'info'?200:400);
        } else {
            switch ($type) {
                case 'info':
                   return Response::ok($message);
                    break;
                case 'error':
                   return Response::badRequest($title.', '.$message);
                    break;  
                default:
                    return Response::ok($message);
                    break;
            }
        }
    }

    public function setNotification(ObjectsNotification $notification){
        if(config('nativephp.nativephp_running')){
            $notif = Notification::title('Ihand Cashier : '.$notification->title)
                    ->message($notification->message);
            foreach ($notification->actions as $key => $act) {
                $notif->addAction($act);
            }
            $notif->reference($notification->reference);
            $notif->hasReply($notification->reply);
            $notif->show();
        }
    }

    public function validate(array $rules){
        $validator = Validator::make(request()->all(),$rules,[
            'required'             => 'Inputan :attribute wajib diisi.',
            'max'                  => 'Inputan :attribute maksimal :max karakter.',
            'min'                  => 'Inputan :attribute minimal :min karakter.',
            'email'                => 'Inputan :attribute harus berupa email yang valid.',
            'unique'               => 'Inputan :attribute sudah digunakan.',
            'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
            'date'                 => 'Inputan :attribute harus berupa tanggal yang valid.',
            'numeric'              => 'Inputan :attribute harus berupa angka.',
            'integer'              => 'Inputan :attribute harus berupa bilangan bulat.',
            'string'               => 'Inputan :attribute harus berupa teks.',
            'boolean'              => 'Inputan :attribute harus true atau false.',
            'array'                => 'Inputan :attribute harus berupa array.',
            'regex'                => 'Format  :attribute tidak valid.',
            'url'                  => 'Inputan :attribute harus berupa URL yang valid.',
            'in'                   => 'Inputan :attribute harus salah satu dari :values.',
            'not_in'               => 'Inputan :attribute tidak boleh salah satu dari :values.',
            'same'                 => 'Inputan :attribute harus sama dengan :other.',
            'exists'               => 'Inputan :attribute tidak valid.',
            'file.file'            => ':attribute harus berupa file yang valid.',
            'file.mimes'           => ':attribute hanya boleh berupa file tipe: :values',
            'file.max'             => ':attribute maksimal berukuran :max KB.',
        ]);
        if ($validator->fails()) {
            return Response::badRequest('Validasi gagal :','',$validator->errors());
        }
        return $validator->validated();
       
    }
}