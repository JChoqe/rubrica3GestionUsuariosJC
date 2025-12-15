<?php
// Carga de utilidades comunes del proyecto (sesiones, BD, validaciones, etc.)
require_once "utils.php";

// Inclusión de los modelos necesarios
require_once "./models/Usuarios.php";
require_once "./models/Roles.php";

// Instanciamos el modelo Usuario
$usu = new Usuario();

// Obtención de parámetros enviados por GET
$accion = $_GET['action'] ?? '';
$list   = $_GET['listado'] ?? false;

// Verificamos que la petición se haya realizado mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogida y saneado de datos enviados desde el formulario
    $email      = $_POST['email'] ?? '';
    $nombre     = $_POST['nombre'] ?? '';
    $apellidos  = $_POST['apellidos'] ?? '';
    $usuario    = $_POST['usuario'] ?? '';
    $password   = $_POST['password'] ?? '';
    $rol_id     = (int)($_POST['rol_id'] ?? 2);
    $usuario_id = (int)($_POST['usuario_id'] ?? 0);

    // Se obtiene el usuario desde la base de datos si existe
    $usu = new Usuario();
    $usu = $usu->obtenerPorId($pdo, $usuario_id);

    // Se asignan los valores al objeto Usuario
    $usu->setEmail($email);
    $usu->setNombre($nombre);
    $usu->setApellidos($apellidos);
    $usu->setUsuario($usuario);
    $usu->setPassword($password);
    $usu->setRolId($rol_id);
    $usu->setId($usuario_id);

    // Según la acción solicitada se ejecuta la operación correspondiente
    switch ($accion) {
        case 'guardar':
            // Validación previa a la modificación
            $error = validar();
            if ($error == "") {
                $usu->guardar($pdo);
            }
            volver($error, $list);
            break;

        case 'eliminar':
            // Validación previa a la eliminación
            $error = validar();
            if ($error == "") {
                $usu->eliminar($pdo);
            }
            volver($error, $list);
            break;

        case 'anadir':
            // Validación previa a la creación de un nuevo usuario
            $error = validar();
            if ($error == "") {
                $usu->guardar($pdo);
            }
            volver($error, $list);
            break;

        default:
            // Acción no reconocida
            break;
    }
}

/**
 * Redirige a la página correspondiente tras la acción realizada.
 * En caso de error, devuelve al formulario con los datos introducidos.
 */
function volver($error = "", $list = false)
{
    global $email, $nombre, $apellidos, $usuario, $rol_id, $usuario_id;

    // Si no hay errores, redirige a listado o inicio
    if ($error == "") {
        if ($list == true) {
            header('Location: listado.php?ok=1');
        } else {
            header('Location: index.php?ok=1');
        }
    } else {
        // En caso de error se vuelve al formulario manteniendo los datos
        header(
            'Location: ficha.php?error=' . $error .
            '&email=' . $email .
            '&nombre=' . $nombre .
            '&apellidos=' . $apellidos .
            '&usuario=' . $usuario .
            '&rol_id=' . $rol_id .
            '&usuario_id=' . $usuario_id
        );
    }
    exit();
}

/**
 * Valida los datos recibidos según la acción solicitada.
 * Devuelve una cadena de errores o una cadena vacía si todo es correcto.
 */
function validar(): string
{
    global $accion, $usuario_id, $email, $usuario, $password, $pdo;
    $error = "";

    switch ($accion) {

        case 'guardar':
            // Validaciones para modificación
            if ($usuario_id == 0) {
                $error .= "Tiene que seleccionar un usuario para poder modificarlo--";
            }
            if (!comprobarPatronEmail($email)) {
                $error .= "El email no tiene el formato correcto--";
            }
            if ($usuario == "") {
                $error .= "Es necesario rellenar el campo usuario--";
            }
            if ($password == "") {
                $error .= "Es necesario rellenar el campo password--";
            }
            if (!comprobarPassword($password)) {
                $error .= "La password debe cumplir los requisitos mínimos--";
            }
            break;

        case 'eliminar':
            // Validaciones para eliminación
            if ($usuario_id == 0) {
                $error .= "Tiene que seleccionar un usuario para poder eliminarlo";
            }
            break;

        case 'anadir':
            // Validaciones para alta
            if (!comprobarPatronEmail($email)) {
                $error .= "El email no tiene el formato correcto--";
            }
            if ($usuario == "") {
                $error .= "Es necesario rellenar el campo usuario--";
            }
            if ($password == "") {
                $error .= "Es necesario rellenar el campo password--";
            }
            if (!comprobarPassword($password)) {
                $error .= "La password debe cumplir los requisitos mínimos--";
            }
            break;

        case 'login':
            // Validaciones para login
            if ($usuario == "") {
                $error .= "Es necesario rellenar el campo usuario--";
            }
            if ($password == "") {
                $error .= "Es necesario rellenar el campo password--";
            }

            // Comprobación de credenciales en base de datos
            $usu = new Usuario();
            $usu = $usu->login($pdo, $usuario, $password);

            if (!isset($usu)) {
                $error .= "No hay ningún usuario con esas credenciales--";
            } else {
                // Creación de sesión si el login es correcto
                crearSesion($usu);
            }
            break;

        default:
            break;
    }

    return $error;
}
