@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Ajouter un Enseignant')
@section('page-title', 'Nouvel Enseignant')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informations de l'Enseignant</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.enseignants.store') }}" method="POST">
                @csrf
                @include('admin.enseignants._form', ['enseignant' => new \App\Models\Enseignant()])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.enseignants.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer Enseignant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('admin.enseignants._form_scripts', ['enseignant' => new \App\Models\Enseignant()])
@endpush