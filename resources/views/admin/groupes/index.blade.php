@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Groupes')
@section('page-title', 'Liste des Groupes Scolaires')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Gestion des Groupes</h4>
        @can('create_groupes')
        <a href="{{ route('admin.groupes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un Groupe
        </a>
        @endcan
    </div>

    {{-- Optional: Add filter form for annee_scolaire here if needed --}}
    {{-- <form method="GET" action="{{ route('admin.groupes.index') }}" class="mb-3"> ... </form> --}}

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($groupes->isEmpty())
                <p class="text-center p-4">Aucun groupe trouvé.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom du Groupe</th>
                                <th>Niveau</th>
                                <th>Année Scolaire</th> {{-- <<< NEW COLUMN HEADER --}}
                                <th class="text-center">Nombre d'Élèves</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupes as $groupe)
                                <tr>
                                    <td class="fw-medium">{{ $groupe->nom }}</td>
                                    <td>{{ $groupe->niveau }}</td>
                                    <td>{{ $groupe->annee_scolaire ?? 'N/A' }}</td> {{-- <<< DISPLAY ANNEE SCOLAIRE --}}
                                    <td class="text-center">{{ $groupe->eleves_count ?? $groupe->eleves->count() }}</td>
                                    <td class="text-center">
                                        @can('view_groupes')
                                        <a href="{{ route('admin.groupes.show', $groupe->id) }}" class="btn btn-sm btn-outline-info me-1" title="Voir Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('edit_groupes')
                                        <a href="{{ route('admin.groupes.edit', $groupe->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        @endcan
                                        @can('delete_groupes')
                                        <form action="{{ route('admin.groupes.destroy', $groupe->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
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
            @endif
        </div>
        @if ($groupes->hasPages())
            <div class="card-footer bg-light py-2">
                {{ $groupes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection