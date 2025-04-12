<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfficesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Oficinas específicas para compañías conocidas
        $offices = [
            [
                'company_name' => 'Teknok',
                'name' => 'Teknok Headquarters',
                'city' => 'Mexico City',
                'state' => 'CDMX',
                'country' => 'Mexico',
                'address' => 'Av. Insurgentes Sur 1234',
                'phone' => '+52 55 1234 5678',
                'code_office' => 'TEK-HQ-001',
                'zip_code' => '06100',
                //'status' => 'active',
                'type' => 'Sales office',
                //'coordinates' => DB::raw("POINT(-99.1332, 19.4326)"), // Coordenadas de CDMX
                'business_hours' => json_encode([
                    'monday' => ['open' => '09:00', 'close' => '19:00'],
                    'tuesday' => ['open' => '09:00', 'close' => '19:00'],
                    'wednesday' => ['open' => '09:00', 'close' => '19:00'],
                    'thursday' => ['open' => '09:00', 'close' => '19:00'],
                    'friday' => ['open' => '09:00', 'close' => '18:00'],
                    'saturday' => ['open' => '10:00', 'close' => '14:00'],
                    'sunday' => ['open' => 'closed', 'close' => 'closed']
                ])
            ],
            [
                'company_name' => 'Moonlight',
                'name' => 'Moonlight Tech Hub',
                'city' => 'Monterrey',
                'state' => 'Nuevo León',
                'country' => 'Mexico',
                'address' => 'Av. Eugenio Garza Sada 123',
                'phone' => '+52 81 2345 6789',
                'code_office' => 'MLN-TH-001',
                'zip_code' => '64849',
                //'status' => 'active',
                'type' => 'Customer service center',
                //'coordinates' => DB::raw("POINT(-100.3161, 25.6866)"), // Coordenadas de Monterrey
                'business_hours' => json_encode([
                    'monday' => ['open' => '08:30', 'close' => '18:30'],
                    'tuesday' => ['open' => '08:30', 'close' => '18:30'],
                    'wednesday' => ['open' => '08:30', 'close' => '18:30'],
                    'thursday' => ['open' => '08:30', 'close' => '18:30'],
                    'friday' => ['open' => '08:30', 'close' => '17:30'],
                    'saturday' => ['open' => 'closed', 'close' => 'closed'],
                    'sunday' => ['open' => 'closed', 'close' => 'closed']
                ])
            ]
        ];

        foreach ($offices as $officeData) {
            $company = Company::where('commercial_name', $officeData['company_name'])->first();

            if ($company) {
                unset($officeData['company_name']);
                $officeData['company_id'] = $company->id;

                Office::updateOrCreate(
                    ['code_office' => $officeData['code_office']],
                    $officeData
                );
            }
        }

        // Crear oficinas adicionales usando el Factory
        Office::factory()
            ->count(10) // 10 oficinas aleatorias
            ->create();

        // Oficinas específicas usando estados del Factory
        Office::factory()
            ->forTeknok()
            ->CorporateOffice()
            ->active()
            ->create();

        Office::factory()
            ->forMoonlight()
            ->CorporateOffice()
            ->active()
            ->create();

        // Crear sucursales adicionales para Teknok
        Office::factory()
            ->forTeknok()
            ->count(3)
            ->create([
                'type' => 'Branch office',
                'name' => 'Teknok Branch'
            ]);
    }
}
