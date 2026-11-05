@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Évaluations')
@section('page-title', 'Liste des Évaluations')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            <form method="GET" action="{{ route('admin.evaluations.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0" id="filterForm">
                <div class="btn-group">
                    <select name="matiere_id_filter" class="form-select form-select-sm" style="min-width: 200px;">
                        <option value="">Toutes les Matières</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ request('matiere_id_filter') == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom_matiere ?? $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="btn-group">
                    <select name="groupe_id_filter" class="form-select form-select-sm" style="min-width: 200px;">
                        <option value="">Tous les Groupes</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" {{ request('groupe_id_filter') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->nom }} ({{ $groupe->niveau }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="btn-group">
                    <select name="type_evaluation_filter" class="form-select form-select-sm" style="min-width: 180px;">
                        <option value="">Tous les Types</option>
                         @foreach($typesEvaluation as $type) {{-- Passed from controller --}}
                            <option value="{{ $type }}" {{ request('type_evaluation_filter') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.evaluations.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>

            <div>
                @can('create_evaluations')
                <a href="{{ route('admin.evaluations.create') }}" class="btn btn-primary">
                    <i class="fas fa-file-medical me-1"></i> Ajouter Évaluation
                </a>
                @endcan
            </div>
        </div>

        <div class="card-body p-0">
            @if(session('success')) <div class="alert alert-success m-3">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger m-3">{{ session('error') }}</div> @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Matière</th>
                            <th>Groupe</th>
                            <th class="text-center">Barème</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($evaluations as $evaluation)
                        <tr>
                            <td>
                                <a href="{{ route('admin.evaluations.show', $evaluation->id) }}">{{ $evaluation->titre_evaluation }}</a>
                            </td>
                            <td>{{ $evaluation->type_evaluation }}</td>
                            <td>{{ $evaluation->date_evaluation->format('d/m/Y') }}</td>
                            <td>{{ $evaluation->matiere->nom_matiere ?? $evaluation->matiere->nom ?? 'N/A' }}</td>
                            <td>{{ $evaluation->groupe->nom ?? 'N/A' }} ({{ $evaluation->groupe->niveau ?? 'N/A' }})</td>
                            <td class="text-center">{{ $evaluation->bareme_total }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.evaluations.show', $evaluation->id) }}" class="btn btn-sm btn-outline-info me-1" title="Voir Détails"><i class="fas fa-eye"></i></a>
                                @can('manage_notes')
                                    <a href="{{ route('admin.notes.manageForEvaluation', $evaluation->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Gérer les Notes"><i class="fas fa-marker"></i></a>
                                @endcan
                                @can('edit_evaluations')
                                <a href="{{ route('admin.evaluations.edit', $evaluation->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                                @endcan
                                @can('delete_evaluations')
                                <form action="{{ route('admin.evaluations.destroy', $evaluation->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr? Supprimer cette évaluation supprimera aussi toutes les notes associées.');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4">Aucune évaluation trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($evaluations->hasPages())
        <div class="card-footer bg-light py-2">{{ $evaluations->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection