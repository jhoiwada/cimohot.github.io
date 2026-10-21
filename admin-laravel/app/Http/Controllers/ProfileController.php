<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $userId = session('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = DB::table('users')->where('user_id', $userId)->first();
        if (!$user) {
            return redirect()->route('login');
        }

        // Keep session synced
        if (!session()->has('user_id')) {
            session(['user_id' => $user->user_id, 'name' => $user->name, 'role' => $user->role]);
        }

        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('order_date', 'desc')
            ->get();

        $total_orders = $orders->count();
        $total_spent = $orders->sum('total_amount');
        $join_date = !empty($user->created_at) ? date('M Y', strtotime($user->created_at)) : '2024';

        return view('profile.index', compact('user', 'orders', 'total_orders', 'total_spent', 'join_date'));
    }

    public function update(Request $request)
    {
        $userId = session('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        DB::table('users')->where('user_id', $userId)->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        session(['name' => $request->name]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function changePassword(Request $request)
    {
        $userId = session('user_id') ?: \Illuminate\Support\Facades\Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = DB::table('users')->where('user_id', $userId)->first();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Kata sandi saat ini tidak cocok.');
        }

        DB::table('users')->where('user_id', $userId)->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah!');
    }
}
