<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Agent;
use App\Models\ApiAccount;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Hash;

use Firebase\JWT\JWT;

class AuthController extends Controller
{

    public function getToken(){
        $app = ApiAccount::first();
        $data = [
            'operator' => 'cockpit_gaming',
            'username' => 'cg_jm',
            'player_id' => 20221229001,
        ];
        return JWT::encode($data, $app->secret, 'HS256');
    }

    public function generateToken(Request $request){

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];

            $token = substr(str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@$'), 0, 100);

            $player = Player::where(['partner_id' => $request->player_id])->first();

            if($request->username && $request->player_id && $request->operator){
                if($player){
                    $agent = Agent::where('username' , 'cg_'.$request->operator)->first();
                    if(!$agent){
                        $agent = $this->addAgent($request);
                    }

                    if($player->agent_username != $agent->username){
                        $player->agent_id = $agent->id;
                        $player->agent_username = $agent->username;
                    }
                    $player->token = $token;
                    $save = $player->save();

                }else{
                    $agent = Agent::where('username' , 'cg_'.$request->operator)->first();
                    if(!$agent){
                        $agent = $this->addAgent($request);
                    }
                    $player_acc_no = [
                        'table' => 'players',
                        'field' => 'account_no',
                        'length' => 9,
                        'prefix' => 'A'.date('y').date('m').date('d'),
                        'reset_on_prefix_change' => true
                    ];
                    $save = Player::create([
                        'username' => $request->username,
                        'partner_id' => $request->player_id,
                        'credits' => 0,
                        'password' => $token,
                        'token' => $token,
                        'status' => 'ACTIVE',
                        'email_verified' => 'YES',
                        'agent_id' => $agent->id,
                        'agent_username' => $agent->username,
                        'agent_level' => $agent->level,
                        'account_no' => IdGenerator::generate($player_acc_no),
                    ]);

                }
            }else{
                $response = [ 'error' => true, 'message' => 'Incorrect parameters! Please check all the coresponding parameter.' ];
            }

            if($save)
            {
                $url = ApiAccount::first();
                $response['error'] = false;
                $response['message'] = 'Success';
                $response['token'] = $token;
                $response['game_url'] = $url->return_url.'load-game?token='.$token;

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

    public function addAgent($request){
        $agent_acc_no = [
            'table' => 'agents',
            'field' => 'account_no',
            'length' => 10,
            'prefix' => 'CG'.date('y').date('m').date('d'),
            'reset_on_prefix_change' => true
        ];
        $referral_id = [
            'table' => 'agents',
            'field' => 'referral_id',
            'length' => 6,
            'prefix' => 'R-',
            'reset_on_prefix_change' => true
        ];
        $agent = Agent::create([
            'username' => 'cg_'.strtolower($request->operator),
            'account_no' => IdGenerator::generate($agent_acc_no),
            'referral_id' => IdGenerator::generate($referral_id),
            'password' => Hash::make('change_me'),
            'ma_convention' => 'cg_'.substr(strtolower($request->operator), 0, 4),
            'commission_rate' => 4.0,
            'level' => 1,
            'status' => 'ACTIVE', 
        ]);

        return $agent;
    }
}
