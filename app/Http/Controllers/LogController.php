<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserLog;

class LogController extends Controller
{
    public function getPlayerLogs(Request $request){

        $limit = $request->limit ? $request->limit : 10;

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];

            $status_code = 200;
            $logs = UserLog::where(
                function ($query) use ($request) {
                    if(isset($request->from_date) && isset($request->to_date)){
                        if($request->from_date && $request->to_date){
                            $query->where('created', '>=', date('Y-m-d H:i:s', strtotime($request->from_date . '00:00:00') ));
                            $query->where('created', '<=', date('Y-m-d H:i:s', strtotime($request->to_date . '23:59:59')) );
                        }
                    }

                    if(isset($request->player_id)){
                        if($request->player_id){
                            $query->where('player_id', $request->player_id);
                        }
                    }
                })->where( function ($q) use ($request){
                    if(isset($request->action)){
                        $q->orWhere('action', $request->action);
                    }
                })->paginate($limit);
            
            $response = [
                'error' => false,
                'message' => 'success',
                'data' => formatDefaultPagination($logs),
            ];
            
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
