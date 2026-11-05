@extends('layouts.app')
@section('title', 'Modifier l\'évaluation')
@section('page-title', 'Modifier l\'évaluation')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Modifier l'évaluation</h4>
        </div>
    </div>

    <form method="POST" action="{{ route('enseignant.evaluations.update', $evaluation->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titre_evaluation" class="form-label">Titre de l'évaluation</label>
            <input type="text" class="form-control @error('titre_evaluation') is-invalid @enderror" id="titre_evaluation" name="titre_evaluation" value="{{ old('titre_evaluation', $evaluation->titre_evaluation) }}" required>
            @error('titre_evaluation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="type_evaluation" class="form-label">Type d'évaluation</label>
            <select class="form-select @error('type_evaluation') is-invalid @enderror" id="type_evaluation" name="type_evaluation" required>
                @foreach($typesEvaluationOptions as $type)
                    <option value="{{ $type }}" {{ old('type_evaluation', $evaluation->type_evaluation) == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('type_evaluation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date_evaluation" class="form-label">Date de l'évaluation</label>
            <input type="date" class="form-control @error('date_evaluation') is-invalid @enderror" id="date_evaluation" name="date_evaluation" value="{{ old('date_evaluation', $evaluation->date_evaluation ? \Carbon\Carbon::parse($evaluation->date_evaluation)->format('Y-m-d') : '') }}" required>
            @error('date_evaluation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="bareme_total" class="form-label">Barème total</label>
            <input type="number" class="form-control @error('bareme_total') is-invalid @enderror" id="bareme_total" name="bareme_total" value="{{ old('bareme_total', $evaluation->bareme_total) }}" min="1" max="1000" required>
            @error('bareme_total')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="matiere_id" class="form-label">Matière</label>
            <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                @foreach($matieres as $matiere)
                    <option value="{{ $matiere->id }}" {{ old('matiere_id', $evaluation->matiere_id) == $matiere->id ? 'selected' : '' }}>
                        {{ $matiere->nom_matiere }}
                    </option>
                @endforeach
            </select>
            @error('matiere_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="groupe_id" class="form-label">Groupe</label>
            <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
                @foreach($groupes as $groupe)
                    <option value="{{ $groupe->id }}" {{ old('groupe_id', $evaluation->groupe_id) == $groupe->id ? 'selected' : '' }}>
                        {{ $groupe->nom }} ({{ $groupe->niveau }})
                    </option>
                @endforeach
            </select>
            @error('groupe_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        <a href="{{ route('enseignant.evaluations.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection