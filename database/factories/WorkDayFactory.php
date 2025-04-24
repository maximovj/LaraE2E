<?php

namespace Database\Factories;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkDayFactory extends Factory
{
    protected static ?array $usedDates = []; // Almacena fechas usadas

    public function definition(): array
    {
        $date = $this->generateUniqueDate();

        return [
            'employee_id' => Employee::factory(),
            'date' => $date,
            'hourly_rate' => $this->faker->randomFloat(2, 10, 50),
            'total_hours' => $this->faker->randomFloat(2, 1, 12),
            'total_events' => $this->faker->numberBetween(1, 10),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'billable' => $this->faker->boolean(80), // 80% de probabilidad de ser billable
            'status' => $this->faker->randomElement([
                'pending', // Pendiente
                'approved', // Aprobado
                'rejected', // Rechazado
                'paid', // Pagado
                'unpaid' // No Pagado
            ]),
            'details' => $this->faker->sentence,
            'tags' => json_encode($this->faker->words(3)),
            'note' => $this->faker->optional()->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function generateUniqueDate(): string
    {
        $maxAttempts = 100; // Prevenir bucles infinitos
        $attempts = 0;

        do {
            $date = Carbon::today()
                ->subDays(rand(0, 365))
                ->format('Y-m-d');

            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \Exception('No se pudo generar una fecha única después de '.$maxAttempts.' intentos');
            }

        } while (in_array($date, self::$usedDates));

        self::$usedDates[] = $date;
        return $date;
    }

    // Resetear fechas usadas al finalizar
    public static function resetUsedDates(): void
    {
        self::$usedDates = [];
    }
}
