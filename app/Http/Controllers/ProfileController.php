<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(): View
    {
        $profile = auth()->user()->profile;

        return view('profile.show', compact('profile'));
    }

    public function edit(): View
    {
        $profile = auth()->user()->profile;

        return view('profile.edit', compact('profile'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $profile = $user->profile()->updateOrCreate([], $data);

        if ($request->hasFile('profile_image')) {
            $imageFile = $request->file('profile_image');
            $manager = ImageManager::gd();
            $image = $manager->read($imageFile->getPathname())->cover(300, 300);
            $encoded = $image->toJpeg();
            $path = 'profile_images/' . uniqid() . '.jpg';
            Storage::disk('public')->put($path, $encoded->toString());
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $profile->profile_image = $path;
            $profile->save();
        }

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado.');
    }

    public function destroy(): RedirectResponse
    {
        $user = auth()->user();
        if ($user->profile) {
            if ($user->profile->profile_image) {
                Storage::disk('public')->delete($user->profile->profile_image);
            }
            $user->profile->delete();
        }

        return redirect()->route('profile.show')->with('success', 'Perfil eliminado.');
    }
}
