<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkDay;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear actividades para el empleado específico (TK-0004)
        $employee = Employee::where('employee_number', 'TK-0004')->first();

        if ($employee) {
            // Obtener sus días de trabajo
            $workDays = WorkDay::where('employee_id', $employee->id)->get();

            foreach ($workDays as $day) {
                // Crear 2-5 actividades por día
                WorkActivity::factory()
                    ->count(1)
                    ->create([
                        'employee_id' => $employee->id,
                        'work_day_id' => $day->id,
                    ]);
            }
        }

        // Crear actividades aleatorias para otros empleados (opcional)
        WorkActivity::factory()
            ->count(30)
            ->create();
    }
}
