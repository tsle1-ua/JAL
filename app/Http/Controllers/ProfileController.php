<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function show()
    {
        $profile = auth()->user()->profile()->firstOrCreate([]);
        return view('profile.show', compact('profile'));
    }

    public function edit()
    {
        $profile = auth()->user()->profile()->firstOrCreate([]);
        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            $profile = auth()->user()->profile()->create([]);
        }

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
            $imageManager = new ImageManager(new Driver());
            $image = $imageManager->read($request->file('profile_image')->getPathname())
                ->cover(300, 300);

            $filename = 'profiles/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, $image->toJpeg()->toString());
            $data['profile_image'] = $filename;
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
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $profile->delete();
        }

        return redirect()->route('home')->with('success', 'Perfil eliminado.');
    }
}
