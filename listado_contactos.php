<?php
//Me traigo el fichero que tiene todas las librerías básicas del proyecto
require_once "utils.php";
require_once "./models/Contactos.php";
require_once "./models/Clientes.php";

// Creo mis clases de las entidades que necesito
$cont = new Contacto();
$cli = new Cliente();

// Obtengo el cliente_id del query string (si viene desde un cliente específico)
$cliente_id = $_GET['cliente_id'] ?? null;

// Variable para saber si estamos filtrando por cliente
$filtrado = false;
$nombre_cliente = "";

// Si viene cliente_id, filtro los contactos de ese cliente
if ($cliente_id !== null && $cliente_id > 0) {
    $contactos = Contacto::obtenerPorCliente($pdo, (int)$cliente_id);
    $filtrado = true;
    
    // Obtengo el nombre del cliente para mostrarlo
    $cliente = Cliente::obtenerPorId($pdo, (int)$cliente_id);
    $nombre_cliente = $cliente->getNombre() . " " . $cliente->getApellidos();
} else {
    // Si NO viene cliente_id, muestro TODOS los contactos
    $contactos = Contacto::obtenerTodos($pdo);
}

// Extraigo el usuario conectado para verificar si es admin
if (isset($_SESSION["usuario"])) {
    $usu_conectado = $_SESSION["usuario"];
    $rol_id_usuario = $usu_conectado->getRolId();
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Listado de Contactos</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="tabla-contenedor">

        <?php if ($filtrado): ?>
            <!-- Si estamos filtrando, mostramos el nombre del cliente -->
            <h1>Contactos de: <?= htmlspecialchars($nombre_cliente) ?></h1>
        <?php else: ?>
            <!-- Si NO filtramos, mostramos todos -->
            <h1>Todos los Contactos</h1>
        <?php endif; ?>

        <!-- Botón para cerrar sesión -->
        <button class="btn cerrarsesion" onclick="cerrarSesion()">
            Cerrar Sesión
        </button>

        <!-- Botón para volver a la lista de clientes -->
        <button class="btn cerrarsesion" onclick="IrListadoClientes()">
            Volver a Clientes
        </button>

        <?php if ($rol_id_usuario == 1): ?>
            <!-- Solo Admin puede añadir contactos -->
            <a href="ficha_contacto.php?contacto_id=..." onclick="javascript:IrFichaContacto(true, <?= $cliente_id ?? 0 ?>)" class="btn primary anadir">
                ➕ Añadir Contacto
            </a>
        <?php endif; ?>

        <!-- Tabla con todos los contactos -->
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Cliente</th>
                <?php if ($rol_id_usuario == 1): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>

            <?php foreach ($contactos as $c): ?>
            <tr>
                <td><?= $c->getId() ?></td>
                <td><?= htmlspecialchars($c->getNombre()) ?></td>
                <td><?= htmlspecialchars($c->getApellidos()) ?></td>
                <td><?= htmlspecialchars($c->getEmail()) ?></td>
                <td><?= htmlspecialchars($c->getTelefono()) ?></td>
                <td>
                    <?php 
                        // Obtengo el nombre del cliente al que pertenece este contacto
                        $cliente_contacto = Cliente::obtenerPorId($pdo, $c->getClienteId());
                        echo htmlspecialchars($cliente_contacto->getNombre() . " " . $cliente_contacto->getApellidos());
                    ?>
                </td>
                <?php if ($rol_id_usuario == 1): ?>
                    <!-- Solo Admin ve los botones de editar y borrar -->
                    <td class="acciones">
                        <a class="btn editar"
                            href="ficha_contacto.php?contacto_id=<?= $c->getId() ?>&listado=true&cliente_id=<?= $cliente_id ?? 0 ?>">
                            Editar
                        </a>

                        <button class="btn borrar"
                            onclick="eliminarContacto(<?= $c->getId() ?>, <?= $cliente_id ?? 0 ?>)">
                            Borrar
                        </button>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>

    <!-- Formulario oculto para eliminar (lo usa JavaScript) -->
    <form action="" method="post" id="frmEli" name="frmEli" style="visibility: hidden;">
        <input type="hidden" name="contacto_id" id="contacto_id">
        <input type="hidden" name="cliente_id_hidden" id="cliente_id_hidden">
    </form>
</body>

</html>
