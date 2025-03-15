<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the admin role (it's created in RoleSeeder)
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            echo "Admin role not found. Run the RoleSeeder first.\n";
            return;
        }

        // Check if an admin user exists
        $admin = User::where('email', 'admin@blaaiz.com')->first();

        if (!$admin) {
            $admin = User::create([
                'firstname' => 'Blaaiz',
                'lastname' => 'Admin',
                'email' => 'admin@blaaiz.com',
                'password' => Hash::make('password123'), // Change this in production
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign admin role
            $admin->assignRole($adminRole);

            //echo "Admin user created and assigned role successfully.\n";
        } 
        // else {
        //     echo "Admin user already exists.\n";
        // }
    }
}
