@extends('layouts.app') {{-- Or a specific parent layout if you have one --}}
@section('title', 'Tableau de Bord Parent')
@section('page-title', 'Mes Enfants')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Bienvenue, {{ $guardian->getFullNameAttribute() ?? Auth::user()->name }}!</h4>
            <p class="text-muted">Voici la liste de vos enfants inscrits et leurs informations scolaires.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Mes Enfants</h5>
        </div>
        <div class="card-body p-0">
            @if($children->isEmpty())
                <div class="text-center p-5">
                    <p>Vous n'avez actuellement aucun enfant associé à votre compte.</p>
                    {{-- Optional: Link to contact admin --}}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom Complet de l'Enfant</th>
                                <th>Niveau Actuel</th>
                                <th>Groupe Actuel</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($children as $child)
                                <tr>
                                    <td class="fw-medium">{{ $child->prenom }} {{ $child->nom }}</td>
                                    <td>{{ $child->groupe->niveau ?? 'N/A' }}</td>
                                    <td>{{ $child->groupe->nom ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('parent.child.details', ['eleve' => $child->id]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Voir Détails & Notes
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection