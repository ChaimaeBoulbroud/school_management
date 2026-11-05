<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Matiere;
use App\Models\Groupe;
// use App\Models\Enseignant; // Not directly used in this simplified version
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = Evaluation::with(['matiere', 'groupe']);

        if ($request->filled('matiere_id_filter')) {
            $query->where('matiere_id', $request->input('matiere_id_filter'));
        }
        if ($request->filled('groupe_id_filter')) {
            $query->where('groupe_id', $request->input('groupe_id_filter'));
        }
        if ($request->filled('type_evaluation_filter')) {
            $query->where('type_evaluation', $request->input('type_evaluation_filter'));
        }

        $evaluations = $query->latest('date_evaluation')->paginate(15);

        $matieres = Matiere::orderBy('nom_matiere')->get(['id', 'nom_matiere', 'code_matiere']);
        $groupes = Groupe::orderBy('niveau')->orderBy('nom')->get(['id', 'nom', 'niveau']);

        // Fetch distinct evaluation types for the filter dropdown
        // Option A: From database
        $typesEvaluation = Evaluation::distinct()->orderBy('type_evaluation')->pluck('type_evaluation');
        // Option B: Static list (if your types are fixed and you want all possible options shown)
        // $typesEvaluation = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre']; // Ensure this matches your system

        return view('admin.evaluations.index', compact(
            'evaluations',
            'matieres',
            'groupes',
            'typesEvaluation' // Now passing $typesEvaluation
        ));
    }


    public function create(Request $request)
    {
        $matieres = Matiere::orderBy('nom_matiere')->get(['id', 'nom_matiere', 'code_matiere']);
        $groupes = Groupe::orderBy('niveau')->orderBy('nom')->get(['id', 'nom', 'niveau']);
        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre']; 
        $preselectedMatiereId = $request->query('matiere_id');

        return view('admin.evaluations.create', compact('matieres', 'groupes',  'typesEvaluationOptions','preselectedMatiereId'));
    }

    public function store(Request $request)
    {
        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre']; // Ensure this matches options in create form
        $validatedData = $request->validate([
            'titre_evaluation' => 'required|string|max:255',
            'type_evaluation' => ['required', Rule::in($typesEvaluationOptions)],
            'date_evaluation' => 'required|date',
            'bareme_total' => 'required|integer|min:1|max:1000',
            'matiere_id' => 'required|exists:matieres,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        Evaluation::create($validatedData);

        return redirect()->route('admin.evaluations.index')->with('success', 'Évaluation ajoutée avec succès.');
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['matiere', 'groupe']);
        return view('admin.evaluations.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation)
    {
        // CORRECTED ORDER BY and SELECT specific columns
        $matieres = Matiere::orderBy('nom_matiere')->get(['id', 'nom_matiere', 'code_matiere']);
        $groupes = Groupe::orderBy('niveau')->orderBy('nom')->get(['id', 'nom', 'niveau']);
        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre']; // Ensure this matches options in create form

        return view('admin.evaluations.edit', compact('evaluation', 'matieres', 'groupes', 'typesEvaluationOptions'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre']; // Ensure this matches options in create form
        $validatedData = $request->validate([
            'titre_evaluation' => 'required|string|max:255',
            'type_evaluation' => ['required', Rule::in($typesEvaluationOptions)],
            'date_evaluation' => 'required|date',
            'bareme_total' => 'required|integer|min:1|max:1000',
            'matiere_id' => 'required|exists:matieres,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        $evaluation->update($validatedData);

        return redirect()->route('admin.evaluations.index')->with('success', 'Évaluation mise à jour avec succès.');
    }

    public function destroy(Evaluation $evaluation)
    {
        // onDelete('cascade') for notes.evaluation_id should handle deleting related notes.
        $evaluation->delete();
        return redirect()->route('admin.evaluations.index')->with('success', 'Évaluation supprimée avec succès.');
    }
}