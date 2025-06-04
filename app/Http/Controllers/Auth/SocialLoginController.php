<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     */
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider and log them in.
     */
    public function callback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user) {
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName() ?: $socialUser->getNickname(),
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                ]
            );

            $user->provider = $provider;
            $user->provider_id = $socialUser->getId();
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->route('home');
    }
}

