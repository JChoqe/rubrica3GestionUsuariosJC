<?php
/**
 * index.php
 * Página principal del Sistema de Registro de Usuarios
 */

require_once "utils.php";

// Al acceder a inicio se elimina cualquier sesión activa
borrarSesion();
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="./estilos/estilos.css">
    <title>Sistema de Registro de Usuarios</title>
</head>

<body>
    <div class="container">
        <div class="inicio-header">
            <h1>Sistema de Registro de Usuarios</h1>
            <p class="subtitulo">Gestión completa de usuarios con PHP y SQLite</p>
        </div>

        <div class="inicio-opciones">
            <div class="opcion-card">
                <h2>¿Nuevo Usuario?</h2>
                <p>Crea tu cuenta para acceder al sistema</p>
                <button class="btn primary" onclick="window.location.href='register.php'">
                    Registrarse
                </button>
            </div>

            <div class="opcion-card">
                <h2>¿Ya tienes cuenta?</h2>
                <p>Inicia sesión para gestionar tu perfil</p>
                <button class="btn secondary" onclick="window.location.href='login.php'">
                    Iniciar Sesión
                </button>
            </div>
        </div>
    </div>
</body>

</html>