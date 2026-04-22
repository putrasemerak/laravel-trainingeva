<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('ui.login') }} - AMSB Training Evaluation</title>
    
    <script src="/assets/theme.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/theme.css">
    
    <style>
        /* --- Page wrapper --- */
        .login-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 16px;
        }

        /* --- Card --- */
        .login-card {
            width: 100%;
            max-width: 370px;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.5rem 1.6rem 1.2rem;
            box-shadow: var(--shadow);
        }

        /* --- Brand --- */
        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .login-brand img {
            height: 2.2em;
            width: auto;
        }
        .login-brand-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-body);
            white-space: nowrap;
        }

        /* --- Title area --- */
        .login-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-body);
            margin-bottom: 2px;
        }
        .login-subtitle {
            font-size: 10px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        /* --- Input with icon --- */
        .input-icon-group {
            position: relative;
            margin-bottom: 10px;
        }
        .input-icon-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }
        .input-icon-group input {
            width: 100%;
            padding: 10px 12px 10px 36px !important;
            font-size: 13px !important;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            background: var(--input-bg);
            color: var(--input-text);
            outline: none;
        }

        /* --- Theme Switch --- */
        .theme-switch-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
            margin-top: 10px;
            user-select: none;
        }
        .tg-icon-sun { color: #e6a817; }
        .tg-icon-moon { color: #e67e22; }
        
        .theme-toggle-custom {
            display: flex;
            align-items: center;
            position: relative;
            width: 110px;
            height: 28px;
            border-radius: 8px;
            cursor: pointer;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            margin-bottom: 0;
        }
        .theme-toggle-custom input { display: none; }
        .theme-toggle-custom .tg-half {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            z-index: 1;
            position: relative;
            color: var(--text-muted);
            transition: color .3s;
        }
        .theme-toggle-custom .tg-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: calc(50% - 2px);
            height: calc(100% - 4px);
            border-radius: 6px;
            background: #5b9bd5;
            transition: transform .3s ease, background .3s;
            z-index: 0;
        }
        .theme-toggle-custom input:checked ~ .tg-thumb {
            transform: translateX(calc(100% + 2px));
            background: #e67e22;
        }
        .theme-toggle-custom input:not(:checked) ~ .tg-left { color: #fff; }
        .theme-toggle-custom input:checked ~ .tg-right { color: #fff; }

        /* --- Buttons --- */
        .btn-login-main {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            border: 1.5px solid #1565C0;
            background: #025ca7;
            color: #ffffff;
            cursor: pointer;
            transition: opacity .2s;
        }
        .btn-login-main:hover { opacity: .8; color: #fff; }

        .cred-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-align: center;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: var(--text-muted);
            line-height: 1.6;
            opacity: .7;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-brand">
            <img src="/ain_logo.png" alt="Logo">
            <span class="login-brand-name">AIN MEDICARE SDN. BHD.</span>
        </div>

        <div class="text-center mb-3">
            <div class="login-title">Training Evaluation System</div>
            <div class="login-subtitle">v2.0 Laravel Conversion</div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 alert-dismissible fade show" id="loginAlert" style="font-size: 11px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="this.parentElement.remove()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            @php $loc = app()->getLocale(); @endphp

            <!-- Language toggle -->
            <div class="d-flex justify-content-end mb-2" style="gap: 8px;">
                <a href="{{ route('lang.switch', 'ms') }}" class="lang-btn {{ $loc==='ms' ? 'lang-btn-active' : '' }}">BM</a>
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ $loc==='en' ? 'lang-btn-active' : '' }}">EN</a>
            </div>

            <div class="input-icon-group">
                <i class="bi bi-person-fill"></i>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="{{ __('ui.employee_no') }}" required autofocus>
            </div>

            <div class="input-icon-group">
                <i class="bi bi-lock-fill"></i>
                <input type="password" name="password" placeholder="{{ __('ui.password') }}" required>
            </div>

            <div class="cred-hint">
                <i class="bi bi-info-circle"></i>
                Login with <span style="color:#e74c3c;">AIN</span><span style="color:#3498db;">System</span> credential
            </div>

            <div class="theme-switch-row">
                <i class="bi bi-sun-fill tg-icon-sun"></i>
                <label class="theme-toggle-custom">
                    <input type="checkbox" id="themeSwitch">
                    <span class="tg-half tg-left">LIGHT</span>
                    <span class="tg-half tg-right">DARK</span>
                    <span class="tg-thumb"></span>
                </label>
                <i class="bi bi-moon-fill tg-icon-moon"></i>
            </div>

            <button type="submit" class="btn-login-main">LOGIN</button>
        </form>

        <div class="login-footer">
            Developed By:<br>
            System Development Department<br>
            &copy; {{ date('Y') }} Ain Medicare Sdn. Bhd.
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var sw = document.getElementById('themeSwitch');
    sw.checked = document.documentElement.getAttribute('data-theme') === 'dark';
    sw.addEventListener('change', function() {
        window.AINTheme.apply(this.checked ? 'dark' : 'light');
    });

    // Auto close alert after 5 seconds
    var alert = document.getElementById('loginAlert');
    if (alert) {
        setTimeout(function() {
            alert.style.transition = "opacity 0.6s ease";
            alert.style.opacity = "0";
            setTimeout(function() {
                alert.remove();
            }, 600);
        }, 5000);
    }
});
</script>
</body>
</html>
