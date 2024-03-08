<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $globalPermissions = [
            'list idea',
            'create idea',
            'update idea',
            'delete idea',
        ];

        $roles = [
            [
                'name' => 'Admin',
                'permissions' => [
                    'list staff',
                    'create staff',
                    'update staff',
                    'delete staff',
                    'view staff',
                    'list department',
                    'create department',
                    'update department',
                    'delete department',
                    'update academic start date',
                    'update closure date',
                    'update final closure date',
                ],
            ],
            [
                'name' => 'QA Manager',
                'permissions' => [
                    'list category',
                    'create category',
                    'update category',
                    'delete category',
                    'download idea',
                ],
            ],
            [
                'name' => 'QA Coordinator',
                'permissions' => [],
            ],
            [
                'name' => 'Academic Staff',
                'permissions' => [],
            ],
            [
                'name' => 'Support',
                'permissions' => [],
            ]
        ];

        foreach ($roles as $role) {
            $allPermissions = array_merge($globalPermissions, $role['permissions']);

            foreach ($allPermissions as $permission) {
                Permission::firstOrCreate(
                    [
                        'name' => $permission,
                        'guard_name' => 'staff',
                    ],
                    [
                        'name' => $permission,
                        'guard_name' => 'staff',
                    ]
                );
            }

            $createdRole = Role::firstOrCreate(
                [
                    'name' => $role['name'],
                ],
                [
                    'name' => $role['name'],
                ]
            );

            $createdRole->syncPermissions($allPermissions);
        }
    }
}
