{{-- resources/views/admin/eleves/_form.blade.php --}}

<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Informations Personnelles de l'Élève</legend>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $eleve->prenom ?? '') }}" required>
            @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $eleve->nom ?? '') }}" required>
            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="genre" class="form-label fw-semibold">Genre <span class="text-danger">*</span></label>
            <select class="form-select @error('genre') is-invalid @enderror" id="genre" name="genre" required>
                <option value="" disabled {{ old('genre', $eleve->genre ?? '') == '' && !isset($eleve->id) ? 'selected' : '' }}>Choisir...</option>
                <option value="Homme" {{ old('genre', $eleve->genre ?? '') == 'Homme' ? 'selected' : '' }}>Homme</option>
                <option value="Femme" {{ old('genre', $eleve->genre ?? '') == 'Femme' ? 'selected' : '' }}>Femme</option>
                {{-- REMOVE 'Autre' option if it was here --}}
                {{-- <option value="Autre" {{ old('genre', $eleve->genre ?? '') == 'Autre' ? 'selected' : '' }}>Autre</option> --}}
            </select>
            @error('genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="date_naissance" class="form-label fw-semibold">Date de Naissance <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', isset($eleve->date_naissance) ? \Carbon\Carbon::parse($eleve->date_naissance)->format('Y-m-d') : '') }}" required>
            @error('date_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="lieu_naissance" class="form-label fw-semibold">Lieu de Naissance</label>
            <input type="text" class="form-control @error('lieu_naissance') is-invalid @enderror" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance', $eleve->lieu_naissance ?? '') }}">
            @error('lieu_naissance') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="adresse" class="form-label fw-semibold">Adresse</label>
            <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ old('adresse', $eleve->adresse ?? '') }}">
            @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</fieldset>

<hr>
<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Informations Parent/Gardien & Scolaires</legend>

    {{-- Guardian Selection/Creation --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Parent/Gardien <span class="text-danger">*</span></label>
        <div class="form-check">
            <input class="form-check-input guardian-option-radio" type="radio" name="guardian_option_display" id="guardian_existing_radio" value="existing"
                   data-bs-toggle="collapse" data-bs-target="#existing_guardian_fields_collapse" aria-expanded="false" aria-controls="existing_guardian_fields_collapse new_guardian_fields_collapse"
                   {{ old('guardian_option_display', (isset($eleve->parent_id) || !isset($eleve->id) && !old('guardian_option_display')) ? 'existing' : null) == 'existing' ? 'checked' : '' }}>
            <label class="form-check-label" for="guardian_existing_radio">
                Sélectionner un Parent/Gardien Existant
            </label>
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input guardian-option-radio" type="radio" name="guardian_option_display" id="guardian_new_radio" value="new"
                   data-bs-toggle="collapse" data-bs-target="#new_guardian_fields_collapse" aria-expanded="false" aria-controls="new_guardian_fields_collapse existing_guardian_fields_collapse"
                   {{ old('guardian_option_display') == 'new' ? 'checked' : '' }}>
            <label class="form-check-label" for="guardian_new_radio">
                Créer un Nouveau Parent/Gardien
            </label>
        </div>
        @error('parent_id') <div class="text-danger small mt-1">Veuillez sélectionner ou créer un parent/gardien. {{ $message }}</div> @enderror
    </div>

    {{-- This hidden input will hold the actual parent_id submitted with the Eleve form --}}
    <input type="hidden" name="parent_id" id="selected_parent_id_for_eleve" value="{{ old('parent_id', $eleve->parent_id ?? '') }}">

    {{-- Collapsible section for Existing Guardian --}}
    <div class="collapse {{ old('guardian_option_display', (isset($eleve->parent_id) || !isset($eleve->id) && !old('guardian_option_display')) ? 'existing' : null) == 'existing' ? 'show' : '' }}" id="existing_guardian_fields_collapse">
        <div class="mb-3">
            <label for="parent_id_select" class="form-label visually-hidden">Parent/Gardien Existant</label>
            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id_select">
                <option value="">Choisir un parent/gardien existant...</option>
                @if(isset($guardians))
                    @foreach($guardians as $guardian_option_item)
                        <option value="{{ $guardian_option_item->id }}" data-name="{{ $guardian_option_item->getFullNameAttribute() }} (CIN: {{ $guardian_option_item->CIN ?? 'N/A' }})"
                            {{ old('parent_id', $eleve->parent_id ?? '') == $guardian_option_item->id ? 'selected' : '' }}>
                            {{ $guardian_option_item->getFullNameAttribute() }} (CIN: {{ $guardian_option_item->CIN ?? 'N/A' }})
                        </option>
                    @endforeach
                @endif
            </select>
            {{-- Error for parent_id from main form validation (if existing is chosen but nothing selected) --}}
        </div>
    </div>

    {{-- Collapsible section for New Guardian --}}
    <div class="collapse {{ old('guardian_option_display') == 'new' ? 'show' : '' }}" id="new_guardian_fields_collapse">
        <div class="mb-3 p-3 bg-light border rounded">
            <p class="fw-semibold text-muted mb-2 small">Entrer les informations du nouveau parent/gardien :</p>
            <div id="ajax_guardian_errors" class="alert alert-danger py-2 d-none"></div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="guardian_prenom_ajax" class="form-label fw-semibold">Prénom (Nouveau Parent) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="guardian_prenom_ajax" name="guardian_prenom_ajax_input"> {{-- Name changed to avoid conflict --}}
                    <div id="guardian_prenom_ajax_error" class="text-danger small"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="guardian_nom_ajax" class="form-label fw-semibold">Nom (Nouveau Parent) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="guardian_nom_ajax" name="guardian_nom_ajax_input">
                    <div id="guardian_nom_ajax_error" class="text-danger small"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="guardian_CIN_ajax" class="form-label fw-semibold">CIN (Nouveau Parent) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="guardian_CIN_ajax" name="guardian_CIN_ajax_input">
                    <div id="guardian_CIN_ajax_error" class="text-danger small"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="guardian_telephone_ajax" class="form-label fw-semibold">Téléphone (Nouveau Parent)</label>
                    <input type="text" class="form-control" id="guardian_telephone_ajax" name="guardian_telephone_ajax_input">
                    <div id="guardian_telephone_ajax_error" class="text-danger small"></div>
                </div>
            </div>
            <button type="button" id="save_new_guardian_ajax_btn" class="btn btn-sm btn-success">
                <i class="fas fa-plus-circle"></i> Enregistrer et Sélectionner ce Parent
            </button>
            <span id="guardian_ajax_loader" class="ms-2 d-none"><i class="fas fa-spinner fa-spin"></i> Enregistrement...</span>
        </div>
    </div>
    {{-- End Guardian Selection/Creation --}}


    <div class="row mt-3">
        <div class="col-md-6 mb-3">
            <label for="niveau_select" class="form-label fw-semibold">Niveau (pour filtrer les groupes) <span class="text-danger">*</span></label>
            <select id="niveau_select" class="form-select @error('groupe_id') is-invalid @enderror" name="niveau_pour_groupe_display" required> {{-- Name is for UI interaction --}}
                <option value="">Choisir un niveau...</option>
                @if(isset($niveaux))
                    @foreach($niveaux as $niveauOption)
                        <option value="{{ $niveauOption }}"
                            {{ old('niveau_pour_groupe_display', ($eleve->groupe->niveau ?? ($eleve->exists && $eleve->groupe_id ? (\App\Models\Groupe::find($eleve->groupe_id)->niveau ?? '') : '') )) == $niveauOption ? 'selected' : '' }}
                        >
                            {{ $niveauOption }}
                        </option>
                    @endforeach
                @endif
            </select>
            {{-- This field 'niveau_pour_groupe_display' is for UI only, the actual 'groupe_id' is what's submitted for Eleve --}}
        </div>

        <div class="col-md-6 mb-3">
            <label for="groupe_id" class="form-label fw-semibold">Groupe <span class="text-danger">*</span></label>
            <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
                <option value="">D'abord choisir un niveau...</option>
                @php
                    $currentGroupeIdForForm = old('groupe_id', $eleve->groupe_id ?? '');
                @endphp
                @if($eleve->exists && isset($groupes) && $groupes->count() > 0) {{-- $groupes passed from edit controller --}}
                    @foreach($groupes as $groupeOption_item)
                        <option value="{{ $groupeOption_item->id }}" {{ $currentGroupeIdForForm == $groupeOption_item->id ? 'selected' : '' }}>
                            {{ $groupeOption_item->nom }} ({{ $groupeOption_item->niveau }})
                        </option>
                    @endforeach
                @elseif($currentGroupeIdForForm && ($selectedOldGroupe = \App\Models\Groupe::find($currentGroupeIdForForm)))
                     {{-- This handles repopulating after validation error on create if a groupe was selected --}}
                    <option value="{{ $selectedOldGroupe->id }}" selected>{{ $selectedOldGroupe->nom }} ({{ $selectedOldGroupe->niveau }})</option>
                @endif
            </select>
            @error('groupe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</fieldset>