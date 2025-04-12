<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Office;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = Company::inRandomOrder()->first() ?? Company::factory()->create();
        $office = Office::where('company_id', $company->id)->inRandomOrder()->first() ?? Office::factory()->create(['company_id' => $company->id]);

        return [
            'user_id' => null, // Se asigna después si es necesario
            'company_id' => $company->id,
            'office_id' => $office->id,
            'manager_id' => null, // Se asigna después si es necesario
            'employee_number' => $this->faker->unique()->regexify('[A-Z]{2}-\d{4}'),
            'job_title' => $this->faker->jobTitle,
            'position' => $this->faker->randomElement(['Gerente', 'Supervisor', 'Desarrollador', 'Diseñador', 'Analista', 'Asistente']),
            'hired_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'status' => $this->faker->randomElement(['active', 'on_leave', 'fired']),
            'salary' => $this->faker->randomFloat(2, 5000, 50000),
            'shift' => $this->faker->randomElement(['9 AM - 6 PM', '8 AM - 5 PM', '7 AM - 4 PM', '2 PM - 11 PM']),
            'emergency_contact' => $this->faker->name . ' - ' . $this->faker->phoneNumber,
        ];
    }

    // Estados personalizados
    public function active()
    {
        return $this->state([
            'status' => 'active',
        ]);
    }

    public function withUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory()->create()->id,
            ];
        });
    }

    public function withManager()
    {
        return $this->state(function (array $attributes) {
            $manager = User::factory()->create();
            return [
                'manager_id' => $manager->id,
            ];
        });
    }

    public function forCompany($company)
    {
        return $this->state(function (array $attributes) use ($company) {
            $office = Office::where('company_id', $company->id)->inRandomOrder()->first() ??
                     Office::factory()->create(['company_id' => $company->id]);

            return [
                'company_id' => $company->id,
                'office_id' => $office->id,
            ];
        });
    }
}
