<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\WorkDay;
use Illuminate\Database\Seeder;

class WorkDaySeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el empleado específico
        $employee = Employee::where('employee_number', 'TK-0004')->first();

        if ($employee) {
            // Crear días de trabajo para este empleado
            WorkDay::factory()
                ->count(rand(50, 100))
                ->create([
                    'employee_id' => $employee->id,
                ]);
        }
    }
}
