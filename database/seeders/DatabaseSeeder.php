<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            ClassesSeeder::class
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@bintangindokarya.co.id',
            'password' => '1234567890'
        ]);
    }
}
