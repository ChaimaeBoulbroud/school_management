<?php

namespace App\Http\Controllers\Admin; // Or your admin controller namespace

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Eleve;       // Changed from Stagiaire
use App\Models\Evaluation;
use App\Models\Enseignant;  // Changed from Formateur (used for potential filtering)
use App\Models\Matiere;     // Changed from Module
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\Auth; // If you want to log who created/updated the note

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $query = Note::with(['eleve', 'evaluation.matiere', 'evaluation.groupe']);

        // Filters
        if ($request->filled('eleve_id_filter')) {
            $query->where('eleve_id', $request->input('eleve_id_filter'));
        }
        if ($request->filled('evaluation_id_filter')) {
            $query->where('evaluation_id', $request->input('evaluation_id_filter'));
        }
        if ($request->filled('matiere_id_filter')) {
            $query->whereHas('evaluation.matiere', function ($q) use ($request) {
                $q->where('id', $request->input('matiere_id_filter'));
            });
        }
        if ($request->filled('groupe_id_filter')) {
            $query->whereHas('evaluation.groupe', function ($q) use ($request) {
                $q->where('id', $request->input('groupe_id_filter'));
            });
        }

        $notes = $query->latest('created_at')->paginate(20);

        // For filter dropdowns
        $eleves = Eleve::orderBy('nom')->orderBy('prenom')->get(['id', 'nom', 'prenom']); // Assuming 'nom' exists on eleves
        
        // CORRECTED ORDER BY for Matieres and select specific columns
        $matieres = Matiere::orderBy('nom_matiere')->get(['id', 'nom_matiere', 'code_matiere']); 
        
        $groupes = Groupe::orderBy('niveau')->orderBy('nom')->get(['id', 'nom', 'niveau']); // Assuming 'nom' exists on groupes
        
        $evaluationIdsWithNotes = Note::distinct()->pluck('evaluation_id');
        $evaluations = Evaluation::whereIn('id', $evaluationIdsWithNotes)
                                 ->with('matiere') // Eager load matiere for display in dropdown
                                 ->select('id', 'titre_evaluation', 'date_evaluation', 'matiere_id') // Ensure matiere_id is selected
                                 ->orderBy('date_evaluation', 'desc')
                                 ->orderBy('titre_evaluation')
                                 ->get();

        session(['notes_index_filters' => $request->query()]);

        return view('admin.notes.index', compact('notes', 'eleves', 'evaluations',  'matieres', 'groupes'));
    }


    public function create()
    {

        $evaluations = Evaluation::with('groupe.eleves', 'matiere') // Load necessary data
                                 ->orderBy('date_evaluation', 'desc')->get();
        return view('admin.notes.create_or_edit_bulk', compact('evaluations')); 
    }


    public function store(Request $request) 
    {
        $evaluation = Evaluation::findOrFail($request->input('evaluation_id'));

        $validatedData = $request->validate([
            'eleve_id' => [
                'required',
                'exists:eleves,id', // Changed
                Rule::unique('notes')->where(function ($query) use ($request) {
                    return $query->where('evaluation_id', $request->input('evaluation_id'));
                })->ignore($request->input('note_id')) 
            ],
            'evaluation_id' => 'required|exists:evaluations,id',
            'note_obtenue' => ['required', 'numeric', 'min:0', 'max:' . $evaluation->bareme_total],
            'observations' => 'nullable|string|max:1000',
        ]);


        Note::create($validatedData);

        return redirect()->route('admin.notes.index', session('notes_index_filters', []))
                         ->with('success', 'Note ajoutée avec succès.');
    }


    public function show(Note $note)
    {
        $note->load(['eleve', 'evaluation.matiere', 'evaluation.groupe']);
        return view('admin.notes.show', compact('note'));
    }


    public function edit(Note $note)
    {
        $note->load(['eleve', 'evaluation.matiere', 'evaluation.groupe']);

        return view('admin.notes.edit', compact('note'));
    }


    public function update(Request $request, Note $note) 
    {
        $evaluation = Evaluation::findOrFail($note->evaluation_id);

        $validatedData = $request->validate([
            'note_obtenue' => ['required', 'numeric', 'min:0', 'max:' . $evaluation->bareme_total],
            'observations' => 'nullable|string|max:1000',

        ]);



        $note->update($validatedData);

        return redirect()->route('admin.notes.index', session('notes_index_filters', []))
                         ->with('success', 'Note mise à jour avec succès.');
    }


    public function destroy(Note $note)
    {
        $note->delete();

        return redirect()->route('admin.notes.index', session('notes_index_filters', []))
                         ->with('success', 'Note supprimée avec succès.');
    }



    public function manageNotesForEvaluation(Evaluation $evaluation)
    {
        $evaluation->load([
            'groupe.eleves' => function ($query) { 
                $query->orderBy('nom')->orderBy('prenom');
            },
            'notes',
            'matiere'
        ]);

        $studentsWithNotes = $evaluation->groupe->eleves->map(function ($eleve) use ($evaluation) {
            $eleve->note = $evaluation->notes->firstWhere('eleve_id', $eleve->id);
            return $eleve;
        });

        return view('admin.notes.manage_for_evaluation', compact('evaluation', 'studentsWithNotes'));
    }


    public function storeManagedNotesForEvaluation(Request $request, Evaluation $evaluation)
    {
        $request->validate([
            'notes' => 'required|array',
            'notes.*.eleve_id' => 'required|exists:eleves,id',
            'notes.*.note_obtenue' => ['nullable', 'numeric', 'min:0', 'max:' . $evaluation->bareme_total],
            'notes.*.observations' => 'nullable|string|max:1000',
        ]);

        foreach ($request->input('notes') as $eleveId => $noteData) {
            if (!isset($noteData['note_obtenue']) && !isset($noteData['observations'])) {
                Note::where('evaluation_id', $evaluation->id)->where('eleve_id', $eleveId)->delete();
                continue;
            }

            Note::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'eleve_id' => $eleveId,
                ],
                [
                    'note_obtenue' => $noteData['note_obtenue'] ?? null, // Handle if not submitted
                    'observations' => $noteData['observations'] ?? null,

                ]
            );
        }

        return redirect()->route('admin.evaluations.show', $evaluation) // Or back to manage notes page
                         ->with('success', 'Notes enregistrées avec succès pour l\'évaluation.');
    }
}