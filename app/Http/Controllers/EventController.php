<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function getEvents(Request $request){
        
        $data = decodeToken($request);
        if(!$data['error']){
            $request = $data['data'];
            

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
