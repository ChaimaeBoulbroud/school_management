@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Matières')
@section('page-title', 'Liste des Matières')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            <form method="GET" action="{{ route('admin.matieres.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0" id="filterForm">
                <div class="btn-group">
                    <input type="text" name="search_filter" class="form-control form-control-sm" placeholder="Nom, Code..." value="{{ request('search_filter') }}" style="min-width: 250px;">
                </div>
                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.matieres.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>

            <div>
                <a href="{{ route('admin.matieres.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Ajouter Matière
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if(session('success')) <div class="alert alert-success m-3">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger m-3">{{ session('error') }}</div> @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom Complet de la Matière</th> {{-- Changed header --}}
                            <th>Code Matière</th>
                            <th class="text-center">Masse Horaire</th>
                            <th class="text-center">Coefficient</th>
                            <th class="text-center">Actions</th> {{-- Reduced colspan in empty case by 1 --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($matieres as $matiere)
                        <tr>
                            <td>
                                <a href="{{ route('admin.matieres.show', $matiere->id) }}">{{ $matiere->nom_matiere ?? 'N/A' }}</a>
                            </td>
                            <td>{{ $matiere->code_matiere ?? 'N/A' }}</td>
                            <td class="text-center">{{ $matiere->masse_horaire ?? 'N/A' }}</td>
                            <td class="text-center">{{ $matiere->coefficient ?? 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.matieres.show', $matiere->id) }}" class="btn btn-sm btn-outline-info me-1" title="Voir Détails"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.matieres.edit', $matiere->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('admin.matieres.destroy', $matiere->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr? Cela pourrait affecter les affectations de cours et les évaluations.');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4">Aucune matière trouvée.</td></tr> {{-- Adjusted colspan --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($matieres->hasPages())
        <div class="card-footer bg-light py-2">{{ $matieres->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection