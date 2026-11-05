@extends('layouts.app')
@section('title', 'Affecter une Matière à l\'Enseignant: ' . $enseignant->getFullNameAttribute())
@section('page-title', 'Affecter une Matière à cet Enseignant')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Affecter une Matière à <span class="fw-bold text-primary">{{ $enseignant->getFullNameAttribute() }}</span></h5>
            <small class="text-muted">Sélectionnez une matière et un groupe pour cet enseignant.</small>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.enseignants.affectations.store', $enseignant->id) }}">
                @csrf

                <div class="mb-3">
                    <label for="matiere_id" class="form-label fw-semibold">Matière <span class="text-danger">*</span></label>
                    <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                        <option value="">Choisir une matière...</option>
                        @foreach($matieres as $matiere_option)
                            <option value="{{ $matiere_option->id }}" {{ old('matiere_id') == $matiere_option->id ? 'selected' : '' }}>
                                {{ $matiere_option->nom_matiere ?? $matiere_option->nom }} ({{ $matiere_option->code_matiere ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('matiere_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="groupe_id" class="form-label fw-semibold">Groupe <span class="text-danger">*</span></label>
                    <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
                        <option value="">Choisir un groupe...</option>
                        @foreach($groupes as $groupe_option)
                            <option value="{{ $groupe_option->id }}" {{ old('groupe_id') == $groupe_option->id ? 'selected' : '' }}>
                                {{ $groupe_option->nom }} (Niveau: {{ $groupe_option->niveau }})
                            </option>
                        @endforeach
                    </select>
                    @error('groupe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.enseignants.show', $enseignant->id) }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-link me-1"></i> Affecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection