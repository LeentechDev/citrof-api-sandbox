<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Fight;
use App\Models\Bet;
use Validator;
use Illuminate\Support\Facades\DB;

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
                        $q->orWhere('status','=', trim($statuses[$i]));
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

    public function getRake(Request $request){
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
                    'from_date' => 'nullable|date',
                    'to_date' => 'nullable|date',
                ];
                $message = [
                    'from_date.date' => 'Invalid date format!',
                    'to_date.date' => 'Invalid date format!',
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
                    $query = Bet::query();
                    $query = $query
                            ->select('created as Event date', 'event_id as Event id', DB::raw('SUM(rake) as `Total Rake`'))
                            ->where('result', 'WIN')
                            ->groupBy('event_id')
                            ->orderBy('Total Rake' , 'DESC');

                    if (isset($request['from_date']) && isset($request['to_date'])) {
                        $query = $query->whereDate('created', '>=', $request['from_date'])
                        ->whereDate('created', '<=', $request['to_date']);
                    }else{
                        $query = $query->whereDate('created', '>=', date('Y-m-01'))
                        ->whereDate('created', '<=', date('Y-m-t'));
                    }
            
                    $total_rake = $query->get();
    
                    return response()->json(
                        [
                            'error' => false,
                            'message' => 'success',
                            'data' => $total_rake,
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
