<?php

namespace App\Policies;

use App\Models\Academic;
use App\Models\Comment;
use App\Models\Staff;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->can('list comment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Comment $comment): bool
    {
        return $staff->can('list comment');
    }

    /**
     * Determine whether the user can react the comment.
     */
    public function react(Staff $staff, Comment $comment): bool
    {
        return $staff->can('react comment') && Academic::isDateBetweenStartAndFinalClosureDate();
    }

    /**
     * Determine whether the user can react the idea.
     */
    public function export(Staff $staff): bool
    {
        return $staff->can('export comment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->can('create comment') && Academic::isDateBetweenStartAndFinalClosureDate();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Comment $comment): bool
    {
        return $staff->can('update comment') &&
        $comment->staff->id == $staff->id && Academic::isDateBetweenStartAndFinalClosureDate();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Comment $comment): bool
    {
        return ($staff->can('delete comment') && $comment->staff->id == $staff->id && Academic::isDateBetweenStartAndFinalClosureDate()) || $staff->hasRole('Admin');
    }
}
