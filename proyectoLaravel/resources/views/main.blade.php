<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MACUIN - Plataforma</title>
    @vite('resources/css/app.css')
</head>
<body>

    <nav class="mcq-navbar">
        <a href="/" class="brand">
            <span class="brand-accent"></span>
            MACUIN
        </a>
        <div class="nav-links">
            <a href="/catalogo">Catálogo</a>
            <a href="/pedidos">Mis Pedidos</a>
            <a href="/">Cerrar sesión</a>
        </div>
    </nav>

    <div class="mcq-page">
        @yield('contenido')
    </div>

</body>
</html>