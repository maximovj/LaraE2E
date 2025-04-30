<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\WorkDay;
use Illuminate\Database\Seeder;

class WorkDaySeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::whereHas('user', function($query){
            $query->role('regular-user');
        })->get();

        foreach($employees as $employee) {
            // Crear días de trabajo para este empleado
            WorkDay::factory()
                ->count(rand(50, 100))
                ->create([
                    'employee_id' => $employee->id,
                ]);
        }
    }
}
