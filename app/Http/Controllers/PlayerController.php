<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Bet;
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

    public function bets(Request $request){
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
                $payload = (array)$payload['data'];
                $rules = [
                    'player_id' => 'required',
                    'from_date' => 'nullable|date',
                    'to_date' => 'nullable|date',
                ];
                $message = [
                    'player_id.required' => 'Player id is missing!',
                    'from_date.date' => 'Invalid date format!',
                    'to_date.date' => 'Invalid date format!',
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
                    $player = Player::where('partner_id', $payload['player_id'])->first();

                    if($player){
                        $query = Bet::query()->where('player_id', $player->id)->select(
                            'event_id',
                            'fight_no',
                            'side',
                            'amount',
                            'result',
                            'created', 
                        );

                        if(isset($payload['event_id'])){
                            $query = $query->where('event_id' , $payload['event_id']);
                        }

                        if (isset($payload['from_date']) && isset($payload['to_date'])) {
                            $query = $query->whereDate('created', '>=', $payload['from_date'])
                            ->whereDate('created', '<=', $payload['to_date']);
                        }else{
                            $query = $query->whereDate('created', '>=', date('Y-m-01'))
                            ->whereDate('created', '<=', date('Y-m-t'));
                        }
                
                        $bets = $query->paginate($request->limit ? $request->limit : 10);
        
                        return response()->json(
                            [
                                'error' => false,
                                'message' => 'success',
                                'bets' => formatDefaultPagination($bets),
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
