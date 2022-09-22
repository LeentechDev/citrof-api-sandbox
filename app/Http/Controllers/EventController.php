<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Fight;

class EventController extends Controller
{
    public function getEvents(Request $request){

        $limit = $request->limit ? $request->limit : 10;

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];

            $events = Event::where( function ($q) use ($request){
                if(isset($request->date)){
                    $q->where('date', $request->date);
                }
            })->where( function ($q) use ($request){
                if(isset($request->status)){
                    $statuses = explode(',', $request->status);
                    for($i = 0; $i < count($statuses); $i++)
                        $q->orWhere('status','=', $statuses[$i]);
                }
            })->paginate($limit);

            /* $sql = $query->toSql();
            $bindings = $query->getBindings();
            var_dump($sql); die; */

            return response()->json(
                [
                    'error' => false,
                    'message' => 'success',
                    'events' => formatDefaultPagination($events),
                ], 
            200);

        }else{
            return response()->json(
                [
                    'error' => true,
                    'message' => $data['message']
                ], 
            400);
        }
    }

    public function getEvent(Request $request){

        $data = decodeToken($request);

        if(!$data['error']){
            $request = $data['data'];
            $status_code = 400;

            if(isset($request->id)){
                $event = Event::find($request->id);
                
                if($event){
                    $response = [
                        'error' => false,
                        'message' => 'success',
                        'data' => $event,
                    ];
                }else{
                    $response = [
                        'error' => true,
                        'message' => 'Event not found',
                    ];
                }
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

}
