<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\ApiAccount;
use Illuminate\Support\Facades\Log;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $app = ApiAccount::first();

        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
            $valid_passwords = array ($app->app_id => $app->authorization);
            $valid_users = array_keys($valid_passwords);

            $user = $_SERVER['PHP_AUTH_USER'];
            $pass = $_SERVER['PHP_AUTH_PW'];

            Log::info('Username: '.$user);
            Log::info('Authorization key: '.$pass);

            $validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

            if (!$validated) {
                header('WWW-Authenticate: Basic realm="API Sandbox"');
                header('HTTP/1.0 401 Unauthorized');
                return response()->json(['error' => true,
                    'message' => 'Not authorized',
                    'reason' => "The authentication token doesn't match any of our authorized integrators. Please check if your username (App ID) and password is correct (Authorization Key)"
                ],401);
            }

            return $next($request);
        }else{
            header('WWW-Authenticate: Basic realm="API Sandbox"');
            header('HTTP/1.0 401 Unauthorized');
            return response()->json(['error' => true,
                'message' => 'Not authorized',
                'reason' => "The authentication token doesn't match any of our authorized integrators. Please check if your username (App ID) and password is correct (Authorization Key)"
            ],401);
        }
    }
}