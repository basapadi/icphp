<?php

namespace App\Traits;
use Native\Laravel\Facades\{Alert,Notification};
use App\Http\Response;
use App\Objects\Notification as ObjectsNotification;

trait BaseHelper
{
    public function setAlert($type = 'info',$title = '', $message = ''){
        if(config('nativephp.nativephp_running')){
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

    public function setNotification(ObjectsNotification $notification){
        if(config('nativephp.nativephp_running')){
            $notif = Notification::title('IhandCashier : '.$notification->title)
                    ->message($notification->message);
            foreach ($notification->actions as $key => $act) {
                $notif->addAction($act);
            }
            $notif->reference($notification->reference);
            $notif->hasReply($notification->reply);
            $notif->show();
        }
    }
}