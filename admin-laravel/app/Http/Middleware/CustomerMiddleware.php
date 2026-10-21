<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (!$request->session()->has('user_id')) {
                $user = Auth::user();
                $request->session()->put('user_id', $user->user_id ?? $user->id);
                $request->session()->put('name', $user->name);
                $request->session()->put('role', $user->role);
                $request->session()->put('email', $user->email);
            }
        } elseif ($request->session()->has('user_id')) {
            Auth::loginUsingId($request->session()->get('user_id'));
        } else {
            return redirect()->route('login')->with('redirect_after', $request->path());
        }

        return $next($request);
    }
}

