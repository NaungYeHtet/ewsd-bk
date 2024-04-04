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
        $departments = \App\Models\Department::inRandomOrder()->limit(3)->get();

        $admins = [
            [
                'username' => 'naungyehtet_am',
                'name' => 'Naung Ye Htet (AM)',
                'email' => 'naungyehtet717@gmail.com',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            $admin['password'] = bcrypt('password@123');
            $admin['department_id'] = $departments->random()->id;
            $admin = \App\Models\Staff::factory()->create($admin);
            $admin->refresh();
            $admin->assignRole('Admin');
        }

        $qaManagers = [
            [
                'username' => 'aungmyatkaung_qam',
                'name' => 'Aung Myat Kaung (QAM)',
                'email' => 'amkaung1@kmd.edu.mm',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($qaManagers as $qaManager) {
            $qaManager['password'] = bcrypt('password@123');
            $qaManager['department_id'] =$departments->random()->id;
            $qaManager = \App\Models\Staff::factory()->create($qaManager);
            $qaManager->refresh();
            $qaManager->assignRole('QA Manager');
        }

        $qaCoordinators = [
            [
                'username' => 'heinhtetaung',
                'name' => 'Naung Ye Htet (QAC)',
                'email' => 'rickylin103@gmail.com',
                'email_verified_at' => now(),
            ],
            [
                'username' => 'khantkyawswar',
                'name' => 'Khant Kyaw Swar (QAC)',
                'email' => 'kkswar1@kmd.edu.mm',
                'email_verified_at' => now(),
            ],
            [
                'username' => 'aungmyatkaung_qac',
                'name' => 'Aung Myat Kaung (QAC)',
                'email' => 'amkaung.henry@gmail.com',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($qaCoordinators as $key =>  $qaCoordinator) {
            $qaCoordinator['password'] = bcrypt('password@123');
            $qaCoordinator['department_id'] = $departments[$key];
            $qaCoordinator = \App\Models\Staff::factory()->create($qaCoordinator);
            $qaCoordinator->refresh();
            $qaCoordinator->assignRole('QA Coordinator');
        }

        $staffs = [
            [
                'username' => 'naythuaung',
                'name' => 'Nay Thu Aung (SP/AS)',
                'email' => 'ntaung1@kmd.edu.mm',
                'email_verified_at' => now(),
            ],
            [
                'username' => 'thawzinhtoo',
                'name' => 'Thaw Zin Htoo (SP/AS)',
                'email' => 'thawzin99777@gmail.com',
                'email_verified_at' => now(),
            ],
            [
                'username' => 'yumonkyaw',
                'name' => 'Yu Mon Kyaw (SP/AS)',
                'email' => 'yumonkyaw921@gmail.com',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($staffs as $staff) {
            $staff['password'] = bcrypt('password@123');
            $staff['department_id'] =$departments->random()->id;
            $staff = \App\Models\Staff::factory()->create($staff);
            $staff->refresh();
            $staff->assignRole(fake()->randomElement(['Support', 'Academic Staff']));
        }
    }
}
