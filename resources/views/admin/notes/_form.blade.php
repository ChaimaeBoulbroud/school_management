{{-- resources/views/admin/notes/_form.blade.php --}}

{{-- Display context information, not editable here --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Élève:</label>
    <input type="text" class="form-control" value="{{ $note->eleve->getFullNameAttribute() ?? 'N/A' }}" readonly>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Évaluation:</label>
    <input type="text" class="form-control" value="{{ $note->evaluation->titre_evaluation ?? 'N/A' }} ({{ $note->evaluation->matiere->nom_matiere ?? $note->evaluation->matiere->nom ?? 'N/A' }})" readonly>
    <input type="hidden" name="eleve_id" value="{{ $note->eleve_id }}">
    <input type="hidden" name="evaluation_id" value="{{ $note->evaluation_id }}">
</div>

<div class="mb-3">
    <label for="note_obtenue" class="form-label fw-semibold">Note Obtenue <span class="text-danger">*</span> (sur {{ $note->evaluation->bareme_total ?? 'N/A' }})</label>
    <input type="number" step="0.01" class="form-control @error('note_obtenue') is-invalid @enderror" id="note_obtenue" name="note_obtenue"
           value="{{ old('note_obtenue', $note->note_obtenue ?? '') }}"
           min="0" max="{{ $note->evaluation->bareme_total ?? 1000 }}" required>
    @error('note_obtenue')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="observations" class="form-label fw-semibold">Observations (Optionnel)</label>
    <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations" rows="3">{{ old('observations', $note->observations ?? '') }}</textarea>
    @error('observations')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>