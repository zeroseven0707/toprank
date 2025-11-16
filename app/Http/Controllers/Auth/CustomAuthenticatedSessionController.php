<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\LoginRequest;

class CustomAuthenticatedSessionController extends AuthenticatedSessionController
{
    public function store(LoginRequest $request)
    {
        $response = parent::store($request);
        $user = auth()->user();
        if ($user->roles[0]->name === 'User') {
            return redirect()->route('home');
        }
        return redirect()->route('dashboard');
    }
}
