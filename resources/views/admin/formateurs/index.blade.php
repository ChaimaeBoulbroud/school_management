@extends('layouts.app')
@section('title', 'Gestion des Formateurs')
@section('page-title', 'Liste des Formateurs')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">Liste des Formateurs</h5>
            <a href="{{ route('admin.formateurs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Ajouter un Formateur
            </a>
        </div>

        <div class="card-body p-0">
            @if(session('success'))
            <div class="alert alert-success m-3">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger m-3">
                {{ session('error') }}
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom Complet</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Grade</th>
                            <th>Date de Recrutement</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($formateurs as $formateur)
                        <tr>
                            <td>{{ $formateur->prenom }} {{ $formateur->nom }}</td>
                            <td>{{ $formateur->email }}</td>
                            <td>{{ $formateur->telephone ?? 'N/A' }}</td>
                            <td>{{ $formateur->grade }}</td>
                            <td>{{ \Carbon\Carbon::parse($formateur->date_recrutement)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.formateurs.show', $formateur->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Voir Détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.formateurs.edit', $formateur->id) }}" class="btn btn-sm btn-outline-success me-1" title="Modifier">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a href="{{ route('admin.enseignements.create_for_formateur', $formateur->id) }}" class="btn btn-sm btn-outline-info">
                                    🔗 
                                </a>
                                <form action="{{ route('admin.formateurs.destroy', $formateur->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Aucun formateur trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($formateurs->hasPages())
        <div class="card-footer bg-light py-2">
            {{ $formateurs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection