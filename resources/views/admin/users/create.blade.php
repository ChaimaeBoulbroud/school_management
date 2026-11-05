@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Ajouter un Utilisateur')
@section('page-title', 'Nouvel Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informations de l'Utilisateur</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                {{-- Pass an empty user model and the roles collection --}}
                @include('admin.users._form', [
                    'user' => new \App\Models\User(),
                    'allRoles' => $allRoles ?? collect(),
                    'userAssignedRoles' => [] // No roles assigned yet for a new user
                ])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer Utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection