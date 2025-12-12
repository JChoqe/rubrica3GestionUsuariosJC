<?php
//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php');
    exit();
}
require_once "./models/Usuarios.php";
require_once "./models/Roles.php";

$usu = new Usuario();
$rol = new Rol();

$usuarios = $usu->obtenerTodos($pdo);

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
    <title>Listado de Usuarios</title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="tabla-contenedor">

        <h1>Listado de Usuarios</h1>
        <button class="btn cerrarsesion" onclick="cerrarSesion()">
            Cerrar Sesion
        </button>
        <button class="btn cerrarsesion" onclick="IrListadoClientes()">
            Clientes
        </button>
        <?php if ($rol_id_usuario == 1): ?>
            <a href="#" onclick="javascript:IrFicha(true)" class="btn primary anadir">➕ Añadir Usuario</a>
        <?php endif; ?>

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
                    <td><?= $u->getId() ?></td>
                    <td><?= $u->getUsuario() ?></td>
                    <td><?= $u->getEmail() ?></td>
                    <td><?= $u->getNombre() ?></td>
                    <td><?= $u->getApellidos() ?></td>
                    <td><?= $u->getRolId() == 1 ? 'Admin' : 'Usuario' ?>
                    </td>
                    <?php if ($rol_id_usuario == 1): ?>
                        <td class="acciones">


                            <a class="btn editar"
                                href="ficha.php?usuario_id=<?= $u->getId() ?>&listado=true">
                                Editar
                            </a>

                            <button class="btn borrar"
                                onclick="eliminarUsuario(<?= $u->getId() ?>)">
                                Borrar
                            </button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>
    <form action="" method="post" id="frmEli" name="frmEli" style="visibility: hidden;">
        <input type="hidden" name="usuario_id" id="usuario_id">
    </form>
</body>

</html>