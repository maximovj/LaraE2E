<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $legalName = $this->faker->unique()->company;

        return [
            'legal_name' => $legalName,
            'tax_id' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{6}[A-Z0-9]{3}'),
            'commercial_name' => $legalName,
            'corporate_email_domain' => $this->faker->unique()->domainName,
        ];
    }

    // Opcional: estado para datos específicos
    public function teknok()
    {
        return $this->state([
            'legal_name' => 'Teknok Solutions S.A. de C.V.',
            'tax_id' => 'TEK123456789',
            'commercial_name' => 'Teknok',
            'corporate_email_domain' => 'teknok.com',
        ]);
    }

    public function moonlight()
    {
        return $this->state([
            'legal_name' => 'Moonlight Technologies S.A. de C.V.',
            'tax_id' => 'MLT987654321',
            'commercial_name' => 'Moonlight',
            'corporate_email_domain' => 'moonlight.com',
        ]);
    }
}
