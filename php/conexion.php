<?php

$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "biblioteca_flakk";

$conexion = mysqli_connect(
    $host,
    $usuario,
    $password,
    $baseDatos
);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

?>