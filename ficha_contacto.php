<?php
//Me traigo el fichero que tiene todas las librerías básicas del proyecto
require_once "utils.php";

//Incluyo mis clases necesarias
require_once "./models/Contactos.php";
require_once "./models/Clientes.php";

//Creo mis clases
$cont = new Contacto();
$cli = new Cliente();

//Me traigo el query string del id del contacto
$contacto_id = $_GET['contacto_id'] ?? '0';

//Me traigo el query string del error si lo hubiera
$error = $_GET['error'] ?? '';

//Me traigo el query string de si viene de listado
$listado = $_GET['listado'] ?? '';

//Me traigo el cliente_id por si venimos de un cliente específico
$cliente_id = $_GET['cliente_id'] ?? '0';

//Esta variable me indica si es nuevo contacto
$nuevo = false;

//Declaro un objeto contacto nuevo o existente
if (isset($contacto_id) == true && $contacto_id != 0) {
    //Viene de listado - es una modificación
    $nuevo = false;
} else {
    //Es un alta de contacto nuevo
    $nuevo = true;
}

//Si es nuevo lo creo vacío, si no cargo sus datos
$nombre = "";
$apellidos = "";
$email = "";
$telefono = "";

//Obtengo por id mi contacto
if ($error != "") {
    //Si tengo error me traigo los datos del GET para no tener que rellenarlos otra vez
    $nombre = $_GET['nombre'] ?? '';
    $apellidos = $_GET['apellidos'] ?? '';
    $email = $_GET['email'] ?? '';
    $telefono = $_GET['telefono'] ?? '';
    $contacto_id = (int)($_GET['contacto_id'] ?? 0);
    $cliente_id = (int)($_GET['cliente_id'] ?? 0);
    
    // Muestro el error con saltos de línea
    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";
} else {
    //Cargo los datos del contacto desde la BD
    $cont = $cont->obtenerPorId($pdo, (int)$contacto_id);
    $nombre = $cont->getNombre();
    $apellidos = $cont->getApellidos();
    $email = $cont->getEmail();
    $telefono = $cont->getTelefono();
    $cliente_id = $cont->getClienteId();
}

//Extraigo el usuario conectado para verificar rol
if (isset($_SESSION["usuario"])) {
    $usu_conectado = $_SESSION["usuario"];
    $rol_id_usuario = $usu_conectado->getRolId();
}

//Obtengo todos los clientes para el select
$clientes = Cliente::obtenerTodos($pdo);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php if ($nuevo): ?>
    <title>Nuevo Contacto</title>
    <?php else: ?>
    <title>Modificar Contacto</title>
    <?php endif; ?>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <?php if ($nuevo): ?>
        <h1>Nuevo Contacto</h1>
        <?php else: ?>
        <h1>Modificar Contacto</h1>
        <?php endif; ?>

        <form method="post">
            <!-- Campo oculto con el ID del contacto -->
            <input type="hidden" id="contacto_id" name="contacto_id" value="<?= $contacto_id ?>">
            
            <div>
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del contacto" required
                    value="<?= htmlspecialchars($nombre) ?>">
            </div>

            <div>
                <label for="apellidos">Apellidos *</label>
                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos del contacto" required
                    value="<?= htmlspecialchars($apellidos) ?>">
            </div>

            <div>
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required
                    value="<?= htmlspecialchars($email) ?>">
            </div>

            <div>
                <label for="telefono">Teléfono (España) *</label>
                <input type="text" id="telefono" name="telefono" placeholder="Ej: 612345678 o 912345678" required
                    value="<?= htmlspecialchars($telefono) ?>">
                <small>Formato: 9 dígitos (móvil: 6/7, fijo: 8/9)</small>
            </div>

            <div>
                <label for="cliente_id">Cliente *</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">-- Selecciona un cliente --</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente->getId() ?>" 
                            <?= ($cliente->getId() == $cliente_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cliente->getNombre() . " " . $cliente->getApellidos()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($nuevo): ?>
            <button onclick="anadirContacto(<?= ($listado == 'true' ? 'true' : 'false') ?>, <?= $cliente_id ?>)">
                Crear Contacto
            </button>
            <?php else: ?>
            <button onclick="modificarContacto(<?= ($listado == 'true' ? 'true' : 'false') ?>, <?= $cliente_id ?>)">
                Modificar Contacto
            </button>
            <?php endif; ?>

        </form>
    </div>
</body>

</html>