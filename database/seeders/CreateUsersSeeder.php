<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
            'email' => 'techpulse@natanfiuza.dev.br',
        ], [
            'name' => 'Admin',
            'email' => 'techpulse@natanfiuza.dev.br',
            'password' => bcrypt('techpulse'),

        ]);
    }
}
