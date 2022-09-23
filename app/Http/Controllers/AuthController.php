<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\ApiAccount;

use Firebase\JWT\JWT;

class AuthController extends Controller
{

    public function getToken(){
        $app = ApiAccount::first();
        $data = [
            'player_id' => 2022090905,
            'type' => 1,
            // 'date_from' => '2022-09-22',
            // 'date_to' => '2022-09-22',
        ];
        return JWT::encode($data, $app->secret, 'HS256');
    }

    public function generateToken(Request $request){

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];

            $token = substr(str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@$'), 0, 100);

            $player = Player::where(['partner_id' => $request->player_id])->first();

            if($request->username && $request->player_id){
                if($player){
                    if($player->username == $request->username){
                        $player->token = $token;
                        $player->username = $request->username;
                        $player->status = 'ACTIVE';
                        $save = $player->save();
                    }
                }else{
                    $save = Player::create([
                        'created' => date('Y-m-d H:i:s'),
                        'password' => $token,
                        'token' => $token,
                        'username' => $request->username,
                        'partner_id' => $request->player_id,
                        'jwt' => '',
                        'status' => 'ACTIVE',
                    ]);
                }
            }else{
                $response = [ 'error' => true, 'message' => 'Missing parameter! Please check all the coresponding parameter.' ];
            }

            if($save)
            {
                $response['error'] = false;
                $response['message'] = 'Success';
                $response['token'] = $token;

                return response()->json($response, 200);
            }

        }else{
            return response()->json(
                [
                    'error' => true,
                    'message' => $data['message']
                ], 
            400);
        }
    }
}
