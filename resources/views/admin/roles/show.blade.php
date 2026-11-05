@extends('layouts.app')

@section('title', 'Détails du Rôle: ' . $role->nom)
@section('page-title', 'Détails du Rôle')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">{{ $role->nom }}</h5>
            <div>
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nom du Rôle :</dt>
                <dd class="col-sm-9">{{ $role->nom }}</dd>

                <dt class="col-sm-3">Créé le :</dt>
                <dd class="col-sm-9">{{ $role->created_at->format('d/m/Y H:i:s') }}</dd>

                <dt class="col-sm-3">Dernière Modification :</dt>
                <dd class="col-sm-9">{{ $role->updated_at->format('d/m/Y H:i:s') }}</dd>

                <dt class="col-sm-3">Permissions Attribuées :</dt>
                <dd class="col-sm-9">
                    @if(!empty($role->permissions) && is_array($role->permissions))
                        <ul class="list-unstyled mb-0">
                            @foreach($role->permissions as $permissionKey)
                                <li>
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    {{-- Affiche le label lisible de la permission --}}
                                    {{ $permissions[$permissionKey] ?? ucfirst(str_replace('_', ' ', $permissionKey)) }}
                                    <em><small class="text-muted">({{ $permissionKey }})</small></em>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        Aucune permission spécifique attribuée à ce rôle.
                    @endif
                </dd>
            </dl>
        </div>
        <div class="card-footer text-end">
            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer ce Rôle
                </button>
            </form>
        </div>
    </div>
</div>
@endsection