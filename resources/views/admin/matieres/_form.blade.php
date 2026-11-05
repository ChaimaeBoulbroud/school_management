{{-- resources/views/admin/matieres/_form.blade.php --}}

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="code_matiere" class="form-label fw-semibold">Code Matière (Optionnel)</label>
        <input type="text" class="form-control @error('code_matiere') is-invalid @enderror" id="code_matiere" name="code_matiere" value="{{ old('code_matiere', $matiere->code_matiere ?? '') }}" placeholder="Ex: M01, PHYS-002">
        @error('code_matiere')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="nom_matiere" class="form-label fw-semibold">Nom Complet de la Matière <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('nom_matiere') is-invalid @enderror" id="nom_matiere" name="nom_matiere" value="{{ old('nom_matiere', $matiere->nom_matiere ?? '') }}" required placeholder="Ex: Mathématiques, Langue Française">
    @error('nom_matiere')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="masse_horaire" class="form-label fw-semibold">Masse Horaire (heures/semaine ou totale)</label>
        <input type="number" class="form-control @error('masse_horaire') is-invalid @enderror" id="masse_horaire" name="masse_horaire" value="{{ old('masse_horaire', $matiere->masse_horaire ?? '') }}" min="0" placeholder="Ex: 4">
        @error('masse_horaire')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="coefficient" class="form-label fw-semibold">Coefficient</label>
        <input type="number" step="0.1" class="form-control @error('coefficient') is-invalid @enderror" id="coefficient" name="coefficient" value="{{ old('coefficient', $matiere->coefficient ?? '') }}" min="0" placeholder="Ex: 2 ou 1.5">
        @error('coefficient')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>