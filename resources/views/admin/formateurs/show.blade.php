@extends('layouts.app')

@section('title', 'Détails du Formateur: ' . $formateur->prenom . ' ' . $formateur->nom)
@section('page-title', 'Détails du Formateur')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">{{ $formateur->prenom }} {{ $formateur->nom }}</h5>
            <div>
                <a href="{{ route('admin.formateurs.edit', $formateur->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                <a href="{{ route('admin.formateurs.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-4">Nom Complet :</dt>
                        <dd class="col-sm-8">{{ $formateur->prenom }} {{ $formateur->nom }}</dd>

                        {{-- Si tu avais un matricule_formateur dans ta migration/modèle --}}
                        @if($formateur->matricule_formateur)
                        <dt class="col-sm-4">Matricule Formateur :</dt>
                        <dd class="col-sm-8">{{ $formateur->matricule_formateur }}</dd>
                        @endif

                        <dt class="col-sm-4">Email :</dt>
                        <dd class="col-sm-8">{{ $formateur->email }}</dd>

                        <dt class="col-sm-4">Téléphone :</dt>
                        <dd class="col-sm-8">{{ $formateur->telephone ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Grade :</dt>
                        <dd class="col-sm-8">{{ $formateur->grade }}</dd>

                        <dt class="col-sm-4">Date de Recrutement :</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($formateur->date_recrutement)->format('d/m/Y') }}</dd>

                        {{-- Si le formateur est lié à un compte User (ce que nous n'avons pas fait pour l'instant) --}}
                        {{-- @if($formateur->user)
                        <dt class="col-sm-4">Compte Utilisateur (Email) :</dt>
                        <dd class="col-sm-8">{{ $formateur->user->email }}</dd>
                        @endif --}}

                        <dt class="col-sm-4">Profil Créé le :</dt>
                        <dd class="col-sm-8">{{ $formateur->created_at->format('d/m/Y H:i:s') }}</dd>

                        <dt class="col-sm-4">Dernière Modification :</dt>
                        <dd class="col-sm-8">{{ $formateur->updated_at->format('d/m/Y H:i:s') }}</dd>
                    </dl>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-chalkboard-teacher fa-7x text-secondary opacity-50 mb-3"></i>
                        <h6 class="text-muted">Profil Formateur</h6>
                    </div>
                </div>
            </div>

            {{-- Section pour afficher les modules enseignés (relation many-to-many) --}}
            @if($formateur->relationLoaded('modules') && $formateur->modules->count() > 0)
                <hr>
                <h5>Modules Enseignés par ce Formateur :</h5>
                <ul class="list-group list-group-flush">
                    @foreach($formateur->modules as $module)
                        <li class="list-group-item">
                            {{ $module->nom_module }}
                        </li>
                    @endforeach
                </ul>
            @elseif($formateur->relationLoaded('modules'))
                <p>Ce formateur n'est actuellement associé à aucun module spécifique.</p>
            @endif

            {{-- Plus tard : Section pour afficher les affectations (groupes/modules enseignés) --}}
            {{-- @if($formateur->relationLoaded('enseignements') && $formateur->enseignements->count() > 0)
                <hr>
                <h5>Affectations d'Enseignement :</h5>
                <ul class="list-group list-group-flush">
                    @foreach($formateur->enseignements as $groupe)
                        <li class="list-group-item">
                            Groupe: <a href="#">{{ $groupe->nom }}</a> -
                            Module: <a href="#">{{ $groupe->pivot->module_id ? \App\Models\Module::find($groupe->pivot->module_id)->nom_module : 'Module non spécifié' }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif --}}

        </div>
        <div class="card-footer text-end">
            <form action="{{ route('admin.formateurs.destroy', $formateur->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer ce Formateur
                </button>
            </form>
        </div>
    </div>
</div>
@endsection