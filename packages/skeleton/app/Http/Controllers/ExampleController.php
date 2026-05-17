<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Gemriser\Http\Response;

class ExampleController
{
    public function index(): \Psr\Http\Message\ResponseInterface
    {
        $examples = \App\Models\User::all();
        return Response::view('examples.index', ['examples' => $examples]);
    }
}
