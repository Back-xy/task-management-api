<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Product Owner with known credentials
        User::create([
            'name'     => 'Product Owner',
            'email'    => 'po@example.com',
            'password' => Hash::make('111111'),
            'role'     => 'product_owner',
        ]);
        User::factory()->count(2)->state(['role' => 'product_owner'])->create();

        // Developer with known credentials
        User::create([
            'name'     => 'Developer',
            'email'    => 'dev@example.com',
            'password' => Hash::make('111111'),
            'role'     => 'developer',
        ]);
        User::factory()->count(3)->state(['role' => 'developer'])->create();

        // Product Owner with known credentials
        User::create([
            'name'     => 'Tester',
            'email'    => 'tester@example.com',
            'password' => Hash::make('111111'),
            'role'     => 'tester',
        ]);
        User::factory()->count(2)->state(['role' => 'tester'])->create();
    }
}
