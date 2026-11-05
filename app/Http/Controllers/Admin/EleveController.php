<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Guardian;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EleveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Eleve::with(['guardian', 'groupe']);

        if ($request->filled('groupe_id_filter')) {
            $query->where('groupe_id', $request->input('groupe_id_filter'));
        }
        if ($request->filled('nom_filter')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('nom_filter') . '%')
                  ->orWhere('prenom', 'like', '%' . $request->input('nom_filter') . '%');
            });
        }

        $eleves = $query->orderBy('nom')->orderBy('prenom')->paginate(15);
        $groupes = Groupe::orderBy('nom')->get(['id', 'nom', 'niveau']);

        // For dynamic dropdowns: get all distinct niveaux
        $niveaux = Groupe::distinct()->orderBy('niveau')->pluck('niveau');

        return view('admin.eleves.index', compact('eleves', 'groupes', 'niveaux'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guardians = Guardian::orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom','CIN']);
        $niveaux = Groupe::distinct()->orderBy('niveau')->pluck('niveau');
        // Start with empty groupes, will be filled by AJAX
        $groupes = collect();
        return view('admin.eleves.create', compact('guardians', 'niveaux', 'groupes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'genre' => ['required', Rule::in(['Homme', 'Femme'])],
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'parent_id' => 'required|exists:parents,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        Eleve::create($validatedData);

        return redirect()->route('admin.eleves.index')->with('success', 'Élève ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Eleve $eleve)
    {
        $eleve->load(['guardian', 'groupe', 'notes.evaluation.matiere']);
        return view('admin.eleves.show', compact('eleve'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Eleve $eleve)
    {
        $guardians = Guardian::orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom','CIN']);
        $niveaux = Groupe::distinct()->orderBy('niveau')->pluck('niveau');
        // Preload groupes for the current niveau of the eleve
        $groupes = Groupe::where('niveau', $eleve->groupe->niveau ?? null)
            ->orderBy('nom')
            ->get(['id', 'nom', 'niveau']);
        return view('admin.eleves.edit', compact('eleve', 'guardians', 'niveaux', 'groupes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Eleve $eleve)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'genre' => ['required', Rule::in(['Homme', 'Femme'])],
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'parent_id' => 'required|exists:parents,id',
            'groupe_id' => 'required|exists:groupes,id',
        ]);

        $eleve->update($validatedData);

        return redirect()->route('admin.eleves.index')->with('success', 'Élève mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Eleve $eleve)
    {
        $eleve->delete();
        return redirect()->route('admin.eleves.index')->with('success', 'Élève supprimé avec succès.');
    }

    /**
     * AJAX endpoint: Get groupes by niveau.
     */
    public function getGroupesByNiveau(Request $request)
    {
        $niveau = $request->input('niveau');
        $groupes = Groupe::where('niveau', $niveau)
            ->orderBy('nom')
            ->get(['id', 'nom', 'niveau']);
        return response()->json($groupes);
    }
}