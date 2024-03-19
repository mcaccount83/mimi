<?php

namespace App\Services;

use App\Models\User;
use App\Models\CoordinatorDetails;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Config;

class RoleAssignmentService
{
    public function assignRolesBasedOnJobDescriptions()
    {
        $jobRoleMappings = Config::get('job_role_mappings');

        // Retrieve all users with their coordinator details
        $usersWithDetails = User::with('coordinatorDetails')->get();

        foreach ($usersWithDetails as $user) {
            // Assuming each user can have only one coordinator detail
            $coordinatorDetail = $user->coordinatorDetails->first();

            // Check if coordinator detail exists and if it has job descriptions
            if ($coordinatorDetail && ($roleName = $jobRoleMappings[$coordinatorDetail->position_id] ?? null)) {
                $this->assignRoleToUser($user, $roleName);
            } elseif ($coordinatorDetail && ($roleName = $jobRoleMappings[$coordinatorDetail->sec_position_id] ?? null)) {
                $this->assignRoleToUser($user, $roleName);
            }
        }
    }

    private function assignRoleToUser($user, $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->assignRole($role);
        }
    }
}

