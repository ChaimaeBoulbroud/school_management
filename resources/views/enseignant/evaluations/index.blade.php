@extends('layouts.app')
@section('title', 'Mes Évaluations')
@section('page-title', 'Liste de Mes Évaluations')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Mes Évaluations</h4>
            <p class="text-muted">Liste des évaluations pour vos matières et groupes assignés.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('enseignant.evaluations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Évaluation
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="matiere_id_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Toutes les matières</option>
                @foreach($matieresForFilter as $matiere)
                    <option value="{{ $matiere->id }}" {{ request('matiere_id_filter') == $matiere->id ? 'selected' : '' }}>
                        {{ $matiere->nom_matiere }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="groupe_id_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tous les groupes</option>
                @foreach($groupesForFilter as $groupe)
                    <option value="{{ $groupe->id }}" {{ request('groupe_id_filter') == $groupe->id ? 'selected' : '' }}>
                        {{ $groupe->nom }} ({{ $groupe->niveau }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="type_evaluation_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                @foreach($typesEvaluationForFilter as $type)
                    <option value="{{ $type }}" {{ request('type_evaluation_filter') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-outline-secondary w-100">Filtrer</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($evaluations->isEmpty())
                <div class="text-center p-5">
                    <p>Aucune évaluation trouvée pour vos matières/groupes assignés.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Matière</th>
                                <th>Groupe</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Bareme</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations as $evaluation)
                                <tr>
                                    <td>{{ $evaluation->titre_evaluation }}</td>
                                    <td>{{ $evaluation->matiere->nom_matiere ?? '-' }}</td>
                                    <td>{{ $evaluation->groupe->nom ?? '-' }}</td>
                                    <td>{{ $evaluation->type_evaluation }}</td>
                                    <td>{{ \Carbon\Carbon::parse($evaluation->date_evaluation)->format('d/m/Y') }}</td>
                                    <td>{{ $evaluation->bareme_total }}</td>
                                    <td>
                                        @can('manage_notes')
                                        <a href="{{ route('enseignant.evaluations.notes.manage', $evaluation->id) }}" class="btn btn-sm btn-info me-1" title="Gérer les Notes">
                                            <i class="fas fa-marker"></i> Notes
                                        </a>
                                        @endcan

                                        <a href="{{ route('enseignant.evaluations.edit', $evaluation->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('enseignant.evaluations.destroy', $evaluation->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Supprimer cette évaluation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $evaluations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection