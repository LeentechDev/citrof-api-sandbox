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
                                'player_id' => $player->partner_id,
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
                401);
            }
        }
    }
    
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
                $payload = (array)$payload['data'];
                $rules = [
                    'player_id' => 'required',
                    'type' => 'required|integer',
                    'date_from' => 'nullable|date',
                    'date_to' => 'nullable|date',
                ];
                $message = [
                    'player_id.required' => 'Player id is missing!',
                    'type.required' => 'Loading type is missing!',
                    'type.integer' => 'Loading type must be integer!',
                    'date_from.date' => 'Date from must be a date format!',
                    'date_to.date' => 'Date to must be a date format!',
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
                    $per_page = 10;
                    if(($request->per_page)){
                        $per_page = $request->per_page;
                    }
    
                    $query = PlayerLoadings::query();

                    $query = $query->where('player_id', $payload['player_id'])->where('type', $payload['type']);

                    if (isset($payload['transaction_no'])) {
                        $transaction_no = $payload['transaction_no'];
                        $query = $query->where('transaction_no', 'like', '%'.$payload['transaction_no'].'%');
                    }

                    if (isset($payload['date_from']) && isset($payload['date_to'])) {
                        $query = $query->whereDate('created_at', '>=', $payload['date_from'])
                        ->whereDate('created_at', '<=', $payload['date_to']);
                    }
            
                    $loading_history = $query->paginate($request->per_page ? $request->per_page : $per_page);
    
                    return response()->json(
                        [
                            'error' => false,
                            'message' => 'success',
                            'loading_history' => $loading_history,
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
