<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class NigerianUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Your specific requested users
            [
                'full_name' => 'Lawal Victor',
                'email' => 'lawalthb@gmail.com',
                'phone_number' => '+2348012345671',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Lawal Toheeb',
                'email' => 'lawalvct@gmail.com',
                'phone_number' => '+2348012345672',
                'country' => 'Nigeria',
                'role' => 'user',
            ],

            // Additional Nigerian users with popular names
            [
                'full_name' => 'Adebayo Emmanuel',
                'email' => 'adebayo.emmanuel@gmail.com',
                'phone_number' => '+2348123456789',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Chinwe Okoro',
                'email' => 'chinwe.okoro@yahoo.com',
                'phone_number' => '+2347012345678',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Ibrahim Musa',
                'email' => 'ibrahim.musa@gmail.com',
                'phone_number' => '+2348034567890',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Folake Adeyemi',
                'email' => 'folake.adeyemi@outlook.com',
                'phone_number' => '+2348045678901',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Chinedu Okafor',
                'email' => 'chinedu.okafor@gmail.com',
                'phone_number' => '+2347056789012',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Aisha Aliyu',
                'email' => 'aisha.aliyu@gmail.com',
                'phone_number' => '+2348067890123',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Olumide Oladapo',
                'email' => 'olumide.oladapo@yahoo.com',
                'phone_number' => '+2348078901234',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Kemi Adebola',
                'email' => 'kemi.adebola@gmail.com',
                'phone_number' => '+2347089012345',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Emeka Nwankwo',
                'email' => 'emeka.nwankwo@hotmail.com',
                'phone_number' => '+2348090123456',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Fatima Yusuf',
                'email' => 'fatima.yusuf@gmail.com',
                'phone_number' => '+2348101234567',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Tunde Ogundipe',
                'email' => 'tunde.ogundipe@yahoo.com',
                'phone_number' => '+2347112345678',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Blessing Ikechukwu',
                'email' => 'blessing.ikechukwu@gmail.com',
                'phone_number' => '+2348123456780',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Yusuf Abdullahi',
                'email' => 'yusuf.abdullahi@outlook.com',
                'phone_number' => '+2348134567891',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Ngozi Eze',
                'email' => 'ngozi.eze@gmail.com',
                'phone_number' => '+2347145678902',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Samuel Adamu',
                'email' => 'samuel.adamu@yahoo.com',
                'phone_number' => '+2348156789013',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Amina Hassan',
                'email' => 'amina.hassan@gmail.com',
                'phone_number' => '+2348167890124',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Segun Afolabi',
                'email' => 'segun.afolabi@hotmail.com',
                'phone_number' => '+2347178901235',
                'country' => 'Nigeria',
                'role' => 'user',
            ],
            [
                'full_name' => 'Grace Okonkwo',
                'email' => 'grace.okonkwo@gmail.com',
                'phone_number' => '+2348189012346',
                'country' => 'Nigeria',
                'role' => 'user',
            ]
        ];

        foreach ($users as $userData) {
            User::create([
                'full_name' => $userData['full_name'],
                'email' => $userData['email'],
                'phone_number' => $userData['phone_number'],
                'country' => $userData['country'],
                'role' => $userData['role'],
                'password' => Hash::make('password123'), // Default password
                'email_verified_at' => now(),
                'birthdate' => $this->generateRandomBirthdate(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('20 Nigerian users created successfully!');
    }

    /**
     * Generate random birthdate between 1980 and 2005
     */
    private function generateRandomBirthdate()
    {
        $startYear = 1980;
        $endYear = 2005;
        $randomYear = rand($startYear, $endYear);
        $randomMonth = rand(1, 12);
        $randomDay = rand(1, 28); // Safe day for all months

        return "{$randomYear}-{$randomMonth}-{$randomDay}";
    }
}
