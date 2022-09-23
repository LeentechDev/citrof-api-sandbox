<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Validator;

class PlayerController extends Controller
{
    public function index(Request $request){
        $rules = [
            'payload' => 'required'
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => true,
                    'message' => 'Payload is missing'
                ], 
            400);
        }else{
            $payload = decodeToken($request);
            if(!$payload['error']){
                $payload = $payload['data'];

                $query = Player::query();

                if (isset($payload->username)) {
                    $query = $query->where('username', 'like', '%'.$payload->username.'%');
                }
        
                $players = $query->paginate($request->limit ? $request->limit : 10);

                return response()->json(
                    [
                        'error' => false,
                        'message' => 'success',
                        'players' => formatDefaultPagination($players),
                    ], 
                200);

            }else{
                return response()->json(
                    [
                        'error' => true,
                        'message' => $payload['message'],
                    ], 
                401);
            }
        }           
    }

    public function get(Request $request){
        $rules = [
            'payload' => 'required',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => true,
                    'message' => 'Payload is missing'
                ], 
            400);
        }else{
            $payload = decodeToken($request);
            if(!$payload['error']){
                $request = $payload['data'];

                $query = Player::query();

                $query = $query->where('partner_id', $request->player_id);

                if(isset($request->status)){
                    $query = $query->where('status', $request->status);
                }

                $player = $query->first();

                if($player){
                    return response()->json(
                        [
                            'error' => false,
                            'message' => 'success',
                            'data' => $player,
                        ], 
                    200);
                }else{
                    return response()->json(
                        [
                            'error' => true,
                            'message' => 'Player not found!',
                        ], 
                    200);
                }
            }else{
                return response()->json(
                    [
                        'error' => true,
                        'message' => $payload['message'],
                    ], 
                401);
            }
        }
    }
}
