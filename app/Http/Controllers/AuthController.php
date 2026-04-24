<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // ambil input
        $credentials = $request->only('email', 'password');
        $role = $request->role;

        if (Auth::attempt($credentials, $request->filled('remember'))) {

            $user = Auth::user();

            if ($user->role !== $role) {
                Auth::logout();
                return back()->with('error', 'Role tidak sesuai!');
            }

            if ($user->role === 'dokter') {
                return redirect('/dokter/dashboard');
            } else if ($user->role === 'apoteker'){
                return redirect('/apoteker/dashboard');
            } else {
                return redirect('/admin/dashboard');
            }
        }

        return back()->with('error', 'Email atau password salah');
    }

}

// use App\Models\User;
// use Illuminate\Support\Facades\Hash;

// User::create([
//     'name' => 'Test',
//     'email' => 'admin@test.com',
//     'password' => Hash::make('112233'),
//     'role' => 'admin'
// ]);
