@extends('layouts.app') {{-- Or your specific enseignant layout --}}
@section('title', 'Planifier une Nouvelle Évaluation')
@section('page-title', 'Nouvelle Évaluation (Enseignant)')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Planifier une Nouvelle Évaluation</h4>
            <p class="text-muted">Remplissez les détails ci-dessous. Vous ne pouvez créer des évaluations que pour les matières et groupes auxquels vous êtes assigné.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('enseignant.evaluations.store') }}" method="POST">
                @csrf

                {{-- You can use a shared _form partial if you create one,
                     or put the fields directly here as shown below. --}}

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="titre_evaluation" class="form-label fw-semibold">Titre de l'Évaluation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('titre_evaluation') is-invalid @enderror" id="titre_evaluation" name="titre_evaluation" value="{{ old('titre_evaluation') }}" required>
                        @error('titre_evaluation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type_evaluation" class="form-label fw-semibold">Type d'Évaluation <span class="text-danger">*</span></label>
                        <select class="form-select @error('type_evaluation') is-invalid @enderror" id="type_evaluation" name="type_evaluation" required>
                            <option value="">Choisir un type...</option>
                            @if(isset($typesEvaluationOptions))
                                @foreach($typesEvaluationOptions as $type)
                                    <option value="{{ $type }}" {{ old('type_evaluation') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('type_evaluation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_evaluation" class="form-label fw-semibold">Date de l'Évaluation <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_evaluation') is-invalid @enderror" id="date_evaluation" name="date_evaluation" value="{{ old('date_evaluation') }}" required>
                        @error('date_evaluation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bareme_total" class="form-label fw-semibold">Barème Total <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('bareme_total') is-invalid @enderror" id="bareme_total" name="bareme_total" value="{{ old('bareme_total', '20') }}" required min="1">
                        @error('bareme_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="matiere_id" class="form-label fw-semibold">Matière Concernée <span class="text-danger">*</span></label>
                        <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                            <option value="">Choisir une matière...</option>
                            @if(isset($matieres) && $matieres->count() > 0)
                                @foreach($matieres as $matiere_option)
                                    <option value="{{ $matiere_option->id }}"
                                        {{ old('matiere_id') == $matiere_option->id ? 'selected' : '' }}
                                    >
                                        {{ $matiere_option->nom_matiere ?? $matiere_option->nom }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Vous n'êtes assigné à aucune matière.</option>
                            @endif
                        </select>
                        @error('matiere_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="groupe_id" class="form-label fw-semibold">Groupe Concerné <span class="text-danger">*</span></label>
                        <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
                            <option value="">Choisir un groupe...</option>
                            @if(isset($groupes) && $groupes->count() > 0)
                                @foreach($groupes as $groupe_option)
                                    <option value="{{ $groupe_option->id }}" {{ old('groupe_id') == $groupe_option->id ? 'selected' : '' }}>
                                        {{ $groupe_option->nom }} (Niveau: {{ $groupe_option->niveau }})
                                    </option>
                                @endforeach
                            @else
                                 <option value="" disabled>Vous n'êtes assigné à aucun groupe.</option>
                            @endif
                        </select>
                        {{-- Custom error for assignment check from controller --}}
                        @error('groupe_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('enseignant.evaluations.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-times me-1"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer Évaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection