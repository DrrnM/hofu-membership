<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'Username tidak terdaftar.',
            ])->onlyInput('username');
        }

        if ($this->checkPassword($request->password, $user->password)) {
            if ($this->isPlainPassword($user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            Auth::login($user);
            $request->session()->regenerate();

            // Redirect ke intended URL atau dashboard sesuai role
            return redirect()->intended($this->getDashboardRoute($user));
        }

        return back()->withErrors([
            'password' => 'Password salah.',
        ])->onlyInput('username');
    }

    private function checkPassword($inputPassword, $storedPassword)
    {
        if (Hash::isHashed($storedPassword)) {
            return Hash::check($inputPassword, $storedPassword);
        }
        return $inputPassword === $storedPassword;
    }

    private function isPlainPassword($password)
    {
        return !Hash::isHashed($password);
    }

    private function getDashboardRoute($user)
    {
        if ($user->username === 'owner') {
            return '/owner/dashboard';
        } elseif ($user->username === 'kasir') {
            return '/kasir/dashboard';
        }
        return '/';
    }

    private function redirectUser($user)
    {
        return redirect($this->getDashboardRoute($user));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}