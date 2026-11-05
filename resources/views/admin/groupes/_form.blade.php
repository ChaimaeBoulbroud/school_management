{{-- resources/views/admin/groupes/_form.blade.php --}}

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nom" class="form-label fw-semibold">Nom du Groupe <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $groupe->nom ?? '') }}" required placeholder="Ex: A, B, Les Aigles">
        @error('nom')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Nom spécifique du groupe (ex: A, B, Section Alpha).</div>
    </div>

    <div class="col-md-6 mb-3">
        <label for="niveau" class="form-label fw-semibold">Niveau <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('niveau') is-invalid @enderror" id="niveau" name="niveau" value="{{ old('niveau', $groupe->niveau ?? '') }}" required placeholder="Ex: CM1, 1ère Année, Maternelle PS">
        @error('niveau')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Niveau scolaire du groupe (ex: CM1, 1ère Année Primaire).</div>
    </div>

    <div class="col-md-4 mb-3"> {{-- Adjust column size as needed --}}
        <label for="annee_scolaire" class="form-label fw-semibold">Année Scolaire <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('annee_scolaire') is-invalid @enderror"
               id="annee_scolaire" name="annee_scolaire"
               value="{{ old('annee_scolaire', $groupe->annee_scolaire ?? '') }}"
               placeholder="Ex: 2023-2024" required>
        @error('annee_scolaire') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

{{-- Optional: Description field if you add it to your model/migration --}}
{{--
<div class="mb-3">
    <label for="description" class="form-label fw-semibold">Description (Optionnel)</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $groupe->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
--}}