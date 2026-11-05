@extends('layouts.app') {{-- Or your admin layout, e.g., layouts.admin --}}
@section('title', 'Gestion des Élèves')
@section('page-title', 'Gérez vos Élèves')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3">
            <form method="GET" action="{{ route('admin.eleves.index') }}" class="d-flex flex-wrap gap-2 align-items-center mb-2 mb-md-0" id="filterForm">
                {{-- Filter by Niveau --}}
                <div class="btn-group">
                    <select name="niveau_filter" id="niveau_filter_select" class="form-select form-select-sm" style="min-width: 180px;">
                        <option value="">Tous les Niveaux</option>
                        @if(isset($niveauxForFilter))
                            @foreach($niveauxForFilter as $niveau)
                                <option value="{{ $niveau }}" {{ request('niveau_filter') == $niveau ? 'selected' : '' }}>
                                    {{ $niveau }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                {{-- Filter by Groupe (populated by Niveau selection or all groupes if no Niveau selected) --}}
                <div class="btn-group">
                    <select name="groupe_id_filter" id="groupe_filter_select" class="form-select form-select-sm" style="min-width: 180px;">
                        <option value="">Tous les Groupes</option>
                        @if(isset($groupesForFilter)) {{-- This would be all groupes, or groupes for selected niveau from controller --}}
                            @foreach($groupesForFilter as $groupe)
                                <option value="{{ $groupe->id }}" {{ request('groupe_id_filter') == $groupe->id ? 'selected' : '' }}>
                                    {{ $groupe->nom }} ({{ $groupe->niveau }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="btn-group">
                    <input type="text" name="nom_filter" class="form-control form-control-sm" placeholder="Nom ou Prénom..." value="{{ request('nom_filter') }}" style="min-width: 200px;">
                </div>
                <button type="submit" class="btn btn-sm btn-info">Filtrer</button>
                <a href="{{ route('admin.eleves.index') }}" class="btn btn-sm btn-secondary">Réinitialiser</a>
            </form>

            <div>
                <a href="{{ route('admin.eleves.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Ajouter un Élève
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if(session('success')) <div class="alert alert-success m-3">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger m-3">{{ session('error') }}</div> @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom Complet</th>
                            <th>Genre</th>
                            <th>Date de Naissance</th>
                            <th>Groupe</th>
                            <th>Niveau du Groupe</th>
                            <th>Parent/Gardien</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($eleves as $eleve)
                        <tr>
                            <td>{{ $eleve->prenom }} {{ $eleve->nom }}</td>
                            <td>{{ $eleve->genre }}</td>
                            <td>{{ $eleve->date_naissance ? \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $eleve->groupe->nom ?? 'N/A' }}</td>
                            <td>{{ $eleve->groupe->niveau ?? 'N/A' }}</td>
                            <td>{{ $eleve->guardian->getFullNameAttribute() ?? 'N/A' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.eleves.show', $eleve->id) }}" class="btn btn-sm btn-outline-info me-1" title="Voir Détails"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.eleves.edit', $eleve->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Modifier"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('admin.eleves.destroy', $eleve->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?');"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4">Aucun élève trouvé pour les filtres actuels.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($eleves->hasPages())
        <div class="card-footer bg-light py-2">{{ $eleves->appends(request()->query())->links() }}</div> {{-- Preserve filters on pagination --}}
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const niveauFilterSelect = document.getElementById('niveau_filter_select');
    const groupeFilterSelect = document.getElementById('groupe_filter_select');
    // const filterForm = document.getElementById('filterForm'); // Not strictly needed if using a submit button

    function fetchGroupesForFilter(niveau, selectedGroupeId = null) {
        groupeFilterSelect.innerHTML = '<option value="">Chargement...</option>';
        if (!niveau) {
            groupeFilterSelect.innerHTML = '<option value="">Tous les Groupes</option>';
            // Optionally fetch all groupes if no niveau is selected, or leave as "Tous les Groupes"
            // fetch(`{{ route('admin.eleves.getGroupesByNiveau') }}`) // without ?niveau
            // .then(...)
            return;
        }

        fetch(`{{ route('admin.eleves.getGroupesByNiveau') }}?niveau=${encodeURIComponent(niveau)}`)
            .then(response => response.json())
            .then(groupes => {
                groupeFilterSelect.innerHTML = '<option value="">Tous les Groupes</option>';
                groupes.forEach(groupe => {
                    const option = new Option(`${groupe.nom} (${groupe.niveau})`, groupe.id);
                    if (String(groupe.id) === String(selectedGroupeId)) {
                        option.selected = true;
                    }
                    groupeFilterSelect.add(option);
                });
            })
            .catch(error => {
                console.error('Erreur:', error);
                groupeFilterSelect.innerHTML = '<option value="">Erreur</option>';
            });
    }

    if (niveauFilterSelect) {
        niveauFilterSelect.addEventListener('change', function () {
            // When niveau changes, reset groupe selection and fetch new groupes
            // We don't auto-submit here; let the user click the "Filtrer" button.
            fetchGroupesForFilter(this.value);
        });

        // Au chargement, si un niveau était déjà sélectionné (via URL), peupler les groupes
        const currentSelectedNiveau = "{{ request('niveau_filter', '') }}";
        const currentSelectedGroupe = "{{ request('groupe_id_filter', '') }}";
        if (currentSelectedNiveau) {
            fetchGroupesForFilter(currentSelectedNiveau, currentSelectedGroupe);
        } else if (currentSelectedGroupe) {
            // If only groupe is selected, it might be from "Tous les Niveaux"
            // The initial options passed from controller should handle this.
        }
    }
});
</script>
@endpush
@endsection