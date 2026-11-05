<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Http\Request; // Ideally use FormRequests
// use App\Http\Requests\Admin\StoreEnseignantRequest;
// use App\Http\Requests\Admin\UpdateEnseignantRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Enseignant::with('user'); // Eager load the associated user account

        if ($request->filled('nom_filter')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('nom_filter') . '%')
                  ->orWhere('prenom', 'like', '%' . $request->input('nom_filter') . '%');
            });
        }
        if ($request->filled('cin_filter')) {
            $query->where('CIN', 'like', '%' . $request->input('cin_filter') . '%');
        }
        // Add more filters if needed (e.g., by date_recrutement range)

        $enseignants = $query->orderBy('nom')->orderBy('prenom')->paginate(15);

        return view('admin.enseignants.index', compact('enseignants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.enseignants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // Replace with StoreEnseignantRequest
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'CIN' => ['nullable', 'string', 'max:50', Rule::unique('enseignants', 'CIN')],
            'adresse' => 'nullable|string|max:255',
            'date_recrutement' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            // Optional User account creation fields
            'create_user_account' => 'nullable|boolean',
            'email' => ['nullable', 'required_if:create_user_account,1', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['nullable', 'required_if:create_user_account,1', 'string', 'min:8', 'confirmed'],
        ]);
        
        $enseignantData = collect($validatedData)->except(['create_user_account', 'email', 'password', 'password_confirmation'])->toArray();
        $enseignant = Enseignant::create($enseignantData);

        // More robust check for the boolean flag
        $shouldCreateUserAccount = false;
        if (isset($validatedData['create_user_account'])) {
            if ($validatedData['create_user_account'] === true || $validatedData['create_user_account'] === '1') {
                $shouldCreateUserAccount = true;
            }
        }

        \Illuminate\Support\Facades\Log::info('Checking user account creation condition.', [
            'shouldCreateUserAccount_flag' => $shouldCreateUserAccount,
            'create_user_account_validated' => $validatedData['create_user_account'] ?? null,
            'email_present' => !empty($validatedData['email']),
            'password_present' => !empty($validatedData['password'])
        ]);

        if ($shouldCreateUserAccount && !empty($validatedData['email']) && !empty($validatedData['password'])) {
            \Illuminate\Support\Facades\Log::info('Condition MET: Creating user account for enseignant ID: ' . $enseignant->id);

            $user = User::create([
                'name' => $validatedData['prenom'] . ' ' . $validatedData['nom'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $updateResult = $enseignant->update(['user_id' => $user->id]);

            \Illuminate\Support\Facades\Log::info('Enseignant update result for user_id linking: ' . ($updateResult ? 'Success' : 'Failure'), [
                'enseignant_id' => $enseignant->id,
                'user_id_assigned' => $user->id,
                'enseignant_after_update' => Enseignant::find($enseignant->id)->toArray()
            ]);

            $user->assignRole('Enseignant');
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            \Illuminate\Support\Facades\Log::info('Role "Enseignant" assigned and cache cleared for user ID: ' . $user->id);

        } else {
            \Illuminate\Support\Facades\Log::info('Condition NOT MET: Skipping user account creation.', [
                 'shouldCreateUserAccount_flag' => $shouldCreateUserAccount,
                 'create_user_account_value_from_validation' => $validatedData['create_user_account'] ?? 'not_set',
                 'email_is_empty' => empty($validatedData['email']),
                 'password_is_empty' => empty($validatedData['password']),
            ]);
        }

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enseignant $enseignant)
    {
        $enseignant->load([
            'user', 
            'affectationCours.groupe', 
            'affectationCours.matiere'
        ]);
        return view('admin.enseignants.show', compact('enseignant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enseignant $enseignant)
    {
        $enseignant->load('user'); // Load user if it exists for the form
        return view('admin.enseignants.edit', compact('enseignant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enseignant $enseignant) // Replace with UpdateEnseignantRequest
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'CIN' => ['nullable', 'string', 'max:50', Rule::unique('enseignants', 'CIN')->ignore($enseignant->id)],
            'adresse' => 'nullable|string|max:255',
            'date_recrutement' => 'nullable|date',
            'telephone' => 'nullable|string|max:50',
            // User account fields - allowing update or creation if not exists
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($enseignant->user_id)], // Ignore self if user exists
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Password only updated if provided
        ]);

        $enseignantData = collect($validatedData)->except(['email', 'password', 'password_confirmation'])->toArray();
        $enseignant->update($enseignantData);

        // Handle User Account
        if ($request->filled('email')) { // If email is provided, attempt to update or create user
            if ($enseignant->user) { // If user account exists
                $enseignant->user->name = $validatedData['prenom'] . ' ' . $validatedData['nom'];
                $enseignant->user->email = $validatedData['email'];
                if ($request->filled('password')) {
                    $enseignant->user->password = Hash::make($validatedData['password']);
                }
                $enseignant->user->save();
            } elseif ($request->filled('password')) { // If user does not exist, but email and password are provided, create one
                $user = User::create([
                    'name' => $validatedData['prenom'] . ' ' . $validatedData['nom'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                ]);
                $enseignant->update(['user_id' => $user->id]);
                // If using Spatie roles: $user->assignRole('Enseignant');
            }
        } elseif ($request->filled('password') && $enseignant->user) { // Only password change for existing user
             $enseignant->user->password = Hash::make($validatedData['password']);
             $enseignant->user->save();
        }


        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enseignant $enseignant)
    {
        
        $enseignant->delete();

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant supprimé avec succès.');
    }

    
    public function createAccountForm(Enseignant $enseignant)
    {
        if ($enseignant->user) {
            return redirect()->route('admin.enseignants.show', $enseignant->id)
                             ->with('info', 'Cet enseignant a déjà un compte utilisateur: ' . $enseignant->user->email);
        }
        // Pass enseignant to the view so you can pre-fill the name
        return view('admin.enseignants.create_account', compact('enseignant'));
    }

    /**
     * Store a new user account for an existing enseignant and link it.
     */
    public function storeAccount(Request $request, Enseignant $enseignant)
    {
        if ($enseignant->user) {
            return redirect()->route('admin.enseignants.show', $enseignant->id)
                             ->with('error', 'Compte utilisateur déjà existant pour cet enseignant.');
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create the User
        $user = User::create([
            'name' => $enseignant->prenom . ' ' . $enseignant->nom, // Or use $enseignant->getFullNameAttribute() if defined
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign the 'Enseignant' role
        $user->assignRole('Enseignant');

        // Link the new user to the existing Enseignant
        $enseignant->user_id = $user->id;
        $enseignant->save();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.enseignants.show', $enseignant->id)
                         ->with('success', 'Compte utilisateur créé et lié avec succès à ' . $user->name . '.');
    }
}