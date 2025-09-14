<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use App\Objects\Notification;
use App\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends BaseController
{
    public function __construct(){
        $this->setModel(User::class);
        $this->setModule('master.user');
        $this->setFilterColumnsLike(['username','name'],request('q')??'');

        $conf = config('ihandcashier.roles');
        $roles = [];
        foreach ($conf as $key => $c) {
           $roles[$c] = strtoupper($c);
        }

        $form = $this->getResourceForm('user');
        injectData($form, [
            'roles' => $roles,
            'username_disabled' => false
        ]);
        $this->setForm($form);
    }

    public function store(Request $request) {
        $roles = config('ihandcashier.roles');
        $rules = [
            'role' => 'required|string|in:'.implode(',',$roles),
            'email' => 'required|string|unique:users,email',
            'name' => 'required|string',
            'username' => 'nullable|string|unique:users,username',
            'password' => 'nullable|string|min:8|confirmed',
            'active' => 'required|numeric|in:0,1',
            'id' => 'nullable|numeric'
        ];

        try {
            if(!isset($request->id)){
                $this->allowAccessModule('master.user', 'create');

                $rules['password'] = 'required|string|min:8';
                $rules['password_confirmation'] = 'required|string|min:8|same:password';
                $rules['username'] = 'required|string|unique:users,username';
                
                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;
                $preInsert = [
                    'role' => trim($data['role']),
                    'email' => trim($data['email']),
                    'name' => trim($data['name']),
                    'username' => trim($data['username']),
                    'password' => Hash::make(trim($data['password'])),
                    'active' => trim($data['active'])
                ];

                User::insert($preInsert);
                return $this->setAlert('info','Berhasil',$preInsert['name'].' berhasil disimpan');
            }else{
                $this->allowAccessModule('master.user', 'update');
                if(isset($request->password) || isset($request->password_confirmation)){
                    $rules['password'] = 'required|string|min:8';
                    $rules['password_confirmation'] = 'required|string|min:8|same:password';
                }
                $rules['email'] = ['required','string',Rule::unique('users', 'email')->ignore($request->id)];
                $rules['username'] = ['required','string',Rule::unique('users', 'username')->ignore($request->id)];
                
                $data = $this->validate($rules);
                if ($data instanceof \Illuminate\Http\JsonResponse) return $data;
                
                $exist = User::where('id',$data['id'])->first();
                if(empty($exist)) return $this->setAlert('error','Galat!','Data tidak ditemukan');

                $exist->role = $data['role'];
                $exist->email = $data['email'];
                $exist->name = $data['name'];
                $exist->active = $data['active'];
                if(isset($data['password'])) $exist->password = Hash::make(trim($data['password']));
                $exist->save();

                return $this->setAlert('info','Berhasil',$exist->name.' berhasil disimpan');
            }
        }catch(Exception $e){
            dd($e);
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    
    }

    public function edit(Request $request,$id){
        $this->allowAccessModule('master.user', 'edit');
        $id = $this->decodeId($id);
        $data = User::where('id',$id)->first();
        if(empty($data)) return $this->setAlert('error','Galat!','Data yang tidak ditemukan!.');

        $conf = config('ihandcashier.roles');
        $roles = [];
        foreach ($conf as $key => $c) {
           $roles[$c] = strtoupper($c);
        }

        $form = $this->getResourceForm('user');
        injectData($form, [
            'roles' => $roles,
            'username_disabled' => true
        ]);
        // dd($form);
        return Response::ok('loaded',[
            'data' => $data,
            'sections' => $form
        ]);
    }
}
