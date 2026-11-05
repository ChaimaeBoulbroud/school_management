@extends('layouts.app')
@section('title', 'Détails du Groupe: ' . $groupe->nom)
@section('page-title', 'Détails du Groupe')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Groupe : <span class="fw-bold text-primary">{{ $groupe->nom }}</span> (Niveau : {{ $groupe->niveau }})</h5>
        </div>
        <div class="card-body">
            <h6 class="fw-bold">Matières que vous enseignez dans ce groupe :</h6>
            @if($affectations->isEmpty())
                <p class="text-muted">Vous n'êtes pas affecté à une matière dans ce groupe.</p>
            @else
                <ul>
                    @foreach($affectations as $affectation)
                        <li>
                            {{ $affectation->matiere->nom_matiere ?? $affectation->matiere->nom }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <hr>
            <h6 class="fw-bold">Liste des élèves du groupe :</h6>
            @if($groupe->eleves->isEmpty())
                <p class="text-muted">Aucun élève dans ce groupe.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Parent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupe->eleves as $eleve)
                                <tr>
                                    <td>{{ $eleve->nom }}</td>
                                    <td>{{ $eleve->prenom }}</td>
                                    <td>
                                        @if($eleve->parent)
                                            {{ $eleve->parent->nom ?? '' }} {{ $eleve->parent->prenom ?? '' }}
                                        @else
                                            <span class="text-muted">Aucun parent lié</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <a href="{{ route('enseignant.groupes.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste de mes groupes
    </a>
</div>
@endsection