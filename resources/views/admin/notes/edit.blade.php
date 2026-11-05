@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier une Note')
@section('page-title', 'Modifier la Note de: ' . $note->eleve->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                Modifier la Note pour {{ $note->eleve->getFullNameAttribute() }} <br>
                <small class="text-muted">Évaluation: {{ $note->evaluation->titre_evaluation }} - {{ $note->evaluation->matiere->nom_matiere ?? $note->evaluation->matiere->nom }}</small>
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.notes.update', $note->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.notes._form', ['note' => $note])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ session('notes_index_filters') ? route('admin.notes.index', session('notes_index_filters')) : route('admin.notes.index') }}" class="btn btn-outline-secondary me-2">Annuler & Retour</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer la Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection