<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;

class FaqController extends Controller
{
    public function index()
    {
        $supportWhatsapp = LandingSetting::query()->value('support_whatsapp');
        $supportNumber = $supportWhatsapp
            ? preg_replace('/\D+/', '', (string) $supportWhatsapp)
            : null;

        return view('public.faq', [
            'supportNumber' => $supportNumber,
        ]);
    }
}
