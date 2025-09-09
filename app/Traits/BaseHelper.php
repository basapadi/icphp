<?php

namespace App\Traits;
use Native\Laravel\Facades\{Alert,Notification};
use App\Http\Response;
trait BaseHelper
{
    public function setAlert($type = 'info',$title = '', $message = ''){
        if(config('nativephp.nativephp_running')){
            Notification::title('IhandCashier : '.$title)
                ->message($message)
                ->show();
            Alert::new()->type($type)->title($title)->show($message);
            return null;
        } else {
            switch ($type) {
                case 'info':
                   return Response::ok($message);
                    break;
                case 'error':
                   return Response::badRequest($message);
                    break;
                default:
                    return Response::ok($message);
                    break;
            }
        }
    }
}