<?php
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

if (!function_exists('ihandCashierConfigToOptions')) {
    function ihandCashierConfigToOptions(string $config) {
        $result=[];
        $configs = config('ihandcashier.'.$config);
        if(!isset($configs) || empty($configs)) return [];
        
        foreach ($configs as $key => $c) {
            array_push($result, [
                'value' => $key,
                'label' => $c['label']
            ]);
        }

        return $result;
    }
}

if (!function_exists('ihandCashierConfigToSelect')) {
    function ihandCashierConfigToSelect(string $config, array $except = []) {
        $result=[];
        $configs = config('ihandcashier.'.$config);
        if(!isset($configs) || empty($configs)) return [];
        
        foreach ($configs as $key => $c) {
            if(in_array($key, $except)) continue;
            $result[$key] = $c['label'];
        }

        return $result;
    }
}

if (!function_exists('roleConfigToOptions')) {
    function roleConfigToOptions() {
        $result=[];
        $configs = config('ihandcashier.roles');
        if(!isset($configs) || empty($configs)) return [];
        
        foreach ($configs as $key => $c) {
            array_push($result, [
                'value' => $c,
                'label' => $c
            ]);
        }

        return $result;
    }
}

if (!function_exists('ihandCashierConfigKeyToArray')) {
    function ihandCashierConfigKeyToArray(string $config) {
        $result=[];
        $configs = config('ihandcashier.'.$config);
        if(!isset($configs) || empty($configs)) return [];
        
        foreach ($configs as $key => $c) {
            array_push($result,$key);
        }

        return $result;
    }
}

if(!function_exists('updateEnv')){
    function updateEnv($key, $value) {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        $pattern = "/^{$key}=.*$/m";

        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
        } else {
            $envContent .= "\n{$key}={$value}";
        }

        file_put_contents($envPath, $envContent);
    }
}

if(!function_exists('applyMailConfig')){
    function applyMailConfig($driver='smtp')
    {
        $setting = Setting::where('name','mailing')->where('status',true)->first();
        if(empty($setting)) return false;
        $data = $setting->data;
        switch ($driver) {
            case 'smtp':
                Config::set('mail.mailers.smtp', [
                    'transport'  => @$data->driver,
                    'host'       => @$data->host,
                    'port'       => @$data->port,
                    'encryption' => @$data->encryption,
                    'username'   => @$data->username,
                    'password'   => decrypt(@$data->password),
                ]);
                // updateEnv('MAIL_MAILER', @$data->driver);
                // updateEnv('MAIL_HOST', @$data->host);
                // updateEnv('MAIL_PORT', @$data->port);
                // updateEnv('MAIL_USERNAME', @$data->username);
                // updateEnv('MAIL_PASSWORD', decrypt(@$data->password));

                break;
            case 'sendmail':
                Config::set('mail.mailers.sendmail', [
                    'transport' => 'sendmail',
                    'path'      => @$data->sendmail_path ?? '/usr/sbin/sendmail -bs',
                ]);
                break;
        }

        Config::set('mail.from.address', @$data->fromAddress);
        Config::set('mail.from.name', @$data->fromName);
        // updateEnv('MAIL_FROM_ADDRESS', @$data->fromAddress);

        return true;
        
    }
}

if(!function_exists('company')){
    function company()
    {
       return Setting::where('name', 'toko')->where('status', true)->first()->data ?? (object) [];
    }
}