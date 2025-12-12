<?php
//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Incluyo mi clases necesarias
require_once "./models/Usuarios.php";
require_once "./models/Roles.php";

//Creo mis dos clases de las entidades que necesito
$usu = new Usuario();
$rol = new Rol();

//Me traigo el query string del id
$usuario_id = $_GET['usuario_id'] ?? '0';

//Me traigo el query string del error si lo hubiera
$error = $_GET['error'] ?? '';

//Me traigo el query string de si viene de listado
$listado = $_GET['listado'] ?? '';

//Esta variable me indica si es nuevo
$nuevo = false;

//Declaro un objeto usuario nuevo
if (isset($usuario_id) == true && $usuario_id != 0) {
    //Viene de listado
    $nuevo = false;
} else {
    //Viene de inicio entonces es el alta de un usuario normal
    $nuevo = true;
}

//Si es nuevo lo creo vacio si no cargo sus datos
$usuario = "";
$email = "";
$nombre = "";
$password = "";
$apellidos = "";
$rol_id = 2; // Por defecto usuario normal

//Obtengo por id mi usuario
if ($error != "") {
    //Si tengo error me traigo los datos del get por que me ha dado un error de validacion y no los quiero rellenar otra vez
    $email = $_GET['email'] ?? '';
    $nombre = $_GET['nombre'] ?? '';
    $apellidos = $_GET['apellidos'] ?? '';
    $usuario = $_GET['usuario'] ?? '';
    $password = $_GET['password'] ?? '';
    $rol_id = (int)($_GET['rol_id'] ?? 2);
    $usuario_id = (int)($_GET['usuario_id']  ?? 0);
    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";
} else {
    //Cojo los datos del usuario solo si NO es nuevo
    if (!$nuevo) {
        $usu = $usu->obtenerPorId($pdo, (int)$usuario_id);
        $usuario = $usu->getUsuario();
        $email = $usu->getEmail();
        $nombre = $usu->getNombre();
        $password = ""; // No mostramos la password por seguridad
        $apellidos = $usu->getApellidos();
        $rol_id = $usu->getRolId();
    }
}

//Extraigo el usuario conectado (si existe)
$rol_id_usuario = 2; // Por defecto usuario normal
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
    <title>Nuevo Usuario</title>
    <?php else: ?>
    <title>Modificar usuario</title>
    <?php endif; ?>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <?php if ($nuevo): ?>
        <h1>Nuevo Usuario</h1>
        <?php else: ?>
        <h1>Modificar usuario</h1>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" id="usuario_id" name="usuario_id"
                value="<?= htmlspecialchars($usuario_id) ?>">
            <div>
                <label for="usuario">Usuario *</label>
                <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" required
                    value="<?= htmlspecialchars($usuario) ?>">
            </div>

            <div>
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required
                    value="<?= htmlspecialchars($email) ?>">
            </div>

            <div>
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre" required
                    value="<?= htmlspecialchars($nombre) ?>">
            </div>

            <div>
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos"
                    value="<?= htmlspecialchars($apellidos) ?>">
            </div>

            <?php if ($rol_id_usuario == 1): ?>
            <div>
                <label for="rol_id">Rol *</label>
                <select id="rol_id" name="rol_id" required>
                    <option value="1" <?= ($rol_id == 1) ? 'selected' : '' ?>>Admin</option>
                    <option value="2" <?= ($rol_id == 2) ? 'selected' : '' ?>>Usuario</option>
                </select>
            </div>
            <?php else: ?>
            <!-- Si no es admin, forzamos rol_id = 2 (Usuario) -->
            <input type="hidden" id="rol_id" name="rol_id" value="2">
            <?php endif; ?>

            <div>
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required value="">
                <small>Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial</small>
            </div>

            <?php if ($nuevo): ?>
            <button type="button"
                onclick="anadirUsuario(<?= ($listado == 'true' ? 'true' : 'false') ?>)">Crear
                Usuario</button>
            <?php else: ?>
            <button type="button"
                onclick="modificarUsuario(<?= ($listado == 'true' ? 'true' : 'false') ?>)">Modificar
                Usuario</button>
            <?php endif; ?>

        </form>
    </div>
</body>

</html>