<!-- resources/views/layouts/partials/admin_sidebar.blade.php -->
{{-- Or rename to something more generic like sidebar.blade.php if it will serve multiple roles --}}
<aside class="sidebar" style="height: 100vh; overflow-y: auto;">
    <div class="sidebar-header" style="padding: 20px 0;">
        <div class="logo-container" style="display: flex; justify-content: center; align-items: center;">
            <img src="{{ asset('images/ettefl-logo2.png') }}" alt="Logo École" style="width: 200px; height: auto;">
        </div>
    </div>
    <ul class="nav-links">
        @auth {{-- Ensure user is authenticated before checking roles --}}

            {{-- ADMIN AND/OR PERSONNEL SPECIFIC LINKS --}}
            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Personnel'))
                <li class="nav-item">
                    @can('view_dashboard')
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Tableau de Bord
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_eleves')
                    <a href="{{ route('admin.eleves.index') }}" class="nav-link {{ request()->routeIs('admin.eleves.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i> Gestion des Élèves
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_parents')
                    <a href="{{ route('admin.guardians.index') }}" class="nav-link {{ request()->routeIs('admin.guardians.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Gestion des Parents
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_groupes')
                    <a href="{{ route('admin.groupes.index') }}" class="nav-link {{ request()->routeIs('admin.groupes.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Gestion des Groupes
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_enseignants')
                    <a href="{{ route('admin.enseignants.index') }}" class="nav-link {{ request()->routeIs('admin.enseignants.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher fa-fw"></i> Gestion des Enseignants
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_personnel')
                    <a href="{{ route('admin.personnel_directions.index') }}" class="nav-link {{ request()->routeIs('admin.personnel_directions.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie fa-fw"></i> Gestion du Personnel
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_matieres')
                    <a href="{{ route('admin.matieres.index') }}" class="nav-link {{ request()->routeIs('admin.matieres.*') ? 'active' : '' }}">
                        <i class="fas fa-book fa-fw"></i> Gestion des Matières
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_evaluations')
                    <a href="{{ route('admin.evaluations.index') }}"
                        class="nav-link {{ request()->routeIs('admin.evaluations.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt fa-fw"></i> Gestion des Évaluations
                    </a>
                    @endcan
                </li>
                <li class="nav-item">
                    @can('view_notes')
                    <a href="{{ route('admin.notes.index') }}"
                        class="nav-link {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
                        <i class="fas fa-marker fa-fw"></i> Gestion des Notes
                    </a>
                    @endcan
                </li>

                {{-- Utilisateurs & Rôles - Only for Admin (or users with specific permission) --}}
                @can('manage_users')
                    @php
                        $isUserManagementActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*');
                    @endphp
                    <li class="nav-item">
                        <a href="#userManagementSubmenu" data-bs-toggle="collapse"
                            aria-expanded="{{ $isUserManagementActive ? 'true' : 'false' }}"
                            class="nav-link d-flex justify-content-between align-items-center {{ $isUserManagementActive ? 'active-parent' : '' }}">
                            <span>
                                <i class="fas fa-users-cog fa-fw"></i> Utilisateurs & Rôles
                            </span>
                            <i class="fas fa-xs fa-caret-down icon-collapse-caret"></i>
                        </a>
                        <ul class="collapse list-unstyled {{ $isUserManagementActive ? 'show' : '' }}"
                            id="userManagementSubmenu" style="padding-left: 1.5rem;">
                            <li class="nav-item">
                                <a class="nav-link sub-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                    href="{{ route('admin.users.index') }}">
                                    Gestion des Utilisateurs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sub-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                                    href="{{ route('admin.roles.index') }}">
                                    Gestion des Rôles
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
            @endif


            {{-- PARENT SPECIFIC LINKS --}}
            @if(Auth::user()->hasRole('Parent'))
                <li class="nav-item">
                    <a href="{{ route('parent.dashboard') }}" class="nav-link {{ request()->routeIs('parent.dashboard') || request()->routeIs('parent.child.details') ? 'active' : '' }}">
                        <i class="fas fa-id-card-alt"></i> Mon Espace Parent {{-- Changed icon for parent --}}
                    </a>
                </li>
                {{-- You can add more parent-specific links here if needed in the future
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-calendar-alt"></i> Événements Scolaires
                    </a>
                </li>
                --}}
            @endif


            {{-- ENSEIGNANT SPECIFIC LINKS --}}
            @if(Auth::user()->hasRole('Enseignant'))
                <li class="nav-item">
                    <a href="{{ route('enseignant.accueil') }}" class="nav-link {{ request()->routeIs('enseignant.accueil') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Mon Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enseignant.groupes.index') }}" class="nav-link {{ request()->routeIs('enseignant.groupes.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Mes Groupes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enseignant.evaluations.index') }}" class="nav-link {{ request()->routeIs('enseignant.evaluations.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Mes Évaluations & Notes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enseignant.notes.index') }}" class="nav-link {{ request()->routeIs('enseignant.notes.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check"></i> Consulter Mes Notes
                    </a>
                </li>
            @endif

            {{-- Add more @if blocks for other roles like 'Eleve' if they get to log in --}}


        @endauth {{-- End of @auth check --}}
    </ul>
    
</aside>