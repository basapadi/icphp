<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Response;
use Illuminate\Support\Facades\File;
use Exception;
class CommonController extends BaseController
{
    public function ChangeLog(Request $request){
        $owner = 'basapadi';
        $repo = 'icphp';
        $token = config('ihandcashier.github_token');
        $branch = config('ihandcashier.github_branch');

        $uri = "https://api.github.com/repos/{$owner}/{$repo}/commits?sha={$branch}&per_page=10";

        $response = Http::withHeaders([
            'Authorization' => "token ${token}",
            'Accept'        => 'application/vnd.github.v3+json',
            'User-Agent'    => 'Ihand-Cashier',
        ])->get($uri);

        if($response->failed()){
            Response::badRequest('Github API Error');
        }

        $commits = collect($response->json())->map(function($item){
            return [
                'message'   => $item['commit']['message'],
                'author'    => $item['commit']['author']['name'],
                'date'      => $item['commit']['author']['date'],
                'avatar'    => $item['author']['avatar_url'] ?? null,
                'username'  => $item['author']['login'] ?? null,
                'profile'   => $item['author']['html_url'] ?? null
            ];
        });

        return Response::ok('Loaded', $commits);
    }

    public function saveColumns(Request $request){
        $rules = [
            'columns'   => 'required|array',
            'module'    => 'required|string'
        ];
        $data = $this->validate($rules);
        if ($data instanceof \Illuminate\Http\JsonResponse) return $data;
        try {
            $path = base_path('resources/data/columns/'.trim($request->module).'.json');
            $schema = collect(json_decode(file_get_contents($path),true));
            $editedColumns = collect($request->columns);
            $result = $editedColumns->map(function($col) use ($schema){
                $s = $schema->where('name',$col['name'])->first();
                $col['options'] = @$s['options']??[];
                return $col;
            });

            $dir = resource_path("data/columns");
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true, true);
            }else {
                File::makeDirectory($dir, 0775, true, true);
            }
            file_put_contents($path, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            chmod($path, 0664);
            return Response::ok('Pengaturan kolom berhasil disimpan');
        }catch(Exception $e){
            return $this->setAlert('error','Gagal',$e->getMessage());
        }
    }
}