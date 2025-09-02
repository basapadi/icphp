<?php

namespace App\Http;
use Btx\Http\Traits\StaticResponse;

class Response {
    use StaticResponse;

    public static function ok($text,$data = [],$append = null){
        $resp = self::response200($text,$data,$append);
        return response()->json($resp, $resp['code']);
    }

    public static function badRequest($text,$dir = ''){
        $resp =  self::response400($text,$dir);
        return response()->json($resp, $resp['code']);
    }

    public static function movedPermanently($text,$dir = ''){
        $resp = self::response301($text,$dir);
        return response()->json($resp, $resp['code']);
    }

    public static function unauthorized(){
        $resp = self::response401();
        return response()->json($resp, $resp['code']);
    }

    public static function notFound($text,$dir = ''){
        $resp = self::response404($text,$dir);
        return response()->json($resp, $resp['code']);
    }

    public static function notAllowed($text,$dir = ''){
        $resp = self::response405($text,$dir);
        return response()->json($resp, $resp['code']);
    }

    public static function internalServerError($text,$data = []){
        $resp = self::response500($text,$data);
        return response()->json($resp, $resp['code']);
    }
}