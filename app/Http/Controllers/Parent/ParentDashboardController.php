<?php

namespace App\Http\Controllers\Parent; // Note the namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Eleve; // Your Eleve model
use App\Models\Guardian; // Your Guardian model
use App\Models\Matiere; // Your Matiere model
use App\Models\AffectationCours; // Your AffectationCours model

class ParentDashboardController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated users with the 'Parent' role can access this
        $this->middleware(['auth', 'role:Parent']);
    }

    /**
     * Display the parent's dashboard listing their children.
     */
    public function index()
    {
        $user = Auth::user();
        $guardian = $user->parentGuardian; // Assumes parentGuardian() relationship on User model

        if (!$guardian) {
            // This parent user is not linked to a Guardian profile
            // Or the relationship name on User model is different
            // You might want to redirect them or show an error message
            abort(403, 'Profil parent non trouvé pour cet utilisateur.');
        }

        // Eager load children with their groupe
        $children = $guardian->eleves()->with('groupe')->get();

        return view('parent.dashboard', compact('children', 'guardian'));
    }

    /**
     * Display details for a specific child, including matieres and notes.
     */
    public function showChildDetails(Eleve $eleve) // Route model binding for Eleve
    {
        $user = Auth::user();
        $guardian = $user->parentGuardian;

        // Authorization: Ensure the logged-in parent is actually the parent of this eleve
        if (!$guardian || $eleve->parent_id !== $guardian->id) {
            abort(403, 'Accès non autorisé à ce profil élève.');
        }

        // Eager load necessary data for the child
        $eleve->load([
            'groupe',
            'notes.evaluation.matiere' // Get notes -> evaluation -> matiere for each note
        ]);

        // Get Matieres for the child's groupe
        $matieresDuGroupe = collect(); // Default to empty collection
        if ($eleve->groupe) {
            // Assuming AffectationCours links Enseignant, Matiere, and Groupe
            // We need matieres taught in this specific groupe
            $matieresDuGroupe = Matiere::whereHas('affectationCours', function ($query) use ($eleve) {
                $query->where('groupe_id', $eleve->groupe_id);
            })->distinct()->orderBy('nom_matiere')->get(); // Or your matiere name column
        }

        // Prepare data for the view (e.g., group notes by matiere)
        $notesParMatiere = [];
        if ($eleve->notes) {
            foreach ($eleve->notes as $note) {
                if ($note->evaluation && $note->evaluation->matiere) {
                    $matiereId = $note->evaluation->matiere->id;
                    if (!isset($notesParMatiere[$matiereId])) {
                        $notesParMatiere[$matiereId] = [
                            'matiere' => $note->evaluation->matiere,
                            'evaluations' => [],
                            'notes_count' => 0,
                            'total_bareme' => 0,
                            'total_obtenu' => 0,
                        ];
                    }
                    $notesParMatiere[$matiereId]['evaluations'][] = [
                        'titre' => $note->evaluation->titre_evaluation,
                        'date' => $note->evaluation->date_evaluation,
                        'note_obtenue' => $note->note_obtenue,
                        'bareme_total' => $note->evaluation->bareme_total,
                        'observations' => $note->observations,
                    ];
                    $notesParMatiere[$matiereId]['notes_count']++;
                    $notesParMatiere[$matiereId]['total_bareme'] += $note->evaluation->bareme_total;
                    $notesParMatiere[$matiereId]['total_obtenu'] += $note->note_obtenue;
                }
            }
        }


        return view('parent.child_details', compact('eleve', 'guardian', 'matieresDuGroupe', 'notesParMatiere'));
    }
}