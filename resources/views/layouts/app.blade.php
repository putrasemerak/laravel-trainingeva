<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @auth data-role="{{ auth()->user()->role }}" @endauth>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AMSB Training Evaluation')</title>

    <script src="/assets/theme.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/theme.css">
    
    <style>
        .navbar-brand img { height: 26px; width: auto; }
        .nav-link { font-weight: 600; }
        .nav-theme-toggle {
            display: flex; align-items: center; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 2px 8px; gap: 5px; cursor: pointer;
        }
        [data-theme="dark"] .nav-theme-toggle { background: rgba(0,0,0,0.2); }
    </style>
    @yield('styles')
</head>
<body class="fixed-nav-padding" style="padding-top: 75px;">

@auth
<!-- Top Greeting Bar -->
<div id="greetingBar">
    <div>
        <i class="bi bi-person-circle"></i>
        {{ __('ui.welcome') }}, <strong>{{ session('user_name') }}</strong> ({{ auth()->user()->EmpNo }})
        <span class="ml-2 badge badge-info">{{ strtoupper(auth()->user()->role) }}</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="lang-toggle-bar mr-3">
            <a href="{{ route('lang.switch', 'ms') }}" class="{{ app()->getLocale() == 'ms' ? 'active' : '' }}">BM</a>
            <span>|</span>
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
        </div>
        <div class="nav-theme-toggle" onclick="window.AINTheme.toggle()">
            <i class="bi bi-sun-fill tg-icon-sun"></i>
            <i class="bi bi-moon-stars-fill tg-icon-moon"></i>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/dashboard') }}">
            <img src="/ain_logo.png" alt="Logo" class="mr-3" style="height: 30px; width: auto;">
            <span style="font-weight: 800; letter-spacing: 1px; font-size: 14px;">TRAINING EVALUATION</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('dashboard') || Route::is('user.dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                        <i class="bi bi-speedometer2"></i> {{ __('ui.dashboard') }}
                    </a>
                </li>

                @if(auth()->user()->isSuperUser() || auth()->user()->hasLegacyAccess('HG10', ['01','02','03']))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('training/*') || Route::is('evaluations') ? 'active' : '' }}" href="#" id="trainingDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-book"></i> {{ __('ui.training') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="trainingDropdown" style="background-color: var(--bg-card) !important;">
                        <a class="dropdown-item py-2 {{ Route::is('training.attendance') ? 'active' : '' }}" href="{{ route('training.attendance') }}" style="color: var(--text-body) !important;">
                            <i class="bi bi-person-plus mr-2"></i> {{ __('ui.training_entry') }}
                        </a>
                        <a class="dropdown-item py-2 {{ Route::is('training.master_form') ? 'active' : '' }}" href="{{ route('training.master_form') }}" style="color: var(--text-body) !important;">
                            <i class="bi bi-file-earmark-medical mr-2"></i> {{ __('ui.evaluation_form') }}
                        </a>
                        <div class="dropdown-divider" style="border-top-color: var(--border-color) !important;"></div>
                        <a class="dropdown-item py-2 {{ Route::is('training.notifications') ? 'active' : '' }}" href="{{ route('training.notifications') }}" style="color: var(--text-body) !important;">
                            <i class="bi bi-bell mr-2"></i> {{ __('ui.training_notifications') }}
                        </a>
                        <a class="dropdown-item py-2 {{ Route::is('evaluations.list') ? 'active' : '' }}" href="{{ route('evaluations.list') }}" style="color: var(--text-body) !important;">
                            <i class="bi bi-list-check mr-2"></i> All Evaluations
                        </a>
                    </div>
                </li>
                @endif

                @if(auth()->user()->isSuperUser())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ Request::is('admin/*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-shield-lock"></i> Admin
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="adminDropdown" style="background-color: var(--bg-card) !important;">
                        <a class="dropdown-item py-2 {{ Route::is('admin.audit') ? 'active' : '' }}" href="{{ route('admin.audit') }}" style="color: var(--text-body) !important;">
                            <i class="bi bi-receipt mr-2"></i> {{ __('ui.audit_trail') }}
                        </a>
                        <a class="dropdown-item py-2 {{ Route::is('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}" style="color: var(--text-body) !important;">
                            <i class="bi bi-gear mr-2"></i> {{ __('ui.system_settings') }}
                        </a>
                    </div>
                </li>
                @endif

                <li class="nav-item ml-lg-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('ui.logout') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endauth

<div class="container mt-4">
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
    window.addEventListener('themeChanged', function(e) {
        // Handle theme change if needed
    });
</script>

@yield('scripts')
</body>
</html>
