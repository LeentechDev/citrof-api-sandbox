<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bet;

class BetController extends Controller
{
    public function getBettingTable(Request $request){

        $limit = $request->limit ? $request->limit : 10;

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];
            $status_code = 400;
            if(!isset($request->event_id)){
                $response = [ 'error' => true,  'message' => 'Missing Parameter Event ID' ];
                return response()->json($response, $status_code);
            }

            if(!isset($request->fight_id)){
                $response = [ 'error' => true,  'message' => 'Missing Parameter Fight ID' ];
                return response()->json($response, $status_code);
            }
            $bets = Bet::where('event_id', $request->event_id)->where('fight_id', $request->fight_id)->where(
                function ($query) use ($request) {

                    if(isset($request->player_id)){
                        if($request->player_id){
                            $query->where('player_id', $request->player_id);
                        }
                    }

                })->paginate($limit);
            
            $response = [
                'error' => false,
                'message' => 'success',
                'data' => formatDefaultPagination($bets),
            ];
            $status_code = 200;
            
            return response()->json($response, $status_code);
        }else{
            return response()->json(
                [
                    'error' => true,
                    'message' => $data['message']
                ], 
            400);
        }
    }

    public function getBettingHistory(Request $request){

        $limit = $request->limit ? $request->limit : 10;

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];
            $status_code = 400;
            if(isset($request->event_id)){
                $bets = Bet::where('event_id', $request->event_id)->where(
                    function ($query) use ($request) {
                        if(isset($request->from_date) && isset($request->to_date)){
                            if($request->from_date && $request->to_date){
                                $query->where('created', '>=', date('Y-m-d H:i:s', strtotime($request->from_date . '00:00:00') ));
                                $query->where('created', '<=', date('Y-m-d H:i:s', strtotime($request->to_date . '23:59:59')) );
                            }
                        }

                        if(isset($request->fight_id)){
                            if($request->fight_id){
                                $query->where('fight_id', $request->fight_id);
                            }
                        }

                        if(isset($request->player_id)){
                            if($request->player_id){
                                $query->where('player_id', $request->player_id);
                            }
                        }

                        if(isset($request->agent_id)){
                            if($request->agent_id){
                                $query->where('agent_id', $request->agent_id);
                            }
                        }
                    })->where( function ($q) use ($request){
                        if(isset($request->result)){
                            $results = explode(',', $request->result);
                            for($i = 0; $i < count($results); $i++)
                                $q->orWhere('result', $results[$i]);
                        }
                    })->paginate($limit);
                
                $response = [
                    'error' => false,
                    'message' => 'success',
                    'data' => formatDefaultPagination($bets),
                ];
                $status_code = 200;
            }else{
                $response = [
                    'error' => true,
                    'message' => 'Missing Parameter Event ID',
                ];
            }
            
            return response()->json($response, $status_code);
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
