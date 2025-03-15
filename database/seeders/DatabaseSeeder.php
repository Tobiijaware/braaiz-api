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
        $this->call([
            RoleSeeder::class,  // Ensure roles are seeded first
            AdminSeeder::class, // Then seed the admin user
            //CurrencyRateSeeder::class //add currencies to db for test purposes
        ]);
    }
}
