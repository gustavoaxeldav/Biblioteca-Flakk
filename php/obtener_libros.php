<?php

header("Content-Type: application/json; charset=UTF-8");

include("auth.php");
include("conexion.php");

$sql = "SELECT id, titulo, autor, categoria, precio, stock, imagen
        FROM libros
        ORDER BY id DESC";

$resultado = $conexion->query($sql);

$libros = [];

while ($fila = $resultado->fetch_assoc()) {
    $libros[] = $fila;
}

echo json_encode([
    "exito" => true,
    "libros" => $libros
]);

$conexion->close();