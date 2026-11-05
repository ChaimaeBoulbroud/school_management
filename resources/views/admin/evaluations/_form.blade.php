{{-- resources/views/admin/evaluations/_form.blade.php --}}

<div class="row">
    <div class="col-md-12 mb-3">
        <label for="titre_evaluation" class="form-label fw-semibold">Titre de l'Évaluation <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('titre_evaluation') is-invalid @enderror" id="titre_evaluation" name="titre_evaluation" value="{{ old('titre_evaluation', $evaluation->titre_evaluation ?? '') }}" required placeholder="Ex: Contrôle Continu N°1, Examen Semestriel">
        @error('titre_evaluation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="type_evaluation" class="form-label fw-semibold">Type d'Évaluation <span class="text-danger">*</span></label>
        <select class="form-select @error('type_evaluation') is-invalid @enderror" id="type_evaluation" name="type_evaluation" required>
            <option value="">Choisir un type...</option>
            @if(isset($typesEvaluationOptions))
                @foreach($typesEvaluationOptions as $type)
                    <option value="{{ $type }}" {{ old('type_evaluation', $evaluation->type_evaluation ?? '') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            @else {{-- Fallback if options not passed --}}
                <option value="Contrôle Continu" {{ old('type_evaluation', $evaluation->type_evaluation ?? '') == 'Contrôle Continu' ? 'selected' : '' }}>Contrôle Continu</option>
                <option value="Examen Final" {{ old('type_evaluation', $evaluation->type_evaluation ?? '') == 'Examen Final' ? 'selected' : '' }}>Examen Final</option>
                <option value="Projet" {{ old('type_evaluation', $evaluation->type_evaluation ?? '') == 'Projet' ? 'selected' : '' }}>Projet</option>
                <option value="Autre" {{ old('type_evaluation', $evaluation->type_evaluation ?? '') == 'Autre' ? 'selected' : '' }}>Autre</option>
            @endif
        </select>
        @error('type_evaluation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="date_evaluation" class="form-label fw-semibold">Date de l'Évaluation <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('date_evaluation') is-invalid @enderror" id="date_evaluation" name="date_evaluation" value="{{ old('date_evaluation', isset($evaluation->date_evaluation) ? \Carbon\Carbon::parse($evaluation->date_evaluation)->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('date_evaluation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="matiere_id" class="form-label fw-semibold">Matière Concernée <span class="text-danger">*</span></label>
        <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
            <option value="">Choisir une matière...</option>
            @if(isset($matieres))
                @foreach($matieres as $matiere)
                    <option value="{{ $matiere->id }}" {{ old('matiere_id', $evaluation->matiere_id ?? '') == $matiere->id ? 'selected' : '' }}>
                        {{ $matiere->nom_matiere ?? $matiere->nom }} {{ $matiere->code_matiere ? '(' . $matiere->code_matiere . ')' : '' }}
                    </option>
                @endforeach
            @endif
        </select>
        @error('matiere_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="groupe_id" class="form-label fw-semibold">Groupe Concerné <span class="text-danger">*</span></label>
        <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
            <option value="">Choisir un groupe...</option>
            @if(isset($groupes))
                @foreach($groupes as $groupe)
                    <option value="{{ $groupe->id }}" {{ old('groupe_id', $evaluation->groupe_id ?? '') == $groupe->id ? 'selected' : '' }}>
                        {{ $groupe->nom }} ({{ $groupe->niveau }})
                    </option>
                @endforeach
            @endif
        </select>
        @error('groupe_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="bareme_total" class="form-label fw-semibold">Barème Total (Points) <span class="text-danger">*</span></label>
    <input type="number" class="form-control @error('bareme_total') is-invalid @enderror" id="bareme_total" name="bareme_total" value="{{ old('bareme_total', $evaluation->bareme_total ?? '20') }}" required min="1">
    @error('bareme_total')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Optional: Description field --}}
{{--
<div class="mb-3">
    <label for="description_evaluation" class="form-label fw-semibold">Description / Instructions (Optionnel)</label>
    <textarea class="form-control @error('description_evaluation') is-invalid @enderror" id="description_evaluation" name="description_evaluation" rows="4">{{ old('description_evaluation', $evaluation->description_evaluation ?? '') }}</textarea>
    @error('description_evaluation')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
--}}