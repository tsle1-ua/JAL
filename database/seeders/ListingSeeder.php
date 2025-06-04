<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            return;
        }

        $listings = [
            [
                'title' => 'Piso luminoso en el centro',
                'description' => 'Apartamento ideal para estudiantes, cercano a la universidad.',
                'address' => 'Calle Mayor 1',
                'city' => 'Valencia',
                'zip_code' => '46001',
                'price' => 750.00,
                'type' => 'apartamento',
                'square_meters' => 80,
                'current_occupants' => 1,
                'max_occupants' => 3,
                'phone' => '600111222',
                'bedrooms' => 3,
                'bathrooms' => 1,
                'available_from' => now()->addDays(5),
                'is_available' => true,
                'image_paths' => [],
                'latitude' => 39.4702,
                'longitude' => -0.3768,
            ],
            [
                'title' => 'Habitación en piso compartido',
                'description' => 'Se alquila habitación en piso tranquilo.',
                'address' => 'Avenida de Aragón 25',
                'city' => 'Valencia',
                'zip_code' => '46021',
                'price' => 300.00,
                'type' => 'habitacion',
                'square_meters' => 90,
                'current_occupants' => 2,
                'max_occupants' => 3,
                'phone' => '600222333',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'available_from' => now()->addDays(15),
                'is_available' => true,
                'image_paths' => [],
                'latitude' => 39.4699,
                'longitude' => -0.3541,
            ],
            [
                'title' => 'Estudio cerca de la playa',
                'description' => 'Pequeño estudio totalmente equipado.',
                'address' => 'Calle del Mar 5',
                'city' => 'Valencia',
                'zip_code' => '46011',
                'price' => 500.00,
                'type' => 'estudio',
                'square_meters' => 40,
                'current_occupants' => 0,
                'max_occupants' => 1,
                'phone' => '600333444',
                'bedrooms' => 1,
                'bathrooms' => 1,
                'available_from' => now()->addDays(30),
                'is_available' => true,
                'image_paths' => [],
                'latitude' => 39.4650,
                'longitude' => -0.3300,
            ],
        ];

        foreach ($listings as $data) {
            Listing::firstOrCreate(
                ['title' => $data['title'], 'address' => $data['address']],
                array_merge($data, ['user_id' => $user->id])
            );
        }

        $this->command->info('Anuncios de alojamiento creados.');
    }
}
