<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('login', ['role' => 'admin']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required|string',
        ], [
            'email_or_phone.required' => 'Email atau Nomor Telepon wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = trim($request->input('email_or_phone'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Cari user berdasarkan email atau no telp
        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return back()->withInput($request->only('email_or_phone'))
                ->with('error', 'Kombinasi email/nomor telepon dan password tidak ditemukan.');
        }

        // Cek password hash
        if (!Hash::check($password, $user->password)) {
            return back()->withInput($request->only('email_or_phone'))
                ->with('error', 'Password yang Anda masukkan salah.');
        }

        // Pastikan hanya role admin yang diizinkan
        if ($user->role !== 'admin') {
            return back()->withInput($request->only('email_or_phone'))
                ->with('error', 'Akses ditolak! Akun ini bukan akun administrator Cimohot.');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();
        $request->session()->put('user_id', $user->user_id);
        $request->session()->put('name', $user->name);
        $request->session()->put('role', $user->role);

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login', ['role' => 'admin'])
            ->with('info', 'Anda berhasil logout dari Command Center.');
    }

    public function ssoLogin(Request $request)
    {
        $uid = (int) $request->query('uid');
        $ts = (int) $request->query('ts');
        $sig = (string) $request->query('sig');

        $secret = 'CIMOHOT_SECURE_ADMIN_SSO_KEY_2026';
        $expected = hash_hmac('sha256', $uid . '|' . $ts, $secret);

        if (hash_equals($expected, $sig) && (abs(time() - $ts) < 600)) {
            $user = User::where('user_id', $uid)->where('role', 'admin')->first();
            if ($user) {
                Auth::login($user, true);
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
        }

        return redirect()->route('login', ['role' => 'admin'])->with('error', 'Sesi SSO telah kedaluwarsa. Silakan login manual.');
    }
}
