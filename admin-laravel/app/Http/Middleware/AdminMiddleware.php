<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() && $request->session()->has('user_id')) {
            Auth::loginUsingId($request->session()->get('user_id'));
        }

        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login telah habis. Silakan login kembali.'
                ], 401);
            }
            return redirect()->route('login', ['role' => 'admin'])->with('error', 'Silakan login terlebih dahulu untuk mengakses Command Center.');
        }

        if (Auth::user()->role !== 'admin') {
            if ($request->expectsJson() || $request->is('admin/api/*') || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Akun Anda bukan akun administrator.'
                ], 403);
            }
            return redirect()->route('login', ['role' => 'admin'])->with('error', 'Akses ditolak! Anda tidak memiliki hak akses administrator.');
        }

        return $next($request);
    }
}
