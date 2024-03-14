<?php

namespace App\Data;

use App\Models\Idea;
use App\Models\Staff;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class SidebarData extends Data
{
    public function __construct(
        public string $title,
        public ?string $key,
        public string $icon,
        public string $url,
        public array $permissions,
        public array|Optional $reactionPermissions,
        public array|Optional $commentPermissions,
    ) {
    }

    public static function getData(Staff $staff): DataCollection|array
    {
        return self::collect([
            [
                'title' => 'Dashboard',
                'icon' => 'LayoutDashboard',
                'url' => '/',
                'permissions' => ['/'],
            ], [
                'title' => 'Idea',
                'icon' => 'MailQuestion',
                'url' => '/staffs',
                'permissions' => Idea::getCrudPermissions($staff),
                'reactionPermissions' => Idea::getReactionPermissions($staff),
                'commentPermissions' => Idea::getCommentPermissions($staff),
            ], [
                'title' => 'Category',
                'icon' => 'BarChartHorizontal',
                'url' => '/categories',
                'permissions' => self::getCrudPermissions('category', $staff),
            ], [
                'title' => 'Department',
                'icon' => 'Building2',
                'url' => '/departments',
                'permissions' => self::getCrudPermissions('department', $staff),
            ], [
                'title' => 'Staff',
                'icon' => 'Users',
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
