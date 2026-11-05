@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Créer Compte pour ' . $enseignant->getFullNameAttribute())
@section('page-title', 'Créer un Compte Utilisateur pour: ' . $enseignant->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Enseignant: {{ $enseignant->prenom }} {{ $enseignant->nom }} (CIN: {{ $enseignant->CIN ?? 'N/A' }})</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Veuillez entrer les informations pour le nouveau compte utilisateur.</p>
                    <form method="POST" action="{{ route('admin.enseignants.storeAccount', $enseignant->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name_display" class="form-label">Nom d'utilisateur (auto-généré)</label>
                            <input id="name_display" class="form-control" type="text" value="{{ $enseignant->getFullNameAttribute() }}" readonly />
                            <small class="form-text text-muted">Le nom d'utilisateur sera basé sur le nom et prénom de l'enseignant.</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Adresse Email <span class="text-danger">*</span></label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nom.prenom@example.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="••••••••">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
                        </div>

                        <div class="d-flex justify-content-end">
                             <a href="{{ route('admin.enseignants.show', $enseignant->id) }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Créer et Lier le Compte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection