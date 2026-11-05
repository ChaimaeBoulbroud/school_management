@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Détail de la Note')
@section('page-title', 'Détail de la Note')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Note pour: {{ $note->eleve->getFullNameAttribute() }}</h5>
            <div>
                <a href="{{ route('admin.notes.edit', $note->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                <a href="{{ session('notes_index_filters') ? route('admin.notes.index', session('notes_index_filters')) : route('admin.notes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Élève:</dt>
                <dd class="col-sm-9">{{ $note->eleve->getFullNameAttribute() }}</dd>

                <dt class="col-sm-3">Évaluation:</dt>
                <dd class="col-sm-9">
                    <a href="{{ route('admin.evaluations.show', $note->evaluation_id) }}">
                        {{ $note->evaluation->titre_evaluation }}
                    </a>
                </dd>

                <dt class="col-sm-3">Matière:</dt>
                <dd class="col-sm-9">{{ $note->evaluation->matiere->nom_matiere ?? $note->evaluation->matiere->nom }}</dd>

                <dt class="col-sm-3">Groupe (Éval):</dt>
                <dd class="col-sm-9">{{ $note->evaluation->groupe->nom }} ({{ $note->evaluation->groupe->niveau }})</dd>

                <dt class="col-sm-3">Note Obtenue:</dt>
                <dd class="col-sm-9 fw-bold">{{ $note->note_obtenue }} / {{ $note->evaluation->bareme_total }}</dd>

                <dt class="col-sm-3">Observations:</dt>
                <dd class="col-sm-9">{{ $note->observations ?: 'Aucune' }}</dd>

                <dt class="col-sm-3">Saisie le:</dt>
                <dd class="col-sm-9">{{ $note->created_at->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Modifiée le:</dt>
                <dd class="col-sm-9">{{ $note->updated_at->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection