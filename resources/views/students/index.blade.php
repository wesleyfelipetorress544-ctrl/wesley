@extends('layouts.library')

@section('title', 'Alunos | Biblioteca Sesi-Tatuí')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Alunos cadastrados</h1>
            <p class="small text-secondary mb-0">{{ $students->total() }} contas registradas</p>
        </div>
        <form method="GET" action="{{ route('students.index') }}" role="search" class="d-flex gap-2">
            <label for="student-search" class="visually-hidden">Buscar alunos</label>
            <input id="student-search" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Buscar nome ou e-mail">
            <button class="btn btn-sm btn-outline-danger" style="--bs-btn-color: #ed1c24; --bs-btn-border-color: #ed1c24; --bs-btn-hover-color: #fff; --bs-btn-hover-bg: #ed1c24; --bs-btn-hover-border-color: #ed1c24;">Buscar</button>
        </form>
    </div>

    <div class="table-responsive bg-white rounded-4 border-0 shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Aluno</th><th>E-mail</th><th>Empréstimos</th><th>Cadastro</th></tr></thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td class="fw-semibold">{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->loans_count }}</td>
                        <td>{{ $student->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-secondary py-4">Nenhuma conta encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $students->links() }}</div>
@endsection