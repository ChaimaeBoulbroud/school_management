{{-- resources/views/admin/enseignants/_form.blade.php --}}
@php
    // Check if $enseignant is set and has a user, common for edit, less so for create
    $enseignantUser = null;
    if (isset($enseignant) && $enseignant->user) {
        $enseignantUser = $enseignant->user;
    }
@endphp

<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Informations Personnelles de l'Enseignant</legend>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $enseignant->prenom ?? '') }}" required>
            @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $enseignant->nom ?? '') }}" required>
            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="CIN" class="form-label fw-semibold">Numéro CIN</label>
            <input type="text" class="form-control @error('CIN') is-invalid @enderror" id="CIN" name="CIN" value="{{ old('CIN', $enseignant->CIN ?? '') }}">
            @error('CIN') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="telephone" class="form-label fw-semibold">Téléphone</label>
            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $enseignant->telephone ?? '') }}">
            @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="adresse" class="form-label fw-semibold">Adresse</label>
        <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="3">{{ old('adresse', $enseignant->adresse ?? '') }}</textarea>
        @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="date_recrutement" class="form-label fw-semibold">Date de Recrutement</label>
        <input type="date" class="form-control @error('date_recrutement') is-invalid @enderror" id="date_recrutement" name="date_recrutement" value="{{ old('date_recrutement', isset($enseignant->date_recrutement) ? \Carbon\Carbon::parse($enseignant->date_recrutement)->format('Y-m-d') : '') }}">
        @error('date_recrutement') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</fieldset>

<hr>
<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Compte Utilisateur (Optionnel)</legend>

    @if(isset($enseignant->id) && $enseignantUser) {{-- Edit mode and user exists --}}
        <div class="alert alert-info small p-2">
            Cet enseignant a déjà un compte utilisateur associé (Email: {{ $enseignantUser->email }}).
            <br>Vous pouvez modifier l'email ou définir un nouveau mot de passe ci-dessous.
        </div>
    @elseif(!isset($enseignant->id)) {{-- Create mode --}}
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="create_user_account" id="create_user_account" value="1"
                   {{ old('create_user_account') ? 'checked' : '' }}
                   data-bs-toggle="collapse" data-bs-target="#user_account_fields_collapse_enseignant"
                   aria-expanded="{{ old('create_user_account') ? 'true' : 'false' }}"
                   aria-controls="user_account_fields_collapse_enseignant">
            <label class="form-check-label" for="create_user_account">
                Créer un compte utilisateur
            </label>
        </div>
    @else {{-- Edit mode but no user exists --}}
         <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="create_user_account" id="create_user_account_edit" value="1"
                   {{ old('create_user_account') ? 'checked' : '' }}
                   data-bs-toggle="collapse" data-bs-target="#user_account_fields_collapse_enseignant"
                   aria-expanded="{{ old('create_user_account') ? 'true' : 'false' }}"
                   aria-controls="user_account_fields_collapse_enseignant">
            <label class="form-check-label" for="create_user_account_edit">
                Créer un compte utilisateur pour cet enseignant (n'existe pas encore)
            </label>
        </div>
    @endif

    <div class="collapse {{ (isset($enseignant->id) && $enseignantUser) || old('create_user_account') ? 'show' : '' }}" id="user_account_fields_collapse_enseignant">
        <div class="card card-body border-dashed bg-light-subtle p-3">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email du compte
                    <span class="text-danger {{ (isset($enseignant->id) && $enseignantUser) || old('create_user_account') ? '' : 'd-none' }}" id="email_required_star_enseignant">*</span>
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $enseignantUser->email ?? '') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold">
                        @if(isset($enseignant->id) && $enseignantUser) Nouveau Mot de passe @else Mot de passe @endif
                        <span class="text-danger {{ (isset($enseignant->id) && $enseignantUser) || old('create_user_account') ? '' : 'd-none' }}" id="password_required_star_enseignant">*</span>
                        @if(isset($enseignant->id) && $enseignantUser)<small class="text-muted ms-1">(Laisser vide pour ne pas changer)</small>@endif
                    </label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirmer Mot de passe</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
            </div>
        </div>
    </div>
</fieldset>