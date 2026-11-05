@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Notes')
@section('page-title', 'Liste des Notes Enregistrées')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            <form method="GET" action="{{ route('admin.notes.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0" id="filterForm">
                {{-- Eleve Filter --}}
                <div class="btn-group">
                    <select name="eleve_id_filter" class="form-select form-select-sm" style="min-width: 200px;">
                        <option value="">Tous les Élèves</option>
                        @foreach($eleves as $eleve)
                            <option value="{{ $eleve->id }}" {{ request('eleve_id_filter') == $eleve->id ? 'selected' : '' }}>
                                {{ $eleve->getFullNameAttribute() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Evaluation Filter --}}
                <div class="btn-group">
                    <select name="evaluation_id_filter" class="form-select form-select-sm" style="min-width: 250px;">
                        <option value="">Toutes les Évaluations</option>
                        @foreach($evaluations as $evaluation)
                            <option value="{{ $evaluation->id }}" {{ request('evaluation_id_filter') == $evaluation->id ? 'selected' : '' }}>
                                {{ Str::limit($evaluation->titre_evaluation, 30) }} ({{ $evaluation->matiere->nom_matiere ?? $evaluation->matiere->nom }}) - {{ $evaluation->date_evaluation->format('d/m/y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Matiere Filter (filters notes based on the matiere of their evaluation) --}}
                <div class="btn-group">
                    <select name="matiere_id_filter" class="form-select form-select-sm" style="min-width: 180px;">
                        <option value="">Toutes les Matières</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ request('matiere_id_filter') == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom_matiere ?? $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Groupe Filter (filters notes based on the groupe of their evaluation) --}}
                <div class="btn-group">
                    <select name="groupe_id_filter" class="form-select form-select-sm" style="min-width: 180px;">
                        <option value="">Tous les Groupes</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" {{ request('groupe_id_filter') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }} ({{ $groupe->niveau }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.notes.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>

            <div>
                {{-- Link to a page where admin can select an evaluation to manage its notes --}}
                @can('manage_notes')
                <a href="{{ route('admin.evaluations.index') }}" class="btn btn-primary">
                    <i class="fas fa-tasks me-1"></i> Gérer Notes par Évaluation
                </a>
                @endcan
            </div>
        </div>

        <div class="card-body p-0">
            @if(session('success')) <div class="alert alert-success m-3">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger m-3">{{ session('error') }}</div> @endif

            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Élève</th>
                            <th>Évaluation (Matière)</th>
                            <th>Groupe (Éval)</th>
                            <th class="text-center">Note / Barème</th>
                            <th>Date Saisie</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notes as $note)
                        <tr>
                            <td>{{ $note->eleve->getFullNameAttribute() ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.evaluations.show', $note->evaluation_id) }}">{{ $note->evaluation->titre_evaluation ?? 'N/A' }}</a>
                                <small class="d-block text-muted">{{ $note->evaluation->matiere->nom_matiere ?? $note->evaluation->matiere->nom ?? 'N/A Matière' }}</small>
                            </td>
                            <td>{{ $note->evaluation->groupe->nom ?? 'N/A' }} ({{ $note->evaluation->groupe->niveau ?? 'N/A' }})</td>
                            <td class="text-center fw-bold">
                                {{ $note->note_obtenue }} / {{ $note->evaluation->bareme_total ?? 'N/A' }}
                            </td>
                            <td>{{ $note->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                @can('manage_notes')
                                <a href="{{ route('admin.notes.edit', $note->id) }}" class="btn btn-xs btn-outline-warning me-1" title="Modifier Note"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('admin.notes.destroy', $note->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Supprimer Note" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note?');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">Aucune note trouvée pour les filtres actuels.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($notes->hasPages())
        <div class="card-footer bg-light py-2">{{ $notes->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection