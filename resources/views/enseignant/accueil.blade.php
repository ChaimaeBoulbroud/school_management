@extends('layouts.app')
@section('title', 'Accueil Enseignant')
@section('page-title', 'Bienvenue, ' . $enseignant->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <p class="lead mb-4">Ceci est votre espace enseignant. Voici un aperçu de vos activités et informations importantes.</p>

    <div class="row">
        {{-- Column 1: Quick Actions & Upcoming Tasks --}}
        <div class="col-lg-7 mb-4">
            {{-- Upcoming Evaluations --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Prochaines Évaluations</h5>
                </div>
                <div class="card-body">
                    @if($upcomingEvaluations && $upcomingEvaluations->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($upcomingEvaluations as $evaluation)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('enseignant.evaluations.notes.manage', $evaluation->id) }}" class="fw-bold">
                                            {{ $evaluation->titre_evaluation }}
                                        </a>
                                        <small class="d-block text-muted">
                                            {{ $evaluation->matiere->nom_matiere ?? $evaluation->matiere->nom }} -
                                            Groupe: {{ $evaluation->groupe->nom ?? 'N/A' }}
                                        </small>
                                    </div>
                                    <span class="badge bg-info rounded-pill">{{ \Carbon\Carbon::parse($evaluation->date_evaluation)->format('d/m/Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aucune évaluation à venir prochainement.</p>
                    @endif
                    <div class="mt-3 text-end">
                        <a href="{{ route('enseignant.evaluations.index') }}" class="btn btn-outline-primary btn-sm">Toutes les évaluations</a>
                    </div>
                </div>
            </div>

            {{-- Action Items (Example: Evaluations needing grades) --}}
            {{-- You would need to fetch $evaluationsSansNotes in your controller --}}
            {{--
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Actions Requises</h5>
                </div>
                <div class="card-body">
                    @if(isset($evaluationsSansNotes) && $evaluationsSansNotes->count() > 0)
                        <p>Évaluations passées nécessitant la saisie des notes :</p>
                        <ul class="list-group list-group-flush">
                            @foreach($evaluationsSansNotes as $eval)
                                <li class="list-group-item">
                                    <a href="{{ route('enseignant.evaluations.notes.manage', $eval->id) }}">Saisir notes pour: {{ $eval->titre_evaluation }} ({{ $eval->matiere->nom_matiere }})</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Aucune action requise pour le moment concernant les notes.</p>
                    @endif
                </div>
            </div>
            --}}

        </div>

        
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>Accès Rapides</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('enseignant.evaluations.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle fa-fw me-2"></i>Planifier une Nouvelle Évaluation
                    </a>
                    <a href="{{ route('enseignant.groupes.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-users fa-fw me-2"></i>Voir Mes Groupes
                    </a>
                    <a href="{{ route('enseignant.evaluations.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-tasks fa-fw me-2"></i>Gérer Toutes Mes Évaluations
                    </a>
                    
                    <a href="{{route('enseignant.notes.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-book-reader fa-fw me-2"></i>Consulter Toutes Mes Notes
                    </a>
                    
                </div>
            </div>

            
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Aperçu Rapide</h5>
                </div>
                <div class="card-body">
                    <p>Nombre de groupes actifs: <strong>{{ $stats['active_groupes_count'] ?? 'N/A' }}</strong></p>
                    <p>Nombre total d'élèves: <strong>{{ $stats['total_eleves_count'] ?? 'N/A' }}</strong></p>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection