<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Player;
use App\Models\Transaction;
use App\Models\Loading;
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
                                'table' => 'loadings',
                                'field' => 'no',
                                'length' => 15,
                                'prefix' => 'L'.date('Y').date('m').date('d'),
                                'reset_on_prefix_change' => true
                            ];

                            $player_loading = Loading::create([
                                'no' => IdGenerator::generate($idConfig),
                                'player_id' => $player->partner_id,
                                'amount' => $request['amount'],
                                'agent_id' => $player->agent_id,
                                'weekly_summary_id' => 1,
                                'description' => 'Added credit for loading user '. $player->partner_id .' with the amount of '. $request['amount']
                            ]);
                            if($player_loading->save()){
                                $idConfig = [
                                    'table' => 'transactions',
                                    'field' => 'no',
                                    'length' => 15,
                                    'prefix' => 'T'.date('y').date('m').date('d'),
                                    'reset_on_prefix_change' => true
                                ];
                                $transaction = Transaction::create([
                                    'no' => IdGenerator::generate($idConfig),
                                    'player_id' => $player->partner_id,
                                    'agent_id' => $player->agent_id,
                                    'type' => 'CREDIT',
                                    'loading_id' => $player_loading->id,
                                    'amount' => $request['amount'],
                                    'previous_credit' => $player['credits'] - $request['amount'],
                                    'current_credit' => $player['credits'],
                                    'description' => 'Added credit for loading user '. $player->partner_id .' with the amount of '. $request['amount']
                                ]);
                                return response()->json(
                                    [
                                        'error' => false,
                                        'message' => 'success',
                                        'data' => [
                                            'credits' => $player->credits,
                                            'description' => $transaction->description,
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
                401);
            }
        }
    }
    
    public function getCashinHistory(Request $request){
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
                $payload = (array)$payload['data'];
                $rules = [
                    'player_id' => 'required',
                    'from_date' => 'nullable|date',
                    'to_date' => 'nullable|date',
                ];
                $message = [
                    'player_id.required' => 'Player id is missing!',
                    'from_date.date' => 'Date from must be a date format!',
                    'to_date.date' => 'Date to must be a date format!',
                ];

                $validator = Validator::make($payload,$rules,$message);
                    if ($validator->fails()) {
                        return response()->json(
                        [
                            'error' => true,
                            'message' => $validator->errors()->first()
                        ], 
                    400);
                }else{
                    $keyword = null;
    
                    $query = Loading::query();

                    $query = $query->where('player_id', $payload['player_id']);

                    if (isset($payload['transaction_no'])) {
                        $transaction_no = $payload['transaction_no'];
                        $query = $query->where('no', 'like', '%'.$payload['transaction_no'].'%');
                    }

                    if (isset($payload['from_date']) && isset($payload['to_date'])) {
                        $query = $query->whereDate('created', '>=', $payload['from_date'])
                        ->whereDate('created', '<=', $payload['to_date']);
                    }
            
                    $cashin_history = $query->paginate($request->limit ? $request->limit : 10);
    
                    return response()->json(
                        [
                            'error' => false,
                            'message' => 'success',
                            'cashin_history' => formatDefaultPagination($cashin_history),
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

    public function cash_out(Request $request){
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
                    'amount' => 'required'
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
                            'credits' => $player->credits -= $request['amount']
                        ]);
                        if($update){
                            $idConfig = [
                                'table' => 'transactions',
                                'field' => 'no',
                                'length' => 15,
                                'prefix' => 'T'.date('y').date('m').date('d'),
                                'reset_on_prefix_change' => true
                            ];
                            $transaction = Transaction::create([
                                'no' => IdGenerator::generate($idConfig),
                                'player_id' => $player->partner_id,
                                'agent_id' => $player->agent_id,
                                'type' => 'DEBIT',
                                'amount' => $request['amount'],
                                'previous_credit' => $player['credits'] + $request['amount'],
                                'current_credit' => $player['credits'],
                                'description' => 'Debit '. $request['amount']. ' for cashout request of user '. $player->partner_id
                            ]);

                            return response()->json(
                                [
                                    'error' => false,
                                    'message' => 'success',
                                    'data' => [
                                        'credits' => $player->credits,
                                        'description' => $transaction->description,
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
                401);
            }
        }
    }

    public function getCashoutHistory(Request $request){
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
                $payload = (array)$payload['data'];
                $rules = [
                    'player_id' => 'required',
                    'from_date' => 'nullable|date',
                    'to_date' => 'nullable|date',
                ];
                $message = [
                    'player_id.required' => 'Player id is missing!',
                    'from_date.date' => 'Date from must be a date format!',
                    'to_date.date' => 'Date to must be a date format!',
                ];

                $validator = Validator::make($payload,$rules,$message);
                    if ($validator->fails()) {
                        return response()->json(
                        [
                            'error' => true,
                            'message' => $validator->errors()->first()
                        ], 
                    400);
                }else{    
                    $query = Transaction::query();

                    $query = $query->where('type', 'DEBIT')->where('player_id', $payload['player_id']);

                    if (isset($payload['transaction_no'])) {
                        $transaction_no = $payload['transaction_no'];
                        $query = $query->where('no', 'like', '%'.$payload['transaction_no'].'%');
                    }

                    if (isset($payload['from_date']) && isset($payload['to_date'])) {
                        $query = $query->whereDate('created', '>=', $payload['from_date'])
                        ->whereDate('created', '<=', $payload['to_date']);
                    }
            
                    $cash_out_history = $query->paginate($request->limit ? $request->limit : 10);
    
                    return response()->json(
                        [
                            'error' => false,
                            'message' => 'success',
                            'cash_out_history' => formatDefaultPagination($cash_out_history),
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
