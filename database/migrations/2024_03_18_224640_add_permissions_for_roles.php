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
        // $permissionAddCord = Permission::create(['name' => 'add-coordinator']);
        // $permissionEditCord = Permission::create(['name' => 'edit-coordinator']);
        // $permissionRetCord = Permission::create(['name' => 'retire-coordinator']);
        // $permissionViewCord = Permission::create(['name' => 'view-coordinator']);
        // $permissionAddChap = Permission::create(['name' => 'add-chapter']);
        // $permissionEditChap = Permission::create(['name' => 'edit-chapter']);
        // $permissionZapChap = Permission::create(['name' => 'zap-chapter']);
        // $permissionViewChap = Permission::create(['name' => 'view-chapter']);
        // $permissionViewInt = Permission::create(['name' => 'view-international']);
        // $permissionViewInq = Permission::create(['name' => 'view-inquiries']);
        // $permissionEditWeb = Permission::create(['name' => 'edit-webstatus']);
        // $permissionViewWeb = Permission::create(['name' => 'view-webstatus']);
        // $permissionEditEIN = Permission::create(['name' => 'edit-EIN-number']);
        // Define more permissions as needed

        //BoardList permissions
        // $permissionCreateCategories = Permission::create(['name' => 'createCategories']);
        // $permissionMoveCategories = Permission::create(['name' => 'moveCategories']);
        // $permissionManageCategories = Permission::create(['name' => 'manageCategories']);
        // $permissionCreateThreads = Permission::create(['name' => 'createThreads']);
        // $permissionMoveThreads = Permission::create(['name' => 'moveThreads']);
        // $permissionManageThreads = Permission::create(['name' => 'manageThreads']);
        // $permissionDeleteThreads = Permission::create(['name' => 'deleteThreads']);
        // $permissionRestoreThreads = Permission::create(['name' => 'restoreThreads']);
        // $permissionLockThreads = Permission::create(['name' => 'lockThreads']);
        // $permissionPinThreads = Permission::create(['name' => 'pinThreads']);
        // $permissionMarkThreadsAsRead = Permission::create(['name' => 'markThreadsAsRead']);
        // $permissionMoveThreadsFrom = Permission::create(['name' => 'moveThreadsFrom']);
        // $permissionDeletePosts = Permission::create(['name' => 'deletePosts']);
        // $permissionRestorePosts = Permission::create(['name' => 'restorePosts']);
        // $permissionViewTrashed = Permission::create(['name' => 'viewTrashed']);
        // $permissionDelete = Permission::create(['name' => 'delete']);
        // $permissionEdit = Permission::create(['name' => 'edit']);
        // $permissionReply = Permission::create(['name' => 'reply']);
        // $permissionRestore = Permission::create(['name' => 'restore']);
        // $permissionRrename = Permission::create(['name' => 'rename']);

        // Assign permissions to SuperAdmin
        $permissions = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries', 'edit-EIN-number', 'view-international',
                'createCategories', 'moveCategories', 'manageCategories', 'createThreads', 'moveThreads', 'manageThreads', 'deleteThreads', 'restoreThreads', 'lockThreads',
                'pinThreads', 'markThreadsAsRead', 'moveThreadsFrom', 'deletePosts', 'restorePosts', 'viewTrashed', 'delete', 'edit', 'reply', 'restore', 'rename'];
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleSuperAdmin->givePermissionTo($permission);
            }
        }

        // Assign permissions to BoardList Admin
        $permissionsLIST = ['createCategories', 'moveCategories', 'manageCategories', 'createThreads', 'moveThreads', 'manageThreads', 'deleteThreads', 'restoreThreads', 'lockThreads',
                'pinThreads', 'markThreadsAsRead', 'moveThreadsFrom', 'deletePosts', 'restorePosts', 'viewTrashed', 'delete', 'edit', 'reply', 'restore', 'rename'];
        foreach ($permissionsLIST as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $roleLIST->givePermissionTo($permission);
            }
        }

        // Assign multiple permissions to ConfCord role using array
        $permissionsCC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries', 'edit-EIN-number'];
        foreach ($permissionsCC as $permissionNameCC) {
            $permissionCC = Permission::where('name', $permissionNameCC)->first();
            if ($permissionCC) {
                $roleCC->givePermissionTo($permissionCC);
            }
        }

        $permissionsACC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries', 'edit-EIN-number'];
        foreach ($permissionsACC as $permissionNameACC) {
            $permissionACC = Permission::where('name', $permissionNameACC)->first();
            if ($permissionACC) {
                $roleACC->givePermissionTo($permissionACC);
            }
        }

        $permissionsRC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries', 'edit-EIN-number'];
        foreach ($permissionsRC as $permissionNameRC) {
            $permissionRC = Permission::where('name', $permissionNameRC)->first();
            if ($permissionRC) {
                $roleRC->givePermissionTo($permissionRC);
            }
        }

        $permissionsARC = ['add-coordinator', 'edit-coordinator', 'retire-coordinator', 'add-chapter', 'edit-chapter', 'zap-chapter',
                'edit-webstatus', 'view-inquiries'];
        foreach ($permissionsARC as $permissionNameARC) {
            $permissionARC = Permission::where('name', $permissionNameARC)->first();
            if ($permissionARC) {
                $roleARC->givePermissionTo($permissionARC);
            }
        }

        $permissionsSC = [ 'edit-coordinator', 'edit-chapter', 'view-webstatus'];
        foreach ($permissionsSC as $permissionNameSC) {
            $permissionSC = Permission::where('name', $permissionNameSC)->first();
            if ($permissionSC) {
                $roleSC->givePermissionTo($permissionSC);
            }
        }

        $permissionsAC = ['view-chapter'];
        foreach ($permissionsAC as $permissionNameAC) {
            $permissionAC = Permission::where('name', $permissionNameAC)->first();
            if ($permissionAC) {
                $roleAC->givePermissionTo($permissionAC);
            }
        }

        $permissionsBS = ['view-chapter'];
        foreach ($permissionsBS as $permissionNameBS) {
            $permissionBS = Permission::where('name', $permissionNameBS)->first();
            if ($permissionBS) {
                $roleBS->givePermissionTo($permissionBS);
            }
        }

        $permissionsMentor = [ 'view-coordinator'];
        foreach ($permissionsMentor as $permissionNameMentor) {
            $permissionMentor = Permission::where('name', $permissionNameMentor)->first();
            if ($permissionMentor) {
                $roleMentor->givePermissionTo($permissionMentor);
            }
        }

        $permissionsEIN = [ 'view-international', 'edit-EIN-number'];
        foreach ($permissionsEIN as $permissionNameEIN) {
            $permissionEIN = Permission::where('name', $permissionNameEIN)->first();
            if ($permissionEIN) {
                $roleEIN->givePermissionTo($permissionEIN);
            }
        }

        $permissionsIC = [ 'view-inquiries'];
        foreach ($permissionsIC as $permissionNameIC) {
            $permissionIC = Permission::where('name', $permissionNameIC)->first();
            if ($permissionIC) {
                $roleIC->givePermissionTo($permissionIC);
            }
        }

        $permissionsWR = [ 'edit-webstatus'];
        foreach ($permissionsWR as $permissionNameWR) {
            $permissionWR = Permission::where('name', $permissionNameWR)->first();
            if ($permissionWR) {
                $roleWR->givePermissionTo($permissionWR);
            }
        }
        // Assign permissions to other roles as needed

    }

    public function down()
    {
        // Define rollback logic if needed
    }
}
