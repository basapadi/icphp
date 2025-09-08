<?php

namespace App\Http\Controllers;

use App\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDO;
use PDOException;
use Illuminate\Support\Facades\Artisan;

class DatabaseController extends BaseController
{
    public function __construct(){
        
    }

    public function form(Request $request){
        $this->allowAccessModule('setting.database', 'view');
        $default = config('database.default');
        $config = config('database.connections.'.$default);
        return Response::ok('loaded',$config);
    }

    public function test(Request $request){
        try {
            $pdo = new PDO("sqlite:" . $request->database);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query("SELECT 1");

            return Response::ok('Database berhasil terhubung');
        } catch (PDOException $e) {
            return Response::badRequest($e->getMessage());
        }
    }

    public function saveLocalConfig(Request $request){
       if($request->driver == 'sqlite'){
            $data = [
                'driver' => $request->driver,
                'database' => $request->database,
            ];
            $rules = [
                'driver' => 'required|in:sqlite',
                'database' => 'required',
            ];

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) return Response::badRequest($validator->errors());

            updateEnv('DB_CONNECTION', 'sqlite');
            updateEnv('DB_DATABASE', trim($request->database));

            Artisan::call('config:cache');
       }
    }

    public function runCommnad(Request $request){
        if(isset($request->migrate_db)){
            if($request->migrate_db == 1) Artisan::call('migrate');
            if($request->migrate_db == 2) Artisan::call('migrate:rollback');
        }
        if(isset($request->seed_db) && $request->seed_db) Artisan::call('db:seed');
        if(isset($request->config_cache) && $request->config_cache) Artisan::call('config:cache');
        if(isset($request->route_cache) && $request->route_cache) Artisan::call('route:cache');

        return Response::ok('Command berhasil dijalankan');
    }
}
