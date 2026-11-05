@extends('layouts.app')
@section('title', 'Détails Utilisateur: ' . $user->name)
@section('page-title', 'Détails du Compte Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">{{ $user->name }}</h5>
            <div>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm me-2"><i class="fas fa-pencil-alt me-1"></i> Modifier</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Retour</a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nom Complet:</dt>
                <dd class="col-sm-9">{{ $user->name }}</dd>

                <dt class="col-sm-3">Email:</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>

                <dt class="col-sm-3">Rôles:</dt>
                <dd class="col-sm-9">
                    @forelse ($user->roles as $role)
                    <span class="badge bg-info me-1">{{ $role->nom }}</span>
                    @empty
                    Aucun rôle assigné.
                    @endforelse
                </dd>

                <dt class="col-sm-3">Compte créé le:</dt>
                <dd class="col-sm-9">{{ $user->created_at->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Dernière mise à jour:</dt>
                <dd class="col-sm-9">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>

                {{-- Afficher le profil lié (Stagiaire, Formateur, etc.) --}}
                @php
                $linkedProfile = null;
                $profileType = '';
                if ($user->stagiaire) { $linkedProfile = $user->stagiaire; $profileType = 'Stagiaire'; $profileRoute = 'admin.stagiaires.show';}
                elseif ($user->formateur) { $linkedProfile = $user->formateur; $profileType = 'Formateur'; $profileRoute = 'admin.formateurs.show';}
                // elseif ($user->conseiller) { ... }
                @endphp

                @if($linkedProfile)
                <dt class="col-sm-3">Profil {{ $profileType }} Lié:</dt>
                <dd class="col-sm-9">
                    
                        {{ $linkedProfile->prenom }} {{ $linkedProfile->nom }}
                        @if($profileType === 'Stagiaire' && $linkedProfile->matricule)
                        (Mat: {{ $linkedProfile->matricule }})
                        @elseif($profileType === 'Formateur' && $linkedProfile->matricule_formateur)
                        (Mat: {{ $linkedProfile->matricule_formateur }})
                        @endif
                    
                </dd>
                @endif
            </dl>
        </div>
        <div class="card-footer text-end">
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr ?');">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer ce Compte
                </button>
            </form>
        </div>
    </div>
</div>
@endsection