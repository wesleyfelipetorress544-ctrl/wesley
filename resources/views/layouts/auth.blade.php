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
<body class="m-0 p-0 bg-body-tertiary text-dark overflow-x-hidden" style="--bs-danger-rgb: 237, 28, 36; --bs-body-font-family: 'DM Sans', 'Segoe UI', system-ui, sans-serif;">
    <main class="container-fluid m-0 p-0">
        <div class="row g-0 min-vh-100">
            <aside class="col-md-5 bg-danger p-4 p-lg-5">
                <img src="{{ asset('images/sesi-logo.png') }}" alt="SESI S&atilde;o Paulo" class="img-fluid w-25">
            </aside>
            <section class="col-md-7 d-flex align-items-center justify-content-center bg-body-tertiary p-3 p-lg-5">
                <div class="row justify-content-center w-100">
                    <div class="col-12 col-lg-8 col-xl-7">
                        <div class="card border-0 rounded-4 shadow-sm w-100 p-3 p-lg-4">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>