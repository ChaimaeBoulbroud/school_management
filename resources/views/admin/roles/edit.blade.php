@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier le Rôle: ' . $role->name)
@section('page-title', 'Modifier Rôle') {{-- Optional --}}

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4 class="text-primary">Modifier le Rôle: <span class="fw-bold">{{ $role->name }}</span></h4>
             <p class="text-muted">Mettez à jour le nom du rôle et ses permissions.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @method('PUT')
                {{--
                    Pass the existing $role model.
                    groupedPermissions and rolePermissions are passed from RoleController@edit
                --}}
                @include('admin.roles._form', [
                    'role' => $role,
                    'groupedPermissions' => $groupedPermissions ?? [],
                    'rolePermissions' => $rolePermissions ?? []
                ])
            </form>
        </div>
    </div>
</div>
@endsection