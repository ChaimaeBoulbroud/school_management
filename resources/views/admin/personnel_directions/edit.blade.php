@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier Membre du Personnel')
@section('page-title', 'Modifier: ' . $personnelDirection->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Modifier: {{ $personnelDirection->prenom }} {{ $personnelDirection->nom }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.personnel_directions.update', $personnelDirection->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.personnel_directions._form', ['personnel' => $personnelDirection])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.personnel_directions.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
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
    @include('admin.personnel_directions._form_scripts', ['personnel' => $personnelDirection])
@endpush