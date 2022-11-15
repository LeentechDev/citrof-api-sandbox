<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function getPlayerTransactions(Request $request){

        $limit = $request->limit ? $request->limit : 10;

        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];
            
            $transactions = Transaction::where(
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
                    if(isset($request->type)){
                        $types = explode(',', $request->type);
                        for($i = 0; $i < count($types); $i++)
                            $q->orWhere('type', $types[$i]);
                    }
                })->where( function ($q) use ($request){
                    if(isset($request->module)){
                        switch (strtolower(trim($request->module))) {
                        case 'load':
                                $q->whereNotNull('loading_id');
                            break;
                        case 'bets,':
                                $q->whereNotNull('bet_id');
                            break;
                        break;
                        default:
                            # code...
                            break;
                        }
                    }
                })->paginate($limit);
            
            $response = [
                'error' => false,
                'message' => 'success',
                'data' => formatDefaultPagination($transactions),
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
}
