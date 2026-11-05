@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion du Personnel de Direction')
@section('page-title', 'Liste du Personnel de Direction')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            <form method="GET" action="{{ route('admin.personnel_directions.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0" id="filterForm">
                <div class="btn-group">
                    <input type="text" name="nom_filter" class="form-control form-control-sm" placeholder="Nom ou Prénom..." value="{{ request('nom_filter') }}" style="min-width: 200px;">
                </div>
                <div class="btn-group">
                    <input type="text" name="cin_filter" class="form-control form-control-sm" placeholder="CIN..." value="{{ request('cin_filter') }}" style="min-width: 150px;">
                </div>
                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.personnel_directions.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>

            <div>
                <a href="{{ route('admin.personnel_directions.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Ajouter Personnel
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
                            <th>Nom Complet</th>
                            <th>CIN</th>
                            <th>Téléphone</th>
                            <th>RIB</th>
                            <th>Compte Utilisateur</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($personnels as $personnel) {{-- Variable name from controller --}}
                        <tr>
                            <td>
                                <a href="{{ route('admin.personnel_directions.show', $personnel->id) }}">{{ $personnel->getFullNameAttribute() }}</a>
                            </td>
                            <td>{{ $personnel->CIN ?? 'N/A' }}</td>
                            <td>{{ $personnel->telephone ?? 'N/A' }}</td>
                            <td>{{ $personnel->rib ?? 'N/A' }}</td>
                            <td>
                                @if ($personnel->user)
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> {{ $personnel->user->email }}</span>
                                @else
                                    <a href="{{ route('admin.personnel_directions.createAccountForm', $personnel->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-plus-circle me-1"></i> Créer compte
                                    </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.personnel_directions.show', $personnel->id) }}" class="btn btn-sm btn-outline-info me-1" title="Voir Détails"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.personnel_directions.edit', $personnel->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('admin.personnel_directions.destroy', $personnel->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr?');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">Aucun membre du personnel trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($personnels->hasPages())
        <div class="card-footer bg-light py-2">{{ $personnels->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection