<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function getEvents(Request $request){

        $data = decodeToken($request);

        if(!$data['error']){
            $request = $data['data'];

            $events = Event::where( function ($q) use ($request){
                if(isset($request->date)){
                    $q->where('date', $request->date);
                }

                if(isset($request->status)){
                    $q->where('status','=', $request->status);
                }
            })->paginate(isset($request->per_page) ? $request->per_page : 10 );
            

            return response()->json(
                [
                    'error' => false,
                    'message' => 'success',
                    'events' => $events,
                ], 
            200);

        }else{
            return response()->json(
                [
                    'error' => true,
                    'message' => $data['message']
                ], 
            409);
        }
    }

    public function getEvent(Request $request){

        $data = decodeToken($request);

        if(!$data['error']){
            $request = $data['data'];
            $status_code = 409;

            if(isset($request->id)){
                $event = Event::find($request->id);
                
                $response = [
                    'error' => false,
                    'message' => 'success',
                    'data' => $event,
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
            409);
        }
    }


    public function getEventFights(Request $request){

        $data = decodeToken($request);

        if(!$data['error']){
            $request = $data['data'];
            $status_code = 409;

            if(isset($request->event_id)){
                $event = Event::where('id', $request->event_id)->with('fights',
                    function ($query) use ($request) {
                        if(isset($request->no)){
                            if($request->no){
                                $query->where('no', $request->no);
                            }
                        }

                        if(isset($request->status)){
                            if($request->status){
                                $query->where('status', $request->status);
                            }
                        }

                        if(isset($request->betting_status)){
                            if($request->betting_status){
                                $query->where('betting_status', $request->betting_status);
                            }
                        }
                        
                    })->first();
                
                $response = [
                    'error' => false,
                    'message' => 'success',
                    'data' => $event,
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
            409);
        }
    }

}
