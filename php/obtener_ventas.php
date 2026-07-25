<?php

header("Content-Type: application/json; charset=UTF-8");

include("auth.php");
include("conexion.php");

$sql = "
    SELECT
        ventas.id,
        ventas.id_libro,
        libros.titulo AS libro,
        ventas.cantidad,
        ventas.cliente,
        ventas.fecha
    FROM ventas
    INNER JOIN libros
        ON ventas.id_libro = libros.id
    ORDER BY ventas.fecha DESC, ventas.id DESC
";

$resultado = $conexion->query($sql);

if (!$resultado) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo cargar el historial de ventas.",
        "error" => $conexion->error
    ]);
    exit();
}

$ventas = [];

while ($fila = $resultado->fetch_assoc()) {
    $ventas[] = $fila;
}

echo json_encode([
    "exito" => true,
    "ventas" => $ventas
]);

$conexion->close();