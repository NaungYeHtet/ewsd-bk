<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Staff;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->can('list category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Category $category): bool
    {
        return $staff->can('list category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->can('create category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Staff $staff, Category $category): bool
    {
        return $staff->can('update category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Category $category): bool
    {
        return $staff->can('delete category');
    }
}
