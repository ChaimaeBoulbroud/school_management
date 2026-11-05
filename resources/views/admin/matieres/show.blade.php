@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Détails de la Matière: ' . ($matiere->nom_matiere ?? $matiere->nom))
@section('page-title', 'Détails de la Matière')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Matière: <span class="fw-bold text-primary">{{ $matiere->nom_matiere ?? $matiere->nom }}</span></h5>
            <div>
                <a href="{{ route('admin.matieres.edit', $matiere->id) }}" class="btn btn-warning btn-sm me-1">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                <a href="{{ route('admin.matieres.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Basic Matière Details (Code, Nom, Masse Horaire, Coeff, etc.) --}}
            <dl class="row">
                <dt class="col-sm-3">Code Matière (ID Court):</dt>
                <dd class="col-sm-9">{{ $matiere->code_matiere ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Nom Complet:</dt>
                <dd class="col-sm-9">{{ $matiere->nom_matiere ?? $matiere->nom }}</dd>

                <dt class="col-sm-3">Masse Horaire:</dt>
                <dd class="col-sm-9">{{ $matiere->masse_horaire ?? 'N/A' }} heures</dd>

                <dt class="col-sm-3">Coefficient:</dt>
                <dd class="col-sm-9">{{ $matiere->coefficient ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Créée le:</dt>
                <dd class="col-sm-9">{{ $matiere->created_at ? $matiere->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                <dt class="col-sm-3">Modifiée le:</dt>
                <dd class="col-sm-9">{{ $matiere->updated_at ? $matiere->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
            </dl>

            <hr class="my-4">

            {{-- Section for Affectations (Assignments) --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0 text-primary">
                        Enseignée dans les Groupes / Par
                        <span class="badge bg-secondary rounded-pill ms-1">
                            {{ $matiere->affectationCours ? $matiere->affectationCours->count() : 0 }}
                        </span>
                    </h5>
                    @can('manage_affectations') {{-- Or your specific permission --}}
                    <a href="{{ route('admin.matieres.affectations.create', $matiere->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> Affecter cette matière
                    </a>
                    @endcan
                </div>

                @if($matiere->affectationCours && $matiere->affectationCours->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($matiere->affectationCours as $affectation)
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0"> {{-- ps-0 to align with card body padding --}}
                                <div>
                                    <strong>Groupe:</strong>
                                    <a href="{{ $affectation->groupe ? route('admin.groupes.show', $affectation->groupe->id) : '#' }}">{{ $affectation->groupe->nom ?? 'Groupe Inconnu' }}</a>
                                    (Niveau: {{ $affectation->groupe->niveau ?? 'N/A' }})
                                    <br>
                                    <small class="text-muted">
                                        <strong>Enseignant:</strong>
                                        <a href="{{ $affectation->enseignant ? route('admin.enseignants.show', $affectation->enseignant->id) : '#' }}">{{ $affectation->enseignant->getFullNameAttribute() ?? 'Enseignant Inconnu' }}</a>
                                    </small>
                                </div>
                                @can('manage_affectations') {{-- Or your specific permission for deleting --}}
                                <form action="{{ route('admin.affectations.destroy', $affectation->id) }}?redirect_to=matiere_show&matiere_id={{ $matiere->id }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette affectation ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Supprimer l'affectation">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    {{-- Example Edit Button for an Affectation
                                    <a href="{{ route('admin.affectations.edit', $affectation->id) }}?redirect_to=matiere_show&matiere_id={{ $matiere->id }}" class="btn btn-xs btn-outline-warning ms-1" title="Modifier l'affectation"><i class="fas fa-edit"></i></a>
                                    --}}
                                </form>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Cette matière n'est actuellement affectée à aucun groupe/enseignant.</p>
                @endif
            </div>

            <hr class="my-4">

            {{-- Section for Evaluations for this Matière (from your screenshot) --}}
            <div class="mb-4">
                 <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0 text-primary">
                        Évaluations pour cette Matière
                        <span class="badge bg-secondary rounded-pill ms-1">
                            {{ $matiere->evaluations ? $matiere->evaluations->count() : 0 }}
                        </span>
                    </h5>
                    @can('create_evaluations') {{-- Or your specific permission --}}
                    {{-- Assuming a route to create an evaluation FOR this specific matiere --}}
                    <a href="{{ route('admin.evaluations.create', ['matiere_id' => $matiere->id]) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> Ajouter Évaluation
                    </a>
                    @endcan
                </div>
                @if($matiere->evaluations && $matiere->evaluations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Groupe</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Barème</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matiere->evaluations as $evaluation)
                                <tr>
                                    <td>{{ $evaluation->titre_evaluation }}</td>
                                    <td>{{ $evaluation->groupe->nom ?? 'Tous les groupes' }}</td> {{-- Assuming an evaluation can be for a specific groupe or all --}}
                                    <td>{{ $evaluation->type_evaluation }}</td>
                                    <td>{{ $evaluation->date_evaluation ? \Carbon\Carbon::parse($evaluation->date_evaluation)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $evaluation->bareme_total }}</td>
                                    <td>
                                        <a href="{{ route('admin.evaluations.show', $evaluation->id) }}" class="btn btn-xs btn-outline-primary" title="Voir/Gérer Notes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Add edit/delete for evaluation itself if needed --}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Aucune évaluation programmée pour cette matière pour le moment.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection