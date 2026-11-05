@extends('layouts.app') {{-- Or your admin layout, e.g., layouts.admin --}}
@section('title', 'Ajouter un Groupe')
@section('page-title', 'Nouveau Groupe')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informations du Groupe</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.groupes.store') }}" method="POST">
                @csrf

                @include('admin.groupes._form', ['groupe' => new \App\Models\Groupe()])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.groupes.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer Groupe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection