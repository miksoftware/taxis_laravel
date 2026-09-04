@extends('layouts.guest')

@section('title', 'Iniciar Sesión - Taxi Diamantes')

@push('styles')
<style>
    body {
        background-color: #f0f7ff;
        background-image: 
            radial-gradient(1000px 600px at top left, rgba(186, 230, 253, 0.6) 0%, transparent 60%),
            radial-gradient(800px 500px at top right, rgba(224, 242, 254, 0.7) 0%, transparent 60%),
            radial-gradient(900px 600px at bottom right, rgba(186, 230, 253, 0.5) 0%, transparent 60%),
            radial-gradient(700px 500px at bottom left, rgba(219, 234, 254, 0.6) 0%, transparent 60%),
            linear-gradient(135deg, #f0f7ff 0%, #e2effe 50%, #f4f9fd 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .login-container {
        max-width: 920px;
        width: 100%;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 60px -15px rgba(2, 132, 199, 0.18), 0 0 0 1px rgba(186, 230, 253, 0.7);
        border: none;
    }

    .form-side {
        padding: 3.5rem;
    }

    .image-side {
        background: linear-gradient(145deg, #38bdf8 0%, #0284c7 45%, #0052cc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 3.5rem;
        position: relative;
        overflow: hidden;
    }

    .image-side::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        top: -60px;
        right: -60px;
        pointer-events: none;
    }

    .image-side i {
        font-size: 4.5rem;
        color: #ffffff;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.12));
    }

    .image-side h3 {
        color: #ffffff;
        font-weight: 800;
        margin-top: 1.25rem;
        letter-spacing: 0.5px;
    }

    .image-side p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
    }

    .form-side h2 {
        color: #0a2540;
        font-weight: 800;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0284c7 0%, #0052cc 100%);
        border: none;
        color: #ffffff;
        font-weight: 600;
        padding: 13px;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(2, 132, 199, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0369a1 0%, #0046b3 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(2, 132, 199, 0.42);
    }

    .input-group-text {
        background-color: #f8fafc;
        border-right: none;
        border-color: #cbd5e1;
        color: #64748b;
    }

    .form-control {
        border-color: #cbd5e1;
    }

    .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25);
    }

    .input-group .form-control {
        border-left: none;
    }

    .form-check-input:checked {
        background-color: #0284c7;
        border-color: #0284c7;
    }

    a {
        color: #0284c7;
        text-decoration: none;
    }

    a:hover {
        color: #0052cc;
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row login-container bg-white mx-auto">
        {{-- Lado del formulario --}}
        <div class="col-md-7 form-side">
            <h2 class="mb-4 text-center">Iniciar Sesión</h2>
            <p class="text-muted text-center mb-4">Ingrese sus credenciales para acceder al sistema</p>

            {{-- Mensajes de error --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Usuario o correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text"
                               class="form-control @error('username') is-invalid @enderror"
                               id="username"
                               name="username"
                               value="{{ old('username') }}"
                               placeholder="Ingrese su usuario o email"
                               required
                               autofocus
                               autocomplete="username">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Ingrese su contraseña"
                               required
                               autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Mostrar contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="recordar" name="recordar">
                        <label class="form-check-label" for="recordar">Recordarme</label>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" id="btnLogin">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>

        {{-- Lado decorativo --}}
        <div class="col-md-5 image-side d-none d-md-flex">
            <div class="text-center">
                <i class="bi bi-taxi-front mb-3"></i>
                <h3>Taxi Diamantes</h3>
                <p>Sistema de Gestión de Servicios</p>
                <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.35);">
                <p class="small">La mejor opción para tus viajes</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Disable button on submit to prevent double-click
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('btnLogin');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ingresando...';
    });
</script>
@endpush
