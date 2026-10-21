@extends('layouts.auth')

@section('title', 'Criar conta | Biblioteca Sesi-Tatuí')

@section('content')
    <h1 class="display-6 fw-bold mb-2">Criar conta</h1>
    <p class="text-secondary mb-4">Cadastre-se para acessar a Biblioteca Sesi-Tatuí.</p>

    @if ($errors->any())
        <div class="alert alert-light border border-danger text-danger small" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nome completo</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">E-mail</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="cpf" class="form-label fw-semibold">CPF</label>
            <input class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" type="text" inputmode="numeric" autocomplete="username" placeholder="Digite os 11 números" value="{{ old('cpf') }}" required>
            @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Senha</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Use pelo menos 8 caracteres.</div>
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold">Confirme a senha</label>
            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>
        <button class="btn btn-danger w-100" type="submit" style="--bs-btn-bg: #ed1c24; --bs-btn-border-color: #ed1c24; --bs-btn-hover-bg: #ed1c24; --bs-btn-hover-border-color: #ed1c24; --bs-btn-active-bg: #ed1c24; --bs-btn-active-border-color: #ed1c24; --bs-btn-focus-shadow-rgb: 237, 28, 36;">Criar conta</button>
    </form>

    <p class="text-center mt-4 mb-0">Já tem cadastro? <a href="{{ route('login') }}" class="link-danger">Entrar</a></p>
@endsection



