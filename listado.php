<?php
/**
 * listado.php
 * Panel principal para usuarios autenticados
 */

require_once "utils.php";
require_once "./models/Usuarios.php";
require_once "./models/Roles.php";

// Verificar sesión activa
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php?accion=sesioncaducada');
    exit;
}

$usu = new Usuario();
$rol = new Rol();

// Obtiene el listado completo de usuarios
$usuarios = $usu->obtenerTodos($pdo);

// Usuario conectado y su rol
$usu_conectado = $_SESSION["usuario"];
$rol_id_usuario = $usu_conectado->getRolId();
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="tabla-contenedor">
        <h1>Panel de Administración - Usuarios</h1>

        <div class="barra-navegacion">
            <button class="btn info" onclick="window.location.href='profile.php'">
                Mi Perfil
            </button>
            
            <button class="btn secondary" onclick="IrListadoClientes()">
                Clientes
            </button>

            <button class="btn danger" onclick="cerrarSesion()">
                Cerrar Sesión
            </button>
        </div>

        <?php if ($rol_id_usuario == 1): ?>
            <a href="#" onclick="IrFicha(true)" class="btn primary anadir">
                ➕ Añadir Usuario
            </a>
        <?php endif; ?>

        <?php if (empty($usuarios)): ?>
            <p class="info">No hay usuarios registrados aún.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Rol</th>
                    <?php if ($rol_id_usuario == 1): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>

                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u->getId()) ?></td>
                        <td><?= htmlspecialchars($u->getUsuario()) ?></td>
                        <td><?= htmlspecialchars($u->getEmail()) ?></td>
                        <td><?= htmlspecialchars($u->getNombre()) ?></td>
                        <td><?= htmlspecialchars($u->getApellidos()) ?></td>
                        <td>
                            <span class="badge <?= $u->getRolId() == 1 ? 'badge-admin' : 'badge-user' ?>">
                                <?= $u->getRolId() == 1 ? 'Admin' : 'Usuario' ?>
                            </span>
                        </td>

                        <?php if ($rol_id_usuario == 1): ?>
                            <td class="acciones">
                                <a class="btn editar" 
                                   href="ficha.php?usuario_id=<?= $u->getId() ?>&listado=true">
                                    Editar
                                </a>

                                <?php if ($u->getId() != $usu_conectado->getId()): ?>
                                    <button class="btn borrar" 
                                            onclick="eliminarUsuario(<?= $u->getId() ?>)">
                                        Borrar
                                    </button>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- Formulario oculto para eliminación vía POST -->
    <form method="post" id="frmEli" name="frmEli" style="visibility: hidden;">
        <input type="hidden" name="usuario_id" id="usuario_id">
    </form>
</body>

</html>