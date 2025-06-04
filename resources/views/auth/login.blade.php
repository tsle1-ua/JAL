@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-8">
    <div class="bg-white shadow rounded-lg">
        <div class="bg-blue-600 text-white px-6 py-3">{{ __('Login') }}</div>
        <div class="p-6">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 w-full rounded-md border-gray-300" autocomplete="email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required class="mt-1 w-full rounded-md border-gray-300" autocomplete="current-password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input class="h-4 w-4 mr-2" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="text-sm">{{ __('Remember Me') }}</label>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">{{ __('Login') }}</button>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-blue-600 hover:underline block mt-2" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
