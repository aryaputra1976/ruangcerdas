<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        return response()
            ->view('public.robots')
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}

