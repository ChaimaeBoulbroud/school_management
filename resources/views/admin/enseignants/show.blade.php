@extends('layouts.app')
@section('title', 'Détails Enseignant: ' . $enseignant->getFullNameAttribute())
@section('page-title', 'Profil de l\'Enseignant')

@section('content')
<div class="container-fluid">

    {{-- Informations sur l'Enseignant --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Informations sur l'Enseignant: <span class="fw-bold text-primary">{{ $enseignant->getFullNameAttribute() }}</span></h5>
            <div>
                <a href="{{ route('admin.enseignants.edit', $enseignant->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier Profil
                </a>
                <a href="{{ route('admin.enseignants.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Nom Complet:</dt>
                <dd class="col-sm-8">{{ $enseignant->getFullNameAttribute() }}</dd>
                <dt class="col-sm-4">CIN:</dt>
                <dd class="col-sm-8">{{ $enseignant->CIN ?? 'N/A' }}</dd>
                <dt class="col-sm-4">Téléphone:</dt>
                <dd class="col-sm-8">{{ $enseignant->telephone ?? 'N/A' }}</dd>
                <dt class="col-sm-4">Adresse:</dt>
                <dd class="col-sm-8">{{ $enseignant->adresse ?? 'N/A' }}</dd>
                <dt class="col-sm-4">Date de Recrutement:</dt>
                <dd class="col-sm-8">{{ $enseignant->date_recrutement ? \Carbon\Carbon::parse($enseignant->date_recrutement)->format('d/m/Y') : 'N/A' }}</dd>
            </dl>
            <hr>
            <h6>Compte Utilisateur</h6>
            @if($enseignant->user)
                <dl class="row">
                    <dt class="col-sm-4">Email du Compte:</dt>
                    <dd class="col-sm-8">{{ $enseignant->user->email }}</dd>
                    <dt class="col-sm-4">Compte Créé le:</dt>
                    <dd class="col-sm-8">{{ $enseignant->user->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            @else
                <p class="text-muted">Aucun compte utilisateur associé.</p>
                <a href="{{ route('admin.users.create', ['profile_type' => 'enseignant', 'profile_id' => $enseignant->id]) }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-user-plus me-1"></i> Créer un compte pour cet enseignant
                </a>
            @endif
            <hr>
            <small class="text-muted">Profil créé le: {{ $enseignant->created_at->format('d/m/Y H:i') }}, dernière modification: {{ $enseignant->updated_at->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    {{-- Matières Enseignées par cet Enseignant --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                Matières Enseignées par {{ $enseignant->getFullNameAttribute() }}
                <span class="badge bg-secondary rounded-pill ms-1">
                    {{ $enseignant->affectationCours ? $enseignant->affectationCours->count() : 0 }}
                </span>
            </h5>
            @can('manage_affectations')
            <a href="{{ route('admin.enseignants.affectations.create', $enseignant->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Affecter une Matière
            </a>
            @endcan
        </div>
        <div class="card-body p-0">
            @if($enseignant->affectationCours && $enseignant->affectationCours->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Matière</th>
                                <th>Groupe</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enseignant->affectationCours as $affectation)
                                <tr>
                                    <td>
                                        @if($affectation->matiere)
                                            <a href="{{ route('admin.matieres.show', $affectation->matiere->id) }}">
                                                {{ $affectation->matiere->nom_matiere ?? $affectation->matiere->nom }}
                                            </a>
                                        @else
                                            <span class="text-muted">Matière inconnue</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($affectation->groupe)
                                            <a href="{{ route('admin.groupes.show', $affectation->groupe->id) }}">
                                                {{ $affectation->groupe->nom ?? 'Groupe Inconnu' }}
                                                (Niveau: {{ $affectation->groupe->niveau ?? 'N/A' }})
                                            </a>
                                        @else
                                            <span class="text-muted">Groupe inconnu</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @can('manage_affectations')
                                        <form action="{{ route('admin.affectations.destroy', $affectation->id) }}?redirect_to=enseignant_show&enseignant_id={{ $enseignant->id }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Supprimer l'affectation">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        {{-- Uncomment if you want an edit button
                                        <a href="{{ route('admin.affectations.edit', $affectation->id) }}?redirect_to=enseignant_show&enseignant_id={{ $enseignant->id }}" class="btn btn-xs btn-outline-warning ms-1" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        --}}
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted p-3">Cet enseignant n'a actuellement aucune matière affectée.</p>
            @endif
        </div>
    </div>
</div>
@endsection