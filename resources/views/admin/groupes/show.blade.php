@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Détails du Groupe: ' . $groupe->nom)
@section('page-title', 'Détails du Groupe')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Informations sur le Groupe: <span class="fw-bold text-primary">{{ $groupe->nom }}</span></h5>
            <div>
                @can('edit_groupes')
                <a href="{{ route('admin.groupes.edit', $groupe->id) }}" class="btn btn-warning btn-sm me-1">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                @endcan
                <a href="{{ route('admin.groupes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nom du Groupe:</dt>
                <dd class="col-sm-9">{{ $groupe->nom }}</dd>

                <dt class="col-sm-3">Niveau:</dt>
                <dd class="col-sm-9">{{ $groupe->niveau }}</dd>

                <dt class="col-sm-3">Année Scolaire:</dt> {{-- <<< NEW FIELD --}}
                <dd class="col-sm-9">{{ $groupe->annee_scolaire ?? 'N/A' }}</dd> {{-- <<< DISPLAY ANNEE SCOLAIRE --}}

                <dt class="col-sm-3">Nombre d'Élèves:</dt>
                <dd class="col-sm-9">{{ $groupe->eleves_count ?? $groupe->eleves->count() }}</dd>

                <dt class="col-sm-3">Créé le:</dt>
                <dd class="col-sm-9">{{ $groupe->created_at ? $groupe->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                <dt class="col-sm-3">Modifié le:</dt>
                <dd class="col-sm-9">{{ $groupe->updated_at ? $groupe->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Élèves dans ce Groupe --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 text-primary">Élèves dans ce Groupe ({{ $groupe->eleves->count() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($groupe->eleves->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom Complet</th>
                                <th>Genre</th>
                                <th>Date de Naissance</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupe->eleves as $eleve)
                                <tr>
                                    <td>{{ $eleve->getFullNameAttribute() }}</td>
                                    <td>{{ $eleve->genre }}</td>
                                    <td>{{ $eleve->date_naissance ? \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.eleves.show', $eleve->id) }}" class="btn btn-xs btn-outline-info" title="Voir détails élève">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted p-3">Aucun élève n'est actuellement inscrit dans ce groupe.</p>
            @endif
        </div>
    </div>


    {{-- Section for Matières Affectées à ce Groupe --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    Matières Enseignées dans ce Groupe
                    <span class="badge bg-secondary rounded-pill ms-1">
                        {{ $groupe->affectationCours ? $groupe->affectationCours->count() : 0 }}
                    </span>
                </h5>
                @can('manage_affectations')
                <a href="{{ route('admin.groupes.affectations.create', $groupe->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Affecter Matière/Enseignant
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            @if($groupe->affectationCours && $groupe->affectationCours->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Matière</th>
                                <th>Enseignant Assigné</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupe->affectationCours as $affectation)
                                <tr>
                                    <td>
                                        @if($affectation->matiere)
                                            <a href="{{ route('admin.matieres.show', $affectation->matiere->id) }}">
                                                {{ $affectation->matiere->nom_matiere ?? $affectation->matiere->nom }}
                                                ({{ $affectation->matiere->code_matiere ?? 'N/A' }})
                                            </a>
                                        @else
                                            <span class="text-muted">Matière non spécifiée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($affectation->enseignant)
                                            <a href="{{ route('admin.enseignants.show', $affectation->enseignant->id) }}">
                                                {{ $affectation->enseignant->getFullNameAttribute() }}
                                            </a>
                                        @else
                                            <span class="text-muted">Enseignant non assigné</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @can('manage_affectations')
                                        <a href="{{ route('admin.affectations.edit', $affectation->id) }}?redirect_to=groupe_show&groupe_id={{ $groupe->id }}" class="btn btn-xs btn-outline-warning me-1" title="Modifier l'affectation">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.affectations.destroy', $affectation->id) }}?redirect_to=groupe_show&groupe_id={{ $groupe->id }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette affectation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Supprimer l'affectation">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted p-3">Aucune matière n'est actuellement affectée à ce groupe.</p>
            @endif
        </div>
    </div>


    {{-- Section for "Évaluations pour ce Groupe" --}}
    <div class="card shadow-sm">
        <div class="card-header">
             <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    Évaluations pour ce Groupe
                     <span class="badge bg-secondary rounded-pill ms-1">
                        {{ $groupe->evaluations ? $groupe->evaluations->count() : 0 }}
                    </span>
                </h5>
                @can('create_evaluations')
                <a href="{{ route('admin.evaluations.create', ['groupe_id' => $groupe->id]) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Ajouter Évaluation
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            @if($groupe->evaluations && $groupe->evaluations->count() > 0)
                <div class="table-responsive">
                     <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Matière</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th class="text-center">Barème</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupe->evaluations as $evaluation)
                            <tr>
                                <td>{{ $evaluation->titre_evaluation }}</td>
                                <td>{{ $evaluation->matiere->nom_matiere ?? ($evaluation->matiere->nom ?? 'N/A') }}</td>
                                <td>{{ $evaluation->type_evaluation }}</td>
                                <td>{{ $evaluation->date_evaluation ? \Carbon\Carbon::parse($evaluation->date_evaluation)->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-center">{{ $evaluation->bareme_total }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.evaluations.show', $evaluation->id) }}" class="btn btn-xs btn-outline-info me-1" title="Voir/Gérer Notes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit_evaluations')
                                    <a href="{{ route('admin.evaluations.edit', $evaluation->id) }}" class="btn btn-xs btn-outline-warning" title="Modifier Évaluation">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted p-3">Aucune évaluation planifiée pour ce groupe pour le moment.</p>
            @endif
        </div>
    </div>

</div>
@endsection