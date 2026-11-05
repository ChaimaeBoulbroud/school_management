@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Affecter la Matière: ' . ($matiere->nom_matiere ?? $matiere->nom))
@section('page-title', 'Affecter la Matière') {{-- More specific title --}}

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            {{-- Use the $matiere variable here as well --}}
            <h5 class="mb-0">Affecter: <span class="fw-bold text-primary">{{ $matiere->nom_matiere ?? $matiere->nom }}</span></h5>
            <small class="text-muted">Sélectionnez un groupe et un enseignant pour cette matière.</small>
        </div>
        <div class="card-body">
            {{-- The form action should be correct as it uses $matiere->id --}}
            <form method="POST" action="{{ route('admin.matieres.affectations.store', $matiere->id) }}">
                @csrf

                <div class="mb-3">
                    <label for="groupe_id" class="form-label fw-semibold">Groupe <span class="text-danger">*</span></label>
                    <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
                        <option value="">Choisir un groupe...</option>
                        {{-- $groupes variable (plural) is correctly passed from controller for the loop --}}
                        @foreach($groupes as $groupe_option)
                            <option value="{{ $groupe_option->id }}" {{ old('groupe_id') == $groupe_option->id ? 'selected' : '' }}>
                                {{ $groupe_option->nom }} (Niveau: {{ $groupe_option->niveau }})
                            </option>
                        @endforeach
                    </select>
                    @error('groupe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="enseignant_id" class="form-label fw-semibold">Enseignant <span class="text-danger">*</span></label>
                    <select class="form-select @error('enseignant_id') is-invalid @enderror" id="enseignant_id" name="enseignant_id" required>
                        <option value="">Choisir un enseignant...</option>
                        {{-- $enseignants variable (plural) is correctly passed from controller for the loop --}}
                        @foreach($enseignants as $enseignant_option)
                            <option value="{{ $enseignant_option->id }}" {{ old('enseignant_id') == $enseignant_option->id ? 'selected' : '' }}>
                                {{ $enseignant_option->getFullNameAttribute() }}
                            </option>
                        @endforeach
                    </select>
                    @error('enseignant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.matieres.show', $matiere->id) }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-link me-1"></i> Affecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection