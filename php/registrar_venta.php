<?php

header("Content-Type: application/json; charset=UTF-8");

include("auth.php");
include("conexion.php");

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

$idLibro = (int) ($datos["id_libro"] ?? 0);
$cantidad = (int) ($datos["cantidad"] ?? 0);
$cliente = trim($datos["cliente"] ?? "");

if ($idLibro <= 0 || $cantidad <= 0) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "Selecciona un libro y una cantidad válida."
    ]);
    exit();
}

$conexion->begin_transaction();

try {

    // Buscar el libro y bloquear temporalmente el registro
    $sqlLibro = "
        SELECT titulo, stock
        FROM libros
        WHERE id = ?
        FOR UPDATE
    ";

    $stmtLibro = $conexion->prepare($sqlLibro);

    if (!$stmtLibro) {
        throw new Exception(
            "Error al preparar la consulta del libro: " .
            $conexion->error
        );
    }

    $stmtLibro->bind_param("i", $idLibro);

    if (!$stmtLibro->execute()) {
        throw new Exception(
            "Error al consultar el libro: " .
            $stmtLibro->error
        );
    }

    $resultado = $stmtLibro->get_result();

    if ($resultado->num_rows === 0) {
        throw new Exception("El libro seleccionado no existe.");
    }

    $libro = $resultado->fetch_assoc();
    $stockActual = (int) $libro["stock"];

    if ($stockActual < $cantidad) {
        throw new Exception(
            "No hay suficiente stock. Existencias disponibles: " .
            $stockActual
        );
    }

    // Registrar la venta
    $sqlVenta = "
        INSERT INTO ventas
        (id_libro, cantidad, cliente)
        VALUES (?, ?, ?)
    ";

    $stmtVenta = $conexion->prepare($sqlVenta);

    if (!$stmtVenta) {
        throw new Exception(
            "Error al preparar la venta: " .
            $conexion->error
        );
    }

    $stmtVenta->bind_param(
        "iis",
        $idLibro,
        $cantidad,
        $cliente
    );

    if (!$stmtVenta->execute()) {
        throw new Exception(
            "Error al registrar la venta: " .
            $stmtVenta->error
        );
    }

    $idVenta = $conexion->insert_id;

    // Descontar existencias
    $sqlStock = "
        UPDATE libros
        SET stock = stock - ?
        WHERE id = ?
    ";

    $stmtStock = $conexion->prepare($sqlStock);

    if (!$stmtStock) {
        throw new Exception(
            "Error al preparar la actualización del stock: " .
            $conexion->error
        );
    }

    $stmtStock->bind_param(
        "ii",
        $cantidad,
        $idLibro
    );

    if (!$stmtStock->execute()) {
        throw new Exception(
            "Error al actualizar el stock: " .
            $stmtStock->error
        );
    }

    if ($stmtStock->affected_rows !== 1) {
        throw new Exception(
            "No se pudo actualizar el stock del libro."
        );
    }

    $conexion->commit();

    echo json_encode([
        "exito" => true,
        "mensaje" => "Venta registrada correctamente.",
        "id_venta" => $idVenta,
        "libro" => $libro["titulo"],
        "cantidad" => $cantidad,
        "stock_restante" => $stockActual - $cantidad
    ]);

} catch (Throwable $error) {

    $conexion->rollback();

    echo json_encode([
        "exito" => false,
        "mensaje" => $error->getMessage()
    ]);
}

if (isset($stmtLibro)) {
    $stmtLibro->close();
}

if (isset($stmtVenta)) {
    $stmtVenta->close();
}

if (isset($stmtStock)) {
    $stmtStock->close();
}

$conexion->close();