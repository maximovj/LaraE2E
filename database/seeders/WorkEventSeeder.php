<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkDay;
use App\Models\WorkEvent;
use Carbon\Carbon;
use DateTime;
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

                // Obtener todo las actividades del día trabajado existentes del empleado
                $workActivities = WorkActivity::where('work_day_id', $day->id)->get();

                foreach($workActivities as $work_activity) {
                    $day_date = Carbon::parse($day->date)->toDateString();
                    $work_activity_start_time = Carbon::parse($work_activity->start_time)->format('H:i:s');
                    $work_activity_end_time = Carbon::parse($work_activity->end_time)->format('H:i:s');
                    $work_activity_status = $work_activity->status;

                    $now = Carbon::now();
                    $start = Carbon::createFromFormat('Y-m-d H:i:s', "$day_date $work_activity_start_time")->format('Y-m-d H:i:s');
                    $end = Carbon::createFromFormat('Y-m-d H:i:s', "$day_date $work_activity_end_time")->format('Y-m-d H:i:s');
                    $color = $this->getColorByStatus($work_activity_status);
                    // Verifica si $end ya pasó Y si la diferencia es mayor o igual a 15 días
                    $endPassed_isEditable = ($now->diffInDays($end) >= 15) ? false : true;

                    // Crear 1-3 eventos por día de trabajo
                    WorkEvent::factory()
                        ->create([
                            'employee_id' => $employee->id,
                            'work_day_id' => $day->id,
                            'work_activity_id' => $work_activity->id,
                            'backgroundColor' => $color,
                            'borderColor' => $color,
                            'overlap' => true,
                            'editable' => $endPassed_isEditable,
                            'startEditable' => $endPassed_isEditable,
                            'durationEditable' => $endPassed_isEditable,
                            'start' => $start,
                            'end' => $end,
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

    public function getColorByStatus($status)
    {
        $status_available = [
            'approved' => 'lime',
            'rejected' => 'red',
            'paid' => 'green',
            'unpaid' => 'lightgray',
            'pending' => '', // 'pending'
            '' => '', // 'pending'
        ];
        return isset($status_available[$status]) ? $status_available[$status] : '';
    }


}
