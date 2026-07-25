<?php

session_start();

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/index.html");
    exit();
}

$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";

if ($usuario === "" || $password === "") {
    echo "<script>
        alert('Debes escribir el usuario y la contraseña');
        window.location='index.html';
    </script>";
    exit();
}

$consulta = mysqli_prepare(
    $conexion,
    "SELECT id, nombre, usuario, password
    FROM usuarios
    WHERE usuario = ?
    LIMIT 1"
);

mysqli_stmt_bind_param(
    $consulta,
    "s",
    $usuario
);

mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);

$datosUsuario = mysqli_fetch_assoc($resultado);

if (
    $datosUsuario &&
    password_verify(
        $password,
        $datosUsuario["password"]
    )
) {
    session_regenerate_id(true);

    $_SESSION["id"] = $datosUsuario["id"];
    $_SESSION["nombre"] = $datosUsuario["nombre"];
    $_SESSION["usuario"] = $datosUsuario["usuario"];

    header("Location: menu.php");
    exit();
}

echo "<script>
    alert('Usuario o contraseña incorrectos');
    window.location='index.html';
</script>";

?>
