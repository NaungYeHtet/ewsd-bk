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
            'react comment',
            'create report',
        ];

        $roles = [
            [
                'name' => 'Admin',
                'permissions' => [
                    'list statistics',
                    'list staff',
                    'create staff',
                    'update staff',
                    'delete staff',
                    'view staff',
                    'list report',
                    'delete report',
                    'action report',
                    'list department',
                    'create department',
                    'update department',
                    'delete department',
                    'list academic',
                    'create academic',
                    'update academic',
                    'delete academic',
                    'list password rule',
                    'update password rule',
                ],
            ],
            [
                'name' => 'QA Manager',
                'permissions' => [
                    'list statistics',
                    'list category',
                    'create category',
                    'update category',
                    'delete category',
                    'list staff',
                    'enable staff',
                    'disable staff',
                    'toggle visibility',
                    'list department',
                    'list academic',
                    'create academic',
                    'update academic',
                    'delete academic',
                    'export academic data',
                    'export academic files',
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
            ],
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
