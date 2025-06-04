<?php

namespace Tests\Unit;

use App\Models\Profile;
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase
{
    /**
     * @dataProvider compatibilityDataProvider
     */
    public function test_calculate_compatibility(array $profile1Data, array $profile2Data, float $expected): void
    {
        $profile1 = new Profile($profile1Data);
        $profile2 = new Profile($profile2Data);

        $this->assertSame($expected, $profile1->calculateCompatibility($profile2));
    }

    public static function compatibilityDataProvider(): array
    {
        $base = [
            'university_name'     => 'Uni1',
            'academic_year'       => '2024',
            'smoking_preference'  => 'no',
            'pet_preference'      => 'no',
            'cleanliness_level'   => 3,
            'sleep_schedule'      => 'early',
        ];

        return [
            'all match' => [
                $base,
                $base,
                100.0,
            ],
            'university mismatch' => [
                $base,
                array_merge($base, ['university_name' => 'Uni2']),
                70.0,
            ],
            'smoking mismatch' => [
                $base,
                array_merge($base, ['smoking_preference' => 'yes']),
                75.0,
            ],
            'cleanliness difference 2' => [
                $base,
                array_merge($base, ['cleanliness_level' => 5]),
                95.0,
            ],
            'all mismatch' => [
                $base,
                [
                    'university_name'    => 'Uni2',
                    'academic_year'      => '2025',
                    'smoking_preference' => 'yes',
                    'pet_preference'     => 'yes',
                    'cleanliness_level'  => 6,
                    'sleep_schedule'     => 'late',
                ],
                0.0,
            ],
        ];
    }
}
