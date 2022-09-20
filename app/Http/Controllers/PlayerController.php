<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Player;
use Firebase\JWT\Key;
use Validator;

class PlayerController extends Controller
{
    public function index(Request $request){
        $rules = [
            'payload' => 'required',
            'keyword' => 'max:50',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => true,
                    'message' => 'Invalid request parameters'
                ], 
            400);
        }else{
            $payload = decodeToken($request->payload);
            if(!$payload['error']){
                $data = $payload['data'];
                $keyword = null;
                $per_page = 10;
                if($data->per_page){
                    $per_page = $data->per_page;
                }

                $query = Player::query();

                if ($data->keyword) {
                    $keyword = $data->keyword;
                    $query = $query->where('username', 'like', '%'.$data->keyword.'%');
                }
        
                $players = $query->paginate($data->per_page ? $data->per_page : 10);

                return response()->json(
                    [
                        'error' => false,
                        'message' => 'success',
                        'players' => $players,
                    ], 
                200);

            }else{
                return response()->json(
                    [
                        'error' => true,
                        'message' => $payload['message'],
                    ], 
                409);
            }
        }           
    }
}
