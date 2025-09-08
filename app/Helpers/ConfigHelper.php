<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

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