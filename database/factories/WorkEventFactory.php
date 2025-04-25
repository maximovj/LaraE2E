<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkDay;
use App\Models\WorkEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkEventFactory extends Factory
{
    protected $model = WorkEvent::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $end = $this->faker->dateTimeBetween($start, $start->format('Y-m-d H:i:s') . '+3 hours');
        $editable = $this->faker->boolean(80);

        return [
            'employee_id' => Employee::factory(),
            'work_day_id' => WorkDay::factory(),
            'work_activity_id' => WorkActivity::factory(),
            'title' => $this->faker->sentence(3),
            'allDay' => $this->faker->boolean(10), // 30% de probabilidad de ser evento de día completo
            'start' => $start,
            'end' => $end,
            'url' => $this->faker->optional()->url,
            'classnames' => '',
            'backgroundColor' => '',
            'borderColor' => '',
            'textColor' => '',
            'overlap' => $editable,
            'editable' => $editable,
            'startEditable' => $editable,
            'durationEditable' => $editable,
            'display' => $this->faker->randomElement([
                'auto',
                'block',
                'list-item',
                'background',
                'inverse-background',
                'none',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
