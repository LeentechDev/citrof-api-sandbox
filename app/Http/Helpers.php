<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\ApiAccount;

if (!function_exists('formatDefaultPagination')) {
    function formatDefaultPagination($data)
    {
        $data = $data->toArray();
        $data['total_records'] = $data['total'];
        $data['total_pages'] = $data['last_page'];
        $data['limit'] = $data['per_page'];
        unset($data['first_page_url']);
        unset($data['last_page_url']);
        unset($data['from']);
        unset($data['links']);
        unset($data['next_page_url']);
        unset($data['path']);
        unset($data['prev_page_url']);
        unset($data['to']);
        unset($data['total']);
        unset($data['last_page']);
        unset($data['per_page']);
        return $data;
    }
}

if (!function_exists('decodeToken')) {
    function decodeToken($request)
    {
        if($request->payload){
            $token = $request->payload;

            $app = ApiAccount::first();
            try{
                $apy = JWT::decode($token, new Key($app->secret, 'HS256'));
                $response['error'] = false;
                $response['data'] = $apy;
                return $response;
            }catch(Exception $e){
                $response['error'] = true;
                $response['message'] = $e->getMessage();
                return $response;
            }
        }else{
            $response['error'] = true;
            $response['message'] = 'Missing payload parameter';
            return $response;
        }
    }
}


?>