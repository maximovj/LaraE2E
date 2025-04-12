<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $userEmails = [
            'user@larae2e.com',
            'admin@larae2e.com',
            'teknok@company.com',
            'dark@company.com',
            'moonlight@company.com'
        ];

        foreach ($userEmails as $email) {
            $user = User::where('email', $email)->first();

            if ($user) {
                UserProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'profile_picture' => 'https://i.pravatar.cc/300?u='.$user->email,
                        'first_names' => explode(' ', $user->name)[0],
                        'last_names' => explode(' ', $user->name)[1] ?? 'Doe',
                        'age' => rand(25, 50),
                        'birthdate' => now()->subYears(rand(25, 50))->format('Y-m-d'),
                        'blood_type' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'][rand(0, 7)],
                        'marital_status' => [
                            'single', // soltero
                            'married', // casado
                            'divorced', // divorciado
                            'widowed', // viudo
                            'separated', // separado
                            'engaged', // comprometido
                            'domestic_partnership', // unión libre
                        ][rand(0, 3)],
                        'address' => 'Calle '.rand(1, 100).', Col. Centro',
                        'zip_code' => '1000'.rand(0, 99),
                        'ssn' => 'XXX-XX-'.rand(1000, 9999),
                        'bank' => ['BBVA', 'Santander', 'HSBC', 'Banamex'][rand(0, 3)],
                        'interbank_clabe' => 'CLABE'.rand(100000000000000000, 999999999999999999),
                        'notes' => 'Perfil creado automáticamente para '.$user->name,
                    ]
                );
            }
        }
    }
}
