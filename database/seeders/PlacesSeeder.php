<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $places = [
            [
                'name' => 'Biblioteca Central UPV',
                'description' => 'Amplia biblioteca con salas de estudio y acceso 24h para estudiantes.',
                'address' => 'Camino de Vera s/n',
                'city' => 'Valencia',
                'latitude' => 39.4811,
                'longitude' => -0.3402,
                'category' => 'biblioteca',
                'user_id' => $user?->id ?? 1,
                'image_path' => null,
                'is_verified' => true,
                'rating' => 4.5,
                'rating_count' => 10,
            ],
            [
                'name' => 'Casa del Estudiante',
                'description' => 'Espacio de ocio y estudio con cafetería y salas polivalentes.',
                'address' => 'Avda. de los Naranjos, 1',
                'city' => 'Valencia',
                'latitude' => 39.4767,
                'longitude' => -0.3434,
                'category' => 'ocio',
                'user_id' => $user?->id ?? 1,
                'image_path' => null,
                'is_verified' => true,
                'rating' => 4.2,
                'rating_count' => 7,
            ],
            [
                'name' => 'Parque de Viveros',
                'description' => 'Pulmón verde de la ciudad ideal para hacer deporte o relajarse.',
                'address' => 'Carrer de Cavanilles',
                'city' => 'Valencia',
                'latitude' => 39.4785,
                'longitude' => -0.3620,
                'category' => 'parque',
                'user_id' => $user?->id ?? 1,
                'image_path' => null,
                'is_verified' => true,
                'rating' => 4.7,
                'rating_count' => 5,
            ],
        ];

        foreach ($places as $place) {
            Place::firstOrCreate(
                ['name' => $place['name'], 'address' => $place['address']],
                $place
            );
        }

        $this->command->info('Lugares creados exitosamente.');
    }
}
