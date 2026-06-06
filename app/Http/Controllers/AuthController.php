<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $role = $request->role;

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->role !== $role) {
                Auth::logout();
                return back()->with('error', 'Role tidak sesuai!');
            }

            if ($user->role === 'dokter') {
                return redirect('/dokter/dashboard');
            } elseif ($user->role === 'apoteker') {
                return redirect('/apoteker/dashboard');
            } else {
                return redirect('/admin/dashboard');
            }
        }
        return back()->with('error', 'Email atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}