<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkActivity;
use Illuminate\Auth\Access\Response;

class WorkActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $this->isRegularEmployeeWithPermission($user, 'work_activities.read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkActivity $workActivity): bool
    {
        //
        return $this->isRegularEmployeeWithPermission($user, 'work_activities.read') &&
        $this->isOwnActivity($user, $workActivity);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $this->isRegularEmployeeWithPermission($user, 'work_activities.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkActivity $workActivity): bool
    {
        //
        return $this->isRegularEmployeeWithPermission($user, 'work_activities.update') &&
        $this->isOwnActivity($user, $workActivity);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkActivity $workActivity): bool
    {
        //
        return $this->isRegularEmployeeWithPermission($user, 'work_activities.delete') &&
        $this->isOwnActivity($user, $workActivity);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkActivity $workActivity): bool
    {
        //
        return $this->isRegularEmployeeWithPermission($user, 'work_activities.restore') &&
        $this->isOwnActivity($user, $workActivity);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkActivity $workActivity): bool
    {
        //
        return $this->isRegularEmployeeWithPermission($user, 'work_activities.destroy') &&
        $this->isOwnActivity($user, $workActivity);
    }

    private function isRegularEmployeeWithPermission(User $user, string $permission): bool
    {
        if(!isset($user->employee) || empty($permission)) {
            return false;
        }

        return $user->employee
            && $user->hasRole('regular-user')
            && $user->can($permission);
    }

    private function isOwnActivity(User $user, WorkActivity $workActivity): bool
    {
        return $user->employee?->id === $workActivity->employee_id;
    }

}
