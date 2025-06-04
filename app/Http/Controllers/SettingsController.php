<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function notifications()
    {
        return view('settings.notifications');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notifications_enabled' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->notifications_enabled = $request->boolean('notifications_enabled');
        $user->save();

        return redirect()->route('settings.notifications')->with('status', __('Notification settings updated.'));
    }
}
