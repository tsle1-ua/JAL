<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;
use App\Models\LoginAttempt;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return app(RedirectIfTwoFactorAuthenticatable::class)
            ->handle($request, function () {
                return redirect()->intended($this->redirectPath());
            });
    }

    /**
     * Handle a failed authentication attempt.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        LoginAttempt::create([
            'ip_address' => $request->ip(),
        ]);

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
