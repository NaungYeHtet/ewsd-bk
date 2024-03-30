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
                'name' => 'Naung Ye Htet (AM)',
                'email' => 'naungyehtet717@gmail.com',
            ],
        ];

        foreach ($admins as $admin) {
            $admin['password'] = bcrypt('password@123');
            $admin['department_id'] = \App\Models\Department::inRandomOrder()->first()->id;
            $admin = \App\Models\Staff::factory()->create([
                ...$admin,
                'email_verified_at' => now()
            ]);
            $admin->refresh();
            $admin->assignRole('Admin');
        }

        $qaManagers = [
            [
                'username' => 'aungmyatkaung_qam',
                'name' => 'Aung Myat Kaung (QAM)',
                'email' => 'amkaung1@kmd.edu.mm',
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
                'username' => 'heinhtetaung',
                'name' => 'Naung Ye Htet (QAC)',
                'email' => 'rickylin103@gmail.com',
            ],
            [
                'username' => 'khantkyawswar',
                'name' => 'Khant Kyaw Swar (QAC)',
                'email' => 'kkswar1@kmd.edu.mm',
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
                'username' => 'naythuaung',
                'name' => 'Nay Thu Aung (SP/AS)',
                'email' => 'ntaung1@kmd.edu.mm',
            ],
            [
                'username' => 'thawzinhtoo',
                'name' => 'Thaw Zin Htoo (SP/AS)',
                'email' => 'thawzin99777@gmail.com',
            ],
            [
                'username' => 'yumonkyaw',
                'name' => 'Yu Mon Kyaw (SP/AS)',
                'email' => 'yumonkyaw921@gmail.com',
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
