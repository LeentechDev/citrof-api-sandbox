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
            $payload = decodeToken($request);
            if(!$payload['error']){
                $request = $payload['data'];
                $keyword = null;
                $per_page = 10;
                if($request->per_page){
                    $per_page = $request->per_page;
                }

                $query = Player::query();

                if ($request->keyword) {
                    $keyword = $request->keyword;
                    $query = $query->where('username', 'like', '%'.$request->keyword.'%');
                }
        
                $players = $query->paginate($request->per_page ? $request->per_page : 10);

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

    public function get(Request $request){
        $rules = [
            'payload' => 'required',
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
            $payload = decodeToken($request);
            if(!$payload['error']){
                $request = $payload['data'];

                $query = Player::query();

                $query = $query->where('partner_id', $request->player_id);

                if($request->status){
                    $query = $query->where('status', $request->status);
                }

                $player = $query->first();

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
                        'message' => $payload['message'],
                    ], 
                409);
            }
        }
    }
}
