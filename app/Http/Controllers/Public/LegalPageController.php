<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class LegalPageController extends Controller
{
    public function terms()
    {
        return view('public.legal.terms');
    }

    public function privacy()
    {
        return view('public.legal.privacy');
    }
}
