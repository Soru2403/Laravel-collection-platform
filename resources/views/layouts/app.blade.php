<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colectio</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Vietnes galvene (header) -->
    <header class="bg-light shadow-sm">
        <div class="container d-flex justify-content-between align-items-center py-3">
            <!-- Vietnes nosaukums un logs -->
            <div>
                <h1 class="h4 m-0">
                    <a href="{{ route('home') }}" class="text-decoration-none text-dark">Colectio</a>
                </h1>
            </div>

            <!-- Navigācijas saites -->
            <nav>
                <a href="{{ route('collections.index') }}" class="ms-3 text-decoration-none text-primary">Kolekcijas</a>
                <a href="{{ route('forum.index') }}" class="ms-3 text-decoration-none text-primary">Forums</a>
                <a href="{{ route('media.index') }}" class="ms-3 text-decoration-none text-primary">Mēdiji</a>
                <a href="{{ route('exchange.index') }}" class="ms-3 text-decoration-none text-primary">Apmaiņa</a>
            </nav>

            <!-- Lietotāja paneļa saites -->
            <div>
                @guest
                    <!-- Ja lietotājs nav autentificēts -->
                    <a href="{{ route('login') }}" class="ms-3 text-decoration-none text-primary">Ieiet Colectio</a>
                    <a href="{{ route('register') }}" class="ms-3 text-decoration-none text-primary">Izveidot jaunu kontu</a>
                @else
                    <!-- Ja lietotājs ir autentificēts -->
                    <a href="{{ route('profile') }}" class="ms-3 text-decoration-none text-primary">Mans Profils</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="ms-3 text-decoration-none text-danger">Iziet</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <!-- Galvenais saturs -->
    <main class="container py-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
