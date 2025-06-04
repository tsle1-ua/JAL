<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Place;
use App\Models\Category;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $place = Place::first();
        $category = Category::first();

        if (!$user || !$place || !$category) {
            return;
        }

        $events = [
            [
                'title' => 'Fiesta de bienvenida',
                'description' => 'Celebración para los nuevos estudiantes',
                'date' => now()->addDays(7),
                'time' => '21:00',
                'place_id' => $place->id,
                'user_id' => $user->id,
                'category_id' => $category->id,
                'is_public' => true,
                'price' => 0,
                'max_attendees' => 100,
            ],
            [
                'title' => 'Torneo deportivo',
                'description' => 'Competición amistosa en la universidad',
                'date' => now()->addDays(14),
                'time' => '10:00',
                'place_id' => $place->id,
                'user_id' => $user->id,
                'category_id' => Category::where('name', 'Deportivo')->first()?->id ?? $category->id,
                'is_public' => true,
                'price' => 5.00,
                'max_attendees' => 50,
            ],
            [
                'title' => 'Concierto solidario',
                'description' => 'Música en vivo para recaudar fondos',
                'date' => now()->addDays(30),
                'time' => '19:30',
                'place_id' => $place->id,
                'user_id' => $user->id,
                'category_id' => Category::where('name', 'Musical')->first()?->id ?? $category->id,
                'is_public' => true,
                'price' => 10.00,
                'max_attendees' => 200,
            ],
        ];

        foreach ($events as $data) {
            Event::firstOrCreate(
                ['title' => $data['title'], 'date' => $data['date']],
                $data
            );
        }

        $this->command->info('Eventos de ejemplo creados.');
    }
}
