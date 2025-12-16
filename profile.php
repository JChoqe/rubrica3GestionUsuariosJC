<?php

/**
 * profile.php
 * 
 * Página de perfil de usuario
 * Permite ver y editar datos personales y cambiar contraseña
 */

require_once "utils.php";
require_once "./models/Usuarios.php";

// Verificar sesión activa
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php?accion=sesioncaducada');
    exit;
}

$usu_conectado = $_SESSION["usuario"];
$usuario = Usuario::obtenerPorId($pdo, $usu_conectado->getId());

if (!$usuario || $usuario->getId() === 0) {
    borrarSesion();
    header('Location: login.php?error=' . urlencode('Error al cargar perfil'));
    exit;
}

// Mensajes
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

if ($error !== "") {
    echo "<script>alert('" . str_replace("--", "\\n", addslashes($error)) . "');</script>";
}

if ($success !== "") {
    echo "<script>alert('" . addslashes($success) . "');</script>";
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <h1>Mi Perfil</h1>

        <div class="barra-navegacion">
            <?php if ($usuario->getRolId() == 1): ?>
                <button class="btn secondary" onclick="IrListadoUsuarios()">
                    Usuarios
                </button>
            <?php endif; ?>

            <button class="btn secondary" onclick="IrListadoClientes()">
                Clientes
            </button>

            <button class="btn danger" onclick="cerrarSesion()">
                Cerrar Sesión
            </button>
        </div>

        <!-- SECCIÓN: DATOS PERSONALES -->
        <div class="seccion-perfil">
            <h2>Datos Personales</h2>

            <form method="post" action="actualizar_perfil.php">
                <?= campoCSRF() ?>
                <input type="hidden" name="accion" value="actualizar_datos">

                <div class="campo">
                    <label for="usuario">Usuario</label>
                    <input type="text"
                        id="usuario"
                        name="usuario"
                        value="<?= htmlspecialchars($usuario->getUsuario()) ?>"
                        disabled
                        class="input-disabled">
                    <small>El nombre de usuario no se puede cambiar</small>
                </div>

                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text"
                        id="nombre"
                        name="nombre"
                        value="<?= htmlspecialchars($usuario->getNombre()) ?>"
                        required>
                </div>

                <div class="campo">
                    <label for="apellidos">Apellidos</label>
                    <input type="text"
                        id="apellidos"
                        name="apellidos"
                        value="<?= htmlspecialchars($usuario->getApellidos()) ?>">
                </div>

                <div class="campo">
                    <label for="email">Email *</label>
                    <input type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($usuario->getEmail()) ?>"
                        required>
                </div>

                <div class="campo">
                    <label>Rol</label>
                    <input type="text"
                        value="<?= $usuario->getRolId() == 1 ? 'Administrador' : 'Usuario' ?>"
                        disabled
                        class="input-disabled">
                </div>

                <button type="submit" class="btn primary">💾 Actualizar Datos</button>
            </form>
        </div>

        <!-- SECCIÓN: CAMBIAR CONTRASEÑA -->
        <div class="seccion-perfil">
            <h2>Cambiar Contraseña</h2>

            <form method="post" action="actualizar_perfil.php">
                <?= campoCSRF() ?>
                <input type="hidden" name="accion" value="cambiar_password">

                <div class="campo">
                    <label for="password_actual">Contraseña Actual *</label>
                    <input type="password"
                        id="password_actual"
                        name="password_actual"
                        placeholder="••••••••"
                        required>
                </div>

                <div class="campo">
                    <label for="password_nueva">Nueva Contraseña *</label>
                    <input type="password"
                        id="password_nueva"
                        name="password_nueva"
                        placeholder="••••••••"
                        required>
                    <small>Mínimo 8 caracteres, una mayúscula, un número y un carácter especial</small>
                </div>

                <div class="campo">
                    <label for="password_confirmar">Confirmar Nueva Contraseña *</label>
                    <input type="password"
                        id="password_confirmar"
                        name="password_confirmar"
                        placeholder="••••••••"
                        required>
                </div>

                <button type="submit" class="btn warning">🔒 Cambiar Contraseña</button>
            </form>
        </div>
    </div>
</body>

</html>