<?php

namespace App\Data;

use App\Models\Idea;
use App\Models\Staff;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

/** @typescript */
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
        public array|Optional $accountPermissions,
        public array|Optional $reportPermissions,
        public array|Optional $exportPermissions,
    ) {
    }

    public static function getData(Staff $staff): DataCollection|Collection|array
    {
        $exportPermissions = [];

        if ($staff->can('export academic data')) {
            $exportPermissions[] = '/export-data';
        }

        if ($staff->can('export academic files')) {
            $exportPermissions[] = '/export-files';
        }

        $reportPermissions = self::getCrudPermissions('report', $staff);

        if ($staff->can('action report')) {
            $reportPermissions[] = '/action';
        }

        $staffAccountPermissions = [];

        if ($staff->can('enable staff')) {
            $staffAccountPermissions[] = '/enable';
        }
        if ($staff->can('disable staff')) {
            $staffAccountPermissions[] = '/disable';
        }
        if ($staff->can('toggle visibility')) {
            $staffAccountPermissions[] = '/toggle-visibility';
        }
        
        return self::collect([
            [
                'title' => 'Dashboard',
                'icon' => 'layout-dashboard',
                'url' => '/',
                'permissions' => ['/'],
            ], [
                'title' => 'Idea',
                'icon' => 'mail-question',
                'url' => '/ideas',
                'permissions' => Idea::getCrudPermissions($staff),
                'reactionPermissions' => Idea::getReactionPermissions($staff),
                'commentPermissions' => Idea::getCommentPermissions($staff),
                'reportPermissions' => $staff->can('create report') ? ['/create'] : [],
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
                'title' => 'Academic',
                'icon' => 'graduation-cap',
                'url' => '/academics',
                'permissions' => self::getCrudPermissions('academic', $staff),
                'exportPermissions' => $exportPermissions,
            ], [
                'title' => 'Staff',
                'icon' => 'users',
                'url' => '/staffs',
                'permissions' => self::getCrudPermissions('staff', $staff),
                'accountPermissions' => $staffAccountPermissions,
            ], [
                'title' => 'Reports',
                'icon' => 'circle-alert',
                'url' => '/reports',
                'permissions' => $reportPermissions,
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
