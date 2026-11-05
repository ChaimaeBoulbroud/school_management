@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier le Groupe')
@section('page-title', 'Modifier le Groupe: ' . $groupe->nom . ' (' . $groupe->niveau . ')')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Modifier: {{ $groupe->nom }} ({{ $groupe->niveau }})</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.groupes.update', $groupe->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.groupes._form', ['groupe' => $groupe])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.groupes.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection