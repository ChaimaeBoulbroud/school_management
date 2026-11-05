@extends('layouts.app') {{-- Or your specific enseignant layout --}}
@section('title', 'Saisie des Notes pour: ' . $evaluation->titre_evaluation)
@section('page-title', 'Saisie/Modification des Notes')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Évaluation: <span class="fw-bold text-primary">{{ $evaluation->titre_evaluation }}</span></h4>
            <p class="text-muted">
                Matière: {{ $evaluation->matiere->nom_matiere ?? $evaluation->matiere->nom }} |
                Groupe: {{ $evaluation->groupe->nom ?? 'N/A' }} |
                Date: {{ $evaluation->date_evaluation ? \Carbon\Carbon::parse($evaluation->date_evaluation)->format('d/m/Y') : 'N/A' }} |
                Barème: {{ $evaluation->bareme_total }}
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <p class="mb-0"><strong>Des erreurs sont survenues :</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Saisir les Notes des Élèves</h5>
        </div>
        <form action="{{ route('enseignant.evaluations.notes.store', $evaluation->id) }}" method="POST">
            @csrf
            <div class="card-body p-0">
                @if($eleves->isEmpty())
                    <p class="text-center text-muted p-4">Aucun élève dans le groupe <span class="fw-bold">{{ $evaluation->groupe->nom ?? 'N/A' }}</span> pour cette évaluation.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 35%;">Nom Complet de l'Élève</th>
                                    <th style="width: 15%;" class="text-center">Note / {{ $evaluation->bareme_total }}</th>
                                    <th style="width: 45%;">Observations (Optionnel)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($eleves as $index => $eleve)
                                    @php
                                        $existingNote = $existingNotes->get($eleve->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $eleve->getFullNameAttribute() }}</td>
                                        <td>
                                            <input type="number" step="0.25" min="0" max="{{ $evaluation->bareme_total }}"
                                                   class="form-control form-control-sm @error('notes.'.$eleve->id.'.note_obtenue') is-invalid @enderror"
                                                   name="notes[{{ $eleve->id }}][note_obtenue]"
                                                   value="{{ old('notes.'.$eleve->id.'.note_obtenue', $existingNote->note_obtenue ?? '') }}"
                                                   placeholder="Ex: 15.5">
                                            @error('notes.'.$eleve->id.'.note_obtenue')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text"
                                                   class="form-control form-control-sm @error('notes.'.$eleve->id.'.observations') is-invalid @enderror"
                                                   name="notes[{{ $eleve->id }}][observations]"
                                                   value="{{ old('notes.'.$eleve->id.'.observations', $existingNote->observations ?? '') }}"
                                                   placeholder="Ex: Bon travail">
                                            @error('notes.'.$eleve->id.'.observations')
                                                <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if(!$eleves->isEmpty())
            <div class="card-footer text-end">
                <a href="{{ route('enseignant.evaluations.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour aux Évaluations
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Enregistrer les Notes
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection