<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request; // Replace with FormRequest
use Illuminate\Validation\Rule;

class MatiereController extends Controller
{
    public function index(Request $request)
    {
        $query = Matiere::query();
        if ($request->filled('search_filter')) { // Generic search
            $searchTerm = '%' . $request->input('search_filter') . '%';
            $query->where(function ($q) use ($searchTerm) {
                  $q->Where('nom_matiere', 'like', $searchTerm)
                  ->orWhere('code_matiere', 'like', $searchTerm);
            });
        }
        $matieres = $query->orderBy('nom_matiere')->paginate(15);
        return view('admin.matieres.index', compact('matieres'));
    }

    public function create()
    {
        return view('admin.matieres.create');
    }

    public function store(Request $request) // Use FormRequest
    {
        $validatedData = $request->validate([
            /*'nom' => ['required', 'string', 'max:100', Rule::unique('matieres', 'nom')],*/
            'nom_matiere' => ['required', 'string', 'max:255', Rule::unique('matieres', 'nom_matiere')],
            'code_matiere' => ['nullable', 'string', 'max:50', Rule::unique('matieres', 'code_matiere')], // Ensure unique if not soft deleted
            'masse_horaire' => 'nullable|integer|min:0',
            'coefficient' => 'nullable|numeric|min:0',
        ]);

        Matiere::create($validatedData);
        return redirect()->route('admin.matieres.index')->with('success', 'Matière ajoutée avec succès.');
    }

    public function show(Matiere $matiere)
    {
        $matiere->load([
            'affectationCours.groupe', 
            'affectationCours.enseignant', 
            'evaluations']);
        return view('admin.matieres.show', compact('matiere'));
    }

    public function edit(Matiere $matiere)
    {
        return view('admin.matieres.edit', compact('matiere'));
    }

    public function update(Request $request, Matiere $matiere) // Use FormRequest
    {
        $validatedData = $request->validate([
            /*'nom' => ['required', 'string', 'max:100', Rule::unique('matieres', 'nom')->ignore($matiere->id)],*/
             'nom_matiere' => ['required', 'string', 'max:255', Rule::unique('matieres', 'nom_matiere')->ignore($matiere->id)],
            'code_matiere' => ['nullable', 'string', 'max:50', Rule::unique('matieres', 'code_matiere')->ignore($matiere->id)],
            'masse_horaire' => 'nullable|integer|min:0',
            'coefficient' => 'nullable|numeric|min:0',
        ]);

        $matiere->update($validatedData);
        return redirect()->route('admin.matieres.index')->with('success', 'Matière mise à jour avec succès.');
    }

    public function destroy(Matiere $matiere)
    {
        // AffectationCours and Evaluations have matiere_id with onDelete('cascade')
        // So, related assignments and evaluations will be deleted automatically.
        $matiere->delete();
        return redirect()->route('admin.matieres.index')->with('success', 'Matière supprimée avec succès.');
    }
}