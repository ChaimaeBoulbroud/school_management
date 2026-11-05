@extends('layouts.app')
@section('title', 'Ajouter un Élève')
@section('page-title', 'Nouvel Élève')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informations de l'Élève</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.eleves.store') }}" method="POST" id="eleveForm">
                @csrf
                @include('admin.eleves._form', [
                    'eleve' => new \App\Models\Eleve(),
                    'guardians' => $guardians ?? collect(),
                    'niveaux' => $niveaux ?? collect(),
                    'groupes' => $groupes ?? collect() // Initially empty, populated by AJAX
                ])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.eleves.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer Élève
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('admin.eleves._form_scripts', ['eleve' => new \App\Models\Eleve()])
@endpush