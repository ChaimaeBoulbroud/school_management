@csrf
<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Informations Utilisateur</legend>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="name" class="form-label fw-semibold">Nom Complet <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="email" class="form-label fw-semibold">Adresse Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label fw-semibold">Mot de passe @if(!isset($user) || !$user->exists) <span class="text-danger">*</span> @else (Optionnel) @endif</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" @if(!isset($user) || !$user->exists) required @endif>
            @if(isset($user) && $user->exists) <small class="form-text text-muted">Laissez vide pour ne pas changer.</small> @endif
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">Confirmer le mot de passe @if(!isset($user) || !$user->exists) <span class="text-danger">*</span> @endif</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" @if(!isset($user) || !$user->exists) required @endif>
        </div>
    </div>
</fieldset>

<hr>
<fieldset class="mb-3">
    <legend class="h6 text-primary mb-3">Assignation des Rôles</legend>
    <div class="mb-3">
        <label for="user_roles" class="form-label fw-semibold">Rôles <span class="text-danger">*</span></label>
        @if(isset($allRoles) && $allRoles->count() > 0)
            <select multiple class="form-select @error('user_roles') is-invalid @enderror @error('user_roles.*') is-invalid @enderror" id="user_roles" name="user_roles[]" size="{{ $allRoles->count() > 5 ? 5 : $allRoles->count() + 1 }}" required>
                @foreach($allRoles as $roleNameKey => $roleNameValue)
                    <option value="{{ $roleNameKey }}"
                        @if(isset($userAssignedRoles) && in_array($roleNameKey, old('user_roles', $userAssignedRoles ?? [])))
                            selected
                        @elseif(is_array(old('user_roles')) && in_array($roleNameKey, old('user_roles')))
                            selected
                        @endif
                    >
                        {{ $roleNameValue }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs rôles.</small>
        @else
            <p class="text-warning">Aucun rôle n'a été défini dans le système. Veuillez d'abord <a href="{{ route('admin.roles.create') }}">créer des rôles</a>.</p>
        @endif
        @error('user_roles')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @error('user_roles.*')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</fieldset>