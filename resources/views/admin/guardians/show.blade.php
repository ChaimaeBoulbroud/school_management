@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Détails Parent/Gardien: ' . $guardian->getFullNameAttribute())
@section('page-title', 'Détails du Parent/Gardien')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Profil de : {{ $guardian->getFullNameAttribute() }}</h5>
            <div>
                <a href="{{ route('admin.guardians.edit', $guardian->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                <a href="{{ route('admin.guardians.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                    <h6>Informations Personnelles</h6>
                    <dl class="row">
                        <dt class="col-sm-4">Nom Complet :</dt>
                        <dd class="col-sm-8">{{ $guardian->getFullNameAttribute() }}</dd>

                        <dt class="col-sm-4">Numéro CIN :</dt>
                        <dd class="col-sm-8">{{ $guardian->CIN ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Téléphone :</dt>
                        <dd class="col-sm-8">{{ $guardian->telephone ?? 'N/A' }}</dd>
                    </dl>
                    <hr>
                    <h6>Compte Utilisateur</h6>
                    @if($guardian->user)
                        <dl class="row">
                            <dt class="col-sm-4">Email du Compte :</dt>
                            <dd class="col-sm-8">{{ $guardian->user->email }}</dd>
                            <dt class="col-sm-4">Compte Créé le :</dt>
                            <dd class="col-sm-8">{{ $guardian->user->created_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    @else
                        <p class="text-muted">Aucun compte utilisateur associé.</p>
                        {{-- Optional: Button to create user account for this guardian --}}
                        {{-- <a href="{{ route('admin.users.create', ['profile_type' => 'guardian', 'profile_id' => $guardian->id]) }}" class="btn btn-sm btn-outline-success">Créer un compte</a> --}}
                    @endif
                </div>
                <div class="col-md-5">
                    <h6>Enfants Associés (Élèves)</h6>
                    @if($guardian->eleves && $guardian->eleves->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($guardian->eleves as $eleve)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.eleves.show', $eleve->id) }}">{{ $eleve->getFullNameAttribute() }}</a>
                                    <span class="badge bg-info rounded-pill">{{ $eleve->groupe->nom ?? 'N/A Groupe' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aucun élève associé à ce parent/gardien.</p>
                    @endif
                </div>
            </div>
             <hr>
            <small class="text-muted">Profil créé le: {{ $guardian->created_at->format('d/m/Y H:i') }}, dernière modification: {{ $guardian->updated_at->format('d/m/Y H:i') }}</small>
        </div>
        <div class="card-footer text-end">
            <form action="{{ route('admin.guardians.destroy', $guardian->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Attention ! Supprimer ce parent/gardien pourrait affecter les élèves liés si la contrainte n\'est pas `SET NULL` ou `CASCADE` pour leur `parent_id`. Êtes-vous sûr ?');">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer ce Parent/Gardien
                </button>
            </form>
        </div>
    </div>
</div>
@endsection