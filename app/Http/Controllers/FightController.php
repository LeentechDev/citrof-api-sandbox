<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fight;

class FightController extends Controller
{

    public function getEventFights(Request $request){

        $limit = $request->limit ? $request->limit : 10;

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];
            $status_code = 400;

            if(isset($request->event_id)){
                $fights = Fight::where('event_id', $request->event_id)->where(
                    function ($query) use ($request) {
                        if(isset($request->no)){
                            if($request->no){
                                $query->where('no', $request->no);
                            }
                        }
                    })->where( function ($q) use ($request){
                        if(isset($request->status)){
                            $statuses = explode(',', $request->status);
                            for($i = 0; $i < count($statuses); $i++)
                                $q->orWhere('status','=', $statuses[$i]);
                        }
                    })->where( function ($q) use ($request){
                        if(isset($request->betting_status)){
                            $statuses = explode(',', $request->betting_status);
                            for($i = 0; $i < count($statuses); $i++)
                                $q->orWhere('betting_status','=', $statuses[$i]);
                        }
                    })
                    ->paginate($limit);
                
                $response = [
                    'error' => false,
                    'message' => 'success',
                    'fights' => formatDefaultPagination($fights),
                ];
                $status_code = 200;
            }else{
                $response = [
                    'error' => true,
                    'message' => 'Missing Parameter ID',
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

    public function getFight(Request $request){

        $data = decodeToken($request);

        if(!$data['error']){
            $request = $data['data'];
            $status_code = 409;

            if(isset($request->id)){
                $fight = Fight::find($request->id);
                
                if($fight){
                    $response = [
                        'error' => false,
                        'message' => 'success',
                        'data' => $fight,
                    ];
                    $status_code = 200;
                }else{
                    $response = [
                        'error' => true,
                        'message' => 'Fight not found',
                    ];
                    $status_code = 200;
                }
            }else{
                $response = [
                    'error' => true,
                    'message' => 'Missing Parameter ID',
                ];
            }
            
            return response()->json($response, $status_code);
        }else{
            return response()->json(
                [
                    'error' => true,
                    'message' => $data['message']
                ], 
            409);
        }
    }
}
