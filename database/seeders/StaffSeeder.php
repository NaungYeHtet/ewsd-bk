<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'username' => 'naungyehtet_am',
                'name' => 'Naung Ye Htet (Admin)',
                'email' => 'naungyehtet.admin@gmail.com',
            ],
        ];

        foreach ($admins as $admin) {
            $admin['password'] = bcrypt('password@123');
            $admin['department_id'] = \App\Models\Department::inRandomOrder()->first()->id;
            $admin = \App\Models\Staff::factory()->create($admin);
            $admin->refresh();
            $admin->assignRole('Admin');
        }

        $qaManagers = [
            [
                'username' => 'naungyehtet_qam',
                'name' => 'Naung Ye Htet (QA Manager)',
                'email' => 'naungyehtet.qam@gmail.com',
            ],
        ];

        foreach ($qaManagers as $qaManager) {
            $qaManager['password'] = bcrypt('password@123');
            $qaManager['department_id'] = \App\Models\Department::inRandomOrder()->first()->id;
            $qaManager = \App\Models\Staff::factory()->create($qaManager);
            $qaManager->refresh();
            $qaManager->assignRole('QA Manager');
        }

        $qaCoordinators = [
            [
                'username' => 'naungyehtet_qac',
                'name' => 'Naung Ye Htet (QA Coordinator)',
                'email' => 'naungyehtet717@gmail.com',
            ],
        ];

        foreach ($qaCoordinators as $qaCoordinator) {
            $qaCoordinator['password'] = bcrypt('password@123');
            $qaCoordinator['department_id'] = \App\Models\Department::inRandomOrder()->first()->id;
            $qaCoordinator = \App\Models\Staff::factory()->create($qaCoordinator);
            $qaCoordinator->refresh();
            $qaCoordinator->assignRole('QA Coordinator');
        }

        $staffs = [
            [
                'username' => 'naungyehtet',
                'name' => 'Naung Ye Htet',
                'email' => 'naungyehtet@gmail.com',
            ],
        ];

        foreach ($staffs as $staff) {
            $staff['password'] = bcrypt('password@123');
            $staff['department_id'] = \App\Models\Department::inRandomOrder()->first()->id;
            $staff = \App\Models\Staff::factory()->create($staff);
            $staff->refresh();
            $staff->assignRole(fake()->randomElement(['Support', 'Academic Staff']));
        }
    }
}
