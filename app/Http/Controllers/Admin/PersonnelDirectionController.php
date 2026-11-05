<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonnelDirection;
use App\Models\User;
use Illuminate\Http\Request; // Ideally use FormRequests
// use App\Http\Requests\Admin\StorePersonnelDirectionRequest;
// use App\Http\Requests\Admin\UpdatePersonnelDirectionRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PersonnelDirectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PersonnelDirection::with('user');

        if ($request->filled('nom_filter')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('nom_filter') . '%')
                  ->orWhere('prenom', 'like', '%' . $request->input('nom_filter') . '%');
            });
        }
        if ($request->filled('cin_filter')) {
            $query->where('CIN', 'like', '%' . $request->input('cin_filter') . '%');
        }

        $personnels = $query->orderBy('nom')->orderBy('prenom')->paginate(15); // Renamed variable for clarity

        return view('admin.personnel_directions.index', compact('personnels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.personnel_directions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // Replace with StorePersonnelDirectionRequest
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'CIN' => ['nullable', 'string', 'max:50', Rule::unique('personnel_directions', 'CIN')],
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'rib' => ['nullable', 'string', 'max:100', Rule::unique('personnel_directions', 'rib')], // Max length for RIB might vary
            // Optional User account creation fields
            'create_user_account' => 'nullable|boolean',
            'email' => ['nullable', 'required_if:create_user_account,1', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['nullable', 'required_if:create_user_account,1', 'string', 'min:8', 'confirmed'],
        ]);

        $personnelData = collect($validatedData)->except(['create_user_account', 'email', 'password', 'password_confirmation'])->toArray();
        $personnel = PersonnelDirection::create($personnelData);

        if (!empty($validatedData['create_user_account']) && !empty($validatedData['email']) && !empty($validatedData['password'])) {
            $user = User::create([
                'name' => $validatedData['prenom'] . ' ' . $validatedData['nom'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);
            $personnel->update(['user_id' => $user->id]);
            // If using Spatie roles: $user->assignRole('Personnel Direction'); // Or a more specific role
        }

        return redirect()->route('admin.personnel_directions.index')->with('success', 'Membre du personnel ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PersonnelDirection $personnelDirection) // Route model binding name should match variable
    {
        $personnelDirection->load('user');
        return view('admin.personnel_directions.show', compact('personnelDirection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PersonnelDirection $personnelDirection)
    {
        $personnelDirection->load('user');
        return view('admin.personnel_directions.edit', compact('personnelDirection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PersonnelDirection $personnelDirection) // Replace with UpdatePersonnelDirectionRequest
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'CIN' => ['nullable', 'string', 'max:50', Rule::unique('personnel_directions', 'CIN')->ignore($personnelDirection->id)],
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'rib' => ['nullable', 'string', 'max:100', Rule::unique('personnel_directions', 'rib')->ignore($personnelDirection->id)],
            // User account fields
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($personnelDirection->user_id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $personnelData = collect($validatedData)->except(['email', 'password', 'password_confirmation'])->toArray();
        $personnelDirection->update($personnelData);

        // Handle User Account (similar logic as in EnseignantController)
        if ($request->filled('email')) {
            if ($personnelDirection->user) {
                $personnelDirection->user->name = $validatedData['prenom'] . ' ' . $validatedData['nom'];
                $personnelDirection->user->email = $validatedData['email'];
                if ($request->filled('password')) {
                    $personnelDirection->user->password = Hash::make($validatedData['password']);
                }
                $personnelDirection->user->save();
            } elseif ($request->filled('password')) {
                $user = User::create([
                    'name' => $validatedData['prenom'] . ' ' . $validatedData['nom'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                ]);
                $personnelDirection->update(['user_id' => $user->id]);
                // $user->assignRole('Personnel Direction');
            }
        } elseif ($request->filled('password') && $personnelDirection->user) {
             $personnelDirection->user->password = Hash::make($validatedData['password']);
             $personnelDirection->user->save();
        }

        return redirect()->route('admin.personnel_directions.index')->with('success', 'Membre du personnel mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PersonnelDirection $personnelDirection)
    {
        // Similar to Enseignant, consider User account deletion policy
        // if ($personnelDirection->user) {
        //     $personnelDirection->user->delete();
        // }
        $personnelDirection->delete();
        return redirect()->route('admin.personnel_directions.index')->with('success', 'Membre du personnel supprimé avec succès.');
    }

    /**
     * Show the form to create a new user account for the specified personnel.
     */
    public function createAccountForm(PersonnelDirection $personnelDirection)
    {
        if ($personnelDirection->user) {
            return redirect()->route('admin.personnel_directions.show', $personnelDirection->id)
                ->with('info', 'Ce membre du personnel a déjà un compte utilisateur: ' . $personnelDirection->user->email);
        }
        return view('admin.personnel_directions.create_account', compact('personnelDirection'));
    }

    /**
     * Store the newly created user account for the specified personnel.
     */
    public function storeAccount(Request $request, PersonnelDirection $personnelDirection)
    {
        if ($personnelDirection->user) {
            return redirect()->route('admin.personnel_directions.show', $personnelDirection->id)
                ->with('error', 'Compte utilisateur déjà existant pour ce membre du personnel.');
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $personnelDirection->prenom . ' ' . $personnelDirection->nom,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('Personnel'); // <-- Add this line

        $personnelDirection->user_id = $user->id;
        $personnelDirection->save();

        return redirect()->route('admin.personnel_directions.show', $personnelDirection->id)
            ->with('success', 'Compte utilisateur créé, lié et rôle attribué avec succès à ' . $user->name . '.');
    }
}