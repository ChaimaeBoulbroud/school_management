@extends('layouts.app') {{-- Or your specific enseignant layout if you have one --}}
@section('title', 'Mes Groupes')
@section('page-title', 'Liste de Mes Groupes')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Mes Groupes Assignés</h4>
            <p class="text-muted">Voici les groupes dans lesquels vous enseignez au moins une matière.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Groupes ({{ $groupes->count() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($groupes->isEmpty())
                <div class="text-center p-5">
                    <p>Vous n'êtes actuellement assigné à aucun groupe.</p>
                    {{-- Optional: Link to contact admin or view assignments --}}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom du Groupe</th>
                                <th>Niveau</th>
                                <th class="text-center">Nombre d'Élèves</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupes as $groupe)
                                <tr>
                                    <td class="fw-medium">{{ $groupe->nom }}</td>
                                    <td>{{ $groupe->niveau }}</td>
                                    <td class="text-center">{{ $groupe->eleves_count }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('enseignant.groupes.show', $groupe->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Voir Détails
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        {{-- No pagination shown here, but you can add it if $groupes is paginated in controller --}}
    </div>
</div>
@endsection