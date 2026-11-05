@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Parents/Gardiens')
@section('page-title', 'Liste des Parents/Gardiens')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            <form method="GET" action="{{ route('admin.guardians.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0" id="filterForm">
                <div class="btn-group">
                    <input type="text" name="nom_filter" class="form-control form-control-sm" placeholder="Nom ou Prénom..." value="{{ request('nom_filter') }}" style="min-width: 200px;">
                </div>
                <div class="btn-group">
                    <input type="text" name="cin_filter" class="form-control form-control-sm" placeholder="CIN..." value="{{ request('cin_filter') }}" style="min-width: 150px;">
                </div>
                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.guardians.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>

            <div>
                <a href="{{ route('admin.guardians.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Ajouter Parent/Gardien
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
                            <th>Compte Utilisateur (Email)</th>
                            <th>Nb. Enfants</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guardians as $guardian)
                        <tr>
                            <td>{{ $guardian->getFullNameAttribute() }}</td>
                            <td>{{ $guardian->CIN ?? 'N/A' }}</td>
                            <td>{{ $guardian->telephone ?? 'N/A' }}</td>
                            <td>
                                @if($guardian->user)
                                    <i class="fas fa-check-circle text-success me-1" title="Compte Actif"></i>
                                    {{ $guardian->user->email }}
                                @else
                                    <i class="fas fa-times-circle text-muted me-1" title="Pas de Compte"></i>
                                    N/A
                                @endif
                            </td>
                            <td>{{ $guardian->eleves_count ?? $guardian->eleves()->count() }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.guardians.show', $guardian->id) }}" class="btn btn-sm btn-outline-info me-1" title="Voir Détails"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.guardians.edit', $guardian->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('admin.guardians.destroy', $guardian->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr? Ce parent/gardien et son compte utilisateur (si existant et configuré) pourraient être affectés.');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">Aucun parent/gardien trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($guardians->hasPages())
        <div class="card-footer bg-light py-2">{{ $guardians->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection