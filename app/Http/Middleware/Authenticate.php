<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        return response()->json([
            'status'  => 401,
            'success' => true,
            'data'    => [
                'code'    => 'unauthenticated',
                'message' => '尚未鉴权，请登录后重试'
            ]
        ], 401);
    }
}
