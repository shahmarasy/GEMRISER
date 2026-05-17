<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Gemriser\Http\Response;

class HomeController
{
    public function index(): \Psr\Http\Message\ResponseInterface
    {
        return Response::view('home', [
            'pageTitle' => 'Welcome to Gemriser',
        ]);
    }
}
