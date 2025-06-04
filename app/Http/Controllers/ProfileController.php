<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.show');
    }

    public function edit(): View
    {
        abort(501);
    }

    public function update(Request $request): RedirectResponse
    {
        abort(501);
    }

    public function destroy(): RedirectResponse
    {
        abort(501);
    }
}
