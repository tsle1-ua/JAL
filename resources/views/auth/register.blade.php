@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-8">
    <div class="bg-white shadow rounded-lg">
        <div class="bg-blue-600 text-white px-6 py-3">{{ __('Register') }}</div>
        <div class="p-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="mt-1 w-full rounded-md border-gray-300" autocomplete="name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-md border-gray-300" autocomplete="email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium">{{ __('Register As') }}</label>
                    <select id="role" name="role" required class="mt-1 w-full rounded-md border-gray-300">
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Estudiante</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Propietario</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required class="mt-1 w-full rounded-md border-gray-300" autocomplete="new-password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password-confirm" class="block text-sm font-medium">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required class="mt-1 w-full rounded-md border-gray-300" autocomplete="new-password">
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">{{ __('Register') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
