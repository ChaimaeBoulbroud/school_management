@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Enseignants')
@section('page-title', 'Liste des Enseignants')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            <form method="GET" action="{{ route('admin.enseignants.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0" id="filterForm">
                <div class="btn-group">
                    <input type="text" name="nom_filter" class="form-control form-control-sm" placeholder="Nom ou Prénom..." value="{{ request('nom_filter') }}" style="min-width: 200px;">
                </div>
                <div class="btn-group">
                    <input type="text" name="cin_filter" class="form-control form-control-sm" placeholder="CIN..." value="{{ request('cin_filter') }}" style="min-width: 150px;">
                </div>
                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.enseignants.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>

            <div>
                <a href="{{ route('admin.enseignants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Ajouter Enseignant
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
                            <th>Date Recrutement</th>
                            <th>Compte Utilisateur</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enseignants as $enseignant)
                        <tr>
                            <td>
                                <a href="{{ route('admin.enseignants.show', $enseignant->id) }}">{{ $enseignant->getFullNameAttribute() }}</a>
                            </td>
                            <td>{{ $enseignant->CIN ?? 'N/A' }}</td>
                            <td>{{ $enseignant->telephone ?? 'N/A' }}</td>
                            <td>{{ $enseignant->date_recrutement ? \Carbon\Carbon::parse($enseignant->date_recrutement)->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @if ($enseignant->user)
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> {{ $enseignant->user->email }}</span>
                                    {{-- Optional: Link to edit the user account:
                                    <a href="{{ route('admin.users.edit', $enseignant->user->id) }}" class="btn btn-xs btn-outline-secondary ms-1" title="Gérer le compte">
                                        <i class="fas fa-user-cog"></i>
                                    </a>
                                    --}}
                                @else
                                    <a href="{{ route('admin.enseignants.createAccountForm', ['enseignant' => $enseignant->id]) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-plus-circle me-1"></i> Créer compte
                                    </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.enseignants.show', $enseignant->id) }}" class="btn btn-sm btn-outline-info me-1" title="Voir Détails"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.enseignants.edit', $enseignant->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('admin.enseignants.destroy', $enseignant->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr? Cela pourrait affecter les affectations de cours.');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">Aucun enseignant trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($enseignants->hasPages())
        <div class="card-footer bg-light py-2">{{ $enseignants->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection