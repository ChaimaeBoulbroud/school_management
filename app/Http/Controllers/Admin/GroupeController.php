<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupeController extends Controller
{
    public function __construct()
    {
        // Apply permissions
        $this->middleware('permission:view_groupes')->only(['index', 'show', 'getElevesPourGroupe']);
        $this->middleware('permission:create_groupes')->only(['create', 'store']);
        $this->middleware('permission:edit_groupes')->only(['edit', 'update']);
        $this->middleware('permission:delete_groupes')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groupes = Groupe::withCount('eleves') // Count associated 'eleves'
            ->orderBy('niveau') // Order by niveau first, then nom
            ->orderBy('nom')
            ->paginate(10);

        return view('admin.groupes.index', compact('groupes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
        return view('admin.groupes.create' );
    }


    public function store(Request $request) 
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255', 
            'niveau' => ['required', 'string', 'max:255'], 
            'annee_scolaire' => 'required|string|max:50', // NEW: validate annee_scolaire
        ]);

        Groupe::create([
            'nom' => $validatedData['nom'],
            'niveau' => $validatedData['niveau'],
            'annee_scolaire' => $validatedData['annee_scolaire'],
        ]);

        return redirect()->route('admin.groupes.index')->with('success', 'Groupe ajouté avec succès.');
    }

    public function show(Groupe $groupe)
    {
        $groupe->load([
            'eleves', 
            'affectationCours.matiere',
            'affectationCours.enseignant', 
            'evaluations.matiere'
        ]);
        return view('admin.groupes.show', compact('groupe'));
    }


    public function edit(Groupe $groupe)
    {

        return view('admin.groupes.edit', compact('groupe'/*, 'niveaux'*/));
    }

    public function update(Request $request, Groupe $groupe)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => ['required', 'string', 'max:255'],
            'annee_scolaire' => 'required|string|max:50', // NEW: validate annee_scolaire
        ]);

        $groupe->update([
            'nom' => $validatedData['nom'],
            'niveau' => $validatedData['niveau'],
            'annee_scolaire' => $validatedData['annee_scolaire'],
        ]);
        return redirect()->route('admin.groupes.index')->with('success', 'Groupe mis à jour avec succès.');
    }


    public function destroy(Groupe $groupe)
    {

        if ($groupe->eleves()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce groupe car des élèves y sont encore affectés. Veuillez d\'abord réaffecter ou supprimer les élèves.');
        }

        $groupe->delete();
        return redirect()->route('admin.groupes.index')->with('success', 'Groupe supprimé avec succès.');
    }


    public function getElevesPourGroupe(Groupe $groupe) // Renamed from getStagiairesPourGroupe
    {
        $eleves = $groupe->eleves()->orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom']);

        return response()->json($eleves);
    }
}