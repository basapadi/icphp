<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Native\Laravel\Facades\Window;
use Native\Laravel\Facades\Settings;
use PDO;
use PDOException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();
        if(empty($user)){
            return response()->json([
                'message' => 'Username tidak ditemukan'
            ], 401);
        }
        if(!$user->active){
            return response()->json([
                'message' => 'Akun tidak aktif'
            ], 401);
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'user'          => $user
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        Window::close();
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function init(){
        $initial = Settings::get('initial_setup', true);
        $pdos = PDO::getAvailableDrivers();
        $drivers = [];
        foreach ($pdos as $key => $d) {
           if($d == 'sqlite') $drivers[$d] = 'SQLite';
           else if($d == 'mysql') $drivers[$d] = 'MySQL';
           else if($d == 'mariadb') $drivers[$d] = 'MariaDB';
           else if($d == 'pgsql') $drivers[$d] = 'PostgreSQL';
        }

        $default = config('database.default');
        $config = config('database.connections.'.$default);
        return response()->json([
            'initial' => $initial,
            'drivers' => $drivers,
            'config' => $config
        ]);
    }

    public function test(Request $request){
        if (!isset($request->driver)) {
            return Response::badRequest('Tipe Basis Data tidak boleh kosong');
        }

        try {
            if ($request->driver === 'sqlite') {
                if (!file_exists($request->database)) {
                    return Response::badRequest("Database SQLite tidak ditemukan: {$request->database}");
                }
                $pdo = new PDO("sqlite:" . $request->database);
            } else {
                $dsn = "{$request->driver}:host={$request->host};port={$request->port};dbname={$request->database}";
                $pdo = new PDO($dsn, $request->username, $request->password);
            }
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
        } else {
            $data = [
                'driver' => trim($request->driver),
                'database' => trim($request->database),
                'host' => trim($request->host),
                'port' => trim($request->port),
                'username' => trim($request->username),
                'password' => trim($request->password),
                'collation' => trim($request->collation),
                'charset' => trim($request->charset),
            ];
            $rules = [
                'driver' => 'required|in:mysql,mariadb,pgsql',
                'database' => 'required',
                'host' => 'required',
                'port' => 'required',
                'username' => 'required|string',
                'password' => 'nullable|string',
                'charset' => 'nullable|string',
            ];
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) return Response::badRequest($validator->errors());

            config(['database.connections.'.$data['driver'].'.database' => $data['database']]);
            config(['database.connections.'.$data['driver'].'.host' => $data['host']]);
            config(['database.connections.'.$data['driver'].'.port' => $data['port']]);
            config(['database.connections.'.$data['driver'].'.username' => $data['username']]);
            config(['database.connections.'.$data['driver'].'.password' => $data['password']]);
            config(['database.connections.'.$data['driver'].'.collation' => $data['collation']]);
            config(['database.connections.'.$data['driver'].'.charset' => $data['charset']]);

            DB::purge($data['driver']);
            DB::reconnect($data['driver']);

            if (!Schema::hasTable('migrations')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            }

            updateEnv('DB_CONNECTION', trim($request->driver));
            updateEnv('DB_HOST', trim($request->host));
            updateEnv('DB_PORT', trim($request->port));
            updateEnv('DB_DATABASE', trim($request->database));
            updateEnv('DB_USERNAME', trim($request->username));
            updateEnv('DB_PASSWORD', trim($request->password));
            updateEnv('DB_COLLATION', trim($request->collation));
            updateEnv('DB_CHARSET', trim($request->charset));
        }
        Settings::set('initial_setup', false);
        Artisan::call('config:cache');
        return Response::ok('Data tersimpan');
    }
}
