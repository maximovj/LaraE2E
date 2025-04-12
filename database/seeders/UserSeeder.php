<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Usuario Regular',
                'email' => 'user@larae2e.com',
                'password' => Hash::make('password'), // Cambia 'password' por la contraseña que desees
                'role' => 'user'
            ],
            [
                'name' => 'Administrador',
                'email' => 'admin@larae2e.com',
                'password' => Hash::make('adminpassword'),
                'role' => 'admin'
            ],
            [
                'name' => 'Teknok',
                'email' => 'teknok@company.com',
                'password' => Hash::make('teknokpass'),
                'role' => 'company'
            ],
            [
                'name' => 'Dark',
                'email' => 'dark@company.com',
                'password' => Hash::make('darkpass'),
                'role' => 'company'
            ],
            [
                'name' => 'Moonlight',
                'email' => 'moonlight@company.com',
                'password' => Hash::make('moonlightpass'),
                'role' => 'company'
            ]
        ];

        // Crear roles si no existen
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password']
                ]
            );

            // Asignar rol al usuario
            $user->assignRole($userData['role']);
        }
    }
}
