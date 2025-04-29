<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear permisos base para cada modelo (CRUD)
        $this->createBasePermissions();

        // 2. Crear roles jerárquicos con sus permisos específicos
        $this->createRolesWithPermissions();

        // 3. Crear usuarios de ejemplo asignando roles
        $this->createUsersWithRoles();
    }

    protected function createBasePermissions(): void
    {
        $models = ['users', 'companies', 'employees', 'offices', 'user_profiles', 'work_activities'];
        $actions = ['create', 'read', 'update', 'delete', 'destroy', 'restore', 'manage']; // 'manage' para acciones especiales

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$model}.{$action}"]);
            }
        }

        // Permisos especiales adicionales
        Permission::firstOrCreate(['name' => 'system.settings']);
        Permission::firstOrCreate(['name' => 'reports.generate']);
    }

    protected function createRolesWithPermissions(): void
    {
        // Super Administrador - Acceso total
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Administrador de Compañía
        $companyAdmin = Role::firstOrCreate(['name' => 'company-admin']);
        $companyAdmin->givePermissionTo([
            'companies.read',
            'companies.update',
            'companies.manage',
            'employees.create',
            'employees.read',
            'employees.update',
            'employees.delete',
            'employees.manage',
            'offices.read',
            'user_profiles.create',
            'user_profiles.delete',
            'users.create',
            'users.delete',
            'reports.generate'
        ]);

        // Gerente de Oficina
        $officeManager = Role::firstOrCreate(['name' => 'office-manager']);
        $officeManager->givePermissionTo([
            'offices.read',
            'offices.update',
            'employees.read',
            'employees.update',
            'user_profiles.read'
        ]);

        // Usuario Regular
        $regularUser = Role::firstOrCreate(['name' => 'regular-user']);
        $regularUser->givePermissionTo([
            'work_activities.create',
            'work_activities.read',
            'work_activities.update',
            'work_activities.delete',
            'work_activities.destroy',
            'user_profiles.read',
            'user_profiles.update'
        ]);
    }

    protected function createUsersWithRoles(): void
    {
        $users = [
            // Usuarios para pruebas
            [
                'name' => 'Super Administrador',
                'email' => 'admin@larae2e.com',
                'password' => Hash::make('superadminpass'),
                'role' => 'super-admin'
            ],

            [
                'name' => 'Usuario Regular',
                'email' => 'user@larae2e.com',
                'password' => Hash::make('password'),
                'role' => 'regular-user'
            ],

            // Usuarios para teknok
            [
                'name' => 'Admin Teknok',
                'email' => 'teknok@company.com',
                'password' => Hash::make('teknokpass'),
                'role' => 'company-admin'
            ],
            [
                'name' => 'Gerente Teknok',
                'email' => 'manager@teknok.com',
                'password' => Hash::make('teknokpass'),
                'role' => 'office-manager'
            ],
            [
                'name' => 'Empleado Teknok',
                'email' => 'employee@teknok.com',
                'password' => Hash::make('teknokpass'),
                'role' => 'regular-user'
            ],

            // Usuarios para dark
            [
                'name' => 'Admin Dark',
                'email' => 'dark@company.com',
                'password' => Hash::make('darkpass'),
                'role' => 'company-admin'
            ],
            [
                'name' => 'Gerente Dark',
                'email' => 'manager@dark.com',
                'password' => Hash::make('darkpass'),
                'role' => 'office-manager'
            ],
            [
                'name' => 'Empleado Dark',
                'email' => 'employee@dark.com',
                'password' => Hash::make('darkpass'),
                'role' => 'regular-user'
            ],

            // Usuarios para moonlight
            [
                'name' => 'Admin Moonlight',
                'email' => 'moonlight@company.com',
                'password' => Hash::make('moonlightpass'),
                'role' => 'company-admin'
            ],
            [
                'name' => 'Gerente Moonlight',
                'email' => 'manager@moonlight.com',
                'password' => Hash::make('moonlightpass'),
                'role' => 'office-manager'
            ],
            [
                'name' => 'Empleado Moonlight',
                'email' => 'employee@moonlight.com',
                'password' => Hash::make('moonlightpass'),
                'role' => 'regular-user'
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password']
                ]
            );

            $user->syncRoles($userData['role']);
            $user->forgetCachedPermissions();
        }
    }

}
