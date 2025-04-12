<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Crear empleados para empresas específicas
        $teknok = Company::where('commercial_name', 'Teknok')->first();
        $moonlight = Company::where('commercial_name', 'Moonlight')->first();

        // Empleados para Teknok
        if ($teknok) {
            $teknokEmployees = [
                [
                    'user_id' => User::where('email', 'admin@larae2e.com')->first()->id,
                    'employee_number' => 'TK-0001',
                    'job_title' => 'CEO',
                    'position' => 'Director General',
                    'hired_at' => now()->subYears(3),
                    'status' => 'active',
                    'salary' => 80000,
                    'shift' => '9 AM - 6 PM',
                    'emergency_contact' => 'Maria Lopez - 555-123-4567'
                ],
                [
                    'user_id' => User::where('email', 'teknok@company.com')->first()->id,
                    'employee_number' => 'TK-0002',
                    'job_title' => 'CTO',
                    'position' => 'Director de Tecnología',
                    'hired_at' => now()->subYears(2),
                    'status' => 'active',
                    'salary' => 70000,
                    'shift' => '9 AM - 6 PM',
                    'emergency_contact' => 'Juan Perez - 555-234-5678'
                ]
            ];

            foreach ($teknokEmployees as $employeeData) {
                Employee::updateOrCreate(
                    ['employee_number' => $employeeData['employee_number']],
                    array_merge($employeeData, [
                        'company_id' => $teknok->id,
                        'office_id' => $teknok->offices()->first()->id
                    ])
                );
            }

            // Crear 10 empleados aleatorios para Teknok
            Employee::factory()
                ->count(10)
                ->forCompany($teknok)
                ->create();
        }

        // Empleados para Moonlight
        if ($moonlight) {
            $moonlightEmployees = [
                [
                    'user_id' => User::where('email', 'dark@company.com')->first()->id,
                    'employee_number' => 'ML-0001',
                    'job_title' => 'CEO',
                    'position' => 'Director General',
                    'hired_at' => now()->subYears(4),
                    'status' => 'active',
                    'salary' => 85000,
                    'shift' => '9 AM - 6 PM',
                    'emergency_contact' => 'Ana Garcia - 555-345-6789'
                ],
                [
                    'user_id' => User::where('email', 'moonlight@company.com')->first()->id,
                    'employee_number' => 'ML-0002',
                    'job_title' => 'CFO',
                    'position' => 'Director Financiero',
                    'hired_at' => now()->subYears(1),
                    'status' => 'active',
                    'salary' => 75000,
                    'shift' => '9 AM - 6 PM',
                    'emergency_contact' => 'Carlos Ruiz - 555-456-7890'
                ]
            ];

            foreach ($moonlightEmployees as $employeeData) {
                Employee::updateOrCreate(
                    ['employee_number' => $employeeData['employee_number']],
                    array_merge($employeeData, [
                        'company_id' => $moonlight->id,
                        'office_id' => $moonlight->offices()->first()->id
                    ])
                );
            }

            // Crear 8 empleados aleatorios para Moonlight
            Employee::factory()
                ->count(8)
                ->forCompany($moonlight)
                ->create();
        }

        // Crear 15 empleados aleatorios para otras compañías
        Employee::factory()
            ->count(15)
            ->create();

    }
}
