<?php
/**
 * register.php
 * Página de registro de nuevos usuarios
 */

require_once "utils.php";
require_once "./models/Usuarios.php";

// Al entrar en registro, eliminamos cualquier sesión previa
borrarSesion();

// Parámetros de error si la validación falla
$error = $_GET['error'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$email = $_GET['email'] ?? '';
$usuario = $_GET['usuario'] ?? '';

// Si hay errores, mostrarlos
if ($error !== "") {
    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <h1>Crear Nueva Cuenta</h1>
        <p class="subtitulo">Complete el formulario para registrarse en el sistema</p>

        <form method="post" action="procesar_registro.php">
            
            <div class="campo">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" 
                       id="nombre" 
                       name="nombre" 
                       placeholder="Ingrese su nombre completo" 
                       value="<?= htmlspecialchars($nombre) ?>"
                       required>
            </div>

            <div class="campo">
                <label for="usuario">Nombre de Usuario *</label>
                <input type="text" 
                       id="usuario" 
                       name="usuario" 
                       placeholder="Elija un nombre de usuario" 
                       value="<?= htmlspecialchars($usuario) ?>"
                       required>
                <small>Este será su identificador único en el sistema</small>
            </div>

            <div class="campo">
                <label for="email">Correo Electrónico *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       placeholder="correo@ejemplo.com" 
                       value="<?= htmlspecialchars($email) ?>"
                       required>
            </div>

            <div class="campo">
                <label for="password">Contraseña *</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="••••••••" 
                       required>
                <small>Mínimo 8 caracteres, una mayúscula, un número y un carácter especial</small>
            </div>

            <div class="campo">
                <label for="password_confirm">Confirmar Contraseña *</label>
                <input type="password" 
                       id="password_confirm" 
                       name="password_confirm" 
                       placeholder="••••••••" 
                       required>
            </div>

            <div class="acciones">
                <button type="submit" class="btn primary">Registrarse</button>
            </div>
        </form>

        <p class="enlace">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </p>
        
        <p class="enlace">
            <a href="index.php">← Volver al inicio</a>
        </p>
    </div>
</body>

</html>