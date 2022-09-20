<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\ApiAccount;

if (!function_exists('decodeToken')) {
    function decodeToken($token)
    {
        $app = ApiAccount::first();

        try{
            $apy = JWT::decode($token, new Key($app->secret, 'HS256'));
            // if($apy->app_id){
            //     if($app->app_id == $apy->app_id){
            $response['error'] = false;
            $response['data'] = $apy;
            //     }else{
            //         $response['error'] = true;
            //         $response['message'] = 'Invalid App ID';
            //     }
            // }else{
            //     $response['error'] = true;
            //     $response['message'] = 'Invalid Payload Content';
            // }
            return $response;
        }catch(Exception $e){
            $response['error'] = true;
            $response['message'] = $e->getMessage();
            return $response;
        }
    }
}


?>