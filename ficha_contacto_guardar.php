<?php

//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Incluyo mi clases necesarias
require_once "./models/Contactos.php";

//Me instancio mi clase de contacto
$cont = new Contacto();

// Obtenemos la acción del query string
$accion = $_GET['action'] ?? '';
$list = $_GET['listado'] ?? false;
$cliente_id_param = $_GET['cliente_id'] ?? 0;

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $cliente_id = (int)($_POST['cliente_id'] ?? 0);
    $contacto_id = (int)($_POST['contacto_id']  ?? 0);

    //Creo mi clase de contacto
    if ($contacto_id > 0) {
        $cont = $cont->obtenerPorId($pdo, (int)$contacto_id);
    } else {
        $cont = new Contacto();
    }

    $cont->setNombre($nombre);
    $cont->setApellidos($apellidos);
    $cont->setEmail($email);
    $cont->setTelefono($telefono);
    $cont->setClienteId($cliente_id);
    $cont->setId($contacto_id);

    // Llamamos a la función correspondiente
    switch ($accion) {
        case 'guardar':
            $error = validar();
            if ($error == "") {
                $cont->guardar($pdo);
            }
            volver($error, $list);
            break;
        case 'eliminar':
            $error = validar();
            if ($error == "") {
                $cont->eliminar($pdo);
            }
            volver($error, $list);
            break;
        case 'anadir':
            $error = validar();
            if ($error == "") {
                $cont->guardar($pdo);
            }
            volver($error, $list);
            break;

        default:
    }
}

function volver($error = "", $list = false)
{
    global $nombre;
    global $apellidos;
    global $email;
    global $telefono;
    global $cliente_id;
    global $contacto_id;
    global $cliente_id_param;

    //Volvemos a la página que ha hecho el submit en caso de error
    if ($error == "") {
        if ($list == true) {
            header('Location: listado_contactos.php?ok=1&cliente_id=' . $cliente_id_param);
        } else {
            header('Location: index.php?ok=1');
        }
    } else {
        header('Location: ficha_contacto.php?error=' . $error . '&nombre=' . $nombre . '&apellidos=' . $apellidos . '&email=' . $email . '&telefono=' . $telefono . '&cliente_id=' . $cliente_id . '&contacto_id=' . $contacto_id . '');
    }
    exit();
}

function validar(): string
{
    global $accion;
    global $nombre;
    global $apellidos;
    global $email;
    global $telefono;
    global $cliente_id;
    global $contacto_id;
    global $pdo;
    $error = "";

    switch ($accion) {
        case 'guardar':
            if (!(isset($contacto_id)) || $contacto_id == 0) {
                $error .= "Tiene que seleccionar un contacto para poder modificarlo--";
            }

            if (comprobarPatronEmail($email) == false) {
                $error .= "El email no tiene el formato correcto--";
            }

            if (comprobarTelefonoEspana($telefono) == false) {
                $error .= "El teléfono no tiene el formato correcto (debe ser formato español: 9 dígitos)--";
            }

            if (!(isset($nombre)) || $nombre == "") {
                $error .= "Es necesario rellenar el campo nombre--";
            }

            if (!(isset($apellidos)) || $apellidos == "") {
                $error .= "Es necesario rellenar el campo apellidos--";
            }

            if (!(isset($cliente_id)) || $cliente_id == 0) {
                $error .= "Es necesario seleccionar un cliente--";
            }
            break;

        case 'eliminar':
            if (!(isset($contacto_id)) || $contacto_id == 0) {
                $error .= "Tiene que seleccionar un contacto para poder eliminarlo";
            }
            break;

        case 'anadir':
            if (comprobarPatronEmail($email) == false) {
                $error .= "El email no tiene el formato correcto--";
            }

            if (comprobarTelefonoEspana($telefono) == false) {
                $error .= "El teléfono no tiene el formato correcto (debe ser formato español: 9 dígitos)--";
            }

            if (!(isset($nombre)) || $nombre == "") {
                $error .= "Es necesario rellenar el campo nombre--";
            }

            if (!(isset($apellidos)) || $apellidos == "") {
                $error .= "Es necesario rellenar el campo apellidos--";
            }

            if (!(isset($cliente_id)) || $cliente_id == 0) {
                $error .= "Es necesario seleccionar un cliente--";
            }
            break;

        default:
            break;
    }

    return $error;
}