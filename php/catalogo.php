<?php

include("auth.php");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Biblioteca Flakk | Catálogo</title>

    <link rel="stylesheet" href="../css/biblioteca.css">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body>

<header>

    <div class="logo">
        <img
            src="../img/logo.png"
            alt="Logo"
            class="logo-img"
        >

        <h2>Biblioteca Flakk</h2>
    </div>

    <button type="button" onclick="modoDaltonismo()">
        <i class="fa-solid fa-eye"></i>
        Modo Daltonismo
    </button>

    <nav>

        <a href="menu.php">
            <i class="fa-solid fa-house"></i>
            Inicio
        </a>

        <a href="../html/ventas.html">
            <i class="fa-solid fa-cart-shopping"></i>
            Ventas
        </a>

        <a href="../html/almacen.html">
            <i class="fa-solid fa-boxes-stacked"></i>
            Inventario
        </a>

        <a href="../html/agregar.html">
            <i class="fa-solid fa-plus"></i>
            Agregar libro
        </a>

    </nav>

</header>

<main class="catalogo">

    <h1>Catálogo de libros</h1>

    <div class="barra">

        <input
            type="text"
            id="buscar"
            placeholder="Buscar libro o autor..."
        >

        <select id="filtroCategoria">

            <option value="Todos">
                Todas las categorías
            </option>

            <option value="Infantil">Infantil</option>
            <option value="Terror">Terror</option>
            <option value="Romance">Romance</option>
            <option value="Historia">Historia</option>
            <option value="Fantasía">Fantasía</option>

            <option value="Ciencia Ficción">
                Ciencia Ficción
            </option>

            <option value="Misterio">Misterio</option>
            <option value="Escolar">Escolar</option>

        </select>

    </div>

    <div
        id="contenedorLibros"
        class="contenedor-libros"
    >
    </div>

</main>

<footer>
    Biblioteca Flakk © 2026
</footer>

<script src="../js/biblioteca.js"></script>

</body>
</html>