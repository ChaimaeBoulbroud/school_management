@extends('layouts.app') {{-- Or your admin layout, e.g., admin.layouts.app --}}
@section('title', 'Créer un Nouveau Rôle')
@section('page-title', 'Nouveau Rôle') {{-- Optional: if your layout uses a page-title section --}}

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4 class="text-primary">Créer un Nouveau Rôle</h4>
            <p class="text-muted">Définissez un nouveau rôle et assignez-lui les permissions appropriées.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                {{--
                    Pass an empty Role model for consistency if your partial expects $role->name.
                    groupedPermissions and rolePermissions are passed from RoleController@create
                --}}
                @include('admin.roles._form', [
                    'role' => new \Spatie\Permission\Models\Role(),
                    'groupedPermissions' => $groupedPermissions ?? [],
                    'rolePermissions' => [] // No permissions selected by default for a new role
                ])
            </form>
        </div>
    </div>
</div>
@endsection