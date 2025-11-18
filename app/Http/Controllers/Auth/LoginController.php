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

        // FIX: Handle both bcrypt and plain password
        if ($this->checkPassword($request->password, $user->password)) {
            // If password is plain text, hash it and update
            if ($this->isPlainPassword($user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'password' => 'Password salah.',
        ])->onlyInput('username');
    }

    /**
     * Check password match (both bcrypt and plain text)
     */
    private function checkPassword($inputPassword, $storedPassword)
    {
        // Check if stored password is bcrypt hash
        if (Hash::isHashed($storedPassword)) {
            return Hash::check($inputPassword, $storedPassword);
        }

        // Check plain text password
        return $inputPassword === $storedPassword;
    }

    /**
     * Check if password is stored as plain text
     */
    private function isPlainPassword($password)
    {
        return !Hash::isHashed($password);
    }

    /**
     * Redirect user based on role
     */
    private function redirectUser($user)
    {
        if ($user->username === 'owner') {
            return redirect()->intended('/owner/dashboard');
        } elseif ($user->username === 'kasir') {
            return redirect()->intended('/kasir/dashboard');
        }

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}