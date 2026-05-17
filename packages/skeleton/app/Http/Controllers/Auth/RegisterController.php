<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Gemriser\Http\Request;
use Gemriser\Http\Response;

class RegisterController
{
    public function showForm(): \Psr\Http\Message\ResponseInterface
    {
        return Response::view('auth.register');
    }

    public function register(Request $request): \Psr\Http\Message\ResponseInterface
    {
        $data = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        auth()->login($user);

        return Response::redirect('/dashboard');
    }
}
