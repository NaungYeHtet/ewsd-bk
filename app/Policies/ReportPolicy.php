<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\Staff;

class ReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Staff $staff): bool
    {
        return $staff->can('list report');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Staff $staff, Report $report): bool
    {
        return $staff->can('list report');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Staff $staff): bool
    {
        return $staff->can('create report');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Staff $staff, Report $report): bool
    {
        return $staff->can('delete report');
    }
}
