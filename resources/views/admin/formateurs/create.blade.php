@extends('layouts.app')
@section('title', 'Ajouter un Formateur')
@section('page-title', 'Nouveau Formateur')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Ajouter un Nouveau Formateur</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.formateurs.store') }}" method="POST">
                @csrf

                <h6 class="text-primary">Informations Personnelles</h6>
                <hr class="mt-1 mb-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                        @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                        <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}">
                        @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="grade" class="form-label fw-semibold">Grade <span class="text-danger">*</span></label>
                        <select class="form-select @error('grade') is-invalid @enderror" id="grade" name="grade" required>
                            <option value="" disabled {{ old('grade') ? '' : 'selected' }}>Choisir un grade...</option>
                            <option value="Vacataire" {{ old('grade') == 'Vacataire' ? 'selected' : '' }}>Vacataire</option>
                            <option value="Permanent" {{ old('grade') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="Expert" {{ old('grade') == 'Expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                        @error('grade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="date_recrutement" class="form-label fw-semibold">Date de Recrutement <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date_recrutement') is-invalid @enderror" id="date_recrutement" name="date_recrutement" value="{{ old('date_recrutement', date('Y-m-d')) }}" required>
                    @error('date_recrutement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>


                <h6 class="text-primary mt-4">Modules Maîtrisés (Compétences)</h6>
                <hr class="mt-1 mb-3">
                <div class="mb-3">
                    <label for="module_ids_maitrises" class="form-label fw-semibold">Sélectionner les modules que ce formateur peut enseigner :</label>
                    <select name="module_ids_maitrises[]" id="module_ids_maitrises" class="form-select @error('module_ids_maitrises') is-invalid @enderror @error('module_ids_maitrises.*') is-invalid @enderror" multiple size="8">
                        @if(isset($all_modules_competences) && $all_modules_competences->count() > 0)
                        @foreach($all_modules_competences as $module)
                        <option value="{{ $module->id }}"
                            {{ (is_array(old('module_ids_maitrises')) && in_array($module->id, old('module_ids_maitrises'))) ? 'selected' : '' }}>
                            {{ $module->nom_module }} ({{ $module->code_module }})
                        </option>
                        @endforeach
                        @else
                        <option value="" disabled>Aucun module disponible pour la sélection.</option>
                        @endif
                    </select>
                    <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs modules.</small>
                    @error('module_ids_maitrises') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('module_ids_maitrises.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <h6 class="text-primary mt-4">Affecter ce Formateur à des Groupes</h6>
                <hr class="mt-1 mb-3">
                <div class="mb-3">
                    <label for="annee_scolaire_affectation" class="form-label fw-semibold">Année Scolaire pour les affectations <span class="text-danger">* (si des groupes sont cochés ci-dessous)</span></label>
                    <input type="text" class="form-control @error('annee_scolaire_affectation') is-invalid @enderror" id="annee_scolaire_affectation" name="annee_scolaire_affectation" value="{{ old('annee_scolaire_affectation', date('Y').'-'.(date('Y')+1)) }}" placeholder="Ex: 2024-2025">
                    <small class="form-text text-muted">Cette année sera appliquée à toutes les affectations de groupe cochées.</small>
                    @error('annee_scolaire_affectation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div id="affectation_groupes_dynamic_section">
                    <p class="text-muted">Veuillez d'abord sélectionner les modules maîtrisés ci-dessus. Les options d'affectation aux groupes apparaîtront ici.</p>
                </div>
                @error('affectations') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @error('affectations.*.groupes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @error('affectations.*.groupes.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.formateurs.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer le Formateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modulesMaitrisesSelect = document.getElementById('module_ids_maitrises');
        const affectationContainer = document.getElementById('affectation_groupes_dynamic_section');
        const formateurId = "{{ isset($formateur) ? $formateur->id : '' }}";

        // console.log('Formateur ID:', formateurId);
        // console.log('Affectations existantes (pour édition):', existingAffectationsByModule);
        // console.log('Affectations "old" (après validation échouée):', oldAffectationsSubmitted);


        function renderAffectationOptions(selectedModuleIds) {
            affectationContainer.innerHTML = '<p class="text-muted small">Chargement...</p>';
            if (!selectedModuleIds || selectedModuleIds.length === 0) {
                affectationContainer.innerHTML = '<p class="text-muted">Sélectionnez des modules maîtrisés...</p>';
                return;
            }

            let fetchPromises = selectedModuleIds.map(moduleId_str => { // moduleId est une chaîne ici
                const moduleId = parseInt(moduleId_str); // Convertir en entier pour la clé d'objet et comparaison
                return fetch(`{{ route('admin.formateurs.getFilieresAvecGroupesForModule') }}?module_id=${moduleId}`)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            }).catch(() => {
                                throw new Error(`Erreur HTTP ${response.status} pour module ${moduleId}`);
                            });
                        }
                        return response.json();
                    })
                    .then(fillieresData => {
                        let moduleHtml = `<div class="module-affectation-section mb-4 border p-3 rounded bg-light shadow-sm">`;
                        const selectedModuleOption = modulesMaitrisesSelect.querySelector(`option[value="${moduleId}"]`);
                        moduleHtml += `<h5 class="mb-3">Module: ${selectedModuleOption ? selectedModuleOption.textContent : 'ID ' + moduleId}</h5>`;

                        if (fillieresData && fillieresData.length > 0) {
                            fillieresData.forEach((filliere, index) => {
                                moduleHtml += `<div class="filliere-section mt-2">`;
                                moduleHtml += `<h6 class="text-secondary fw-bold">${filliere.nom_filliere || filliere.nom}</h6>`;
                                if (filliere.groupes && filliere.groupes.length > 0) {
                                    moduleHtml += `<div class="row ps-2">`;
                                    filliere.groupes.forEach(groupe => {
                                        const checkboxId = `affect_m${moduleId}_f${filliere.id}_g${groupe.id}`;
                                        let isChecked = false;

                                        // Priorité à old() si le formulaire a été soumis avec des erreurs
                                        if (oldAffectationsSubmitted && oldAffectationsSubmitted[moduleId] &&
                                            oldAffectationsSubmitted[moduleId]['groupes'] &&
                                            oldAffectationsSubmitted[moduleId]['groupes'].includes(String(groupe.id))) {
                                            isChecked = true;
                                        } else if (!Object.keys(oldAffectationsSubmitted).length && formateurId) {
                                            // Mode édition (pas d'erreur old), on se base sur les données existantes du formateur
                                            // Et on s'assure que oldAffectationsSubmitted est vraiment vide (pas juste un objet vide)
                                            if (existingAffectationsByModule[moduleId] &&
                                                existingAffectationsByModule[moduleId].includes(String(groupe.id))) {
                                                isChecked = true;
                                            }
                                        }


                                        moduleHtml += `
                                        <div class="col-md-4 col-sm-6 mb-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="affectations[${moduleId}][groupes][]" value="${groupe.id}" id="${checkboxId}" ${isChecked ? 'checked' : ''}>
                                                <label class="form-check-label" for="${checkboxId}">${groupe.nom}</label>
                                            </div>
                                        </div>
                                    `;
                                    });
                                    moduleHtml += `</div>`;
                                } else {
                                    moduleHtml += `<p class="small text-muted ms-2">Aucun groupe dans cette filière pour ce module.</p>`;
                                }
                                moduleHtml += `</div>`;
                                if (index < fillieresData.length - 1) {
                                    moduleHtml += `<hr class="my-2">`;
                                }
                            });
                        } else {
                            moduleHtml += `<p class="text-muted">Aucune filière n'est associée à ce module (ou n'a de groupes).</p>`;
                        }
                        moduleHtml += `</div>`;
                        return moduleHtml;
                    });
            });

            Promise.all(fetchPromises)
                .then(htmlSnippets => {
                    affectationContainer.innerHTML = htmlSnippets.join('');
                })
                .catch(overallError => {
                    console.error("Erreur globale:", overallError);
                    affectationContainer.innerHTML = `<p class="text-danger">Erreur majeure: ${overallError.message || 'Erreur inconnue'}</p>`;
                });
        }

        if (modulesMaitrisesSelect) {
            modulesMaitrisesSelect.addEventListener('change', function() {
                const selectedModuleIds = Array.from(this.selectedOptions).map(option => option.value);
                renderAffectationOptions(selectedModuleIds);
            });

            const initialSelectedModuleIds = Array.from(modulesMaitrisesSelect.querySelectorAll('option[selected]')).map(option => option.value);
            if (initialSelectedModuleIds.length > 0) {
                renderAffectationOptions(initialSelectedModuleIds);
            }
        }
    });
</script>
@endpush
@endsection