<?php
/**
 * login.php
 * Formulario de inicio de sesión
 */

require_once "./models/Usuarios.php";
require_once "./models/Roles.php";
require_once "utils.php";

// Al entrar en login se elimina cualquier sesión previa
borrarSesion();

$usu = new Usuario();
$rol = new Rol();

// Parámetros recibidos por query string
$accion = $_GET['accion'] ?? '';
$error = $_GET['error'] ?? '';
$registro = $_GET['registro'] ?? '';

// Gestión de mensajes
$usuario = "";
$mensaje_success = "";

if ($error !== "") {
    $usuario = $_GET['usuario'] ?? '';
    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";
}

if ($accion === "sesioncaducada") {
    echo "<script>alert('Se te ha caducado la sesión, vuelve a hacer login')</script>";
}

if ($registro === "exitoso") {
    $usuario = $_GET['usuario'] ?? '';
    $mensaje_success = "¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta.";
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>

        <?php if ($mensaje_success !== ""): ?>
            <div class="alerta success">
                <?= htmlspecialchars($mensaje_success) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="campo">
                <label for="usuario">Usuario</label>
                <input type="text" 
                       id="usuario" 
                       name="usuario" 
                       placeholder="Nombre de usuario" 
                       value="<?= htmlspecialchars($usuario) ?>"
                       required
                       autofocus>
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="••••••••"
                       required>
            </div>

            <div class="acciones">
                <button type="button" onclick="login()" class="btn primary">Entrar</button>
            </div>
        </form>

        <p class="enlace">
            ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
        </p>
        
        <p class="enlace">
            <a href="index.php">← Volver al inicio</a>
        </p>
    </div>
</body>

</html>