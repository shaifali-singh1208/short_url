<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
   public function run(): void
{
    User::updateOrCreate(
        ['email' => 'admin2@gmail.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('12345678'),
            'role' => User::SUPER_ADMIN,
            'email_verified_at' => now(),
        ]
    );
}

}
