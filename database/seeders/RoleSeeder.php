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
            'react idea',
            'list comment',
            'create comment',
            'update comment',
            'delete comment',
            'create report',
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
                    'list report',
                    'delete report',
                    'list department',
                    'create department',
                    'update department',
                    'delete department',
                    'list academic date',
                    'create academic date',
                    'update academic date',
                    'delete academic date',
                    'list password rule',
                    'update password rule',
                ],
            ],
            [
                'name' => 'QA Manager',
                'permissions' => [
                    'list category',
                    'create category',
                    'update category',
                    'delete category',
                    'list department',
                    'list academic date',
                    'export idea',
                    'export staff',
                    'export category',
                    'export department',
                    'export academic date',
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
