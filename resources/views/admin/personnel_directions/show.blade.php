@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Détails Personnel: ' . $personnelDirection->getFullNameAttribute())
@section('page-title', 'Profil du Membre du Personnel')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $personnelDirection->getFullNameAttribute() }}</h5>
            <div>
                <a href="{{ route('admin.personnel_directions.edit', $personnelDirection->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier Profil
                </a>
                <a href="{{ route('admin.personnel_directions.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                    <h6>Informations Personnelles</h6>
                    <dl class="row">
                        <dt class="col-sm-4">Nom Complet:</dt>
                        <dd class="col-sm-8">{{ $personnelDirection->getFullNameAttribute() }}</dd>

                        <dt class="col-sm-4">CIN:</dt>
                        <dd class="col-sm-8">{{ $personnelDirection->CIN ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Téléphone:</dt>
                        <dd class="col-sm-8">{{ $personnelDirection->telephone ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Adresse:</dt>
                        <dd class="col-sm-8">{{ $personnelDirection->adresse ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">RIB:</dt>
                        <dd class="col-sm-8">{{ $personnelDirection->rib ?? 'N/A' }}</dd>
                    </dl>
                    <hr>
                    <h6>Compte Utilisateur</h6>
                    @if($personnelDirection->user)
                        <dl class="row">
                            <dt class="col-sm-4">Email du Compte:</dt>
                            <dd class="col-sm-8">{{ $personnelDirection->user->email }}</dd>
                            <dt class="col-sm-4">Compte Créé le:</dt>
                            <dd class="col-sm-8">{{ $personnelDirection->user->created_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    @else
                        <p class="text-muted">Aucun compte utilisateur associé.</p>
                         <a href="{{ route('admin.users.create', ['profile_type' => 'personnel_direction', 'profile_id' => $personnelDirection->id]) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-user-plus me-1"></i> Créer un compte
                        </a>
                    @endif
                </div>
                <div class="col-md-5">
                    <div class="text-center p-3">
                        <i class="fas fa-user-tie fa-7x text-secondary opacity-50"></i>
                    </div>
                </div>
            </div>
             <hr>
            <small class="text-muted">Profil créé le: {{ $personnelDirection->created_at->format('d/m/Y H:i') }}, dernière modification: {{ $personnelDirection->updated_at->format('d/m/Y H:i') }}</small>
        </div>
         <div class="card-footer text-end">
            <form action="{{ route('admin.personnel_directions.destroy', $personnelDirection->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre du personnel?');">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer ce Profil
                </button>
            </form>
        </div>
    </div>
</div>
@endsection