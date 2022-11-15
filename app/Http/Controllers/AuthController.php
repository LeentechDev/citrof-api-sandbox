<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Agent;
use App\Models\ApiAccount;
use Illuminate\Support\Facades\Hash;
use Haruncpi\LaravelIdGenerator\IdGenerator;

use Firebase\JWT\JWT;

class AuthController extends Controller
{

    public function getToken(){
        $app = ApiAccount::first();
        $data = [
            'from_date' => '2022-09-01',
            'to_date' => '2022-09-30',
        ];
        return JWT::encode($data, $app->secret, 'HS256');
    }

    public function generateToken(Request $request){

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];

            $token = substr(str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@$'), 0, 100);

            

            if(isset($request->username) && isset($request->player_id)){
                $player = Player::where(['partner_id' => $request->player_id])->first();
                
                $operator = 'default';
                if(isset($request->operator)){
                    $operator = $request->operator;
                }

                if($player){
                    $agent = Agent::where('username' , 'cg_'.$operator)->first();
                    if(!$agent){
                        $agent = $this->addAgent($request);
                    }else{
                        $agent->username = strtolower($agent->username);
                        $agent->ma_convention = strtolower($agent->ma_convention);
                        $agent->commission_rate = 5.0;
                        $agent->save();
                    }

                    if($player->agent_username != $agent->username){
                        $player->agent_id = $agent->id;
                        $player->agent_username = $agent->username;
                    }
                    $player->token = $token;
                    $player->username = $request->username;
                    $save = $player->save();

                }else{
                    $agent = Agent::where('username' , 'cg_'.$operator)->first();
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
            'username' => 'cg_'.$request->operator,
            'account_no' => IdGenerator::generate($agent_acc_no),
            'referral_id' => IdGenerator::generate($referral_id),
            'password' => Hash::make('change_me'),
            'ma_convention' => 'cg_'.substr($request->operator, 0, 4),
            'commission_rate' => 5.0,
            'level' => 1,
            'status' => 'ACTIVE', 
        ]);

        return $agent;
    }
}
