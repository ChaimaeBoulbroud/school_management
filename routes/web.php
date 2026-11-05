<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Enseignant\EnseignantInterfaceController;
// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EleveController;
use App\Http\Controllers\Admin\EnseignantController as AdminEnseignantController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\GroupeController;
use App\Http\Controllers\Admin\MatiereController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\AffectationCoursController;
use App\Http\Controllers\Admin\PersonnelDirectionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Parent\ParentDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login-attempt', [LoginController::class, 'login'])->name('login.perform');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// General Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Personnel')) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->hasRole('Parent')) {
            return redirect()->route('parent.dashboard');
        } elseif (Auth::user()->hasRole('Enseignant')) {
            return redirect()->route('enseignant.accueil');
        }
        return view('dashboard');
    })->name('dashboard');
});


// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
         ->middleware('permission:view_dashboard');

    // --- User and Role Management ---
    Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:manage_roles_permissions');
    Route::get('/users/get-profiles-by-type', [UserController::class, 'getProfilesByType'])->name('users.getProfilesByType')->middleware('permission:manage_users');
    Route::resource('users', UserController::class)->middleware('permission:manage_users');

    // Eleve (Student) Routes
    Route::get('eleves/get-groupes-by-niveau', [EleveController::class, 'getGroupesByNiveau'])->name('eleves.getGroupesByNiveau')->middleware('permission:view_eleves');
    Route::get('eleves', [EleveController::class, 'index'])->name('eleves.index')->middleware('permission:view_eleves');
    Route::get('eleves/create', [EleveController::class, 'create'])->name('eleves.create')->middleware('permission:create_eleves');
    Route::post('eleves', [EleveController::class, 'store'])->name('eleves.store')->middleware('permission:create_eleves');
    Route::get('eleves/{eleve}', [EleveController::class, 'show'])->name('eleves.show')->middleware('permission:view_eleves');
    Route::get('eleves/{eleve}/edit', [EleveController::class, 'edit'])->name('eleves.edit')->middleware('permission:edit_eleves');
    Route::put('eleves/{eleve}', [EleveController::class, 'update'])->name('eleves.update')->middleware('permission:edit_eleves');
    Route::delete('eleves/{eleve}', [EleveController::class, 'destroy'])->name('eleves.destroy')->middleware('permission:delete_eleves');

    // Groupe Routes
    Route::get('/groupes/{groupe}/get-eleves', [GroupeController::class, 'getElevesPourGroupe'])->name('groupes.getEleves')->middleware('permission:view_groupes');
    Route::get('groupes', [GroupeController::class, 'index'])->name('groupes.index')->middleware('permission:view_groupes');
    Route::get('groupes/create', [GroupeController::class, 'create'])->name('groupes.create')->middleware('permission:create_groupes');
    Route::post('groupes', [GroupeController::class, 'store'])->name('groupes.store')->middleware('permission:create_groupes');
    Route::get('groupes/{groupe}', [GroupeController::class, 'show'])->name('groupes.show')->middleware('permission:view_groupes');
    Route::get('groupes/{groupe}/edit', [GroupeController::class, 'edit'])->name('groupes.edit')->middleware('permission:edit_groupes');
    Route::put('groupes/{groupe}', [GroupeController::class, 'update'])->name('groupes.update')->middleware('permission:edit_groupes');
    Route::delete('groupes/{groupe}', [GroupeController::class, 'destroy'])->name('groupes.destroy')->middleware('permission:delete_groupes');
    Route::get('/groupes/{groupe}/affectations/create', [AffectationCoursController::class, 'createForGroupe'])->name('groupes.affectations.create')->middleware('permission:manage_affectations');
    Route::post('/groupes/{groupe}/affectations', [AffectationCoursController::class, 'storeForGroupe'])->name('groupes.affectations.store')->middleware('permission:manage_affectations');

    // Matiere (Subject) Routes
    Route::get('matieres', [MatiereController::class, 'index'])->name('matieres.index')->middleware('permission:view_matieres');
    Route::get('matieres/create', [MatiereController::class, 'create'])->name('matieres.create')->middleware('permission:create_matieres');
    Route::post('matieres', [MatiereController::class, 'store'])->name('matieres.store')->middleware('permission:create_matieres');
    Route::get('matieres/{matiere}', [MatiereController::class, 'show'])->name('matieres.show')->middleware('permission:view_matieres');
    Route::get('matieres/{matiere}/edit', [MatiereController::class, 'edit'])->name('matieres.edit')->middleware('permission:edit_matieres');
    Route::put('matieres/{matiere}', [MatiereController::class, 'update'])->name('matieres.update')->middleware('permission:edit_matieres');
    Route::delete('matieres/{matiere}', [MatiereController::class, 'destroy'])->name('matieres.destroy')->middleware('permission:delete_matieres');
    Route::get('/matieres/{matiere}/affectations/create', [AffectationCoursController::class, 'createForMatiere'])->name('matieres.affectations.create')->middleware('permission:manage_affectations');
    Route::post('/matieres/{matiere}/affectations', [AffectationCoursController::class, 'storeForMatiere'])->name('matieres.affectations.store')->middleware('permission:manage_affectations');

    // Enseignant (Teacher) Admin CRUD & Specific Actions
    Route::get('enseignants', [AdminEnseignantController::class, 'index'])->name('enseignants.index')->middleware('permission:view_enseignants');
    Route::get('enseignants/create', [AdminEnseignantController::class, 'create'])->name('enseignants.create')->middleware('permission:create_enseignants');
    Route::post('enseignants', [AdminEnseignantController::class, 'store'])->name('enseignants.store')->middleware('permission:create_enseignants');
    Route::get('enseignants/{enseignant}', [AdminEnseignantController::class, 'show'])->name('enseignants.show')->middleware('permission:view_enseignants');
    Route::get('enseignants/{enseignant}/edit', [AdminEnseignantController::class, 'edit'])->name('enseignants.edit')->middleware('permission:edit_enseignants');
    Route::put('enseignants/{enseignant}', [AdminEnseignantController::class, 'update'])->name('enseignants.update')->middleware('permission:edit_enseignants');
    Route::delete('enseignants/{enseignant}', [AdminEnseignantController::class, 'destroy'])->name('enseignants.destroy')->middleware('permission:delete_enseignants');
    Route::get('/enseignants/{enseignant}/create-user-account', [AdminEnseignantController::class, 'createAccountForm'])->name('enseignants.createAccountForm')->middleware('permission:assign_user_account_enseignant');
    Route::post('/enseignants/{enseignant}/store-user-account', [AdminEnseignantController::class, 'storeAccount'])->name('enseignants.storeAccount')->middleware('permission:assign_user_account_enseignant');
    Route::get('/enseignants/{enseignant}/affectations/create', [AffectationCoursController::class, 'createForEnseignant'])->name('enseignants.affectations.create')->middleware('permission:manage_enseignant_matieres');
    Route::post('/enseignants/{enseignant}/affectations', [AffectationCoursController::class, 'storeForEnseignant'])->name('enseignants.affectations.store')->middleware('permission:manage_enseignant_matieres');

    // AffectationCours (General Resource)
    Route::get('affectation-cours', [AffectationCoursController::class, 'index'])->name('affectations.index')->middleware('permission:manage_affectations');
    Route::get('affectation-cours/create', [AffectationCoursController::class, 'create'])->name('affectations.create')->middleware('permission:manage_affectations');
    Route::post('affectation-cours', [AffectationCoursController::class, 'store'])->name('affectations.store')->middleware('permission:manage_affectations');
    Route::get('affectation-cours/{affectationCour}', [AffectationCoursController::class, 'show'])->name('affectations.show')->middleware('permission:manage_affectations');
    Route::get('affectation-cours/{affectationCour}/edit', [AffectationCoursController::class, 'edit'])->name('affectations.edit')->middleware('permission:manage_affectations');
    Route::put('affectation-cours/{affectationCour}', [AffectationCoursController::class, 'update'])->name('affectations.update')->middleware('permission:manage_affectations');
    Route::delete('affectation-cours/{affectationCour}', [AffectationCoursController::class, 'destroy'])->name('affectations.destroy')->middleware('permission:manage_affectations');

    // Evaluation Routes (granular permissions)
    Route::get('evaluations', [EvaluationController::class, 'index'])->name('evaluations.index')->middleware('permission:view_evaluations');
    Route::get('evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create')->middleware('permission:create_evaluations');
    Route::post('evaluations', [EvaluationController::class, 'store'])->name('evaluations.store')->middleware('permission:create_evaluations');
    Route::get('evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show')->middleware('permission:view_evaluations');
    Route::get('evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('evaluations.edit')->middleware('permission:edit_evaluations');
    Route::put('evaluations/{evaluation}', [EvaluationController::class, 'update'])->name('evaluations.update')->middleware('permission:edit_evaluations');
    Route::delete('evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy')->middleware('permission:delete_evaluations');

    // Note Routes (linked to evaluation)
    Route::get('evaluations/{evaluation}/notes/manage', [NoteController::class, 'manageNotesForEvaluation'])->name('notes.manageForEvaluation')->middleware('permission:manage_notes');
    Route::post('evaluations/{evaluation}/notes/manage', [NoteController::class, 'storeManagedNotesForEvaluation'])->name('notes.storeManagedForEvaluation')->middleware('permission:manage_notes');
    Route::get('notes/ajax/get-eleves-for-evaluation/{evaluation}', [NoteController::class, 'getElevesForEvaluation'])->name('notes.getElevesForEvaluation')->middleware('permission:view_notes');
    Route::get('notes', [NoteController::class, 'index'])->name('notes.index')->middleware('permission:view_notes');
    Route::get('notes/create', [NoteController::class, 'create'])->name('notes.create')->middleware('permission:manage_notes');
    Route::post('notes', [NoteController::class, 'store'])->name('notes.store')->middleware('permission:manage_notes');
    Route::get('notes/{note}', [NoteController::class, 'show'])->name('notes.show')->middleware('permission:view_notes');
    Route::get('notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit')->middleware('permission:manage_notes');
    Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update')->middleware('permission:manage_notes');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy')->middleware('permission:manage_notes');

    // Guardian (Parent) Routes
    Route::post('/ajax/guardians/store', [GuardianController::class, 'ajaxStore'])->name('ajax.guardians.store')->middleware('permission:create_parents');
    Route::post('/guardians/{guardian}/store-user-account', [GuardianController::class, 'storeAccount'])->name('guardians.storeAccount')->middleware('permission:assign_user_account_parent');
    Route::get('guardians', [GuardianController::class, 'index'])->name('guardians.index')->middleware('permission:view_parents');
    Route::get('guardians/create', [GuardianController::class, 'create'])->name('guardians.create')->middleware('permission:create_parents');
    Route::post('guardians', [GuardianController::class, 'store'])->name('guardians.store')->middleware('permission:create_parents');
    Route::get('guardians/{guardian}', [GuardianController::class, 'show'])->name('guardians.show')->middleware('permission:view_parents');
    Route::get('guardians/{guardian}/edit', [GuardianController::class, 'edit'])->name('guardians.edit')->middleware('permission:edit_parents');
    Route::put('guardians/{guardian}', [GuardianController::class, 'update'])->name('guardians.update')->middleware('permission:edit_parents');
    Route::delete('guardians/{guardian}', [GuardianController::class, 'destroy'])->name('guardians.destroy')->middleware('permission:delete_parents');

    // PersonnelDirection Routes
    Route::get('personnel-directions', [PersonnelDirectionController::class, 'index'])->name('personnel_directions.index')->middleware('permission:view_personnel');
    Route::get('personnel-directions/create', [PersonnelDirectionController::class, 'create'])->name('personnel_directions.create')->middleware('permission:create_personnel');
    Route::post('personnel-directions', [PersonnelDirectionController::class, 'store'])->name('personnel_directions.store')->middleware('permission:create_personnel');
    Route::get('personnel-directions/{personnelDirection}', [PersonnelDirectionController::class, 'show'])->name('personnel_directions.show')->middleware('permission:view_personnel');
    Route::get('personnel-directions/{personnelDirection}/edit', [PersonnelDirectionController::class, 'edit'])->name('personnel_directions.edit')->middleware('permission:edit_personnel');
    Route::put('personnel-directions/{personnelDirection}', [PersonnelDirectionController::class, 'update'])->name('personnel_directions.update')->middleware('permission:edit_personnel');
    Route::delete('personnel-directions/{personnelDirection}', [PersonnelDirectionController::class, 'destroy'])->name('personnel_directions.destroy')->middleware('permission:delete_personnel');
    Route::get('/personnel-directions/{personnelDirection}/create-user-account', [PersonnelDirectionController::class, 'createAccountForm'])->name('personnel_directions.createAccountForm')->middleware('permission:assign_user_account_personnel');
    Route::post('/personnel-directions/{personnelDirection}/store-user-account', [PersonnelDirectionController::class, 'storeAccount'])->name('personnel_directions.storeAccount')->middleware('permission:assign_user_account_personnel');
});

// Parent Interface Routes
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:Parent'])->group(function () {
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/child/{eleve}/details', [ParentDashboardController::class, 'showChildDetails'])->name('child.details');
});

// Enseignant Interface Routes
Route::prefix('enseignant')->name('enseignant.')->middleware(['auth', 'role:Enseignant'])->group(function () {
    Route::get('/accueil', [EnseignantInterfaceController::class, 'accueil'])->name('accueil');
    Route::get('/evaluations', [EnseignantInterfaceController::class, 'listEvaluations'])->name('evaluations.index');
    Route::get('/evaluations/create', [EnseignantInterfaceController::class, 'createEvaluation'])->name('evaluations.create');
    Route::post('/evaluations', [EnseignantInterfaceController::class, 'storeEvaluation'])->name('evaluations.store');
    Route::get('/evaluations/{evaluation}/edit', [EnseignantInterfaceController::class, 'editEvaluation'])->name('evaluations.edit');
    Route::put('/evaluations/{evaluation}', [EnseignantInterfaceController::class, 'updateEvaluation'])->name('evaluations.update');
    Route::delete('/evaluations/{evaluation}', [EnseignantInterfaceController::class, 'destroyEvaluation'])->name('evaluations.destroy');
    Route::get('/evaluations/{evaluation}/notes', [EnseignantInterfaceController::class, 'manageNotesForEvaluation'])->name('evaluations.notes.manage');
    Route::post('/evaluations/{evaluation}/notes', [EnseignantInterfaceController::class, 'storeNotesForEvaluation'])->name('evaluations.notes.store');
    Route::get('/evaluations/{evaluation}/notes/view', [EnseignantInterfaceController::class, 'viewNotesForEvaluation'])->name('evaluations.notes.view');
    Route::get('/groupes', [EnseignantInterfaceController::class, 'listGroupes'])->name('groupes.index');
    Route::get('/groupes/{groupe}', [EnseignantInterfaceController::class, 'showGroupe'])->name('groupes.show');
    Route::get('/notes', [EnseignantInterfaceController::class, 'listAllNotes'])->name('notes.index');
 });