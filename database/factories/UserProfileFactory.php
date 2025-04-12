<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Obtener usuarios existentes por sus emails
        $userEmails = [
            'user@larae2e.com',
            'admin@larae2e.com',
            'teknok@company.com',
            'dark@company.com',
            'moonlight@company.com'
        ];

        $user = User::whereIn('email', $userEmails)
                  ->inRandomOrder()
                  ->first();

        return [
            'user_id' => $user->id,
            'profile_picture' => $this->faker->imageUrl(200, 200, 'people'),
            'first_names' => $this->faker->firstName,
            'last_names' => $this->faker->lastName,
            'age' => $this->faker->numberBetween(18, 70),
            'birthdate' => $this->faker->date(),
            'blood_type' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'marital_status' => $this->faker->randomElement([
                'single', // soltero
                'married', // casado
                'divorced', // divorciado
                'widowed', // viudo
                'separated', // separado
                'engaged', // comprometido
                'domestic_partnership', // unión libre
            ]),
            'address' => $this->faker->address,
            'zip_code' => $this->faker->postcode,
            'ssn' => $this->faker->numerify('###-##-####'),
            'bank' => $this->faker->randomElement(['BBVA', 'Santander', 'HSBC', 'Banamex']),
            'interbank_clabe' => $this->faker->numerify('####################'),
            'notes' => $this->faker->paragraph,
        ];
    }
}
