<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Correct: Use Spatie's Permission model
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    private string $guardName = 'web'; // Defines the guard for roles and permissions

    public function __construct()
    {
        // You can apply middleware here or at the route level.
        // Example: $this->middleware('permission:manage_roles_permissions');
        // For now, we'll assume route-level middleware as set up previously.
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('guard_name', $this->guardName) // Filter by guard
                     ->withCount('users') // Get count of users with this role
                     ->orderBy('name')
                     ->paginate(10); // Adjust pagination as needed
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all permissions for the 'web' guard to display in the form
        $permissions = Permission::where('guard_name', $this->guardName)->orderBy('name')->get();
        // Group permissions for better UI in the view
        $groupedPermissions = $this->groupPermissions($permissions);
        return view('admin.roles.create', compact('groupedPermissions'));
    }

    /**
     * Helper method to group permissions for display.
     * You might want to customize this based on your permission naming convention.
     */
    private function groupPermissions($permissions)
    {
        $grouped = [];
        foreach ($permissions as $permission) {
            // Simple grouping logic: 'view_eleves' -> 'Eleves', 'manage_users' -> 'Users'
            $parts = explode('_', $permission->name);
            $resource = ucfirst($parts[1] ?? $parts[0]); // E.g., Eleves from view_eleves or Dashboard from view_dashboard

            // More specific grouping if needed
            if (str_contains($permission->name, 'enseignant')) $resource = 'Enseignants';
            elseif (str_contains($permission->name, 'eleve')) $resource = 'Eleves';
            elseif (str_contains($permission->name, 'parent')) $resource = 'Parents';
            elseif (str_contains($permission->name, 'personnel')) $resource = 'Personnel';
            elseif (str_contains($permission->name, 'matiere')) $resource = 'Matières';
            elseif (str_contains($permission->name, 'groupe')) $resource = 'Groupes';
            elseif (str_contains($permission->name, 'evaluation')) $resource = 'Évaluations';
            elseif (str_contains($permission->name, 'note')) $resource = 'Notes';
            elseif (str_contains($permission->name, 'user')) $resource = 'Utilisateurs';
            elseif (str_contains($permission->name, 'role')) $resource = 'Rôles & Permissions';
            // Add more custom groupings if your permission names require it

            if (!isset($grouped[$resource])) {
                $grouped[$resource] = [];
            }
            $grouped[$resource][] = $permission; // Store the whole permission object
        }
        ksort($grouped); // Sort groups alphabetically
        return $grouped;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('guard_name', $this->guardName) // Name must be unique for this guard
            ],
            'permissions' => 'nullable|array', // Permissions can be optional or an empty array
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')->where('guard_name', $this->guardName)], // Validate permission IDs
        ]);

        DB::transaction(function () use ($request) {
            // Create the role
            $role = Role::create(['name' => $request->name, 'guard_name' => $this->guardName]);

            // Assign selected permissions to the role
            if ($request->has('permissions')) {
                // Fetch actual Permission objects based on IDs for the correct guard
                $permissionsToAssign = Permission::whereIn('id', $request->permissions)
                                                 ->where('guard_name', $this->guardName)
                                                 ->get();
                $role->syncPermissions($permissionsToAssign);
            }
        });

        // Clear the permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Display the specified resource.
     * Note: For roles, often the 'edit' view serves as the 'show' view.
     * If you need a separate show view, implement it. Otherwise, this can be removed if using `except(['show'])` in routes.
     */
    public function show(Role $role)
    {
        // If you use Route::resource('roles', ...)->except(['show']), this method won't be called.
        // If you want a dedicated show page, you would load the role and its permissions.
        if ($role->guard_name !== $this->guardName) {
            abort(404); // Or redirect with error
        }
        $role->load('permissions'); // Eager load permissions
        return view('admin.roles.show', compact('role')); // You would need to create admin.roles.show.blade.php
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Ensure the role belongs to the correct guard
        if ($role->guard_name !== $this->guardName) {
            abort(404, 'Rôle non trouvé pour ce guard.');
        }

        $permissions = Permission::where('guard_name', $this->guardName)->orderBy('name')->get();
        $groupedPermissions = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('id')->toArray(); // Get IDs of currently assigned permissions

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->guard_name !== $this->guardName) {
            abort(404, 'Rôle non trouvé pour ce guard.');
        }

        // Prevent critical role name changes if needed (e.g., 'Admin')
        if ($role->name === 'Admin' && $request->name !== 'Admin') {
             return back()->with('error', 'Le nom du rôle Admin ne peut pas être modifié.');
        }

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('guard_name', $this->guardName)->ignore($role->id)
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')->where('guard_name', $this->guardName)],
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->name = $request->name;
            $role->save();

            // Sync permissions. If 'permissions' is not in request or empty, all permissions will be removed.
            $permissionsToSyncIds = $request->input('permissions', []);
            $permissionsToAssign = Permission::whereIn('id', $permissionsToSyncIds)
                                             ->where('guard_name', $this->guardName)
                                             ->get();
            $role->syncPermissions($permissionsToAssign);
        });

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->guard_name !== $this->guardName) {
            abort(404);
        }

        // Prevent deleting critical roles
        if (in_array($role->name, ['Admin'])) { // Add other non-deletable roles
            return back()->with('error', "Le rôle '{$role->name}' ne peut pas être supprimé.");
        }

        // Prevent deleting if users are assigned to this role
        if ($role->users()->count() > 0) {
             return back()->with('error', "Le rôle '{$role->name}' est assigné à des utilisateurs et ne peut pas être supprimé.");
        }

        $role->delete(); // Spatie package should handle detaching permissions from the role

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle supprimé avec succès.');
    }
}