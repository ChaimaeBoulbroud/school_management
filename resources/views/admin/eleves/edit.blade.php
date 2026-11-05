@extends('layouts.app')
@section('title', 'Modifier l\'Élève')
@section('page-title', 'Modifier: ' . $eleve->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Modifier: {{ $eleve->prenom }} {{ $eleve->nom }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.eleves.update', $eleve->id) }}" method="POST" id="eleveForm">
                @csrf
                @method('PUT')

                @include('admin.eleves._form', [
                    'eleve' => $eleve,
                    'guardians' => $guardians ?? collect(),
                    'niveaux' => $niveaux ?? collect(),
                    'groupes' => $groupes ?? collect() // Will be pre-filled by controller for current niveau
                ])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.eleves.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('admin.eleves._form_scripts', ['eleve' => $eleve])
@endpush