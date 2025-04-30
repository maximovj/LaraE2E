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

        $employees = Employee::whereHas('user', function($query){
            $query->role('regular-user');
        })->get();

        foreach($employees as $employee) {
            // Buscar al empleado específico (mejoramos con firstOrFail para error controlado)
            //$employee = Employee::where('employee_number', 'TK-0004')->firstOrFail();

            // Obtener sus días de trabajo (con carga eficiente)
            $workDays = WorkDay::where('employee_id', $employee->id)->get();

            foreach ($workDays as $day) {
                // Crear exactamente 15 actividades por día
                WorkActivity::factory()
                    ->count(rand(1, 15)) // Cantidad exacta
                    ->withRandomHours(1, 12) // Estado personalizado para horas
                    ->create([
                        'employee_id' => $employee->id, // Asignación fija
                        'work_day_id' => $day->id,     // Asignación por iteración
                    ]);
            }
        }

        /*
        // Crear actividades aleatorias para otros empleados (opcional)
        WorkActivity::factory()
            ->count(30)
            ->create();
        */
    }
}
