<?php
// Carga de utilidades comunes del proyecto (sesiones, BD, funciones generales)
require_once "utils.php";

// Inclusión de los modelos necesarios
require_once "./models/Usuarios.php";
require_once "./models/Roles.php";

// Creación de instancias de los modelos
$usu = new Usuario();
$rol = new Rol();

// Obtención de parámetros enviados por GET
$usuario_id = $_GET['usuario_id'] ?? 0;
$error      = $_GET['error'] ?? '';
$listado   = $_GET['listado'] ?? '';

// Indica si se trata de un alta o una modificación
$nuevo = ($usuario_id == 0);

// Inicialización de variables del formulario
$usuario   = "";
$email     = "";
$nombre    = "";
$password  = "";
$apellidos = "";
$rol_id    = 0;

// Si existe un error de validación, se recuperan los datos enviados
// para no obligar al usuario a rellenar de nuevo el formulario
if ($error != "") {

    $email      = $_GET['email'] ?? '';
    $nombre     = $_GET['nombre'] ?? '';
    $apellidos  = $_GET['apellidos'] ?? '';
    $usuario    = $_GET['usuario'] ?? '';
    $password   = $_GET['password'] ?? '';
    $rol_id     = (int)($_GET['rol_id'] ?? 2);
    $usuario_id = (int)($_GET['usuario_id'] ?? 0);

    // Muestra el mensaje de error al usuario
    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";

} else {
    // Si no hay errores, se cargan los datos del usuario desde la base de datos
    $usu = $usu->obtenerPorId($pdo, (int)$usuario_id);
    $usuario   = $usu->getUsuario();
    $email     = $usu->getEmail();
    $nombre    = $usu->getNombre();
    $password  = $usu->getPassword();
    $apellidos = $usu->getApellidos();
    $rol_id    = $usu->getRolId();
}

// Obtención del usuario conectado para controlar permisos (roles)
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
                value="<?= $usuario_id ?>">
            <div>
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" required
                    value="<?= $usuario ?>">
            </div>

            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required
                    value="<?= $email ?>">
            </div>

            <div>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre" required
                    value="<?= $nombre ?>">
            </div>

            <div>
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos"
                    value="<?= $apellidos ?>">
            </div>

            <?php if ($rol_id_usuario == 1): ?>
            <div>
                <label for="rol_id">Rol</label>
                <select id="rol_id" name="rol_id" required>
                    <option value="1">Admin</option>
                    <option value="2">Usuario</option>
                </select>
            </div>
            <?php endif; ?>
            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required value="">
            </div>

            <?php if ($nuevo): ?>
            <button
                onclick="anadirUsuario(<?= ($listado == 'true' ? 'true' : '') ?>)">Crear
                Usuario</button>
            <?php else: ?>
            <button
                onclick="modificarUsuario(<?= ($listado == 'true' ? 'true' : '') ?>)">Modificar
                Usuario</button>
            <?php endif; ?>

        </form>
    </div>
</body>

</html>