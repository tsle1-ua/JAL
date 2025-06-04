<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(5)
            ->create()
            ->each(function (User $user) {
                $user->profile()->create(Profile::factory()->make()->toArray());
            });

        $this->command->info('Usuarios adicionales de prueba creados.');
    }
}
