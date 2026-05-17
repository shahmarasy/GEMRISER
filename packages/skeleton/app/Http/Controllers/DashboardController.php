<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Gemriser\Http\Response;

class DashboardController
{
    public function index(): \Psr\Http\Message\ResponseInterface
    {
        return Response::view('dashboard', [
            'user' => auth()->user(),
        ]);
    }
}
