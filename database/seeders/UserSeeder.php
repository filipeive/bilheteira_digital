<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@alphaproducoes.mz'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'porteiro@alphaproducoes.mz'],
            [
                'name' => 'Porteiro',
                'password' => bcrypt('password'),
                'role' => 'operator',
            ]
        );

        User::updateOrCreate(
            ['email' => 'organizador@alphaproducoes.mz'],
            [
                'name' => 'Organizador',
                'password' => bcrypt('password'),
                'role' => 'organizer',
            ]
        );
    }
}
