<?php

namespace App\Policies;

use App\Models\AcademicDate;
use App\Models\Staff;
use Illuminate\Auth\Access\Response;

class AcademicDatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->can('list academic date');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, AcademicDate $academicDate): bool
    {
        return $staff->can('list academic date');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->can('create academic date');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, AcademicDate $academicDate): bool
    {
        return $staff->can('update academic date');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, AcademicDate $academicDate): bool
    {
        return $staff->can('delete academic date');
    }
}
