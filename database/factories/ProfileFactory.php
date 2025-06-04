<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'bio' => $this->faker->sentence(),
            'gender' => 'masculino',
            'age' => 20,
            'smoking_preference' => 'no-fumador',
            'pet_preference' => 'no-mascotas',
            'cleanliness_level' => 3,
            'sleep_schedule' => 'madrugador',
            'hobbies' => ['leer'],
            'academic_year' => '1',
            'major' => 'Engineering',
            'university_name' => 'Uni',
            'looking_for_roommate' => true,
        ];
    }
}
