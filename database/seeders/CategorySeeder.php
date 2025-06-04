<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Académico',
            'Cultural',
            'Deportivo',
            'Social',
            'Musical',
            'Gastronómico',
            'Tecnológico',
            'Artístico',
            'Voluntariado',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Categorías creadas exitosamente.');
    }
}
