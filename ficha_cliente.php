<?php
//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Incluyo mi clases necesarias
require_once "./models/Clientes.php";

//Creo mis dos clases de las entidades que necesito
$cli = new Cliente();

//Me traigo el query string del id
$cliente_id = $_GET['cliente_id'] ?? '0';

//Me traigo el query string del error si lo hubiera
$error = $_GET['error'] ?? '';

//Me traigo el query string de si viene de listado
$listado = $_GET['listado'] ?? '';

//Esta variable me indica si es nuevo
$nuevo = false;

//Esta variable me indica si es usuario normal y no admin
$es_usuario = false;
//Declaro un objeto usuario nuevo
if (isset($cliente_id) == true && $cliente_id != 0) {
    //Viene de listado
    $nuevo = false;
} else {
    //Viene de inicio entonces es el alta de un usuario normal
    $nuevo = true;
}

//echo $nuevo;

//Si es nuevo lo creo vacio si no cargo sus datos
$nombre = "";
$cif = "";
$email = "";
$telefono = "";
$apellidos = "";
$edad = 0;

//Obtengo por id mi cliente
if ($error != "") {

    //Si tengo error me traigo los datos del get por que me ha dado un error de validacion y no los quiero rellenar otra vez

    $nombre = $_GET['nombre'] ?? '';
    $cif = $_GET['cif'] ?? '';
    $email = $_GET['email'] ?? '';
    $telefono = $_GET['telefono'] ?? '';
    $apellidos = $_GET['apellidos'] ?? '';
    $edad = (int)($_GET['edad'] ?? 0);

    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";
} else {
    //Cojo los datos del usuario
    $cli = $cli->obtenerPorId($pdo, (int)$cliente_id);
    $nombre = $cli->getNombre();
    $cif = $cli->getCIF();
    $email = $cli->getEmail();
    $telefono = $cli->getTelefono();
    $apellidos = $cli->getApellidos();
    $edad = $cli->getEdad();
}


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
    <?php if ($nuevo): ?>
    <title>Nuevo Cliente</title>
    <?php else: ?>
    <title>Modificar Cliente</title>
    <?php endif; ?>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <?php if ($nuevo): ?>
        <h1>Nuevo Cliente</h1>
        <?php else: ?>
        <h1>Modificar Cliente</h1>
        <?php endif; ?>


        <form method="post">
            <input type="hidden" id="cliente_id" name="cliente_id"
                value="<?= $cliente_id ?>">
            <div>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre de cliente" required
                    value="<?= $nombre ?>">
            </div>

            <div>
                <label for="cif">CIF</label>
                <input type="text" id="cif" name="cif" placeholder="Cif del cliente" required
                    value="<?= $cif ?>">
            </div>

            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required
                    value="<?= $email ?>">
            </div>

            <div>
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" placeholder="teléfono" required
                    value="<?= $telefono ?>">
            </div>

            <div>
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos"
                    value="<?= $apellidos ?>">
            </div>

            <div>
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" placeholder="edad"
                    value="<?= $edad ?>">
            </div>


            <?php if ($nuevo): ?>
            <button
                onclick="anadirCliente(<?= ($listado == 'true' ? 'true' : '') ?>)">Crear
                Cliente</button>
            <?php else: ?>
            <button
                onclick="modificarCliente(<?= ($listado == 'true' ? 'true' : '') ?>)">Modificar
                Cliente</button>
            <?php endif; ?>

        </form>
    </div>
</body>

</html>