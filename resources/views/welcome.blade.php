@extends('layouts.auth')

@section('title', 'Entrar | Biblioteca Sesi-Tatu&iacute;')

@section('content')
    <h1 class="display-5 fw-bold text-dark mb-2">Bem-vindo(a)</h1>
    <p class="fs-4 lh-sm text-secondary mb-4">Identifique-se para entrar na Biblioteca Sesi-Tatu&iacute;</p>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="bg-body-tertiary text-center text-secondary small fw-semibold py-3 mb-3">Acesso com CPF</div>

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="mb-3">
            <label for="cpf" class="form-label small fw-semibold">CPF</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-danger border-end-0 rounded-start-3 text-danger"><i class="bi bi-person" aria-hidden="true"></i></span>
                <input class="form-control border-danger border-start-0 rounded-end-3 ps-1 @error('cpf') is-invalid @enderror" id="cpf" name="cpf" type="text" inputmode="numeric" autocomplete="username" placeholder="000.000.000-00" value="{{ old('cpf') }}" required autofocus>
                @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label small fw-semibold">Senha</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-secondary-subtle border-end-0 rounded-start-3 text-secondary"><i class="bi bi-lock" aria-hidden="true"></i></span>
                <input class="form-control border-secondary-subtle border-start-0 rounded-end-3 ps-1 @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="current-password" placeholder="Insira sua senha de acesso" required>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                <label class="form-check-label small text-secondary" for="remember">Lembrar-me</label>
            </div>
            <span class="small text-danger">Esqueceu sua senha? Fale com a biblioteca.</span>
        </div>
        <button class="btn btn-danger w-100 py-2 fw-semibold" type="submit" style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24; --bs-btn-hover-bg: #ed1c24; --bs-btn-hover-border-color: #ed1c24; --bs-btn-active-bg: #ed1c24; --bs-btn-active-border-color: #ed1c24; --bs-btn-focus-shadow-rgb: 237, 28, 36;">Entrar</button>
    </form>

    <p class="text-center mt-4 mb-2">Ainda n&atilde;o tem cadastro? <a href="{{ route('register') }}" class="link-danger">Criar conta</a></p>
    <p class="text-center small mb-0"><a href="{{ route('admin.login') }}" class="link-secondary text-decoration-none"><i class="bi bi-shield-lock me-1" aria-hidden="true"></i>Acesso de administrador</a></p>
@endsection