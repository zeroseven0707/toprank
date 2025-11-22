<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\LoginRequest;

class CustomAuthenticatedSessionController extends AuthenticatedSessionController
{
    public function store(LoginRequest $request)
    {
        $response = parent::store($request);
        if (!auth()->check()) {
            return redirect()->route('login')->withErrors(['email' => 'Email atau password salah.']);
        }
        $user = auth()->user();
        $roleName = optional($user->roles->first())->name;
        if ($roleName === 'User') {
            return redirect()->route('home');
        }
        return redirect()->route('dashboard');
    }
}
