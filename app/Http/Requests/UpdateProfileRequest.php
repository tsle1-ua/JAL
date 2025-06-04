<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            'bio' => 'nullable|string|max:2000',
            'gender' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0|max:120',
            'smoking_preference' => 'nullable|string',
            'pet_preference' => 'nullable|string',
            'cleanliness_level' => 'nullable|integer|min:1|max:5',
            'sleep_schedule' => 'nullable|string',
            'hobbies' => 'nullable|array',
            'hobbies.*' => 'string|max:255',
            'academic_year' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'university_name' => 'nullable|string|max:255',
            'looking_for_roommate' => 'boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('looking_for_roommate')) {
            $this->merge([
                'looking_for_roommate' => $this->boolean('looking_for_roommate'),
            ]);
        }
    }
}
