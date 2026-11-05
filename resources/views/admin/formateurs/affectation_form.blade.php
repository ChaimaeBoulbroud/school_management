{{-- resources/views/enseignements/affectation_form.blade.php --}}
@extends('layouts.app') {{-- Adaptez selon votre layout principal --}}

@section('content')
<div class="container">
    <h1>Affecter Formateur : {{ $formateur->nom_complet }}</h1> {{-- Suppose que Formateur a un champ 'nom_complet' --}}

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.enseignements.create_for_formateur', $formateur->id) }}">
        @csrf

        {{-- Le formateur_id est déjà dans l'URL et sera géré par le contrôleur --}}

        <div class="mb-3">
            <label for="groupe_id" class="form-label">Groupe</label>
            <select class="form-select @error('groupe_id') is-invalid @enderror" id="groupe_id" name="groupe_id" required>
                <option value="">-- Sélectionner un Groupe --</option>
                @foreach ($groupes as $groupe)
                <option value="{{ $groupe->id }}" {{ old('groupe_id') == $groupe->id ? 'selected' : '' }}>
                    {{ $groupe->nom_groupe }} {{-- Adaptez le nom du champ --}}
                </option>
                @endforeach
            </select>
            @error('groupe_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="module_id" class="form-label">Module</label>
            <select class="form-select @error('module_id') is-invalid @enderror" id="module_id" name="module_id" required>
                <option value="">-- Sélectionner un Module --</option>
                @foreach ($modules as $module)
                <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                    {{ $module->nom_module }} {{-- Adaptez le nom du champ --}}
                </option>
                @endforeach
            </select>
            @error('module_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="annee_scolaire" class="form-label">Année Scolaire</label>
            <input type="text" class="form-control @error('annee_scolaire') is-invalid @enderror"
                id="annee_scolaire" name="annee_scolaire" value="{{ old('annee_scolaire', date('Y').'-'.(date('Y')+1)) }}"
                placeholder="Ex: 2023-2024" required>
            @error('annee_scolaire')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Champ caché pour la validation d'unicité, ne sera pas utilisé directement pour le stockage --}}
        <input type="hidden" name="formateur_groupe_module_annee" value="check">


        <button type="submit" class="btn btn-primary">Affecter</button>
        <a href="{{ route('admin.formateurs.show', $formateur->id) }}" class="btn btn-secondary">Annuler</a>
        {{-- Ou retour vers la liste : <a href="{{ route('formateurs.index') }}" class="btn btn-secondary">Annuler</a> --}}
    </form>
</div>
@endsection