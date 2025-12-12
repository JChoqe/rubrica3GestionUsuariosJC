<?php
//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";
require_once "./models/Clientes.php";


$cli = new Cliente();

$clientes = $cli->obtenerTodos($pdo);

//Extraigo el usuario conectado
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
    <title>Listado de Clientes</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="tabla-contenedor">

        <h1>Listado de Clientes</h1>
        <button class="btn cerrarsesion" onclick="cerrarSesion()">
            Cerrar Sesion
        </button>
        <button class="btn cerrarsesion" onclick="IrListadoUsuarios()">
            Usuarios
        </button>
        <?php if ($rol_id_usuario == 1): ?>
            <a href="#" onclick="javascript:IrFichaCliente(true)" class="btn primary anadir">➕ Añadir Clientes</a>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Cif</th>
                <th>Edad</th>
                <?php if ($rol_id_usuario == 1): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>

            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= $c->getId() ?></td>
                    <td><?= $c->getNombre() ?></td>
                    <td><?= $c->getApellidos() ?></td>
                    <td><?= $c->getEmail() ?></td>
                    <td><?= $c->getTelefono() ?></td>
                    <td><?= $c->getCIF() ?></td>
                    <td><?= $c->getEdad() ?></td>
                    <?php if ($rol_id_usuario == 1): ?>
                        <td class="acciones">
                            <a class="btn editar"
                                href="ficha_cliente.php?cliente_id=<?= $c->getId() ?>&listado=true">
                                Editar
                            </a>

                            <button class="btn borrar"
                                onclick="eliminarCliente(<?= $c->getId() ?>)">
                                Borrar
                            </button>

                            <!-- NUEVO BOTÓN -->
                            <a class="btn primary"
                                href="listado_contactos.php?cliente_id=<?= $c->getId() ?>">
                                Ver Contactos
                            </a>
                        </td>

                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>
    <form action="" method="post" id="frmEli" name="frmEli" style="visibility: hidden;">
        <input type="hidden" name="cliente_id" id="cliente_id">
    </form>
</body>

</html>