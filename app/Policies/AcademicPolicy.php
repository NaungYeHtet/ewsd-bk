<?php

namespace App\Policies;

use App\Models\Academic;
use App\Models\Staff;
use Illuminate\Auth\Access\Response;

class AcademicPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->can('list academic');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Academic $academic): bool
    {
        return $staff->can('list academic');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->can('create academic');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Academic $academic): bool
    {
        return $staff->can('update academic');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Academic $academic): bool
    {
        return $staff->can('delete academic');
    }
}
