<?php
/**
 * listado_clientes.php
 * 
 * Listado de clientes del sistema
 * Con acceso a sus contactos
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

require_once "utils.php";
require_once "./models/Clientes.php";
require_once "./models/Contactos.php";

// Verificar sesión activa
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php?accion=sesioncaducada');
    exit;
}

// Obtener usuario conectado
$usu_conectado = $_SESSION["usuario"];
$rol_id_usuario = $usu_conectado->getRolId();

// Cargar todos los clientes
$clientes = Cliente::obtenerTodos($pdo);
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="tabla-contenedor">
        <h1>Gestión de Clientes</h1>

        <!-- Barra de navegación -->
        <div class="barra-navegacion">
            <button class="btn info" onclick="window.location.href='profile.php'">
                Mi Perfil
            </button>
            
            <button class="btn secondary" onclick="window.location.href='listado_contactos.php'">
                Todos los Contactos
            </button>
            
            <?php if ($rol_id_usuario == 1): ?>
                <button class="btn secondary" onclick="IrListadoUsuarios()">
                    Usuarios
                </button>
            <?php endif; ?>

            <button class="btn danger" onclick="cerrarSesion()">
                Cerrar Sesión
            </button>
        </div>

        <!-- Botón añadir cliente (solo admin) -->
        <?php if ($rol_id_usuario == 1): ?>
            <a href="#" onclick="javascript:IrFichaCliente(true)" class="btn primary anadir">
                ➕ Añadir Cliente
            </a>
        <?php endif; ?>

        <!-- Tabla de clientes -->
        <?php if (empty($clientes)): ?>
            <p class="info">No hay clientes registrados aún.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>CIF</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Contactos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                        <?php 
                        // Contar contactos de este cliente
                        $num_contactos = Contacto::contarPorCliente($pdo, $c->getId());
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($c->getId()) ?></td>
                            <td><?= htmlspecialchars($c->getNombre()) ?></td>
                            <td><?= htmlspecialchars($c->getCIF()) ?></td>
                            <td><?= htmlspecialchars($c->getEmail()) ?></td>
                            <td><?= htmlspecialchars(formatearTelefono($c->getTelefono())) ?></td>
                            <td>
                                <a href="listado_contactos.php?cliente_id=<?= $c->getId() ?>" 
                                   class="btn-contactos">
                                    <span class="badge-contactos"><?= $num_contactos ?></span>
                                    Ver Contactos
                                </a>
                            </td>
                            <td class="acciones">
                                <?php if ($rol_id_usuario == 1): ?>
                                    <a class="btn editar"
                                       href="ficha_cliente.php?cliente_id=<?= $c->getId() ?>&listado=true">
                                        Editar
                                    </a>

                                    <button class="btn borrar"
                                            onclick="eliminarCliente(<?= $c->getId() ?>)">
                                        Borrar
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Formulario oculto para eliminación -->
    <form action="" method="post" id="frmEli" name="frmEli" style="visibility: hidden;">
        <input type="hidden" name="cliente_id" id="cliente_id">
    </form>
</body>

</html>