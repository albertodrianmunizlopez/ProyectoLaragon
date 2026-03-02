<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MACUIN - Plataforma</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-700 text-white p-4 flex justify-between">
        <h1 class="font-bold text-lg">MACUIN Autopartes</h1>
        <div class="space-x-4">
            <a href="/catalogo" class="hover:underline">Catálogo</a>
            <a href="/pedidos" class="hover:underline">Mis Pedidos</a>
            <a href="/" class="hover:underline">Cerrar sesión</a>
        </div>
    </nav>

    <div class="p-8">
        @yield('contenido')
    </div>

</body>
</html>