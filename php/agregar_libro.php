<?php

header("Content-Type: application/json; charset=UTF-8");

include("auth.php");
include("conexion.php");

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

$titulo = trim($datos["titulo"] ?? "");
$autor = trim($datos["autor"] ?? "");
$categoria = trim($datos["categoria"] ?? "");
$precio = $datos["precio"] ?? "";
$stock = $datos["stock"] ?? "";
$imagen = trim($datos["imagen"] ?? "");

if (
    $titulo === "" ||
    $autor === "" ||
    $categoria === "" ||
    !is_numeric($precio) ||
    !is_numeric($stock)
) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "Completa correctamente todos los campos."
    ]);
    exit();
}

$precio = (float) $precio;
$stock = (int) $stock;

$sql = "INSERT INTO libros
        (titulo, autor, categoria, precio, stock, imagen)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "sssdis",
    $titulo,
    $autor,
    $categoria,
    $precio,
    $stock,
    $imagen
);

if ($stmt->execute()) {

    $idInsertado = $conexion->insert_id;
    $filasAfectadas = $stmt->affected_rows;

    $resultadoBD = $conexion->query(
        "SELECT DATABASE() AS base_datos"
    );

    $filaBD = $resultadoBD->fetch_assoc();

    echo json_encode([
        "exito" => true,
        "mensaje" => "Libro registrado correctamente.",
        "id_insertado" => $idInsertado,
        "filas_afectadas" => $filasAfectadas,
        "base_datos" => $filaBD["base_datos"]
    ]);

} else {

    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo registrar el libro.",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conexion->close();