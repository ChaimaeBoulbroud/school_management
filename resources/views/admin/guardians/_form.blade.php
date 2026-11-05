{{-- resources/views/admin/guardians/_form.blade.php --}}
@php
    $guardianUser = $guardian->user ?? null;
@endphp

<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Informations Personnelles du Parent/Gardien</legend>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $guardian->prenom ?? '') }}" required>
            @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="nom" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $guardian->nom ?? '') }}" required>
            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="CIN" class="form-label fw-semibold">Numéro CIN</label>
            <input type="text" class="form-control @error('CIN') is-invalid @enderror" id="CIN" name="CIN" value="{{ old('CIN', $guardian->CIN ?? '') }}">
            @error('CIN') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="telephone" class="form-label fw-semibold">Téléphone</label>
            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $guardian->telephone ?? '') }}">
            @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</fieldset>

<hr>
<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Compte Utilisateur (Optionnel)</legend>

    @if(isset($guardian->id) && $guardianUser) {{-- Edit mode and user exists --}}
        <div class="alert alert-info small">
            Ce parent/gardien a déjà un compte utilisateur associé ({{ $guardianUser->email }}).
            Vous pouvez modifier l'email ou définir un nouveau mot de passe ci-dessous.
        </div>
    @else {{-- Create mode or edit mode but no user exists --}}
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="create_user_account" id="create_user_account" value="1"
                   {{ old('create_user_account') ? 'checked' : '' }}
                   data-bs-toggle="collapse" data-bs-target="#user_account_fields_collapse" aria-expanded="{{ old('create_user_account') ? 'true' : 'false' }}" aria-controls="user_account_fields_collapse">
            <label class="form-check-label" for="create_user_account">
                Créer/Associer un compte utilisateur
            </label>
        </div>
    @endif

    {{-- Collapsible section for User Account fields --}}
    {{-- For edit mode with existing user, it should always be shown if email/password fields are relevant for update --}}
    {{-- For create mode, it's shown if checkbox is ticked --}}
    <div class="collapse {{ (isset($guardian->id) && $guardianUser) || old('create_user_account') ? 'show' : '' }}" id="user_account_fields_collapse">
        <div class="card card-body border-dashed bg-light-subtle">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email du compte <span class="text-danger {{ (isset($guardian->id) && $guardianUser) || old('create_user_account') ? '' : 'd-none' }}" id="email_required_star">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $guardianUser->email ?? '') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold">
                        @if(isset($guardian->id) && $guardianUser) Nouveau Mot de passe @else Mot de passe @endif
                        <span class="text-danger {{ (isset($guardian->id) && $guardianUser) || old('create_user_account') ? '' : 'd-none' }}" id="password_required_star">*</span>
                        @if(isset($guardian->id) && $guardianUser)<small class="text-muted">(Laisser vide pour ne pas changer)</small>@endif
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