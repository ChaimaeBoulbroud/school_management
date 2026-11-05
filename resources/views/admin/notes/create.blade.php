@extends('layouts.app')
@section('title', 'Ajouter une Note (Admin)')
@section('page-title', 'Nouvelle Note (Saisie Admin)')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Ajouter une Nouvelle Note</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.notes.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="evaluation_id" class="form-label fw-semibold">Évaluation <span class="text-danger">*</span></label>
                        <select class="form-select @error('evaluation_id') is-invalid @enderror" id="evaluation_id" name="evaluation_id" required>
                            <option value="">Choisir une évaluation...</option>
                            @if(isset($evaluations))
                            @foreach($evaluations as $evaluation)
                            <option value="{{ $evaluation->id }}" data-bareme="{{ $evaluation->bareme_total }}" data-groupe-id="{{ $evaluation->groupe_id }}" {{ old('evaluation_id') == $evaluation->id ? 'selected' : '' }}>
                                {{ $evaluation->titre_evaluation }} ({{ $evaluation->module->nom_module }} - Grp: {{ $evaluation->groupe->nom }})
                            </option>
                            @endforeach
                            @endif
                        </select>
                        @error('evaluation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="stagiaire_id" class="form-label fw-semibold">Stagiaire <span class="text-danger">*</span></label>
                        <select class="form-select @error('stagiaire_id') is-invalid @enderror" id="stagiaire_id" name="stagiaire_id" required>
                            <option value="">D'abord choisir une évaluation...</option>
                            {{-- Sera peuplé par JS basé sur le groupe de l'évaluation --}}
                            @if(old('evaluation_id') && isset($stagiairesEvaluationPrecedente) && count($stagiairesEvaluationPrecedente) > 0)
                            @foreach($stagiairesEvaluationPrecedente as $stagiaire)
                            <option value="{{ $stagiaire->id }}" {{ old('stagiaire_id') == $stagiaire->id ? 'selected' : '' }}>
                                {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                        @error('stagiaire_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="note_obtenue" class="form-label fw-semibold">Note Obtenue <span id="bareme_display"></span> <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0"
                            class="form-control @error('note_obtenue') is-invalid @enderror"
                            id="note_obtenue" name="note_obtenue"
                            value="{{ old('note_obtenue') }}" required>
                        @error('note_obtenue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="formateur_id" class="form-label fw-semibold">Note saisie par (Formateur) <span class="text-danger">*</span></label>
                        <select class="form-select @error('formateur_id') is-invalid @enderror" id="formateur_id" name="formateur_id" required>
                            <option value="">Choisir un formateur...</option>
                            @if(isset($formateurs))
                            @foreach($formateurs as $formateur)
                            <option value="{{ $formateur->id }}" {{ old('formateur_id') == $formateur->id ? 'selected' : '' }}>
                                {{ $formateur->prenom }} {{ $formateur->nom }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                        @error('formateur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observations" class="form-label fw-semibold">Observations (Optionnel)</label>
                    <textarea class="form-control @error('observations') is-invalid @enderror"
                        id="observations" name="observations"
                        rows="3">{{ old('observations') }}</textarea>
                    @error('observations') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.notes.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer la Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const evaluationSelect = document.getElementById('evaluation_id');
        const stagiaireSelect = document.getElementById('stagiaire_id');
        const baremeDisplay = document.getElementById('bareme_display');
        const noteObtenueInput = document.getElementById('note_obtenue');
        const oldStagiaireId = "{{ old('stagiaire_id', '') }}"; // Pour pré-sélectionner après validation

        function updateBaremeDisplay() {
            const selectedEvaluationOption = evaluationSelect.options[evaluationSelect.selectedIndex];
            const bareme = selectedEvaluationOption.dataset.bareme;

            if (bareme) {
                baremeDisplay.textContent = '/ ' + bareme;
                noteObtenueInput.max = bareme;
            } else {
                baremeDisplay.textContent = '';
                noteObtenueInput.removeAttribute('max');
            }
        }

        function fetchStagiairesForGroupe(groupeId) {
            stagiaireSelect.innerHTML = '<option value="">Chargement des stagiaires...</option>';
            stagiaireSelect.disabled = true;

            if (!groupeId) {
                stagiaireSelect.innerHTML = '<option value="">Choisir une évaluation avec un groupe valide</option>';
                return;
            }

            // Construit l'URL pour la route Ajax
            // Attention: `admin.groupes.getStagiaires` doit être le nom correct de ta route.
            // Le paramètre de route {groupe} sera remplacé par groupeId.
            let url = `{{ route('admin.groupes.getStagiaires', ['groupe' => ':groupeId']) }}`;
            url = url.replace(':groupeId', groupeId);
            console.log('Fetching stagiaires from URL:', url);


            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP ${response.status} lors de la récupération des stagiaires.`);
                    }
                    return response.json();
                })
                .then(stagiaires => {
                    stagiaireSelect.innerHTML = '<option value="">Choisir un stagiaire...</option>';
                    if (stagiaires.length > 0) {
                        stagiaires.forEach(stagiaire => {
                            const option = new Option(`${stagiaire.prenom} ${stagiaire.nom}`, stagiaire.id);
                            if (String(stagiaire.id) === oldStagiaireId) { // Pré-sélection si old()
                                option.selected = true;
                            }
                            stagiaireSelect.add(option);
                        });
                        stagiaireSelect.disabled = false;
                    } else {
                        stagiaireSelect.innerHTML = '<option value="">Aucun stagiaire dans ce groupe</option>';
                    }
                })
                .catch(error => {
                    console.error('Erreur fetchStagiairesForGroupe:', error);
                    stagiaireSelect.innerHTML = `<option value="">Erreur: ${error.message}</option>`;
                });
        }


        if (evaluationSelect) {
            evaluationSelect.addEventListener('change', function() {
                updateBaremeDisplay();
                const selectedOption = this.options[this.selectedIndex];
                const groupeId = selectedOption.dataset.groupeId;
                fetchStagiairesForGroupe(groupeId);
            });

            // Appel initial si une évaluation est déjà sélectionnée (ex: après une erreur de validation)
            if (evaluationSelect.value) {
                updateBaremeDisplay(); // Mettre à jour le barème aussi
                const initialSelectedOption = evaluationSelect.options[evaluationSelect.selectedIndex];
                const initialGroupeId = initialSelectedOption.dataset.groupeId;
                if (initialGroupeId) {
                    fetchStagiairesForGroupe(initialGroupeId, oldStagiaireId);
                } else {
                    stagiaireSelect.innerHTML = '<option value="">Choisir une évaluation avec un groupe valide</option>';
                    stagiaireSelect.disabled = true;
                }
            } else {
                stagiaireSelect.innerHTML = '<option value="">D\'abord choisir une évaluation...</option>';
                stagiaireSelect.disabled = true;
            }
        }
    });
</script>
@endpush
@endsection