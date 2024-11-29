<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use GuzzleHttp\Psr7\Response;

class CheckUserRoleAndStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        $user = User::with('store')->find($user->id);
        $store = $user->store[0] ?? null;

        if ($user->disabled_at != null) {
            return ResponseFormatter::error('Akun anda telah dinonaktifkan.', 401);
        }

        if (!$store) {
            return ResponseFormatter::error('Anda belum memiliki toko.', 404);
        }

        if (!in_array($user->role_id, $roles)) {
            return ResponseFormatter::error('Anda tidak memiliki hak akses.', 401);
        }

        return $next($request);
    }
}
