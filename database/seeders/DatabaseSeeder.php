<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            "full_name" => "TaloSmart Admin",
            "country" => "Nigeria",
            "phone_number" => "+2347098561293",
            "email" => "admin@talosmart.com",
            "role" => "admin",
            "password" => "Talosmart@1234",
            "password_confirmation" => "Talosmart@1234"
        ]);
    }
}
