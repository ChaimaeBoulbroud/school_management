@php
    $currentUser = Auth::user();
@endphp

<nav class="top-navbar navbar navbar-expand navbar-light bg-light border-bottom px-3">
    <div class="container-fluid">
        {{-- Sidebar toggle for mobile (optional) --}}
        {{--
        <button class="btn btn-link d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
            <i class="fas fa-bars"></i>
        </button>
        --}}

        <div class="ms-auto d-flex align-items-center">
            @if($currentUser)
                <div class="dropdown ms-2"> {{-- Added ms-2 for left margin --}}
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center p-0" id="profileDropdownTop" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                        @php
                            $nameForInitials = trim($currentUser->name ?? 'User');
                            $initialsArray = preg_split('/\s+/', $nameForInitials);
                            $initials = '';
                            if (count($initialsArray) >= 2) {
                                $initials = strtoupper(substr($initialsArray[0], 0, 1) . substr(end($initialsArray), 0, 1));
                            } elseif (count($initialsArray) == 1 && !empty($initialsArray[0])) {
                                $initials = strtoupper(substr($initialsArray[0], 0, 2));
                            } else {
                                $initials = 'U';
                            }
                        @endphp
                        <span class="avatar-initials-circle-sm " title="{{ $currentUser->name }}">
                            {{ $initials }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="profileDropdownTop" style="min-width: 220px;">
                        <li>
                            <div class="px-3 pt-2 pb-1">
                                <div class="fw-bold">{{ $currentUser->name }}</div>
                                <div class="text-muted small">
                                    @if($currentUser->roles->isNotEmpty())
                                        {{ $currentUser->roles->pluck('name')->join(', ') }}
                                    @else
                                        Utilisateur
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-topnav').submit();">
                                <i class="fas fa-sign-out-alt fa-fw me-2 text-muted"></i>Se déconnecter
                            </a>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form-topnav" style="display: none;">@csrf</form>
                        </li>
                    </ul>
                </div>
            @else
                 <a href="{{ route('login') }}" class="nav-link">Se connecter</a>
            @endif
        </div>
    </div>
</nav>

@push('styles')
<style>
    .top-navbar {
        background-color: #ffffff;
        /* box-shadow: 0 2px 4px rgba(0,0,0,.08); */
    }
    .avatar-initials-circle-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #0056b3; /* Example background color */
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
        line-height: 1;
        text-transform: uppercase;
        cursor: pointer;
        /* border: 2px solid #007bff; /* This is the Bootstrap border utility class effect, added directly to span above */
    }
    .dropdown-menu .small {
        font-size: 0.875em;
    }
    .top-navbar .dropdown-toggle.p-0 {
        padding: 0 !important;
    }
    .dropdown-item i.fa-fw {
        width: 1.28571429em;
        text-align: center;
    }
</style>
@endpush

{{-- SCRIPT SECTION FOR THEME SWITCHER REMOVED --}}
{{-- @push('scripts')
<script>
    // THEME SWITCHER JAVASCRIPT WAS HERE
</script>
@endpush --}}