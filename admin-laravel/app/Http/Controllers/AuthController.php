<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session("user_id")) {
            return session("role") === "admin"
                ? redirect()->route("admin.dashboard")
                : redirect()->route("home");
        }
        return view("auth.customer-login");
    }

    public function login(Request $request)
    {
        $request->validate([
            "email_or_phone" => "required|string",
            "password" => "required|string",
        ]);

        $id = trim($request->email_or_phone);
        $user = User::where("email", $id)->orWhere("phone", $id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only("email_or_phone"))
                ->with("error", "Email/no. HP atau password salah.");
        }

        // Store in session (PHP-style for backward compat with api.php)
        $request->session()->put("user_id", $user->user_id);
        $request->session()->put("name", $user->name);
        $request->session()->put("role", $user->role);
        \Illuminate\Support\Facades\Auth::loginUsingId($user->user_id);

        if ($user->role === "admin") {
            return redirect()->route("admin.dashboard");
        }

        $redirect = $request->session()->pull("redirect_after", null);
        return $redirect ? redirect("/" . $redirect) : redirect()->route("home");
    }

    public function showRegister()
    {
        if (session("user_id")) return redirect()->route("home");
        return view("auth.customer-register");
    }

    public function register(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:100",
            "email" => "required|email|unique:users,email",
            "phone" => "required|string|max:20",
            "password" => "required|min:6|confirmed",
        ], [
            "email.unique" => "Email sudah terdaftar.",
            "password.confirmed" => "Konfirmasi password tidak cocok.",
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "password" => Hash::make($request->password),
            "role" => "user",
        ]);

        $request->session()->put("user_id", $user->user_id);
        $request->session()->put("name", $user->name);
        $request->session()->put("role", "user");
        \Illuminate\Support\Facades\Auth::loginUsingId($user->user_id);

        return redirect()->route("home")->with("success", "Akun berhasil dibuat!");
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route("login")->with("info", "Kamu berhasil logout.");
    }
}
