@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Ajouter un Membre du Personnel')
@section('page-title', 'Nouveau Membre du Personnel')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informations du Membre du Personnel</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.personnel_directions.store') }}" method="POST">
                @csrf
                @include('admin.personnel_directions._form', ['personnel' => new \App\Models\PersonnelDirection()])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.personnel_directions.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer Membre du Personnel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('admin.personnel_directions._form_scripts', ['personnel' => new \App\Models\PersonnelDirection()])
@endpush