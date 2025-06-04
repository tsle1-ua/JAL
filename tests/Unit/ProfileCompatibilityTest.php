<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_compatibility(): void
    {
        $profile1 = Profile::factory()->for(User::factory()->create())->create();
        $profile2 = Profile::factory()->for(User::factory()->create())->create([
            'gender' => $profile1->gender,
            'age' => $profile1->age,
            'smoking_preference' => $profile1->smoking_preference,
            'pet_preference' => $profile1->pet_preference,
            'cleanliness_level' => $profile1->cleanliness_level,
            'sleep_schedule' => $profile1->sleep_schedule,
            'academic_year' => $profile1->academic_year,
            'major' => $profile1->major,
            'university_name' => $profile1->university_name,
        ]);

        $score = $profile1->calculateCompatibility($profile2);
        $this->assertEquals(100.0, $score);
    }
}
