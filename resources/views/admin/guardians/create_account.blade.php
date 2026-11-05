@extends('layouts.app')
@section('title', 'Créer Compte pour ' . $guardian->getFullNameAttribute())
@section('page-title', 'Créer un Compte Utilisateur pour: ' . $guardian->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Parent: {{ $guardian->prenom }} {{ $guardian->nom }} (CIN: {{ $guardian->CIN ?? 'N/A' }})</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.guardians.storeAccount', $guardian->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nom d'utilisateur (auto-généré)</label>
                            <input class="form-control" type="text" value="{{ $guardian->getFullNameAttribute() }}" readonly />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.guardians.show', $guardian->id) }}" class="btn btn-outline-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Créer et Lier le Compte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection