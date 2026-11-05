@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier la Matière')
@section('page-title', 'Modifier: ' . ($matiere->nom_matiere ?? $matiere->nom) )

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Modifier: {{ $matiere->nom_matiere ?? $matiere->nom }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.matieres.update', $matiere->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.matieres._form', ['matiere' => $matiere])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.matieres.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection