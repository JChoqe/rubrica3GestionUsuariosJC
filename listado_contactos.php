<?php

/**
 * listado_contactos.php
 * 
 * Listado de contactos del sistema
 * Puede mostrar todos los contactos o filtrar por cliente
 */

require_once "utils.php";
require_once "./models/Contactos.php";
require_once "./models/Clientes.php";

// Verificar sesión activa
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php?accion=sesioncaducada');
    exit;
}

// Obtener usuario conectado
$usu_conectado = $_SESSION["usuario"];
$rol_id_usuario = $usu_conectado->getRolId();

// Verificar filtro por cliente
$cliente_id = (int)($_GET['cliente_id'] ?? 0);
$nombre_cliente = '';
$cliente = null;

// Cargar contactos según filtro
try {
    if ($cliente_id > 0) {
        // Contactos de un cliente específico
        $cliente = Cliente::obtenerPorId($pdo, $cliente_id);

        if ($cliente && $cliente->getId() > 0) {
            $nombre_cliente = $cliente->getNombre();
            $contactos = Contacto::obtenerPorCliente($pdo, $cliente_id);
        } else {
            header('Location: listado_clientes.php?error=' . urlencode('Cliente no encontrado'));
            exit;
        }
    } else {
        // Todos los contactos
        $contactos = Contacto::obtenerTodos($pdo);
    }
} catch (Exception $e) {
    error_log("Error al cargar contactos: " . $e->getMessage());
    $contactos = [];
}

// Mensaje de éxito
$success = $_GET['success'] ?? '';
if ($success !== '') {
    echo "<script>alert('" . htmlspecialchars($success, ENT_QUOTES) . "');</script>";
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Gestión de Contactos</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="tabla-contenedor">

        <?php if ($cliente_id > 0): ?>
            <h1>Contactos de: <?= htmlspecialchars($nombre_cliente) ?></h1>
        <?php else: ?>
            <h1>Todos los Contactos</h1>
        <?php endif; ?>

        <!-- Barra de navegación -->
        <div class="barra-navegacion">
            <button class="btn info" onclick="window.location.href='profile.php'">
                Mi Perfil
            </button>

            <?php if ($cliente_id > 0): ?>
                <button class="btn secondary" onclick="window.location.href='listado_clientes.php'">
                    ← Volver a Clientes
                </button>
            <?php else: ?>
                <button class="btn secondary" onclick="window.location.href='listado_clientes.php'">
                    Clientes
                </button>
            <?php endif; ?>

            <?php if ($rol_id_usuario == 1): ?>
                <button class="btn secondary" onclick="window.location.href='listado.php'">
                    Usuarios
                </button>
            <?php endif; ?>

            <button class="btn danger" onclick="cerrarSesion()">
                Cerrar Sesión
            </button>
        </div>

        <!-- Botón añadir (solo admin) -->
        <?php if ($rol_id_usuario == 1): ?>
            <?php if ($cliente_id > 0): ?>
                <a href="ficha_contacto.php?cliente_id=<?= $cliente_id ?>&volver_cliente=true"
                    class="btn primary anadir">
                    Añadir Contacto
                </a>
            <?php else: ?>
                <a href="ficha_contacto.php" class="btn primary anadir">
                    Añadir Contacto
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Tabla de contactos -->
        <?php if (empty($contactos)): ?>
            <p class="info">
                <?php if ($cliente_id > 0): ?>
                    Este cliente no tiene contactos registrados aún.
                <?php else: ?>
                    No hay contactos registrados en el sistema.
                <?php endif; ?>
            </p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <?php if ($cliente_id == 0): ?>
                            <th>Cliente</th>
                        <?php endif; ?>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <?php if ($rol_id_usuario == 1): ?>
                            <th>Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contactos as $contacto): ?>
                        <tr>
                            <td><?= htmlspecialchars($contacto->getId()) ?></td>

                            <?php if ($cliente_id == 0): ?>
                                <td>
                                    <?php
                                    $cli = Cliente::obtenerPorId($pdo, $contacto->getClienteId());
                                    echo $cli && $cli->getId() > 0 ? htmlspecialchars($cli->getNombre()) : 'N/A';
                                    ?>
                                </td>
                            <?php endif; ?>

                            <td>
                                <?= htmlspecialchars($contacto->getNombre() . ' ' . $contacto->getApellidos()) ?>
                            </td>
                            <td><?= htmlspecialchars($contacto->getEmail()) ?></td>
                            <td><?= htmlspecialchars(formatearTelefono($contacto->getTelefono())) ?></td>

                            <?php if ($rol_id_usuario == 1): ?>
                                <td class="acciones">
                                    <?php
                                    $url_editar = "ficha_contacto.php?contacto_id=" . $contacto->getId();
                                    if ($cliente_id > 0) {
                                        $url_editar .= "&volver_cliente=true&cliente_id=" . $cliente_id;
                                    }
                                    ?>
                                    <a class="btn editar" href="<?= $url_editar ?>">
                                        Editar
                                    </a>

                                    <button class="btn borrar"
                                        onclick="eliminarContacto(<?= $contacto->getId() ?>, <?= $cliente_id ?>)">
                                        Borrar
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Formulario oculto para eliminación -->
    <?php if ($rol_id_usuario == 1): ?>
        <form method="post" id="frmEli" name="frmEli" style="display: none;">
            <input type="hidden" name="contacto_id" id="contacto_id">
            <input type="hidden" name="cliente_id_filtro" id="cliente_id_filtro" value="<?= $cliente_id ?>">
        </form>
    <?php endif; ?>
</body>

</html>