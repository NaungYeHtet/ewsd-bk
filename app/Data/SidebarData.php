<?php

namespace App\Data;

use App\Models\Staff;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class SidebarData extends Data
{
    public function __construct(
        public string $title,
        public ?string $key,
        public string $icon,
        public string $url,
        public array $permissions,
    ) {
    }

    public static function getData(Staff $staff): DataCollection|array
    {
        return self::collect([
            [
                'title' => 'Dashboard',
                'icon' => 'layout-dashboard',
                'url' => '/',
                'permissions' => ['/'],
            ], [
                'title' => 'Idea',
                'icon' => 'mail-question',
                'url' => '/staffs',
                'permissions' => [
                    '/',
                    '/create',
                ],
            ], [
                'title' => 'Category',
                'icon' => 'bar-chart-horizontal',
                'url' => '/categories',
                'permissions' => self::getCrudPermissions('category', $staff),
            ], [
                'title' => 'Department',
                'icon' => 'building-2',
                'url' => '/departments',
                'permissions' => self::getCrudPermissions('department', $staff),
            ], [
                'title' => 'Staff',
                'icon' => 'users',
                'url' => '/staffs',
                'permissions' => self::getCrudPermissions('staff', $staff),
            ],
        ], DataCollection::class)->except('key');
    }

    public static function getCrudPermissions(string $key, Staff $staff): array
    {
        $permissions = [];

        $staff->can('list '.$key) && array_push($permissions, '/');
        $staff->can('create '.$key) && array_push($permissions, '/create');
        $staff->can('update '.$key) && array_push($permissions, '/update');
        $staff->can('delete '.$key) && array_push($permissions, '/delete');

        return $permissions;
    }
}
