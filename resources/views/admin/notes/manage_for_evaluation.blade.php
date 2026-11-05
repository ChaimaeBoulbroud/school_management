@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Saisie des Notes pour: ' . $evaluation->titre_evaluation)
@section('page-title', 'Saisie/Modification des Notes')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Évaluation: {{ $evaluation->titre_evaluation }}</h5>
            <p class="mb-0 small text-muted">
                Matière: {{ $evaluation->matiere->nom_matiere ?? $evaluation->matiere->nom }} |
                Groupe: {{ $evaluation->groupe->nom }} ({{ $evaluation->groupe->niveau }}) |
                Date: {{ $evaluation->date_evaluation->format('d/m/Y') }} |
                Barème: /{{ $evaluation->bareme_total }}
            </p>
        </div>
        <form action="{{ route('admin.notes.storeManagedForEvaluation', $evaluation->id) }}" method="POST">
            @csrf
            <div class="card-body p-0">
                @if(session('success')) <div class="alert alert-success m-3">{{ session('success') }}</div> @endif
                @if($errors->any())
                    <div class="alert alert-danger m-3">
                        <p class="fw-bold">Veuillez corriger les erreurs suivantes :</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Nom de l'Élève</th>
                                <th style="width: 15%;" class="text-center">Note / {{ $evaluation->bareme_total }}</th>
                                <th>Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($studentsWithNotes as $index => $eleve)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $eleve->getFullNameAttribute() }}
                                        <input type="hidden" name="notes[{{ $eleve->id }}][eleve_id]" value="{{ $eleve->id }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01"
                                               name="notes[{{ $eleve->id }}][note_obtenue]"
                                               class="form-control form-control-sm @error('notes.'.$eleve->id.'.note_obtenue') is-invalid @enderror"
                                               value="{{ old('notes.'.$eleve->id.'.note_obtenue', $eleve->current_note->note_obtenue ?? '') }}"
                                               min="0" max="{{ $evaluation->bareme_total }}" placeholder="Ex: 15.5">
                                        @error('notes.'.$eleve->id.'.note_obtenue')
                                            <div class="invalid-feedback d-block small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text"
                                               name="notes[{{ $eleve->id }}][observations]"
                                               class="form-control form-control-sm @error('notes.'.$eleve->id.'.observations') is-invalid @enderror"
                                               value="{{ old('notes.'.$eleve->id.'.observations', $eleve->current_note->observations ?? '') }}"
                                               placeholder="Optionnel...">
                                        @error('notes.'.$eleve->id.'.observations')
                                            <div class="invalid-feedback d-block small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-4">
                                        Aucun élève dans le groupe <a href="{{ route('admin.groupes.show', $evaluation->groupe_id) }}">{{ $evaluation->groupe->nom }}</a>
                                        ou ce groupe n'a pas encore d'élèves.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($studentsWithNotes->count() > 0)
            <div class="card-footer text-end">
                <a href="{{ route('admin.evaluations.show', $evaluation->id) }}" class="btn btn-outline-secondary me-2">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Enregistrer Toutes les Notes
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection