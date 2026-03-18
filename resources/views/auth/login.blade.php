@extends('layouts.guest')

@section('title', 'Iniciar Sesión - Taxi Diamantes')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        max-width: 900px;
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    }

    .form-side {
        padding: 3rem;
    }

    .image-side {
        background: linear-gradient(135deg, #18dff5 0%, #e6b800 50%, #d4a800 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 3rem;
    }

    .image-side i {
        font-size: 4rem;
        color: #1a1a2e;
    }

    .image-side h3 {
        color: #1a1a2e;
        font-weight: 700;
        margin-top: 1rem;
    }

    .image-side p {
        color: #2d2d44;
        font-size: 0.95rem;
    }

    .form-side h2 {
        color: #1a1a2e;
        font-weight: 700;
    }

    .btn-primary {
        background: linear-gradient(135deg, #18dff5, #e6b800);
        border: none;
        color: #1a1a2e;
        font-weight: 600;
        padding: 12px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #e6b800, #d4a800);
        color: #1a1a2e;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(245, 197, 24, 0.4);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .form-control:focus {
        border-color: #18dff5;
        box-shadow: 0 0 0 0.2rem rgba(245, 197, 24, 0.25);
    }

    .input-group .form-control {
        border-left: none;
    }

    .form-check-input:checked {
        background-color: #18dff5;
        border-color: #18dff5;
    }

    a {
        color: #e6b800;
    }

    a:hover {
        color: #d4a800;
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
                <hr class="my-4" style="border-color: #1a1a2e; opacity: 0.3;">
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
