<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadSubscriber;

class LeadSubscriberController extends Controller
{
    public function index()
    {
        $subscribers = LeadSubscriber::query()
            ->with('leadMagnet')
            ->latest('downloaded_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.lead-subscribers.index', compact('subscribers'));
    }
}
