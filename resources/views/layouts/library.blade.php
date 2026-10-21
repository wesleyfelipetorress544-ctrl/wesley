<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'Biblioteca Sesi-Tatu&iacute;')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bs-body-font-family: 'DM Sans', 'Segoe UI', system-ui, sans-serif;
        }

        h1, h2, h3, h4, h5, h6, .display-1, .display-2, .display-3, .display-4, .display-5, .display-6 {
            font-family: 'Manrope', 'DM Sans', sans-serif;
        }
    </style>
</head>
<body class="m-0 {{ request()->routeIs('books.index') ? 'overflow-y-auto' : 'overflow-hidden' }} bg-body-tertiary" style="--bs-danger-rgb: 237, 28, 36; --bs-body-font-family: 'DM Sans', 'Segoe UI', system-ui, sans-serif;">
    <div class="{{ request()->routeIs('books.index') ? 'min-vh-100' : 'vh-100' }} d-flex flex-column {{ request()->routeIs('books.index') ? '' : 'overflow-hidden' }}">
        <header class="flex-shrink-0 bg-white border-bottom shadow-sm">
            <div class="container-fluid d-flex align-items-center justify-content-between px-3 px-lg-4 py-3">
                <a href="{{ route('books.index') }}" class="d-flex align-items-center gap-3 text-decoration-none text-dark">
                    <img src="{{ asset('images/sesi-logo.png') }}" alt="SESI" width="76" class="img-fluid">
                    <span class="border-start align-self-stretch"></span>
                    <span class="fw-semibold small">Sistema Biblioteca SESI</span>
                </a>
                <div class="d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                    <span class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link link-danger text-decoration-none p-0">Sair</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="row g-0 flex-grow-1 {{ request()->routeIs('books.index') ? '' : 'overflow-hidden' }}" style="min-height: 0;">
            <aside class="col-12 col-lg-2 flex-shrink-0 bg-white border-bottom border-lg-end p-2 p-lg-3">
                <nav class="nav nav-pills flex-row flex-wrap flex-lg-column gap-1" style="--bs-nav-pills-link-active-bg: #ed1c24; --bs-nav-pills-link-active-color: #000;" aria-label="Menu principal">
                    <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                        <i class="bi bi-book" aria-hidden="true"></i><span>Livros</span>
                    </a>
                    <a href="{{ route('loans.index') }}" class="nav-link {{ (request()->routeIs('loans.*') && request('status') !== 'active') ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                        <i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Empr&eacute;stimos</span>
                    </a>
                    <a href="{{ route('loans.index', ['status' => 'active']) }}" class="nav-link {{ (request()->routeIs('loans.*') && request('status') === 'active') ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                        <i class="bi bi-arrow-return-left" aria-hidden="true"></i><span>Devolu&ccedil;&otilde;es</span>
                    </a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                            <i class="bi bi-people" aria-hidden="true"></i><span>Alunos</span>
                        </a>
                        <a href="{{ route('admin.crud.index', 'livros') }}" class="nav-link {{ request()->route('resource') === 'livros' ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                            <i class="bi bi-book-half" aria-hidden="true"></i><span>Gerenciar livros</span>
                        </a>
                        <a href="{{ route('admin.crud.index', 'usuarios') }}" class="nav-link {{ request()->route('resource') === 'usuarios' ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                            <i class="bi bi-person-gear" aria-hidden="true"></i><span>Gerenciar usuários</span>
                        </a>
                        <a href="{{ route('admin.crud.index', 'emprestimos') }}" class="nav-link {{ request()->route('resource') === 'emprestimos' ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                            <i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Gerenciar empréstimos</span>
                        </a>
                        <a href="{{ route('admin.crud.index', 'avaliacoes') }}" class="nav-link {{ request()->route('resource') === 'avaliacoes' ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                            <i class="bi bi-star" aria-hidden="true"></i><span>Gerenciar avaliações</span>
                        </a>
                        <a href="{{ route('admin.tables.index') }}" class="nav-link {{ request()->routeIs('admin.tables.*') ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                            <i class="bi bi-database" aria-hidden="true"></i><span>Tabelas do sistema</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : 'text-secondary' }} d-flex align-items-center gap-2 small fw-semibold rounded-3 py-2">
                            <i class="bi bi-bar-chart-line" aria-hidden="true"></i><span>Relat&oacute;rios</span>
                        </a>
                    @endif
                </nav>
            </aside>
            <main class="col min-w-0 {{ request()->routeIs('books.index') ? '' : 'overflow-auto' }} p-3 p-lg-5" style="min-height: 0;">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="status">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>