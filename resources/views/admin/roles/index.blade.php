@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Gestion des Rôles')
@section('page-title', 'Gérez les Rôles et Permissions') {{-- Optional --}}

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h4 class="text-primary mb-0">Liste des Rôles</h4>
            <p class="text-muted mb-0">Visualisez, créez, modifiez ou supprimez les rôles.</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mt-2 mt-md-0">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un Rôle
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($roles->isEmpty())
                <div class="text-center p-5">
                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                    <p>Aucun rôle n'a été créé pour le moment.</p>
                    <p><a href="{{ route('admin.roles.create') }}" class="btn btn-outline-primary btn-sm">Créer le premier rôle</a></p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Nom du Rôle</th>
                                <th style="width: 10%;">Guard</th>
                                <th style="width: 10%;" class="text-center">Utilisateurs</th>
                                <th style="width: 35%;">Permissions Assignées (Aperçu)</th>
                                <th style="width: 20%;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role_item) {{-- Changed to $role_item --}}
                                <tr>
                                    <td class="fw-medium">{{ $role_item->name }}</td>
                                    <td><span class="badge bg-secondary rounded-pill">{{ $role_item->guard_name }}</span></td>
                                    <td class="text-center">{{ $role_item->users_count }}</td>
                                    <td>
                                        @forelse($role_item->permissions->sortBy('name')->take(6) as $permission)
                                            <span class="badge bg-info text-dark me-1 mb-1 fw-normal">{{ ucfirst(str_replace('_', ' ', $permission->name)) }}</span>
                                        @empty
                                            <span class="badge bg-light text-dark fw-normal">Aucune permission</span>
                                        @endforelse
                                        @if($role_item->permissions->count() > 6)
                                           <span class="badge bg-light text-dark fw-normal">...et {{ $role_item->permissions->count() - 6 }} de plus</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.roles.edit', $role_item->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier {{ $role_item->name }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!in_array($role_item->name, ['Admin'])) {{-- Example: Prevent deleting core Admin role --}}
                                            <form action="{{ route('admin.roles.destroy', $role_item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer le rôle \'{{ $role_item->name }}\' ? Cette action ne peut pas être annulée.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer {{ $role_item->name }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                             <button type="button" class="btn btn-sm btn-outline-secondary disabled" title="Suppression non autorisée">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($roles->hasPages())
            <div class="card-footer bg-light py-2">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
{{-- Add any page-specific styles here if needed --}}
<style>
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush