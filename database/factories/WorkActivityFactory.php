<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\WorkActivity;
use App\Models\WorkDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkActivity>
 */
class WorkActivityFactory extends Factory
{
    protected $model = WorkActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->time('H:i:s');
        $endTime = $this->faker->time('H:i:s', $startTime); // Hora posterior a start_time

        return [
            'employee_id' => Employee::factory(),
            'work_day_id' => WorkDay::factory(),
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->optional()->sentence,
            'description' => $this->faker->optional()->paragraph,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_hours' => $this->faker->randomFloat(2, 0.5, 8), // Entre 0.5 y 8 horas
            'notes' => $this->faker->optional()->text(200),
            'tags' => json_encode($this->faker->words(3)),
            'status' => $this->faker->randomElement([
                'pending', // Pendiente
                'approved', // Aprobado
                'rejected', // Rechazado
                'paid', // Pagado
                'unpaid' // No Pagado
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Estado para actividades del empleado específico (TK-0004)
    public function forSpecificEmployee()
    {
        return $this->state(function (array $attributes) {
            return [
                'employee_id' => Employee::where('employee_number', 'TK-0004')->first()->id,
            ];
        });
    }

    // Estado para horas aleatorias pero coherentes
    public function withRandomHours(int $minHours = 1, int $maxHours = 8): Factory
    {
        return $this->state(function (array $attributes) use ($minHours, $maxHours) {
            $start = fake()->time('H:i:s');
            $end = \Carbon\Carbon::parse($start)
                ->addHours(rand($minHours, $maxHours))
                ->format('H:i:s');

            return [
                'start_time' => $start,
                'end_time' => $end,
                'duration_hours' => round(rand($minHours * 100, $maxHours * 100) / 100), // Ej: 3.5
            ];
        });
    }


}
