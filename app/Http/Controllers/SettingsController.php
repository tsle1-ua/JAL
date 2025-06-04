<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function notifications(Request $request)
    {
        $settings = $request->user()->notificationSetting;

        if (!$settings) {
            $settings = $request->user()->notificationSetting()->create();
        }

        return view('settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request)
    {
        $data = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
        ]);

        $request->user()->notificationSetting()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'email_notifications' => $data['email_notifications'] ?? false,
                'push_notifications' => $data['push_notifications'] ?? false,
            ]
        );

        return redirect()->route('settings.notifications')->with('status', 'Configuración guardada.');
    }
}
