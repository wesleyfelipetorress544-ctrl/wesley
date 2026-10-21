@extends('layouts.auth')
@section('title', 'Acesso administrativo | Biblioteca SESI')
@section('content')
    <div class="mb-4">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger mb-3" style="width: 48px; height: 48px;"><i class="bi bi-shield-lock fs-4" aria-hidden="true"></i></span>
        <h1 class="h2 fw-bold mb-2">Acesso administrativo</h1>
        <p class="text-secondary mb-0">Entre com sua conta de administrador da Biblioteca SESI-Tatuí.</p>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('admin.login.attempt') }}" class="vstack gap-3">
        @csrf
        <div>
            <label for="cpf" class="form-label fw-semibold">CPF</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-person-vcard text-secondary" aria-hidden="true"></i></span>
                <input id="cpf" name="cpf" value="{{ old('cpf') }}" class="form-control @error('cpf') is-invalid @enderror" inputmode="numeric" autocomplete="username" placeholder="000.000.000-00" required autofocus>
            </div>
        </div>
        <div>
            <label for="password" class="form-label fw-semibold">Senha</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-lock text-secondary" aria-hidden="true"></i></span>
                <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" placeholder="Digite sua senha" required>
            </div>
        </div>
        <div class="form-check">
            <input id="remember" name="remember" type="checkbox" class="form-check-input">
            <label for="remember" class="form-check-label">Manter conectado</label>
        </div>
        <button type="submit" class="btn btn-danger fw-semibold py-2"><i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Entrar como administrador</button>
    </form>
    <p class="text-center small mt-4 mb-0"><a href="{{ route('login') }}" class="link-danger text-decoration-none">Voltar ao acesso de alunos</a></p>
@endsection