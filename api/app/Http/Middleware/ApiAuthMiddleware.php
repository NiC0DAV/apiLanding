<?php

namespace App\Http\Middleware;

use App\Helpers\JwtAuth;
use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $tokenAuth = $request->header('Authorization');
        $jwtValidator = new JwtAuth();
        $checkToken = $jwtValidator->checkToken($tokenAuth);

        if ($checkToken === true) {
            return $next($request);
        } else {

            $data = array(
                'code' => "403",
                'errorDescription' => $checkToken,
                'traceId' => '105L'
            );

            return response()->json($data, $data['code']);
        }
    }
}
