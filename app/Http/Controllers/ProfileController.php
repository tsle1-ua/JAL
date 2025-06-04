<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display the authenticated user's profile.
     */
    public function show(): View
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile.show', compact('user', 'profile'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the profile in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:18|max:100',
            'smoking_preference' => 'nullable|in:yes,no,flexible',
            'pet_preference' => 'nullable|in:yes,no,flexible',
            'cleanliness_level' => 'nullable|integer|min:1|max:5',
            'sleep_schedule' => 'nullable|in:early,late,flexible',
            'hobbies' => 'nullable|array',
            'hobbies.*' => 'string|max:255',
            'academic_year' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:255',
            'university_name' => 'nullable|string|max:255',
            'looking_for_roommate' => 'nullable|boolean',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')
                ->store('profiles', 'public');
        }

        $user = Auth::user();
        $profile = $user->profile;

        if ($profile) {
            $profile->update($data);
        } else {
            $profile = new Profile($data);
            $user->profile()->save($profile);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Remove the profile from storage.
     */
    public function destroy(): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;

        if ($profile) {
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $profile->delete();
        }

        return redirect()->route('dashboard')
            ->with('success', 'Perfil eliminado correctamente.');
    }
}
