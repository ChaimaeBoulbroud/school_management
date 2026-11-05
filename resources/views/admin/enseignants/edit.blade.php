@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier Enseignant')
@section('page-title', 'Modifier: ' . $enseignant->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Modifier: {{ $enseignant->prenom }} {{ $enseignant->nom }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.enseignants.update', $enseignant->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.enseignants._form', ['enseignant' => $enseignant])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.enseignants.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
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
    @include('admin.enseignants._form_scripts', ['enseignant' => $enseignant])
@endpush