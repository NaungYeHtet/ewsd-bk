<?php

namespace Database\Seeders;

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
            RoleSeeder::class
        ]);
        $this->command->info('Seeding Department factory...');
        \App\Models\Department::factory(10)->create();
        $this->command->info('Department factory seeded.');
        $this->command->info('Seeding Staff factory...');
        \App\Models\Staff::factory(50)->create();
        $this->command->info('Staff factory seeded.');

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
