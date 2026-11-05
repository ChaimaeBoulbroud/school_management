@extends('layouts.app')
@section('title', 'Affecter une Matière au Groupe: ' . $groupe->nom)
@section('page-title', 'Affecter une Matière à ce Groupe')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Affecter une Matière à <span class="fw-bold text-primary">{{ $groupe->nom }}</span></h5>
            <small class="text-muted">Sélectionnez une matière et un enseignant pour ce groupe.</small>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.groupes.affectations.store', $groupe->id) }}">
                @csrf

                <div class="mb-3">
                    <label for="matiere_id" class="form-label fw-semibold">Matière <span class="text-danger">*</span></label>
                    <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                        <option value="">Choisir une matière...</option>
                        @foreach($matieres as $matiere_option)
                            <option value="{{ $matiere_option->id }}" {{ old('matiere_id') == $matiere_option->id ? 'selected' : '' }}>
                                {{ $matiere_option->nom_matiere }} ({{ $matiere_option->code_matiere }})
                            </option>
                        @endforeach
                    </select>
                    @error('matiere_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="enseignant_id" class="form-label fw-semibold">Enseignant <span class="text-danger">*</span></label>
                    <select class="form-select @error('enseignant_id') is-invalid @enderror" id="enseignant_id" name="enseignant_id" required>
                        <option value="">Choisir un enseignant...</option>
                        @foreach($enseignants as $enseignant_option)
                            <option value="{{ $enseignant_option->id }}" {{ old('enseignant_id') == $enseignant_option->id ? 'selected' : '' }}>
                                {{ $enseignant_option->nom }} {{ $enseignant_option->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('enseignant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.groupes.show', $groupe->id) }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-link me-1"></i> Affecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection