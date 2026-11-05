<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffectationCours;
use App\Models\Groupe;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AffectationCoursController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_affectations'); // General permission for all actions
    }

    // ... your existing index, general create, general store, edit, update methods ...
    // Ensure your general store and update have the full unique rule:
    // Rule::unique('affectation_cours')->where(function ($query) use ($request) {
    //     return $query->where('groupe_id', $request->input('groupe_id'))
    //                  ->where('enseignant_id', $request->input('enseignant_id'))
    //                  ->where('matiere_id', $request->input('matiere_id'));
    // })->ignore($affectationCour->id (for update))

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, AffectationCours $affectationCour)
    {
        $redirectRoute = 'admin.affectations.index'; // Default redirect
        $routeParams = [];

        $affectationCour->delete();

        // Handle contextual redirects
        if ($request->query('redirect_to') === 'matiere_show' && $request->filled('matiere_id')) {
            $redirectRoute = 'admin.matieres.show';
            $routeParams = ['matiere' => $request->query('matiere_id')];
        } elseif ($request->query('redirect_to') === 'groupe_show' && $request->filled('groupe_id')) {
            $redirectRoute = 'admin.groupes.show';
            $routeParams = ['groupe' => $request->query('groupe_id')];
        } elseif ($request->query('redirect_to') === 'enseignant_show' && $request->filled('enseignant_id')) {
            $redirectRoute = 'admin.enseignants.show';
            $routeParams = ['enseignant' => $request->query('enseignant_id')];
        }

        return redirect()->route($redirectRoute, $routeParams)
                         ->with('success', 'Affectation supprimée avec succès.');
    }


    // --- FOR MATIERE-SPECIFIC AFFECTATION ---
    public function createForMatiere(Matiere $matiere)
    {
        $groupes = Groupe::orderBy('niveau')->orderBy('nom')->get(['id', 'nom', 'niveau']);
        $enseignants = Enseignant::orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom']);
        return view('admin.affectation_matieres.create_for_matiere', compact('matiere', 'groupes', 'enseignants'));
    }

    public function storeForMatiere(Request $request, Matiere $matiere)
    {
        $validatedData = $request->validate([
            'groupe_id' => ['required', 'exists:groupes,id'],
            'enseignant_id' => ['required', 'exists:enseignants,id',
                // Unique rule for the combination
                Rule::unique('affectation_cours')->where(function ($query) use ($request, $matiere) {
                    return $query->where('matiere_id', $matiere->id)
                                 ->where('groupe_id', $request->input('groupe_id'))
                                 ->where('enseignant_id', $request->input('enseignant_id'));
                }),
            ]
        ], [
            'enseignant_id.unique' => 'Cette matière est déjà affectée à cet enseignant dans ce groupe.',
        ]);

        AffectationCours::create([
            'matiere_id' => $matiere->id,
            'groupe_id' => $validatedData['groupe_id'],
            'enseignant_id' => $validatedData['enseignant_id'],
        ]);

        return redirect()->route('admin.matieres.show', $matiere->id)
                         ->with('success', 'Matière affectée au groupe et à l\'enseignant avec succès.');
    }


    // --- FOR GROUPE-SPECIFIC AFFECTATION ---
    public function createForGroupe(Groupe $groupe)
    {
        $matieres = Matiere::orderBy('nom_matiere')->get(['id', 'nom_matiere', 'code_matiere']);
        $enseignants = Enseignant::orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom']);
        return view('admin.affectation_matieres.create_for_groupe', compact('groupe', 'matieres', 'enseignants'));
    }

    public function storeForGroupe(Request $request, Groupe $groupe)
    {
        $validatedData = $request->validate([
            'matiere_id' => ['required', 'exists:matieres,id'],
            'enseignant_id' => ['required', 'exists:enseignants,id',
                 Rule::unique('affectation_cours')->where(function ($query) use ($request, $groupe) {
                    return $query->where('groupe_id', $groupe->id)
                                 ->where('matiere_id', $request->input('matiere_id'))
                                 ->where('enseignant_id', $request->input('enseignant_id'));
                }),
            ]
        ],[
            'enseignant_id.unique' => 'Cette matière est déjà affectée à cet enseignant dans ce groupe.',
        ]);

        AffectationCours::create([
            'groupe_id' => $groupe->id,
            'matiere_id' => $validatedData['matiere_id'],
            'enseignant_id' => $validatedData['enseignant_id'],
        ]);

        return redirect()->route('admin.groupes.show', $groupe->id)
                         ->with('success', 'Matière affectée à ce groupe et à l\'enseignant avec succès.');
    }


    // --- FOR ENSEIGNANT-SPECIFIC AFFECTATION ---
    public function createForEnseignant(Enseignant $enseignant)
    {
        $matieres = Matiere::orderBy('nom_matiere')->get(['id', 'nom_matiere', 'code_matiere']);
        $groupes = Groupe::orderBy('niveau')->orderBy('nom')->get(['id', 'nom', 'niveau']);
        return view('admin.affectation_matieres.create_for_enseignant', compact('enseignant', 'matieres', 'groupes'));
    }

    public function storeForEnseignant(Request $request, Enseignant $enseignant)
    {
        $validatedData = $request->validate([
            'matiere_id' => ['required', 'exists:matieres,id'],
            'groupe_id' => ['required', 'exists:groupes,id',
                Rule::unique('affectation_cours')->where(function ($query) use ($request, $enseignant) {
                    return $query->where('enseignant_id', $enseignant->id)
                                 ->where('groupe_id', $request->input('groupe_id'))
                                 ->where('matiere_id', $request->input('matiere_id'));
                }),
            ]
        ],[
            'groupe_id.unique' => 'Cet enseignant est déjà affecté à cette matière dans ce groupe.',
        ]);

        AffectationCours::create([
            'enseignant_id' => $enseignant->id,
            'matiere_id' => $validatedData['matiere_id'],
            'groupe_id' => $validatedData['groupe_id'],
        ]);

        return redirect()->route('admin.enseignants.show', $enseignant->id)
                         ->with('success', 'Matière affectée à cet enseignant pour le groupe sélectionné avec succès.');
    }
}