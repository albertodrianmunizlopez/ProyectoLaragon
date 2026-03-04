<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MACUIN Autopartes</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    <!-- NAVBAR -->
    <nav class="bg-blue-700 text-white p-4 flex justify-between">
        <h1 class="font-bold text-lg">MACUIN Autopartes</h1>

        <div class="space-x-4">
            <a href="/" class="hover:underline">Inicio</a>
            <a href="/catalogo" class="hover:underline">Catálogo</a>
            <a href="/pedidos" class="hover:underline">Mis Pedidos</a>
            <a href="/login" class="hover:underline">Login</a>
        </div>
    </nav>

    <div class="p-8">
        @yield('contenido')
    </div>

</body>
</html>