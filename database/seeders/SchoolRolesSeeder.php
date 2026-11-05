<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SchoolRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $guardName = 'web';

        // Define Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guardName]);
        $enseignantRole = Role::firstOrCreate(['name' => 'Enseignant', 'guard_name' => $guardName]);
        $parentRole = Role::firstOrCreate(['name' => 'Parent', 'guard_name' => $guardName]); // For Guardians/Parents
        $personnelRole = Role::firstOrCreate(['name' => 'Personnel', 'guard_name' => $guardName]); // For Admin Staff

        // --- Assign Permissions ---

        // Admin gets all permissions
        $allPermissions = Permission::where('guard_name', $guardName)->pluck('name');
        $adminRole->syncPermissions($allPermissions);
        $this->command->info('Admin role synced with all permissions.');

        // Enseignant Permissions
        $enseignantRole->syncPermissions([
            'view_dashboard',
            'view_eleves',        // Can view students, perhaps filtered to their classes
            'view_groupes',       // Can view their assigned groups/classes
            'view_matieres',      // Can view subjects
            'view_evaluations', 'create_evaluations', 'edit_evaluations', // Manage their evaluations
            'view_notes', 'manage_notes', // Manage grades for their evaluations
            // Potentially: 'view_enseignants' (to see their own profile)
        ]);
        $this->command->info('Enseignant role permissions synced.');

        // Parent Permissions
        $parentRole->syncPermissions([
            'view_dashboard',     // Parent-specific dashboard (child's progress)
            // 'view_own_child_info', // More granular: permission to view their own child's full profile
            'view_notes',           // View their child's grades
            'view_evaluations',     // View evaluations relevant to their child
        ]);
        $this->command->info('Parent role permissions synced.');

        // Personnel Permissions
        $personnelRole->syncPermissions([
            'view_dashboard',
            'view_eleves', 'create_eleves', 'edit_eleves', // Manage student records
            'view_parents', 'create_parents', 'edit_parents', // Manage parent records
            'view_enseignants', 'create_enseignants', 'edit_enseignants', // Manage teacher records
            'view_personnel', 'create_personnel', 'edit_personnel', // Manage other staff records
            'view_groupes', 'create_groupes', 'edit_groupes', // Manage classes
            'view_matieres', 'create_matieres', 'edit_matieres', // Manage subjects
            // But NOT 'manage_users' or 'manage_roles_permissions' unless they are also an Admin
            'assign_user_account_enseignant',
            'assign_user_account_parent',
            'assign_user_account_personnel',
            'manage_enseignant_matieres',
        ]);
        $this->command->info('Personnel role permissions synced.');
    }
}