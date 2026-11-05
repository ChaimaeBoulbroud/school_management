<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging
use App\Models\Enseignant;
use App\Models\Groupe;
use App\Models\Matiere;
use App\Models\Evaluation;
use App\Models\Note;
use App\Models\AffectationCours; // Your assignment model
use App\Models\Eleve; // For listing students
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate; // If using Gates for authorization

class EnseignantInterfaceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Enseignant']);
    }

    /**
     * Helper method to get the Enseignant model instance for the logged-in user.
     */
    private function getLoggedInEnseignant()
    {
        $user = Auth::user();
        if ($user && $user->enseignant) {
            return $user->enseignant;
        }
        Log::warning('Attempt to get logged-in enseignant profile failed for user ID: ' . ($user->id ?? 'Guest/Unknown'));
        return null;
    }

    /**
     * Display the teacher's landing page (accueil).
     */
    public function accueil()
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Profil enseignant non associé à cet utilisateur. Veuillez contacter l\'administration.');
        }

        // Fetch upcoming evaluations (next 2 weeks)
        $teacherAssignments = AffectationCours::where('enseignant_id', $enseignant->id)
                                              ->select('matiere_id', 'groupe_id')
                                              ->distinct()->get();
        $upcomingEvaluations = collect();
        if (!$teacherAssignments->isEmpty()) {
            $evaluationsQuery = Evaluation::query();
            $evaluationsQuery->where(function ($query) use ($teacherAssignments) {
                foreach ($teacherAssignments as $assignment) {
                    $query->orWhere(function ($subQuery) use ($assignment) {
                        $subQuery->where('matiere_id', $assignment->matiere_id)
                                 ->where('groupe_id', $assignment->groupe_id);
                    });
                }
            });
            $upcomingEvaluations = $evaluationsQuery
                ->where('date_evaluation', '>=', now()->toDateString())
                ->where('date_evaluation', '<=', now()->addWeeks(2)->toDateString())
                ->orderBy('date_evaluation', 'asc')
                ->with(['matiere', 'groupe'])
                ->take(5)
                ->get();
        }

        // Fetch evaluations whose date has passed but not all notes are entered (limit 3)
        $evaluationsSansNotes = collect();
        if (!$teacherAssignments->isEmpty()) {
            $evaluationsPassees = Evaluation::where(function ($query) use ($teacherAssignments) {
                    foreach ($teacherAssignments as $assignment) {
                        $query->orWhere(function ($subQuery) use ($assignment) {
                            $subQuery->where('matiere_id', $assignment->matiere_id)
                                     ->where('groupe_id', $assignment->groupe_id);
                        });
                    }
                })
                ->where('date_evaluation', '<', now()->toDateString())
                ->with(['groupe.eleves', 'notes'])
                ->orderBy('date_evaluation', 'desc')
                ->get();

            $evaluationsSansNotes = $evaluationsPassees->filter(function ($evaluation) {
                if ($evaluation->groupe && $evaluation->groupe->eleves->isNotEmpty()) {
                    foreach ($evaluation->groupe->eleves as $eleve) {
                        if (!$evaluation->notes->contains('eleve_id', $eleve->id)) {
                            return true;
                        }
                    }
                }
                return false;
            })->take(3);
        }

        // Stats: number of active groups and total students for this teacher
        $activeGroupesCount = AffectationCours::where('enseignant_id', $enseignant->id)
                                ->distinct('groupe_id')->count('groupe_id');
        $groupeIds = AffectationCours::where('enseignant_id', $enseignant->id)->pluck('groupe_id')->unique();
        $totalElevesCount = Eleve::whereIn('groupe_id', $groupeIds)->count();
        $stats = [
            'active_groupes_count' => $activeGroupesCount,
            'total_eleves_count' => $totalElevesCount,
        ];

        return view('enseignant.accueil', compact(
            'enseignant',
            'upcomingEvaluations',
            'evaluationsSansNotes',
            'stats'
        ));
    }

    // --- Groupes ---
    public function listGroupes()
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { /* Handle error as in accueil */ abort(403, 'Profil Enseignant non trouvé.'); }

        $groupes = Groupe::whereHas('affectationCours', function($q) use ($enseignant) {
            $q->where('enseignant_id', $enseignant->id);
        })->withCount('eleves')->distinct()->orderBy('nom')->get();

        return view('enseignant.groupes.index', compact('enseignant', 'groupes'));
    }

    public function showGroupe(Groupe $groupe)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { /* Handle error */ abort(403, 'Profil Enseignant non trouvé.'); }

        // Authorization: Ensure this teacher is actually assigned to this group
        $isAssignedToGroup = $groupe->affectationCours()->where('enseignant_id', $enseignant->id)->exists();
        if (!$isAssignedToGroup) {
            abort(403, 'Accès non autorisé à ce groupe.');
        }

        // Get matieres taught by THIS enseignant in THIS group
        $affectationsDansCeGroupe = AffectationCours::where('enseignant_id', $enseignant->id)
                                     ->where('groupe_id', $groupe->id)
                                     ->with('matiere') // Eager load the matiere for each assignment
                                     ->get();

        $groupe->load('eleves'); // Load students for display

        return view('enseignant.groupes.show', [
            'groupe' => $groupe,
            'enseignant' => $enseignant,
            'affectations' => $affectationsDansCeGroupe, // <-- change key to 'affectations'
        ]);
    }


    // --- Evaluations (scoped to the teacher) ---
    public function listEvaluations(Request $request)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { /* Handle error */ abort(403, 'Profil Enseignant non trouvé.'); }

        $teacherAssignments = AffectationCours::where('enseignant_id', $enseignant->id)
                                              ->select('matiere_id', 'groupe_id')
                                              ->distinct()
                                              ->get();
        $evaluations = collect();
        if (!$teacherAssignments->isEmpty()) {
            $evaluationsQuery = Evaluation::with(['matiere', 'groupe']);
            $evaluationsQuery->where(function ($query) use ($teacherAssignments) {
                foreach ($teacherAssignments as $assignment) {
                    $query->orWhere(function ($subQuery) use ($assignment) {
                        $subQuery->where('matiere_id', $assignment->matiere_id)
                                 ->where('groupe_id', $assignment->groupe_id);
                    });
                }
            });

            if ($request->filled('matiere_id_filter')) { $evaluationsQuery->where('matiere_id', $request->input('matiere_id_filter')); }
            if ($request->filled('groupe_id_filter')) { $evaluationsQuery->where('groupe_id', $request->input('groupe_id_filter')); }
            if ($request->filled('type_evaluation_filter')) { $evaluationsQuery->where('type_evaluation', $request->input('type_evaluation_filter')); }

            $evaluations = $evaluationsQuery->latest('date_evaluation')->paginate(10);
        }

        $assignedMatiereIds = $teacherAssignments->pluck('matiere_id')->unique()->toArray();
        $assignedGroupeIds = $teacherAssignments->pluck('groupe_id')->unique()->toArray();
        $matieresForFilter = Matiere::whereIn('id', $assignedMatiereIds)->orderBy('nom_matiere')->get(['id', 'nom_matiere', 'code_matiere']);
        $groupesForFilter = Groupe::whereIn('id', $assignedGroupeIds)->orderBy('nom')->get(['id', 'nom', 'niveau']);
        $typesEvaluationForFilter = Evaluation::select('type_evaluation')->whereIn('matiere_id', $assignedMatiereIds)->whereIn('groupe_id', $assignedGroupeIds)->distinct()->orderBy('type_evaluation')->pluck('type_evaluation');

        return view('enseignant.evaluations.index', compact('enseignant', 'evaluations', 'matieresForFilter', 'groupesForFilter', 'typesEvaluationForFilter'));
    }

    public function createEvaluation()
    {
        // $this->authorize('manage_own_evaluations'); // Using Gate/Policy is better
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant || !$enseignant->user->can('create_evaluations')) { // Example direct permission check
             abort(403, 'Action non autorisée.');
        }


        $affectations = AffectationCours::where('enseignant_id', $enseignant->id)
                                       ->with(['matiere', 'groupe'])
                                       ->get();
        // Pluck distinct matieres and groupes from the teacher's assignments
        $matieres = $affectations->pluck('matiere')->filter()->unique('id')->sortBy('nom_matiere');
        $groupes = $affectations->pluck('groupe')->filter()->unique('id')->sortBy('nom');
        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre'];

        return view('enseignant.evaluations.create', compact('enseignant', 'matieres', 'groupes', 'typesEvaluationOptions'));
    }

    public function storeEvaluation(Request $request)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant || !$enseignant->user->can('create_evaluations')) {
             abort(403, 'Action non autorisée.');
        }
        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre'];

        $validatedData = $request->validate([
            'titre_evaluation' => 'required|string|max:255',
            'type_evaluation' => ['required', Rule::in($typesEvaluationOptions)],
            'date_evaluation' => 'required|date',
            'bareme_total' => 'required|integer|min:1|max:1000',
            'matiere_id' => ['required', 'exists:matieres,id'],
            'groupe_id' => ['required', 'exists:groupes,id'],
        ]);

        // Authorization: Check if this enseignant is assigned to teach this matiere in this groupe
        $isAssigned = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $validatedData['matiere_id'])
            ->where('groupe_id', $validatedData['groupe_id'])
            ->exists();

        if (!$isAssigned) {
            return back()->withErrors(['groupe_id' => 'Vous n\'êtes pas assigné pour enseigner cette matière dans le groupe sélectionné.'])->withInput();
        }

        Evaluation::create($validatedData); // matiere_id and groupe_id are directly in validatedData

        return redirect()->route('enseignant.evaluations.index')->with('success', 'Évaluation ajoutée avec succès.');
    }

    public function editEvaluation(Evaluation $evaluation)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { abort(403, 'Profil enseignant non associé.'); }

        // Authorization check
        $isAssigned = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('groupe_id', $evaluation->groupe_id)
            ->exists();

        if (!$isAssigned && !$enseignant->user->can('edit_evaluations')) { // Allow if has general edit perm
            Log::error("ENSEIGNANT AUTH FAIL (edit): Enseignant ID {$enseignant->id} tried to edit Eval ID {$evaluation->id}. Eval M:{$evaluation->matiere_id}, G:{$evaluation->groupe_id}");
            abort(403, 'Accès non autorisé à modifier cette évaluation.');
        }

        $affectations = AffectationCours::where('enseignant_id', $enseignant->id)->with(['matiere', 'groupe'])->get();
        $matieres = $affectations->pluck('matiere')->filter()->unique('id')->sortBy('nom_matiere');
        $groupes = $affectations->pluck('groupe')->filter()->unique('id')->sortBy('nom');
        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre'];

        return view('enseignant.evaluations.edit', compact('enseignant', 'evaluation', 'matieres', 'groupes', 'typesEvaluationOptions'));
    }

    public function updateEvaluation(Request $request, Evaluation $evaluation)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { abort(403, 'Profil enseignant non associé.'); }

        $isAssignedToOriginal = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $evaluation->matiere_id) // Check against original eval's matiere/groupe
            ->where('groupe_id', $evaluation->groupe_id)
            ->exists();

        if (!$isAssignedToOriginal && !$enseignant->user->can('edit_evaluations')) {
             abort(403, 'Accès non autorisé à modifier cette évaluation (original).');
        }

        $typesEvaluationOptions = ['Contrôle Continu', 'Examen Final', 'Projet', 'Autre'];
        $validatedData = $request->validate([
            'titre_evaluation' => 'required|string|max:255',
            'type_evaluation' => ['required', Rule::in($typesEvaluationOptions)],
            'date_evaluation' => 'required|date',
            'bareme_total' => 'required|integer|min:1|max:1000',
            'matiere_id' => ['required', 'exists:matieres,id'],
            'groupe_id' => ['required', 'exists:groupes,id'],
        ]);

        // Authorization: Check if teacher is assigned to the NEW matiere/groupe combination
        $isAssignedToNew = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $validatedData['matiere_id'])
            ->where('groupe_id', $validatedData['groupe_id'])
            ->exists();
        if (!$isAssignedToNew) {
            return back()->withErrors(['groupe_id' => 'Vous n\'êtes pas assigné pour enseigner la nouvelle combinaison matière/groupe.'])->withInput();
        }

        $evaluation->update($validatedData);
        return redirect()->route('enseignant.evaluations.index')->with('success', 'Évaluation mise à jour.');
    }

    public function destroyEvaluation(Evaluation $evaluation)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { abort(403, 'Profil enseignant non associé.'); }

        $isAssigned = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('groupe_id', $evaluation->groupe_id)
            ->exists();

        if (!$isAssigned && !$enseignant->user->can('delete_evaluations')) {
             abort(403, 'Accès non autorisé à supprimer cette évaluation.');
        }

        // Consider deleting associated Notes
        // Note::where('evaluation_id', $evaluation->id)->delete();
        $evaluation->delete();
        return redirect()->route('enseignant.evaluations.index')->with('success', 'Évaluation supprimée.');
    }


    // --- Notes Management (scoped to teacher's evaluations) ---
    public function manageNotesForEvaluation(Evaluation $evaluation)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { abort(403, 'Profil enseignant non associé.'); }

        // Authorization: Ensure this evaluation is one the teacher can manage notes for
        $canManage = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('groupe_id', $evaluation->groupe_id)
            ->exists();

        if (!$canManage && !$enseignant->user->can('manage_own_notes')) {
             abort(403, 'Accès non autorisé à gérer les notes pour cette évaluation.');
        }

        $evaluation->load(['groupe.eleves', 'notes', 'matiere']);
        $eleves = $evaluation->groupe ? $evaluation->groupe->eleves()->orderBy('nom')->orderBy('prenom')->get() : collect();
        $existingNotes = $evaluation->notes->keyBy('eleve_id');

        return view('enseignant.notes.manage', compact('enseignant', 'evaluation', 'eleves', 'existingNotes'));
    }

    public function storeNotesForEvaluation(Request $request, Evaluation $evaluation)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { abort(403, 'Profil enseignant non associé.'); }

        $canManage = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('groupe_id', $evaluation->groupe_id)
            ->exists();

        if (!$canManage && !$enseignant->user->can('manage_own_notes')) {
             abort(403, 'Accès non autorisé à enregistrer les notes.');
        }

        $baremeTotal = $evaluation->bareme_total;
        $request->validate([
            'notes' => 'present|array', // notes array must be present, can be empty
            'notes.*.note_obtenue' => ['nullable', 'numeric', 'min:0', "max:$baremeTotal"],
            'notes.*.observations' => 'nullable|string|max:500',
        ]);

        $eleveIdsInGroupe = $evaluation->groupe ? $evaluation->groupe->eleves->pluck('id')->toArray() : [];

        foreach ($request->input('notes', []) as $eleveId => $noteData) {
            // Ensure we are only saving notes for students actually in the evaluation's group
            if (!in_array($eleveId, $eleveIdsInGroupe)) {
                Log::warning("Attempt to save note for Eleve ID {$eleveId} not in Evaluation ID {$evaluation->id}'s group.");
                continue;
            }

            $noteValue = $noteData['note_obtenue'] ?? null;
            $observationValue = $noteData['observations'] ?? null;

            // Only update/create if there's a note or an observation to save
            // Or if the intention is to clear an existing note/observation by submitting empty values
            if ($noteValue !== null || $observationValue !== null) {
                Note::updateOrCreate(
                    [
                        'evaluation_id' => $evaluation->id,
                        'eleve_id' => $eleveId,
                    ],
                    [
                        'note_obtenue' => $noteValue,
                        'observations' => $observationValue,
                        // 'saisie_par_user_id' => Auth::id(), // Optional: Track who entered/updated
                    ]
                );
            } else {
                // If both note and observation are empty/null, delete any existing note for this student/evaluation
                Note::where('evaluation_id', $evaluation->id)->where('eleve_id', $eleveId)->delete();
            }
        }
        return redirect()->route('enseignant.evaluations.index')
                         ->with('success', 'Notes enregistrées avec succès.');
    }
    public function viewNotesForEvaluation(Evaluation $evaluation)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { abort(403, 'Profil enseignant non associé.'); }

        // Authorization: Ensure this evaluation is one the teacher can view notes for
        $canView = AffectationCours::where('enseignant_id', $enseignant->id)
            ->where('matiere_id', $evaluation->matiere_id)
            ->where('groupe_id', $evaluation->groupe_id)
            ->exists();

        // Also check if the teacher has the general permission to view notes
        if (!$canView && !$enseignant->user->can('view_notes')) { // Using 'view_notes' permission
             abort(403, 'Accès non autorisé à consulter les notes pour cette évaluation.');
        }

        $evaluation->load(['groupe.eleves' => function ($query) {
            $query->orderBy('nom')->orderBy('prenom'); // Order students
        }, 'notes', 'matiere']);

        $eleves = $evaluation->groupe ? $evaluation->groupe->eleves : collect();
        $notesData = $evaluation->notes->keyBy('eleve_id'); // Key notes by eleve_id for easy lookup

        return view('enseignant.notes.view', compact('enseignant', 'evaluation', 'eleves', 'notesData'));
    }
    
    public function listAllNotes(Request $request)
    {
        $enseignant = $this->getLoggedInEnseignant();
        if (!$enseignant) { abort(403, 'Profil enseignant non associé.'); }

        if (!$enseignant->user->can('view_notes')) {
            abort(403, 'Accès non autorisé.');
        }

        // Get IDs of evaluations linked to this teacher's assignments
        $teacherAssignments = AffectationCours::where('enseignant_id', $enseignant->id)
                                          ->select('matiere_id', 'groupe_id')
                                          ->distinct()
                                          ->get();

    $evaluationIds = collect();
    if (!$teacherAssignments->isEmpty()) {
        $evalQuery = Evaluation::query();
        $evalQuery->where(function ($query) use ($teacherAssignments) {
            foreach ($teacherAssignments as $assignment) {
                $query->orWhere(function ($subQuery) use ($assignment) {
                    $subQuery->where('matiere_id', $assignment->matiere_id)
                             ->where('groupe_id', $assignment->groupe_id);
                });
            }
        });
        $evaluationIds = $evalQuery->pluck('id');
    }

    // Start building the query for notes
    $query = Note::whereIn('evaluation_id', $evaluationIds)
                 ->with([
                     'eleve' => function ($query) {
                         $query->select('id', 'nom', 'prenom');
                     },
                     'evaluation.matiere' => function ($query) {
                         $query->select('id', 'nom_matiere', 'code_matiere');
                     },
                     'evaluation.groupe' => function ($query) {
                         $query->select('id', 'nom', 'niveau', 'annee_scolaire');
                     }
                 ])
                 ->join('evaluations', 'notes.evaluation_id', '=', 'evaluations.id')
                 ->join('groupes', 'evaluations.groupe_id', '=', 'groupes.id')
                 ->select('notes.*', 'groupes.annee_scolaire as evaluation_annee_scolaire');

    // --- Apply Filters ---
    if ($request->filled('groupe_filter')) {
        $query->where('evaluations.groupe_id', $request->input('groupe_filter'));
    }

    if ($request->filled('annee_scolaire_filter')) {
        $query->where('groupes.annee_scolaire', $request->input('annee_scolaire_filter'));
    }

    if ($request->filled('matiere_filter')) { // <<< NEW MATIERE FILTER
        $query->where('evaluations.matiere_id', $request->input('matiere_filter'));
    }

    if ($request->filled('eleve_filter')) { // <<< NEW ELEVE FILTER (by ID)
        $query->where('notes.eleve_id', $request->input('eleve_filter'));
    }

    $query->orderBy('groupes.annee_scolaire', 'desc')
          ->orderBy('evaluations.date_evaluation', 'desc')
          ->orderBy('notes.updated_at', 'desc');

    $allNotes = $query->paginate(20);

    // --- Data for Filter Dropdowns ---
    $assignedMatiereIds = $teacherAssignments->pluck('matiere_id')->unique()->toArray();
    $assignedGroupeIds = $teacherAssignments->pluck('groupe_id')->unique()->toArray();

    $matieresForFilter = Matiere::whereIn('id', $assignedMatiereIds)
        ->orderBy('nom_matiere')
        ->get(['id', 'nom_matiere', 'code_matiere']);

    $groupesForFilter = Groupe::whereIn('id', $assignedGroupeIds)
        ->orderBy('annee_scolaire', 'desc')
        ->orderBy('nom')
        ->get(['id', 'nom', 'niveau', 'annee_scolaire']);

    $anneesScolairesForFilter = Groupe::whereIn('id', $assignedGroupeIds)
        ->select('annee_scolaire')
        ->whereNotNull('annee_scolaire')
        ->distinct()
        ->orderBy('annee_scolaire', 'desc')
        ->pluck('annee_scolaire');

    // Get Eleves relevant to the teacher's assignments (for Eleve filter dropdown)
    $elevesForFilter = Eleve::whereIn('groupe_id', $assignedGroupeIds)
        ->orderBy('nom')->orderBy('prenom')
        ->get(['id', 'nom', 'prenom']);

    return view('enseignant.notes.index_all', compact(
        'enseignant',
        'allNotes',
        'groupesForFilter',
        'anneesScolairesForFilter',
        'matieresForFilter',
        'elevesForFilter'
    ));
}
}