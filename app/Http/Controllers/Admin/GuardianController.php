<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian; // Model for 'parents' table
use App\Models\User;
use Illuminate\Http\Request; // Ideally use FormRequests
// use App\Http\Requests\Admin\StoreGuardianRequest;
// use App\Http\Requests\Admin\UpdateGuardianRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class GuardianController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Guardian::with('user');

        if ($request->filled('nom_filter')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('nom_filter') . '%')
                  ->orWhere('prenom', 'like', '%' . $request->input('nom_filter') . '%');
            });
        }
        if ($request->filled('cin_filter')) {
            $query->where('CIN', 'like', '%' . $request->input('cin_filter') . '%');
        }

        $guardians = $query->orderBy('nom')->orderBy('prenom')->paginate(15);

        return view('admin.guardians.index', compact('guardians'));
    }

    
    
    public function create()
    {
        return view('admin.guardians.create');
    }

    
    
    public function store(Request $request) 
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'CIN' => ['nullable', 'string', 'max:50', Rule::unique('parents', 'CIN')], // Table is 'parents'
            'telephone' => 'nullable|string|max:50',
            // Optional User account creation fields
            'create_user_account' => 'nullable|boolean',
            'email' => ['nullable', 'required_if:create_user_account,1', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['nullable', 'required_if:create_user_account,1', 'string', 'min:8', 'confirmed'],
        ]);

        $guardianData = collect($validatedData)->except(['create_user_account', 'email', 'password', 'password_confirmation'])->toArray();
        $guardian = Guardian::create($guardianData);

        if (!empty($validatedData['create_user_account']) && !empty($validatedData['email']) && !empty($validatedData['password'])) {
            $user = User::create([
                'name' => $validatedData['prenom'] . ' ' . $validatedData['nom'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);
            $guardian->update(['user_id' => $user->id]);
            $user->assignRole('Parent'); // <-- Ensure this line is active
        }

        return redirect()->route('admin.guardians.index')->with('success', 'Parent/Gardien ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guardian $guardian)
    {
        $guardian->load(['user', 'eleves.groupe']); // Load user and their associated students (eleves)
        return view('admin.guardians.show', compact('guardian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guardian $guardian)
    {
        $guardian->load('user');
        return view('admin.guardians.edit', compact('guardian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guardian $guardian) // Replace with UpdateGuardianRequest
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'CIN' => ['nullable', 'string', 'max:50', Rule::unique('parents', 'CIN')->ignore($guardian->id)],
            'telephone' => 'nullable|string|max:50',
            // User account fields
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guardian->user_id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $guardianData = collect($validatedData)->except(['email', 'password', 'password_confirmation'])->toArray();
        $guardian->update($guardianData);

        // Handle User Account (similar logic as in EnseignantController)
        if ($request->filled('email')) {
            if ($guardian->user) {
                $guardian->user->name = $validatedData['prenom'] . ' ' . $validatedData['nom'];
                $guardian->user->email = $validatedData['email'];
                if ($request->filled('password')) {
                    $guardian->user->password = Hash::make($validatedData['password']);
                }
                $guardian->user->save();
            } elseif ($request->filled('password')) {
                $user = User::create([
                    'name' => $validatedData['prenom'] . ' ' . $validatedData['nom'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                ]);
                $guardian->update(['user_id' => $user->id]);
                $user->assignRole('Parent'); // <-- Ensure this line is active
            }
        } elseif ($request->filled('password') && $guardian->user) {
             $guardian->user->password = Hash::make($validatedData['password']);
             $guardian->user->save();
        }

        return redirect()->route('admin.guardians.index')->with('success', 'Parent/Gardien mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guardian $guardian)
    {
        // Eleves have parent_id with onDelete('restrict').
        // You cannot delete a Guardian if Eleves are still assigned to them.
        if ($guardian->eleves()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce parent/gardien car des élèves y sont encore affectés.');
        }

        // What about the user account?
        // if ($guardian->user) { $guardian->user->delete(); } // If you want to delete the login too

        $guardian->delete();
        return redirect()->route('admin.guardians.index')->with('success', 'Parent/Gardien supprimé avec succès.');
    }
    public function ajaxStore(Request $request) // Or createGuardian(array $requestData) in service
    {
        // Use keys in validation that match your expected request input names
        $validator = Validator::make($request->all(), [
            'guardian_nom' => 'required|string|max:255',
            'guardian_prenom' => 'required|string|max:255',
            'guardian_CIN' => ['required', 'string', 'max:50', Rule::unique('parents', 'CIN')],
            'guardian_telephone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            // Map the validated data keys to your database column/model attribute names
            $guardian = Guardian::create([
                'nom' => $validatedData['guardian_nom'],         // Map guardian_nom to nom
                'prenom' => $validatedData['guardian_prenom'],   // Map guardian_prenom to prenom
                'CIN' => $validatedData['guardian_CIN'],         // Map guardian_CIN to CIN
                'telephone' => $validatedData['guardian_telephone'], // Map guardian_telephone to telephone
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Parent/Gardien ajouté avec succès.',
                'guardian' => [
                    'id' => $guardian->id,
                    'full_name' => $guardian->getFullNameAttribute() . ($guardian->CIN ? ' (CIN: ' . $guardian->CIN . ')' : '')
                ]
            ]);
        } catch (\Exception $e) {
            logger()->error('AJAX Guardian Creation Error: ' . $e->getMessage() . ' SQL: ' . ($e instanceof \Illuminate\Database\QueryException ? $e->getSql() : 'N/A'));
            return response()->json(['success' => false, 'message' => 'Erreur lors de la création du parent/gardien.'], 500);
        }
    }
    public function storeAccount(Request $request, Guardian $guardian)
    {
        if ($guardian->user) {
            return redirect()->route('admin.guardians.show', $guardian->id)
                ->with('error', 'Compte utilisateur déjà existant pour ce parent/gardien.');
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $guardian->prenom . ' ' . $guardian->nom,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('Parent'); // Or 'Guardian' if that's your role name

        $guardian->user_id = $user->id;
        $guardian->save();

        return redirect()->route('admin.guardians.show', $guardian->id)
            ->with('success', 'Compte utilisateur créé, lié et rôle attribué avec succès à ' . $user->name . '.');
    }
}