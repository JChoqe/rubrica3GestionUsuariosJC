<?php
// Carga de utilidades comunes del proyecto (sesión, BD, validaciones, etc.)
require_once "utils.php";

// Inclusión del modelo Cliente
require_once "./models/Clientes.php";

// Instanciamos el modelo Cliente
$cli = new Cliente();

// Obtención de parámetros enviados por GET
$cliente_id = $_GET['cliente_id'] ?? 0;
$error      = $_GET['error'] ?? '';
$listado   = $_GET['listado'] ?? '';

// Indica si se trata de un alta o una modificación
$nuevo = ($cliente_id == 0);

// Inicialización de variables del formulario
$nombre     = "";
$cif        = "";
$email      = "";
$telefono   = "";
$apellidos  = "";
$edad       = 0;

// Si existe un error de validación, se recuperan los datos enviados
// para no obligar al usuario a rellenar de nuevo el formulario
if ($error != "") {

    $nombre     = $_GET['nombre'] ?? '';
    $cif        = $_GET['cif'] ?? '';
    $email      = $_GET['email'] ?? '';
    $telefono   = $_GET['telefono'] ?? '';
    $apellidos  = $_GET['apellidos'] ?? '';
    $edad       = (int)($_GET['edad'] ?? 0);

    // Muestra el mensaje de error al usuario
    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";

} else {
    // Si no hay errores, se cargan los datos del cliente desde la base de datos
    $cli = $cli->obtenerPorId($pdo, (int)$cliente_id);
    $nombre     = $cli->getNombre();
    $cif        = $cli->getCIF();
    $email      = $cli->getEmail();
    $telefono   = $cli->getTelefono();
    $apellidos  = $cli->getApellidos();
    $edad       = $cli->getEdad();
}

// Obtención del usuario conectado para controlar permisos
if (isset($_SESSION["usuario"])) {
    $usu_conectado  = $_SESSION["usuario"];
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