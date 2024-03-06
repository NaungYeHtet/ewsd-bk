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
            RoleSeeder::class,
        ]);
        $this->command->info('Seeding Department factory...');
        \App\Models\Department::factory(10)->create();
        $this->command->info('Department factory seeded.');

        $this->call([
            StaffSeeder::class,
        ]);

        $this->command->info('Seeding Staff factory...');
        \App\Models\Staff::factory(50)->create();
        $this->command->info('Staff factory seeded.');

        $this->command->info('Seeding Category factory...');
        \App\Models\Category::factory(30)->create();
        $this->command->info('Category factory seeded.');

        $this->command->info('Seeding Idea factory...');
        \App\Models\Idea::factory(rand(100, 150))->create();
        $this->command->info('Idea factory seeded.');

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
