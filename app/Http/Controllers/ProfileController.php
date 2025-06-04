<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function show()
    {
        $profile = auth()->user()->profile;
        return view('profile.show', compact('profile'));
    }

    public function edit()
    {
        $profile = auth()->user()->profile;
        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = auth()->user()->profile;

        $data = $request->validate([
            'bio' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'age' => ['nullable', 'integer'],
            'smoking_preference' => ['nullable', 'string'],
            'pet_preference' => ['nullable', 'string'],
            'cleanliness_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sleep_schedule' => ['nullable', 'string'],
            'hobbies' => ['nullable', 'string'],
            'academic_year' => ['nullable', 'string'],
            'major' => ['nullable', 'string'],
            'university_name' => ['nullable', 'string'],
            'looking_for_roommate' => ['nullable', 'boolean'],
            'profile_image' => ['nullable', 'image'],
        ]);

        $data['looking_for_roommate'] = $request->has('looking_for_roommate');

        if (isset($data['hobbies'])) {
            $data['hobbies'] = array_map('trim', explode(',', $data['hobbies']));
        }

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $data['profile_image'] = $path;
        } else {
            unset($data['profile_image']);
        }

        $profile->update($data);

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado.');
    }

    public function destroy()
    {
        $profile = auth()->user()->profile;
        if ($profile) {
            $profile->delete();
        }
        return redirect()->route('home')->with('success', 'Perfil eliminado.');
    }
}

