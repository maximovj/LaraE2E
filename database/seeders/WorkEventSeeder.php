<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkDay;
use App\Models\WorkEvent;
use Illuminate\Database\Seeder;

class WorkEventSeeder extends Seeder
{
    public function run(): void
    {
        // Crear eventos para el empleado específico (TK-0004)
        $employee = Employee::where('employee_number', 'TK-0004')->first();

        if ($employee) {
            // Obtener días de trabajo existentes del empleado
            $workDays = WorkDay::where('employee_id', $employee->id)->get();

            foreach ($workDays as $day) {
                $workActivity = WorkActivity::where('work_day_id', $day->id)->first();
                if($workActivity) {
                    // Crear 1-3 eventos por día de trabajo
                    WorkEvent::factory()
                        ->count(1)
                        ->create([
                            'employee_id' => $employee->id,
                            'work_day_id' => $day->id,
                            'work_activity_id' => $workActivity->id,
                        ]);
                }
            }
        }

        /*
        // Crear eventos aleatorios para otros empleados (opcional)
        WorkEvent::factory()
            ->count(50)
            ->create();
        */
    }
}
