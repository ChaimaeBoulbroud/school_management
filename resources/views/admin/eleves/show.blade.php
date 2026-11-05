@extends('layouts.app') {{-- Or your admin layout --}}

@section('title', 'Détails de l\'Élève: ' . $eleve->prenom . ' ' . $eleve->nom)
@section('page-title', 'Détails de l\'Élève')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Profil de : {{ $eleve->prenom }} {{ $eleve->nom }}</h5>
            <div>
                <a href="{{ route('admin.eleves.edit', ['eleve' => $eleve]) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt me-1"></i> Modifier
                </a>
                <a href="{{ route('admin.eleves.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-4">Nom Complet :</dt>
                        <dd class="col-sm-8">{{ $eleve->prenom }} {{ $eleve->nom }}</dd>

                        <dt class="col-sm-4">Genre :</dt>
                        <dd class="col-sm-8">{{ $eleve->genre }}</dd>

                        <dt class="col-sm-4">Date de Naissance :</dt>
                        <dd class="col-sm-8">{{ $eleve->date_naissance ? \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') : 'N/A' }}</dd>

                        <dt class="col-sm-4">Lieu de Naissance :</dt>
                        <dd class="col-sm-8">{{ $eleve->lieu_naissance ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Adresse :</dt>
                        <dd class="col-sm-8">{{ $eleve->adresse ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Groupe :</dt>
                        <dd class="col-sm-8">{{ $eleve->groupe->nom ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Niveau du Groupe :</dt>
                        <dd class="col-sm-8">{{ $eleve->groupe->niveau ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Parent/Gardien :</dt>
                        <dd class="col-sm-8">
                            @if($eleve->guardian)
                                <a href="{{ route('admin.guardians.show', $eleve->guardian->id) }}">
                                    {{ $eleve->guardian->getFullNameAttribute() }}
                                </a>
                                (CIN: {{ $eleve->guardian->CIN ?? 'N/A' }})
                            @else
                                N/A
                            @endif
                        </dd>

                        <dt class="col-sm-4">Profil Créé le :</dt>
                        <dd class="col-sm-8">{{ $eleve->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Dernière Modification :</dt>
                        <dd class="col-sm-8">{{ $eleve->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-user-graduate fa-7x text-secondary opacity-50 mb-3"></i>
                        <h6 class="text-muted">Profil Élève</h6>
                    </div>
                </div>
            </div>

            <hr>
            <h5>Notes Récentes :</h5>
            @if($eleve->notes->count() > 0)
                <ul class="list-group">
                    @foreach($eleve->notes->take(5)->sortByDesc('evaluation.date_evaluation') as $note) {{-- Show recent 5 --}}
                        <li class="list-group-item">
                           <strong>{{ $note->evaluation->matiere->nom ?? 'N/A' }}</strong> -
                           {{ $note->evaluation->titre_evaluation ?? 'Évaluation' }}
                           ({{ $note->evaluation->date_evaluation->format('d/m/Y') }}):
                           <span class="badge bg-primary rounded-pill">{{ $note->note_obtenue }}/{{$note->evaluation->bareme_total}}</span>
                           @if($note->observations)<br><small class="text-muted"><em>Obs: {{ $note->observations }}</em></small>@endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Aucune note enregistrée pour cet élève.</p>
            @endif

        </div>
        <div class="card-footer text-end">
            <form action="{{ route('admin.eleves.destroy', $eleve->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer : {{ $eleve->prenom }} {{ $eleve->nom }} ?');">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer cet élève
                </button>
            </form>
        </div>
    </div>
</div>
@endsection