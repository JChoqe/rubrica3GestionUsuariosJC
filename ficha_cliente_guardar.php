<?php
// Carga de utilidades comunes del proyecto (sesión, BD, validaciones, etc.)
require_once "utils.php";

// Inclusión del modelo Cliente
require_once "./models/Clientes.php";

// Instanciamos el modelo Cliente
$cli = new Cliente();

// Obtención de la acción y del origen (listado o no) desde el query string
$accion = $_GET['action'] ?? '';
$list   = $_GET['listado'] ?? false;

// Comprobamos si el formulario ha sido enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogida de datos del formulario
    $nombre     = $_POST['nombre'] ?? '';
    $cif        = $_POST['cif'] ?? '';
    $email      = $_POST['email'] ?? '';
    $telefono   = $_POST['telefono'] ?? '';
    $apellidos  = $_POST['apellidos'] ?? '';
    $edad       = (int)($_POST['edad'] ?? 0);
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);

    // Cargamos el cliente desde la base de datos si existe
    $cli = new Cliente();
    $cli = $cli->obtenerPorId($pdo, (int)$cliente_id);

    // Asignación de valores al objeto Cliente
    $cli->setNombre($nombre);
    $cli->setCIF($cif);
    $cli->setEmail($email);
    $cli->setTelefono($telefono);
    $cli->setApellidos($apellidos);
    $cli->setEdad($edad);
    $cli->setId($cliente_id);

    // Ejecutamos la acción solicitada
    switch ($accion) {
        case 'guardar':
            // Validación previa a la modificación
            $error = validar();
            if ($error == "") {
                $cli->guardar($pdo);
            }
            volver($error, $list);
            break;

        case 'eliminar':
            // Validación previa al borrado
            $error = validar();
            if ($error == "") {
                $cli->eliminar($pdo);
            }
            volver($error, $list);
            break;

        case 'anadir':
            // Validación previa al alta
            $error = validar();
            if ($error == "") {
                $cli->guardar($pdo);
            }
            volver($error, $list);
            break;

        default:
            break;
    }
}

/**
 * Redirige tras la acción realizada.
 * Si hay error, se devuelven los datos para rellenar el formulario.
 */
function volver($error = "", $list = false)
{
    global $nombre, $cif, $email, $telefono, $apellidos, $edad, $cliente_id;

    // Redirección en caso de éxito
    if ($error == "") {
        if ($list == true) {
            header('Location: listado_clientes.php?ok=1');
        } else {
            header('Location: index.php?ok=1');
        }
    } else {
        // Redirección en caso de error, manteniendo los datos introducidos
        header(
            'Location: ficha_cliente.php?error=' . $error .
            '&nombre=' . $nombre .
            '&cif=' . $cif .
            '&email=' . $email .
            '&telefono=' . $telefono .
            '&apellidos=' . $apellidos .
            '&edad=' . $edad .
            '&cliente_id=' . $cliente_id
        );
    }
    exit();
}

/**
 * Valida los datos según la acción solicitada
 */
function validar(): string
{
    global $accion, $nombre, $cif, $email, $cliente_id;
    $error = "";

    switch ($accion) {
        case 'guardar':
            // Validaciones para modificar un cliente
            if ($cliente_id == 0) {
                $error .= "Tiene que seleccionar un cliente para poder modificarlo--";
            }
            if (!comprobarPatronEmail($email)) {
                $error .= "El email no tiene el formato correcto--";
            }
            if (!comprobarDocumento($cif)) {
                $error .= "El cif no tiene el formato correcto--";
            }
            if ($nombre == "") {
                $error .= "Es necesario rellenar el campo nombre--";
            }
            break;

        case 'eliminar':
            // Validación para eliminar
            if ($cliente_id == 0) {
                $error .= "Tiene que seleccionar un cliente para poder eliminarlo";
            }
            break;

        case 'anadir':
            // Validaciones para alta de cliente
            if (!comprobarPatronEmail($email)) {
                $error .= "El email no tiene el formato correcto--";
            }
            if (!comprobarDocumento($cif)) {
                $error .= "El cif no tiene el formato correcto--";
            }
            if ($nombre == "") {
                $error .= "Es necesario rellenar el campo nombre--";
            }
            break;

        default:
            break;
    }

    return $error;
}