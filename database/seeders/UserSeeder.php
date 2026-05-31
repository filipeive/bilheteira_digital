<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@alphaproducoes.mz',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Porteiro',
            'email' => 'porteiro@alphaproducoes.mz',
            'password' => bcrypt('password'),
            'role' => 'operator',
        ]);

        User::create([
            'name' => 'Organizador',
            'email' => 'organizador@alphaproducoes.mz',
            'password' => bcrypt('password'),
            'role' => 'organizer',
        ]);
    }
}
