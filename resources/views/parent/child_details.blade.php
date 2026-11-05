@extends('layouts.app') {{-- Or a specific parent layout --}}
@section('title', 'Détails de ' . $eleve->prenom)
@section('page-title', 'Progression Scolaire de : ' . $eleve->getFullNameAttribute())

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>{{ $eleve->prenom }} {{ $eleve->nom }}</h4>
            <p class="text-muted mb-0">
                Niveau: <span class="fw-bold">{{ $eleve->groupe->niveau ?? 'N/A' }}</span> |
                Groupe: <span class="fw-bold">{{ $eleve->groupe->nom ?? 'N/A' }}</span>
            </p>
        </div>
        <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour au Tableau de Bord
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Matières et Évaluations</h5>
        </div>
        <div class="card-body">
            @if($matieresDuGroupe->isEmpty() && empty($notesParMatiere))
                <p class="text-center">Aucune matière ou évaluation trouvée pour le groupe actuel de cet élève.</p>
            @else
                @if(!$matieresDuGroupe->isEmpty())
                    <p class="mb-1"><strong>Matières étudiées dans le groupe {{ $eleve->groupe->nom ?? '' }}:</strong></p>
                    <ul class="list-inline mb-3">
                        @foreach($matieresDuGroupe as $matiere)
                            <li class="list-inline-item"><span class="badge bg-light text-dark border">{{ $matiere->nom_matiere ?? $matiere->nom }}</span></li>
                        @endforeach
                    </ul>
                    <hr>
                @endif

                @forelse ($notesParMatiere as $data)
                    <div class="mb-4 p-3 border rounded">
                        <h6 class="text-primary">{{ $data['matiere']->nom_matiere ?? $data['matiere']->nom }}</h6>
                        @if(empty($data['evaluations']))
                            <p class="small text-muted"><em>Aucune évaluation enregistrée pour cette matière.</em></p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($data['evaluations'] as $eval)
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <strong>{{ $eval['titre'] }}</strong>
                                            <small class="text-muted d-block"> ({{ $eval['date'] ? \Carbon\Carbon::parse($eval['date'])->format('d/m/Y') : 'Date N/A' }})</small>
                                            @if($eval['observations'])
                                            <small class="text-muted d-block fst-italic">Observation: {{ $eval['observations'] }}</small>
                                            @endif
                                        </div>
                                        <span class="badge {{ ($eval['note_obtenue'] >= ($eval['bareme_total'] / 2)) ? 'bg-success' : 'bg-danger' }} rounded-pill fs-6">
                                            {{ $eval['note_obtenue'] }}/{{ $eval['bareme_total'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            @if($data['notes_count'] > 0 && $data['total_bareme'] > 0)
                            <div class="mt-2 text-end">
                                <strong class="small">Moyenne pour {{ $data['matiere']->nom_matiere ?? $data['matiere']->nom }}:
                                    {{ number_format(($data['total_obtenu'] / $data['total_bareme']) * 20, 2) }}/20
                                </strong>
                                ({{$data['notes_count']}} évaluation(s))
                            </div>
                            @endif
                        @endif
                    </div>
                @empty
                    <p class="text-center">Aucune note d'évaluation disponible pour cet élève pour le moment.</p>
                @endforelse
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        padding-left: 0;
        padding-right: 0;
    }
</style>
@endpush