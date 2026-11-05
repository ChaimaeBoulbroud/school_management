@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Détails Évaluation: ' . $evaluation->titre_evaluation)
@section('page-title', 'Détails de l\'Évaluation')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $evaluation->titre_evaluation }}</h5>
            <div>
                @can('manage_notes')
                <a href="{{ route('admin.notes.manageForEvaluation', $evaluation->id) }}" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-marker me-1"></i> Gérer les Notes
                </a>
                @endcan
                @can('edit_evaluations')
                <a href="{{ route('admin.evaluations.edit', $evaluation->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                @endcan
                <a href="{{ route('admin.evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Titre:</dt>
                <dd class="col-sm-9">{{ $evaluation->titre_evaluation }}</dd>

                <dt class="col-sm-3">Type:</dt>
                <dd class="col-sm-9">{{ $evaluation->type_evaluation }}</dd>

                <dt class="col-sm-3">Date:</dt>
                <dd class="col-sm-9">{{ $evaluation->date_evaluation->format('d/m/Y') }}</dd>

                <dt class="col-sm-3">Matière:</dt>
                <dd class="col-sm-9">
                    @if($evaluation->matiere)
                    <a href="{{ route('admin.matieres.show', $evaluation->matiere_id) }}">
                        {{ $evaluation->matiere->nom_matiere ?? $evaluation->matiere->nom }}
                    </a>
                    @else
                        N/A
                    @endif
                </dd>

                <dt class="col-sm-3">Groupe:</dt>
                <dd class="col-sm-9">
                    @if($evaluation->groupe)
                    <a href="{{ route('admin.groupes.show', $evaluation->groupe_id) }}">
                        {{ $evaluation->groupe->nom }} ({{ $evaluation->groupe->niveau }})
                    </a>
                    @else
                        N/A
                    @endif
                </dd>

                <dt class="col-sm-3">Barème Total:</dt>
                <dd class="col-sm-9">{{ $evaluation->bareme_total }} points</dd>

                {{-- @if($evaluation->description_evaluation)
                <dt class="col-sm-3">Description:</dt>
                <dd class="col-sm-9">{{ $evaluation->description_evaluation }}</dd>
                @endif --}}

                <dt class="col-sm-3">Planifiée le:</dt>
                <dd class="col-sm-9">{{ $evaluation->created_at->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Modifiée le:</dt>
                <dd class="col-sm-9">{{ $evaluation->updated_at->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>
    </div>

    {{-- Section Notes pour cette évaluation --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Notes Enregistrées ({{ $evaluation->notes->count() }})</h6>
            @can('manage_notes')
            <a href="{{ route('admin.notes.manageForEvaluation', $evaluation->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit me-1"></i> Saisir/Modifier les Notes
            </a>
            @endcan
        </div>
        <div class="card-body p-0">
            @if($evaluation->notes->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Élève</th>
                            <th class="text-center">Note Obtenue</th>
                            <th>Observations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluation->notes->sortBy(function($note) { return $note->eleve->getFullNameAttribute(); }) as $note)
                        <tr>
                            <td>{{ $note->eleve->getFullNameAttribute() ?? 'N/A' }}</td>
                            <td class="text-center fw-bold">{{ $note->note_obtenue }} / {{ $evaluation->bareme_total }}</td>
                            <td>{{ Str::limit($note->observations, 70) ?? 'N/A' }}</td>
                            <td>
                                @can('manage_notes')
                                <a href="{{ route('admin.notes.edit', $note->id) }}" class="btn btn-xs btn-outline-warning" title="Modifier Note"><i class="fas fa-pencil-alt"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted p-3">Aucune note n'a encore été saisie pour cette évaluation.</p>
            @endif
        </div>
    </div>

</div>
@endsection