<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SchoolPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $guardName = 'web';

        $permissions = [
            // Dashboard
            'view_dashboard',

            // Eleves (Students)
            'view_eleves', 'create_eleves', 'edit_eleves', 'delete_eleves',

            // Enseignants (Teachers)
            'view_enseignants', 'create_enseignants', 'edit_enseignants', 'delete_enseignants',
            'assign_user_account_enseignant',
            'manage_enseignant_matieres', // For assigning matieres from enseignant's profile

            // Parents (Guardians)
            'view_parents', 'create_parents', 'edit_parents', 'delete_parents',
            'assign_user_account_parent', // Or assign_user_account_guardian

            // Personnel (Staff)
            'view_personnel', 'create_personnel', 'edit_personnel', 'delete_personnel',
            'assign_user_account_personnel',

            // Groupes (Classes/Groups)
            'view_groupes', 'create_groupes', 'edit_groupes', 'delete_groupes',

            // Matieres (Subjects)
            'view_matieres', 'create_matieres', 'edit_matieres', 'delete_matieres',

            // Evaluations
            'view_evaluations', 'create_evaluations', 'edit_evaluations', 'delete_evaluations',
            'manage_evaluations', // A more general one if needed

            // Notes (Grades)
            'view_notes', 'manage_notes',

            // Affectations (Assignments of Matieres to Groupes/Enseignants)
            'manage_affectations',         // <<< ADDED THIS (Covers create, view, edit, delete of affectations)

            // User & Role Management
            'manage_users',
            'manage_roles_permissions',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => $guardName]);
        }

        $this->command->info('School permissions seeded (or verified existing) successfully.');
    }
}