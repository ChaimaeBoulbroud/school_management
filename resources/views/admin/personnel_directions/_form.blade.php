{{-- resources/views/admin/personnel_directions/_form.blade.php --}}
@php
    $personnelUser = null;
    if (isset($personnel) && $personnel->user) {
        $personnelUser = $personnel->user;
    }
@endphp

<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Informations Personnelles du Membre du Personnel</legend>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $personnel->prenom ?? '') }}" required>
            @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $personnel->nom ?? '') }}" required>
            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="CIN" class="form-label fw-semibold">Numéro CIN</label>
            <input type="text" class="form-control @error('CIN') is-invalid @enderror" id="CIN" name="CIN" value="{{ old('CIN', $personnel->CIN ?? '') }}">
            @error('CIN') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="telephone" class="form-label fw-semibold">Téléphone</label>
            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $personnel->telephone ?? '') }}">
            @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="adresse" class="form-label fw-semibold">Adresse</label>
        <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="3">{{ old('adresse', $personnel->adresse ?? '') }}</textarea>
        @error('adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="rib" class="form-label fw-semibold">RIB (Relevé d'Identité Bancaire)</label>
        <input type="text" class="form-control @error('rib') is-invalid @enderror" id="rib" name="rib" value="{{ old('rib', $personnel->rib ?? '') }}">
        @error('rib') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</fieldset>

<hr>
<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Compte Utilisateur (Optionnel)</legend>

    @if(isset($personnel->id) && $personnelUser) {{-- Edit mode and user exists --}}
        <div class="alert alert-info small p-2">
            Ce membre du personnel a déjà un compte utilisateur associé (Email: {{ $personnelUser->email }}).
            <br>Vous pouvez modifier l'email ou définir un nouveau mot de passe ci-dessous.
        </div>
    @elseif(!isset($personnel->id)) {{-- Create mode --}}
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="create_user_account" id="create_user_account" value="1"
                   {{ old('create_user_account') ? 'checked' : '' }}
                   data-bs-toggle="collapse" data-bs-target="#user_account_fields_collapse_personnel"
                   aria-expanded="{{ old('create_user_account') ? 'true' : 'false' }}"
                   aria-controls="user_account_fields_collapse_personnel">
            <label class="form-check-label" for="create_user_account">
                Créer un compte utilisateur
            </label>
        </div>
    @else {{-- Edit mode but no user exists --}}
         <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="create_user_account" id="create_user_account_edit" value="1"
                   {{ old('create_user_account') ? 'checked' : '' }}
                   data-bs-toggle="collapse" data-bs-target="#user_account_fields_collapse_personnel"
                   aria-expanded="{{ old('create_user_account') ? 'true' : 'false' }}"
                   aria-controls="user_account_fields_collapse_personnel">
            <label class="form-check-label" for="create_user_account_edit">
                Créer un compte utilisateur pour ce membre du personnel (n'existe pas encore)
            </label>
        </div>
    @endif

    <div class="collapse {{ (isset($personnel->id) && $personnelUser) || old('create_user_account') ? 'show' : '' }}" id="user_account_fields_collapse_personnel">
        <div class="card card-body border-dashed bg-light-subtle p-3">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email du compte
                    <span class="text-danger {{ (isset($personnel->id) && $personnelUser) || old('create_user_account') ? '' : 'd-none' }}" id="email_required_star_personnel">*</span>
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $personnelUser->email ?? '') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold">
                        @if(isset($personnel->id) && $personnelUser) Nouveau Mot de passe @else Mot de passe @endif
                        <span class="text-danger {{ (isset($personnel->id) && $personnelUser) || old('create_user_account') ? '' : 'd-none' }}" id="password_required_star_personnel">*</span>
                        @if(isset($personnel->id) && $personnelUser)<small class="text-muted ms-1">(Laisser vide pour ne pas changer)</small>@endif
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