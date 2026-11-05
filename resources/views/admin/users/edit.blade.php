@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier l\'Utilisateur')
@section('page-title', 'Modifier: ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Modifier: {{ $user->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.users._form', [
                    'user' => $user,
                    'allRoles' => $allRoles ?? collect(),
                    'userAssignedRoles' => $userAssignedRoles ?? []
                ])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection