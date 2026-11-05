<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // <--- CORRECT IMPORT FOR SPATIE'S ROLE
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules as PasswordRules; // For Laravel 9+ password rules
use Illuminate\Support\Str;

// Assuming these are your profile models
use App\Models\Enseignant; // Was Formateur
use App\Models\Guardian;   // Assuming for 'parent' type
use App\Models\PersonnelDirection; // Assuming for 'personnel' type
// use App\Models\Stagiaire; // If Stagiaires don't have accounts, this might not be needed for user creation

class UserController extends Controller
{
    private string $guardName = 'web';

    public function __construct()
    {
        // Apply middleware to protect user management routes
        $this->middleware('permission:manage_users')->except(['getProfilesByType', 'generateUserEmail']);
        // Allow access to helpers if they are used by other forms that non-admins might access
        // or protect them with more specific permissions if needed.
    }

    /**
     * Display a listing of the resource.
     * This is for the general user management page.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('search_filter')) {
            $search = $request->input('search_filter');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        // Optional: Filter by role if you add a role filter dropdown
        if ($request->filled('role_filter')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->input('role_filter'));
            });
        }

        $users = $query->paginate(15); // Or your preferred pagination
        $allRolesForFilter = Role::where('guard_name', $this->guardName)->orderBy('name')->pluck('name', 'name');

        return view('admin.users.index', compact('users', 'allRolesForFilter'));
    }

    /**
     * Show the form for creating a new general user resource.
     */
    public function create(Request $request)
    {
        // This method is now for creating a general user (Admin, Parent, Personnel directly)
        // The logic for creating users for specific profiles (Enseignant, etc.) can be separate
        // or handled by `createAccountForProfileForm` if you want a unified entry point.

        $allRoles = Role::where('guard_name', $this->guardName)
                        ->orderBy('name')
                        ->pluck('name', 'name'); // Use role name as both key and value

        $pageSpecificTitle = 'Créer un Nouvel Utilisateur';

        // For the specific profile linking functionality (your existing logic)
        $type = $request->query('type_profile_link'); // Renamed query param to avoid confusion
        $profilesToLink = collect();
        $profileLinkTitle = '';

        if ($type) {
            if ($type === 'enseignant') { // Was 'formateur'
                $profilesToLink = Enseignant::whereNull('user_id')->orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom']);
                $profileLinkTitle = 'Lier à un Enseignant existant';
            }
            // Add similar blocks for 'parent', 'personnel' if you have a flow to link them during generic user creation
            // For 'stagiaire': If they don't have accounts, this type for user creation is problematic.
            // elseif ($type === 'stagiaire') {
            //     $profilesToLink = Stagiaire::whereNull('user_id')->orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom', 'numeroCIN']);
            //     $profileLinkTitle = 'Lier à un Stagiaire existant';
            // }
        }

        return view('admin.users.create', compact('allRoles', 'pageSpecificTitle', 'type', 'profilesToLink', 'profileLinkTitle'));
    }


    /**
     * Store a newly created general user resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRules\Password::defaults()],
            'user_roles' => 'required|array', // Roles are now required for a general user
            'user_roles.*' => ['string', Rule::exists('roles', 'name')->where('guard_name', $this->guardName)],
            'profile_type_to_link' => ['nullable', 'string', Rule::in(['enseignant', 'parent', 'personnel'])], // Removed 'stagiaire'
            'profile_id_to_link' => ['nullable', 'integer', Rule::requiredIf(function () use ($request) {
                return !empty($request->input('profile_type_to_link'));
            })],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign selected roles (multi-select)
        $user->syncRoles($request->input('user_roles'));

        // Link to profile if provided (this part adapts your existing logic)
        $profileType = $request->input('profile_type_to_link');
        $profileId = $request->input('profile_id_to_link');
        $profileModel = null;

        if ($profileType && $profileId) {
            if ($profileType === 'enseignant') {
                $profileModel = Enseignant::find($profileId);
            } elseif ($profileType === 'parent') {
                $profileModel = Guardian::find($profileId); // Assuming Guardian model for parents
            } elseif ($profileType === 'personnel') {
                $profileModel = PersonnelDirection::find($profileId);
            }
            // Removed 'stagiaire' linking

            if ($profileModel) {
                if ($profileModel->user_id) {
                    // Handle error: this profile is already linked
                    // This scenario should ideally be prevented by only showing unlinked profiles in the dropdown
                    return back()->with('error', 'Ce profil est déjà lié à un autre utilisateur.')->withInput();
                }
                $profileModel->user_id = $user->id;
                $profileModel->save();
            } else {
                // Handle error: profile not found
                return back()->with('error', 'Profil à lier non trouvé.')->withInput();
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Show the form for editing the specified user resource.
     */
    public function edit(User $user) // Using route model binding for User
    {
        $allRoles = Role::where('guard_name', $this->guardName)
                        ->orderBy('name')
                        ->pluck('name', 'name');
        $userAssignedRoles = $user->getRoleNames()->toArray();

        $pageSpecificTitle = 'Modifier l\'Utilisateur: ' . $user->name;

        // Information about linked profile (optional display)
        $linkedProfileInfo = null;
        if ($user->enseignant) {
            $linkedProfileInfo = ['type' => 'Enseignant', 'name' => $user->enseignant->nom . ' ' . $user->enseignant->prenom];
        } elseif ($user->parentGuardian) {
            $linkedProfileInfo = ['type' => 'Parent', 'name' => $user->parentGuardian->nom . ' ' . $user->parentGuardian->prenom];
        } elseif ($user->personnel) {
            $linkedProfileInfo = ['type' => 'Personnel', 'name' => $user->personnel->nom . ' ' . $user->personnel->prenom];
        }

        return view('admin.users.edit', compact('user', 'allRoles', 'userAssignedRoles', 'pageSpecificTitle', 'linkedProfileInfo'));
    }

    /**
     * Update the specified user resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', PasswordRules\Password::defaults()],
            'user_roles' => 'required|array', // Roles are required
            'user_roles.*' => ['string', Rule::exists('roles', 'name')->where('guard_name', $this->guardName)],
        ]);

        $userData = $request->only(['name', 'email']);
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $user->update($userData);

        $rolesToSync = $request->input('user_roles', []);
        $user->syncRoles($rolesToSync);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    /**
     * Remove the specified user resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the last admin or a specific super admin
        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return back()->with('error', 'Impossible de supprimer le dernier administrateur.');
        }
        // Add more specific checks if needed (e.g., if ($user->email === 'superadmin@example.com'))

        // Detach from profiles before deleting user
        // This assumes one-to-one where profile has user_id
        if ($user->enseignant) $user->enseignant->update(['user_id' => null]);
        if ($user->parentGuardian) $user->parentGuardian->update(['user_id' => null]);
        if ($user->personnel) $user->personnel->update(['user_id' => null]);
        // If Stagiaires had user_id: if ($user->stagiaire) $user->stagiaire->update(['user_id' => null]);

        $user->delete(); // Spatie automatically detaches roles

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return redirect()->route('admin.users.index')->with('success', "L'utilisateur '{$user->name}' a été supprimé.");
    }

    // --- Your existing helper methods for specific profile account creation ---
    // These might still be useful if you have separate "Create account for Enseignant" buttons on Enseignant list/profile.
    // The `EnseignantController` methods `createAccountForm` and `storeAccount` are more direct for that.
    // If these helpers below are primarily for AJAX on a unified form, they need to be adapted.

    /*
    public function getFonctionnairesByType(Request $request) // Renamed to getProfilesByType
    {
        $type = $request->input('type');
        $profiles = collect();

        if ($type === 'enseignant') { // Was 'formateur'
            $profiles = Enseignant::whereNull('user_id')->orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom']);
        }
        // ... add for 'parent', 'personnel' if needed for a dropdown to link
        // Remove 'stagiaire' if they don't get accounts
        return response()->json($profiles);
    }

    public function generateUserEmail(Request $request)
    {
        $type = $request->input('type');
        $profileId = $request->input('profile_id');
        $email = '';
        $nameForUserAccount = '';
        $profileModel = null;

        if ($type === 'enseignant' && $profileId) {
            $profileModel = Enseignant::find($profileId);
            if ($profileModel) {
                $nameForUserAccount = $profileModel->prenom . ' ' . $profileModel->nom;
                $email = strtolower(Str::slug($profileModel->prenom) . '.' . Str::slug($profileModel->nom) . '@ettefl.com'); // Changed domain
            }
        }
        // ... logic for other types (parent, personnel) ...

        $emailExists = !empty($email) ? User::where('email', $email)->exists() : false;

        return response()->json([
            'email' => $email,
            'name_suggestion' => $nameForUserAccount,
            'exists' => $emailExists,
            'message' => $emailExists ? 'Cet email existe déjà.' : 'Email suggéré.'
        ]);
    }
    */
}