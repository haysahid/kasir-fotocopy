<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use GuzzleHttp\Psr7\Response;

class CheckAdminRole
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
        $roles = [1, 2];
        $user = Auth::user();

        if (!in_array($user->role_id, $roles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        return $next($request);
    }
}
