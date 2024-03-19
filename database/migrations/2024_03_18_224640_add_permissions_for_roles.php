<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionsForRoles extends Migration
{
    public function up()
    {
        // Retrieve roles
        $roleSuperAdmin = Role::where('name', 'SuperAdmin')->first();
        $roleCC = Role::where('name', 'ConfCord')->first();
        $roleACC = Role::where('name', 'AsstConfCord')->first();
        $roleRC = Role::where('name', 'RegCord')->first();
        $roleARC = Role::where('name', 'AsstRegCord')->first();
        $roleSC = Role::where('name', 'StateCord')->first();
        $roleAC = Role::where('name', 'AreaCord')->first();
        $roleBS = Role::where('name', 'BigSis')->first();
        $roleIC = Role::where('name', 'InqCord')->first();
        $roleLIST = Role::where('name', 'ListAdmin')->first();
        $roleMentor = Role::where('name', 'MentorCord')->first();
        $roleEIN = Role::where('name', 'EINCord')->first();
        $roleWR = Role::where('name', 'WebReviewer')->first();
        // Retrieve roles as needed

        // Define permissions
        $permissionAddCord = Permission::create(['name' => 'add-coordinator']);
        $permissionEditCord = Permission::create(['name' => 'edit-coordinator']);
        $permissionRetCord = Permission::create(['name' => 'retire-coordinator']);
        $permissionViewCord = Permission::create(['name' => 'view-coordinator']);
        $permissionAddChap = Permission::create(['name' => 'add-chapter']);
        $permissionEditChap = Permission::create(['name' => 'edit-chapter']);
        $permissionZapChap = Permission::create(['name' => 'zap-chapter']);
        $permissionViewChap = Permission::create(['name' => 'view-chapter']);
        $permissionViewInt = Permission::create(['name' => 'view-international']);
        $permissionViewInq = Permission::create(['name' => 'view-inquiries']);
        $permissionEditWeb = Permission::create(['name' => 'edit-webstatus']);
        $permissionViewWeb = Permission::create(['name' => 'view-webstatus']);
        $permissionEditEIN = Permission::create(['name' => 'edit-EIN-number']);
        // Define more permissions as needed

        // Assign permissions to SuperAdmin for ALL
        $permissions = Permission::pluck('id')->all();
        $roleSuperAdmin->syncPermissions($permissions);

        // Assign multiple permissions to ConfCord role using array
        $permissionsCC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries', 'edit-EIN-number'];
        foreach ($permissionsCC as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleCC->givePermissionTo($permission);
            }
        }

        $permissionsACC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries', 'edit-EIN-number'];
        foreach ($permissionsACC as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleACC->givePermissionTo($permission);
            }
        }

        $permissionsRC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries', 'edit-EIN-number'];
        foreach ($permissionsRC as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleRC->givePermissionTo($permission);
            }
        }

        $permissionsARC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries'];
        foreach ($permissionsARC as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleARC->givePermissionTo($permission);
            }
        }

        $permissionsSC = [ 'edit-coordinator', 'edit-chapter', 'view-webstatus'];
        foreach ($permissionsSC as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleSC->givePermissionTo($permission);
            }
        }

        $permissionsAC = ['view-chapter'];
        foreach ($permissionsAC as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleAC->givePermissionTo($permission);
            }
        }

        $permissionsBS = ['view-chapter'];
        foreach ($permissionsBS as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleBS->givePermissionTo($permission);
            }
        }

        $permissionsMentor = [ 'view-coordinator'];
        foreach ($permissionsMentor as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleMentor->givePermissionTo($permission);
            }
        }

        $permissionsEIN = [ 'view-international', 'edit-EIN-number'];
        foreach ($permissionsEIN as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleEIN->givePermissionTo($permission);
            }
        }

        $permissionsIC = [ 'view-inquiries'];
        foreach ($permissionsIC as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleIC->givePermissionTo($permission);
            }
        }

        $permissionsWR = [ 'edit-webstatus'];
        foreach ($permissionsWR as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleWR->givePermissionTo($permission);
            }
        }
        // Assign permissions to other roles as needed

    }

    public function down()
    {
        // Define rollback logic if needed
    }
}
