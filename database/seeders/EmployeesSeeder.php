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
        // Obtener todas las compañías de una sola consulta
        $companies = Company::with('offices')->get()->keyBy('commercial_name');

        // Mapeo de usuarios a empleados por compañía
        $employeeMappings = [
            'Teknok' => [
                'teknok@company.com' => [
                    'employee_number' => 'TK-0002',
                    'job_title' => 'CTO',
                    'position' => 'Director de Tecnología',
                    'salary' => 70000
                ],
                'manager@teknok.com' => [
                    'employee_number' => 'TK-0003',
                    'job_title' => 'Office Manager',
                    'position' => 'Gerente de Oficina',
                    'salary' => 60000
                ],
                'employee@teknok.com' => [
                    'employee_number' => 'TK-0004',
                    'job_title' => 'Usuario Regular',
                    'position' => 'Usuario de pruebas',
                    'salary' => 40000
                ]
            ],
            'Dark' => [
                'dark@company.com' => [
                    'employee_number' => 'DK-0001',
                    'job_title' => 'CEO',
                    'position' => 'Director General',
                    'salary' => 82000
                ],
                'manager@dark.com' => [
                    'employee_number' => 'DK-0002',
                    'job_title' => 'Operations Manager',
                    'position' => 'Gerente de Operaciones',
                    'salary' => 65000
                ],
                'employee@dark.com' => [
                    'employee_number' => 'DK-0004',
                    'job_title' => 'Usuario Regular',
                    'position' => 'Usuario de pruebas',
                    'salary' => 40000
                ]
            ],
            'Moonlight' => [
                'moonlight@company.com' => [
                    'employee_number' => 'ML-0001',
                    'job_title' => 'CEO',
                    'position' => 'Director General',
                    'salary' => 85000
                ],
                'manager@moonlight.com' => [
                    'employee_number' => 'ML-0002',
                    'job_title' => 'Finance Manager',
                    'position' => 'Gerente Financiero',
                    'salary' => 68000
                ],
                'employee@moonlight.com' => [
                    'employee_number' => 'ML-0004',
                    'job_title' => 'Usuario Regular',
                    'position' => 'Usuario de pruebas',
                    'salary' => 40000
                ]
            ]
        ];

        // Crear empleados para cada compañía
        foreach ($employeeMappings as $companyName => $employees) {
            if (!$company = $companies->get($companyName)) {
                continue;
            }

            $officeId = $company->offices->first()->id ?? null;

            foreach ($employees as $email => $employeeData) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    Employee::updateOrCreate(
                        ['employee_number' => $employeeData['employee_number']],
                        array_merge($employeeData, [
                            'user_id' => $user->id,
                            'company_id' => $company->id,
                            'office_id' => $officeId,
                            'hired_at' => now()->subYears(rand(1, 5)),
                            'status' => 'active',
                            'shift' => '9 AM - 6 PM',
                            'emergency_contact' => fake()->name() . ' - ' . fake()->phoneNumber()
                        ])
                    );
                }
            }

            /*
            // Crear empleados aleatorios para la compañía (sin usuario asociado)
            Employee::factory()
                ->count(5) // Ajusta según necesidad
                ->forCompany($company)
                ->create([
                    'office_id' => $company->offices->random()->id ?? null
                ]);
            */
        }

        // Empleados genéricos (sin compañía específica)
        Employee::factory()->count(10)->create();
    }
}
