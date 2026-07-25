<?php

include("auth.php");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Flakk | Menú</title>

    <link rel="stylesheet" href="../css/biblioteca.css">
</head>

<body>

<header>

    <div class="logo">
        <img src="../img/logo.png" alt="Logo">
        <h2>Biblioteca Flakk</h2>
    </div>

    <div class="usuario">
        Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
    </div>

</header>

<section class="bienvenida">

    <h1>Panel principal</h1>
    <p id="fecha"></p>

</section>

<main class="menu">

    <!-- Catálogo -->
    <a class="tarjeta" href="catalogo.php">
        <h3>📚 Catálogo</h3>
        <p>Consultar libros y portadas.</p>
    </a>

    <!-- Agregar libro -->
    <a class="tarjeta" href="../html/agregar.html">
        <h3>➕ Agregar libro</h3>
        <p>Registrar nuevos libros.</p>
    </a>

    <!-- Registrar venta -->
    <a class="tarjeta" href="../html/ventas.html">
        <h3>🛒 Registrar venta</h3>
        <p>Registrar ventas de libros.</p>
    </a>

    <!-- Inventario -->
    <a class="tarjeta" href="../html/almacen.html">
        <h3>📦 Inventario</h3>
        <p>Consultar existencias.</p>
    </a>

</main>

<section class="opciones">

    <button type="button" onclick="modoDaltonismo()">
        Modo daltonismo
    </button>

    <a class="boton cerrar" href="logout.php">
        Cerrar sesión
    </a>

</section>

<footer>
    Biblioteca Flakk © 2026
</footer>

<script src="../js/biblioteca.js"></script>

</body>
</html>