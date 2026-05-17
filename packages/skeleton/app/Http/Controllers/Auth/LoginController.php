<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Gemriser\Http\Request;
use Gemriser\Http\Response;

class LoginController
{
    public function showForm(): \Psr\Http\Message\ResponseInterface
    {
        return Response::view('auth.login');
    }

    public function login(Request $request): \Psr\Http\Message\ResponseInterface
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return Response::redirect('/dashboard');
        }

        return Response::view('auth.login', ['error' => 'Invalid credentials']);
    }

    public function logout(): \Psr\Http\Message\ResponseInterface
    {
        auth()->logout();
        return Response::redirect('/');
    }
}
