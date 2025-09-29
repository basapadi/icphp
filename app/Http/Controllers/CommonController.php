<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Response;
class CommonController extends Controller
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
                'date'      => $item['commit']['author']['date']
            ];
        });

        return Response::ok('Loaded', $commits);
    }
}