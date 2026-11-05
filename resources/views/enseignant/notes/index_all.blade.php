@extends('layouts.app')
@section('title', 'Toutes Mes Notes Saisies')
@section('page-title', 'Consultation de Toutes les Notes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h4>Historique de Toutes les Notes Saisies</h4>
    </div>

    {{-- Filter Form --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('enseignant.notes.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="groupe_filter" class="form-label">Filtrer par Groupe</label>
                    <select name="groupe_filter" id="groupe_filter" class="form-select form-select-sm">
                        <option value="">Tous les Groupes</option>
                        @foreach($groupesForFilter as $groupe)
                            <option value="{{ $groupe->id }}" {{ request('groupe_filter') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }} ({{ $groupe->niveau }}) - {{ $groupe->annee_scolaire }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="matiere_filter" class="form-label">Filtrer par Matière</label>
                    <select name="matiere_filter" id="matiere_filter" class="form-select form-select-sm">
                        <option value="">Toutes les Matières</option>
                        @foreach($matieresForFilter as $matiere)
                            <option value="{{ $matiere->id }}" {{ request('matiere_filter') == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom_matiere ?? $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="annee_scolaire_filter" class="form-label">Filtrer par Année Scolaire</label>
                    <select name="annee_scolaire_filter" id="annee_scolaire_filter" class="form-select form-select-sm">
                        <option value="">Toutes les Années</option>
                        @foreach($anneesScolairesForFilter as $annee)
                            <option value="{{ $annee }}" {{ request('annee_scolaire_filter') == $annee ? 'selected' : '' }}>
                                {{ $annee }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="eleve_filter" class="form-label">Filtrer par Élève</label>
                    <select name="eleve_filter" id="eleve_filter" class="form-select form-select-sm">
                        <option value="">Tous les Élèves</option>
                        @foreach($elevesForFilter as $eleve)
                            <option value="{{ $eleve->id }}" {{ request('eleve_filter') == $eleve->id ? 'selected' : '' }}>
                                {{ $eleve->getFullNameAttribute() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mt-md-3 mt-lg-0">
                    <button type="submit" class="btn btn-sm btn-info">Appliquer Filtres</button>
                    <a href="{{ route('enseignant.notes.index') }}" class="btn btn-sm btn-secondary ms-2">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Liste des Notes @if(request()->hasAny(['groupe_filter', 'matiere_filter', 'annee_scolaire_filter', 'eleve_filter'])) <small class="text-muted fs-6">(Filtres appliqués)</small> @endif</h5>
        </div>
        <div class="card-body p-0">
            @if($allNotes->isEmpty())
                <p class="text-center text-muted p-4">Aucune note trouvée pour les filtres actuels.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Élève</th>
                                <th>Évaluation <small>(Titre)</small></th>
                                <th>Matière</th>
                                <th>Groupe <small>(Année)</small></th>
                                <th class="text-center">Note / Barème</th>
                                <th>Date Éval.</th>
                                <th>Observations</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allNotes as $note)
                                <tr>
                                    <td>{{ $note->eleve->getFullNameAttribute() ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('enseignant.evaluations.notes.manage', $note->evaluation_id) }}">
                                            {{ $note->evaluation->titre_evaluation ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $note->evaluation->matiere->nom_matiere ?? ($note->evaluation->matiere->nom ?? 'N/A') }}</td>
                                    <td>
                                        {{ $note->evaluation->groupe->nom ?? 'N/A' }}
                                        <small class="d-block text-muted">{{ $note->evaluation->groupe->annee_scolaire ?? '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ $note->note_obtenue ?? '-' }}</span> / {{ $note->evaluation->bareme_total ?? '-' }}
                                    </td>
                                    <td>{{ $note->evaluation->date_evaluation ? \Carbon\Carbon::parse($note->evaluation->date_evaluation)->format('d/m/Y') : '' }}</td>
                                    <td>{{ Str::limit($note->observations, 40) ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('enseignant.evaluations.notes.manage', ['evaluation' => $note->evaluation_id, 'highlight_eleve_id' => $note->eleve_id]) }}"
                                           class="btn btn-xs btn-outline-warning" title="Modifier cette note et les autres de l'évaluation">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($allNotes->hasPages())
        <div class="card-footer bg-light py-2">
            {{ $allNotes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection