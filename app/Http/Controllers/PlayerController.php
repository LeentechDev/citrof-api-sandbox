<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Player;
use Firebase\JWT\Key;
use Validator;

class PlayerController extends Controller
{
    public function get(Request $request){
        $payload = $request->payload;
        if($request->payload){
            $data = decodeToken($payload);
            if(!$data['error']){
                $keyword = null;
                $per_page = 10;
                if($request->per_page){
                    $per_page = $request->per_page;
                }
                $rules = [
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
                }
            }else{
                return response()->json(
                    [
                        'error' => true,
                        'message' => $data['message']
                    ], 
                409);
            }
        }else{
            return response()->json(
                [
                    'error' => true,
                    'message' => 'Invalid request parameters'
                ], 
            400);
        }


               
    }
}
