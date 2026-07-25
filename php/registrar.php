<?php

include("conexion.php");

// Evita que entren directamente escribiendo registrar.php en Chrome
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/registro.html");
    exit();
}

// Recibir y limpiar datos
$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";
$confirmar = $_POST["confirmar"] ?? "";

// Comprobar que ningún campo esté vacío
if (
    $nombre === "" ||
    $correo === "" ||
    $usuario === "" ||
    $password === "" ||
    $confirmar === ""
) {
    echo "<script>
        alert('Debes completar todos los campos');
        window.location='../html/registro.html';
    </script>";
    exit();
}

// Validar correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
        alert('El correo electrónico no es válido');
        window.location='../html/registro.html';
    </script>";
    exit();
}

// Longitud mínima de la contraseña
if (strlen($password) < 8) {
    echo "<script>
        alert('La contraseña debe tener mínimo 8 caracteres');
        window.location='../html/registro.html';
    </script>";
    exit();
}

// Verificar que las contraseñas coincidan
if ($password !== $confirmar) {
    echo "<script>
        alert('Las contraseñas no coinciden');
        window.location='../html/registro.html';
    </script>";
    exit();
}

// Revisar si el usuario o correo ya existen
$consulta = mysqli_prepare(
    $conexion,
    "SELECT id FROM usuarios
     WHERE usuario = ? OR correo = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $consulta,
    "ss",
    $usuario,
    $correo
);

mysqli_stmt_execute($consulta);
mysqli_stmt_store_result($consulta);

if (mysqli_stmt_num_rows($consulta) > 0) {
    echo "<script>
        alert('El usuario o correo ya está registrado');
        window.location='../html/registro.html';
    </script>";
    exit();
}

mysqli_stmt_close($consulta);

// Encriptar la contraseña
$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

// Guardar usuario con consulta preparada
$insertar = mysqli_prepare(
    $conexion,
    "INSERT INTO usuarios
    (nombre, correo, usuario, password)
    VALUES (?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $insertar,
    "ssss",
    $nombre,
    $correo,
    $usuario,
    $passwordHash
);

if (mysqli_stmt_execute($insertar)) {

    echo "<script>
        alert('Usuario registrado correctamente');
        window.location='../html/index.html';
    </script>";

} else {

    echo "<script>
        alert('Ocurrió un error al registrar el usuario');
        window.location='../html/registro.html';
    </script>";
}

mysqli_stmt_close($insertar);
mysqli_close($conexion);

?>