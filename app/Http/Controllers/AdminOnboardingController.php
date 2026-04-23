<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOnboardingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->memberships()->exists()) {
            return redirect()->route('dashboard');
        }

        return view('admin.start');
    }
}
