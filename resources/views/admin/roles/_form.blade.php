@csrf
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nom du Rôle <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name ?? '') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr class="my-4">

<div>
    <h5 class="mb-3 text-primary">Permissions pour ce Rôle</h5>
    @if(isset($groupedPermissions) && count($groupedPermissions) > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            @foreach($groupedPermissions as $resource => $resourcePermissions)
                <div class="col">
                    <fieldset class="mb-3 p-3 border rounded h-100">
                        <legend class="fs-6 fw-bold mb-2 text-secondary">{{ $resource }}</legend>
                        @foreach($resourcePermissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                    {{-- For Edit Form: Check if this permission ID is in the role's current permissions --}}
                                    @if(isset($rolePermissions) && in_array($permission->id, old('permissions', $rolePermissions ?? [])))
                                        checked
                                    {{-- For Create Form (or if validation fails on edit): Check old input array --}}
                                    @elseif(is_array(old('permissions')) && in_array($permission->id, old('permissions')))
                                        checked
                                    @endif
                                >
                                <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                    {{-- Prettify permission name: e.g., 'view_eleves' -> 'View Eleves' --}}
                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                </label>
                            </div>
                        @endforeach
                    </fieldset>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-warning">Aucune permission n'a été définie dans le système. Veuillez <a href="#">créer des permissions</a> d'abord (via seeders ou une interface de gestion des permissions si vous en avez une).</p>
    @endif
    @error('permissions') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    @error('permissions.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="d-flex justify-content-end mt-4">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary me-2">
        <i class="fas fa-times me-1"></i> Annuler
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i>
        {{ isset($role) && $role->exists ? 'Modifier le Rôle' : 'Créer le Rôle' }}
    </button>
</div>