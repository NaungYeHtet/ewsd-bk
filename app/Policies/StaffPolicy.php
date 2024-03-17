<?php

namespace App\Policies;

use App\Models\Staff;

class StaffPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $user): bool
    {
        return $user->can('list staff');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $user, Staff $staff): bool
    {
        return $user->can('list staff');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $user): bool
    {
        return $user->can('create staff');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $user, Staff $staff): bool
    {
        return $user->can('update staff');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $user, Staff $staff): bool
    {
        return $user->can('delete staff');
    }
}
