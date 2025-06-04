<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function toggleAdmin(User $user): RedirectResponse
    {
        $user->update(['is_admin' => !$user->is_admin]);

        return back();
    }

    // Placeholder methods referenced in routes
    public function users()
    {
        return view('admin.dashboard');
    }

    public function listings()
    {
        return view('admin.dashboard');
    }

    public function events()
    {
        return view('admin.dashboard');
    }

    public function places()
    {
        return view('admin.dashboard');
    }

    public function verifyPlace()
    {
        return back();
    }
}
