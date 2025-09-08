<?php

namespace App\Traits;
use Native\Laravel\Facades\{Alert,Notification};
trait BaseHelper
{
    public function setAlert($type = 'info',$title = '', $message = ''){
        if(config('nativephp.nativephp_running')){
            Alert::new()->type($type)->title($title)->show($message);
        }
    }
}