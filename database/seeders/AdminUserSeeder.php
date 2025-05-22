<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@spandam.com'],
            [
                'name' => 'Administrador Spandam',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Crear perfil para el administrador
        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'bio' => 'Administrador de la plataforma Spandam',
                'gender' => 'prefiero-no-decir',
                'age' => 30,
                'university_name' => 'Universidad Politécnica de Valencia',
                'major' => 'Administración de Sistemas',
                'academic_year' => 'Graduado',
                'looking_for_roommate' => false,
            ]
        );

        // Crear algunos usuarios de prueba
        $testUsers = [
            [
                'name' => 'María García',
                'email' => 'maria@test.com',
                'university' => 'Universidad de Valencia',
                'major' => 'Medicina',
                'year' => 'Tercer año',
                'age' => 21,
                'bio' => 'Estudiante de medicina, me gusta el deporte y la música.',
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos@test.com',
                'university' => 'Universidad Politécnica de Valencia',
                'major' => 'Ingeniería Informática',
                'year' => 'Segundo año',
                'age' => 20,
                'bio' => 'Apasionado de la tecnología y los videojuegos.',
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana@test.com',
                'university' => 'Universidad de Valencia',
                'major' => 'Psicología',
                'year' => 'Cuarto año',
                'age' => 22,
                'bio' => 'Interesada en la psicología clínica y el bienestar mental.',
            ],
            [
                'name' => 'David Martín',
                'email' => 'david@test.com',
                'university' => 'Universidad Politécnica de Valencia',
                'major' => 'Arquitectura',
                'year' => 'Primer año',
                'age' => 19,
                'bio' => 'Futuro arquitecto, me encanta el diseño y el arte.',
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'email_verified_at' => now(),
                ]
            );

            Profile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'bio' => $userData['bio'],
                    'gender' => collect(['masculino', 'femenino'])->random(),
                    'age' => $userData['age'],
                    'smoking_preference' => collect(['fumador', 'no-fumador', 'flexible'])->random(),
                    'pet_preference' => collect(['tiene-mascotas', 'le-gustan-mascotas', 'no-mascotas', 'flexible'])->random(),
                    'cleanliness_level' => rand(3, 5),
                    'sleep_schedule' => collect(['madrugador', 'noctambulo', 'flexible'])->random(),
                    'hobbies' => collect([
                        ['lectura', 'música', 'deportes'],
                        ['videojuegos', 'programación', 'cine'],
                        ['arte', 'fotografía', 'viajes'],
                        ['cocina', 'baile', 'naturaleza'],
                    ])->random(),
                    'academic_year' => $userData['year'],
                    'major' => $userData['major'],
                    'university_name' => $userData['university'],
                    'looking_for_roommate' => rand(0, 1),
                ]
            );
        }

        $this->command->info('Usuarios de prueba creados exitosamente.');
    }
}