<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@ecom.com');
        $password = env('ADMIN_PASSWORD', '123');
        $name = env('ADMIN_NAME', 'Store Admin');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Admin ready → {$email} / {$password}");
        $this->command->warn('Please log in and change this password immediately from the dashboard.');
    }
}