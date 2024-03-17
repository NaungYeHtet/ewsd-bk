<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\Staff;

class DepartmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->can('list department');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Department $department): bool
    {
        return $staff->can('list department');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->can('create department');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Department $department): bool
    {
        return $staff->can('update department');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Department $department): bool
    {
        return $staff->can('delete department');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Staff $staff, Department $department): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Staff $staff, Department $department): bool
    {
        return false;
    }
}
