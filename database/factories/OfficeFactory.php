<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Office>
 */
class OfficeFactory extends Factory
{
    protected $model = Office::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Obtener una compañía existente o crear una nueva
        $company = Company::inRandomOrder()->first() ?? Company::factory()->create();

        return [
            'company_id' => $company->id,
            'name' => $this->faker->unique()->company.' Office',
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->country,
            'address' => $this->faker->streetAddress,
            'phone' => $this->faker->phoneNumber,
            'code_office' => $this->faker->unique()->bothify('OFF-###-??'),
            'zip_code' => $this->faker->postcode,
            'status' => $this->faker->randomElement([
                'active', // activo
                'inactive', // inactivo
                'closed', // cerrado
                'open',  // abierto
                'available', // disponible
                'unavailable' // no disponible
            ]),
            'type' => $this->faker->randomElement([
                'Corporate office', //  Corporativo
                'Branch office', // Sucursal
                'Warehouse', // Almacén
                'Head office', // Oficina principal / Matriz / Main office
                'Regional office', // Oficina regional
                'Distribution center', // Centro de distribución
                'Administrative office', // Oficina administrativa
                'Remote office', // Oficina remota
                'Sales office', // Oficina de ventas
                'Satellite office', // Oficina satélite
                'Customer service center' // Centro de atención al cliente
            ]),
            'coordinates' => [
                'lat' => $this->faker->latitude(-90.0, 90.0),
                'lng' => $this->faker->longitude(-90.0, 90.0),
            ],
            'business_hours' => [
                'monday' => ['open' => '08:00', 'close' => '18:00'],
                'tuesday' => ['open' => '08:00', 'close' => '18:00'],
                'wednesday' => ['open' => '08:00', 'close' => '18:00'],
                'thursday' => ['open' => '08:00', 'close' => '18:00'],
                'friday' => ['open' => '08:00', 'close' => '17:00'],
                'saturday' => ['open' => '09:00', 'close' => '14:00'],
                'sunday' => ['open' => 'closed', 'close' => 'closed']
            ],
        ];
    }

    // Estados personalizados
    public function CorporateOffice()
    {
        return $this->state([
            'type' => 'Corporate office',
            'name' => 'Corporate office',
        ]);
    }

    public function active()
    {
        return $this->state([
            'status' => 'active',
        ]);
    }

    public function forTeknok()
    {
        return $this->state(function (array $attributes) {
            $teknok = Company::where('commercial_name', 'Teknok')->first();
            return [
                'company_id' => $teknok ? $teknok->id : Company::factory()->teknok()->create()->id,
                'name' => 'Teknok Main Office'
            ];
        });
    }

    public function forMoonlight()
    {
        return $this->state(function (array $attributes) {
            $moonlight = Company::where('commercial_name', 'Moonlight')->first();
            return [
                'company_id' => $moonlight ? $moonlight->id : Company::factory()->moonlight()->create()->id,
                'name' => 'Moonlight Tech Hub'
            ];
        });
    }

}
