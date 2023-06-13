<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->count(3)
            ->hasProducts(0)
            ->create([
                'role' => User::ROLES['BUYER'],
            ]);

        User::factory()
            ->count(2)
            ->hasProducts(4)
            ->create([
                'role' => User::ROLES['SELLER'],
            ]);

        /*  $this->call([
            UserSeeder::class
        ]); */

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
