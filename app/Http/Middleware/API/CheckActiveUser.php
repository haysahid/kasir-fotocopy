<?php

namespace App\Http\Middleware\API;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use GuzzleHttp\Psr7\Response;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user->disabled_at != null) {
            return ResponseFormatter::error('Akun anda telah dinonaktifkan.', 401);
        }

        return $next($request);
    }
}
