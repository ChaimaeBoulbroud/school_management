@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Utilisateurs')
@section('page-title', 'Gérez vos Utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            {{-- Add filters here if needed, similar to Eleves --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0">
                <input type="text" name="search_filter" class="form-control form-control-sm" placeholder="Nom ou Email..." value="{{ request('search_filter') }}" style="min-width: 200px;">
                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Ajouter un Utilisateur
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
                            <th>Email</th>
                            <th>Rôles</th>
                            <th>Créé le</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user_item) {{-- Renamed $user to $user_item to avoid conflict with global $user --}}
                        <tr>
                            <td>{{ $user_item->name }}</td>
                            <td>{{ $user_item->email }}</td>
                            <td>
                                @forelse($user_item->getRoleNames() as $roleName)
                                    <span class="badge bg-primary me-1">{{ $roleName }}</span>
                                @empty
                                    <span class="badge bg-secondary">Aucun rôle</span>
                                @endforelse
                            </td>
                             <td>
                                @if($user_item->created_at)
                                    {{ $user_item->created_at->format('d/m/Y') }}
                                @else
                                    N/A {{-- Or 'Date inconnue', or leave blank --}}
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- No 'show' view for users in this basic setup, edit serves that purpose --}}
                                <a href="{{ route('admin.users.edit', $user_item->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('admin.users.destroy', $user_item->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4">Aucun utilisateur trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
        <div class="card-footer bg-light py-2">{{ $users->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>
@endsection