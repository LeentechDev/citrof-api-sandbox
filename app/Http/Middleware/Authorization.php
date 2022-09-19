<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

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
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
            $valid_passwords = array (env('API_SANDBOX_APPID') => env('API_SANDBOX_SECRETKEY'));
            $valid_users = array_keys($valid_passwords);

            $user = $_SERVER['PHP_AUTH_USER'];
            $pass = $_SERVER['PHP_AUTH_PW'];

            $validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

            if (!$validated) {
                header('WWW-Authenticate: Basic realm="API Sandbox"');
                header('HTTP/1.0 401 Unauthorized');
                die ("Not authorized");
            }

            return $next($request);
        }else{
            header('WWW-Authenticate: Basic realm="Sophia Jewellery"');
            header('HTTP/1.0 401 Unauthorized');
            die ("Not authorized");
        }
    }
}