<?php

namespace App\Policies;

use App\Models\AcademicDate;
use App\Models\Idea;
use App\Models\Staff;

class IdeaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->can('list idea');
    }

    /**
     * Determine whether the user can react the idea.
     */
    public function export(Staff $staff): bool
    {
        return $staff->can('export idea');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Idea $idea): bool
    {
        return $staff->can('list idea');
    }

    /**
     * Determine whether the user can react the idea.
     */
    public function react(Staff $staff, Idea $idea): bool
    {
        return $staff->can('react idea') && AcademicDate::isDateBetweenStartAndFinalClosureDate();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->can('create idea') && AcademicDate::isDateBetweenStartAndClosureDate();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Idea $idea): bool
    {
        return $staff->can('update idea') &&
        $staff->id === $idea->staff_id &&
        AcademicDate::isDateBetweenStartAndClosureDate();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Idea $idea): bool
    {
        return ($staff->can('delete idea') &&
            $staff->id === $idea->staff_id && AcademicDate::isDateBetweenStartAndFinalClosureDate()) ||
            $staff->hasRole('Admin');
    }
}
