<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Player;
use App\Models\PlayerLoadings;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class LoadingController extends Controller
{
    public function cash_in(Request $request){
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
                $request = (array)$payload['data'];
                $rules = [
                    'player_id' => 'required',
                    'amount' => 'required',
                ];
                $message = [
                    'player_id.required' => 'Player id is missing!',
                    'amount.required' => 'Amount id is missing!',
                ];

                $validator = Validator::make($request,$rules,$message);
                if ($validator->fails()) {
                    return response()->json(
                        [
                            'error' => true,
                            'message' => $validator->errors()->first()
                        ], 
                    400);
                }else{
                    $player = Player::where('partner_id', $request['player_id'])->first();
                    if($player){
                        $update = $player->update([
                            'credits' => $player->credits += $request['amount']
                        ]);
                        if($update){
                            $idConfig = [
                                'table' => 'player_loadings',
                                'field' => 'transaction_no',
                                'length' => 12,
                                'prefix' => date('Y').'-'.date('m').'-'.date('d'),
                                'reset_on_prefix_change' => true
                            ];

                            $player_loading = PlayerLoadings::create([
                                'transaction_no' => IdGenerator::generate($idConfig),
                                'player_id' => $player->id,
                                'amount' => $request['amount'],
                                'previous_credits' => $player['credits'] - $request['amount'],
                                'current_credits' => $player['credits'],
                                'type' => 1,
                                'description' => 'Added credit for loading user '. $player->username .' with the amount of '. $request['amount']
                            ]);
                            $player_loading->save();

                            return response()->json(
                                [
                                    'error' => false,
                                    'message' => 'success',
                                    'data' => [
                                        'credits' => $player->credits,
                                        'description' => $player_loading->description,
                                    ]
                                ], 
                            200);
                        }else{
                            return response()->json(
                                [
                                    'error' => true,
                                    'message' => 'Something went wrong!',
                                ], 
                            200);
                        }
                    }else{
                        return response()->json(
                            [
                                'error' => true,
                                'message' => 'Player not found!',
                            ], 
                        200);
                    }
                }
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
