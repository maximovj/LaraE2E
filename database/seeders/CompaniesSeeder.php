<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Crear compañías específicas
        $companies = [
            // [
            //     'legal_name' => 'Teknok Solutions S.A. de C.V.',
            //     'tax_id' => 'TEK123456789',
            //     'commercial_name' => 'Teknok',
            //     'corporate_email_domain' => 'teknok.com',
            // ],
            // [
            //     'legal_name' => 'Moonlight Technologies S.A. de C.V.',
            //     'tax_id' => 'MLT987654321',
            //     'commercial_name' => 'Moonlight',
            //     'corporate_email_domain' => 'moonlight.com',
            // ],
            [
                'legal_name' => 'Dark Enterprises S.A. de C.V.',
                'tax_id' => 'DRK456789123',
                'commercial_name' => 'Dark',
                'corporate_email_domain' => 'dark.com',
            ]
        ];

        foreach ($companies as $companyData) {
            Company::updateOrCreate(
                ['tax_id' => $companyData['tax_id']],
                $companyData
            );
        }

        // Crear compañías adicionales usando el Factory
        Company::factory()
            ->count(7) // Crea 7 compañías aleatorias adicionales
            ->create();

        // Opcional: crear compañías con estados específicos
        Company::factory()
            ->teknok()
            ->create();

        Company::factory()
            ->moonlight()
            ->create();
    }
}
