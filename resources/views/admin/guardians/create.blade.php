@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Ajouter un Parent/Gardien')
@section('page-title', 'Nouveau Parent/Gardien')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informations du Parent/Gardien</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.guardians.store') }}" method="POST">
                @csrf
                @include('admin.guardians._form', ['guardian' => new \App\Models\Guardian()])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.guardians.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer Parent/Gardien
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('admin.guardians._form_scripts', ['guardian' => new \App\Models\Guardian()])
@endpush