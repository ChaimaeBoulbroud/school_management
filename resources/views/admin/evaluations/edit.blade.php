@extends('layouts.app') {{-- Or your admin layout --}}
@section('title', 'Modifier l\'Évaluation')
@section('page-title', 'Modifier: ' . $evaluation->titre_evaluation)

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Modifier: {{ $evaluation->titre_evaluation }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('admin.evaluations._form', [
                    'evaluation' => $evaluation,
                    'matieres' => $matieres,
                    'groupes' => $groupes,
                    'typesEvaluationOptions' => $typesEvaluationOptions
                ])

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.evaluations.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection