<?php

//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Incluyo mi clases necesarias
require_once "./models/Clientes.php";

//Me instancio mi clase de cliente
$cli = new Cliente();

// Obtenemos la acción del query string
$accion = $_GET['action'] ?? '';
$list = $_GET['listado'] ?? false;

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'] ?? '';
    $cif = $_POST['cif'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $edad = (int)($_POST['edad'] ?? 0);
    $cliente_id = (int)($_POST['cliente_id']  ?? 0);

    //Creo mis dos clases de las entidades que necesito
    $cli = new Cliente();
    if ($cliente_id > 0) {
        $cli = $cli->obtenerPorId($pdo, (int)$cliente_id);
    } else {
        $cli = new Cliente();
    }

    $cli->setNombre($nombre);
    $cli->setCIF($cif);
    $cli->setEmail($email);
    $cli->setTelefono($telefono);
    $cli->setApellidos($apellidos);
    $cli->setEdad($edad);
    $cli->setId($cliente_id);

    // Llamamos a la función correspondiente
    switch ($accion) {
        case 'guardar':
            $error = validar();
            if ($error == "") {
                $cli->guardar($pdo);
            }
            volver($error, $list);
            break;
        case 'eliminar':
            print_r("elimina");
            $error = validar();
            print_r($error);
            if ($error == "") {
                $cli->eliminar($pdo);
            }
            volver($error, $list);
            break;
        case 'anadir':
            $error = validar();
            if ($error == "") {
                $cli->guardar($pdo);
            }
            volver($error, $list);
            break;

        default:
    }
}

function volver($error = "", $list = false)
{

    global $nombre;
    global $cif;
    global $email;
    global $telefono;
    global $apellidos;
    global $edad;
    global $cliente_id;

    //Volvemos a la página que ha hecho el submit en caso de error
    if ($error == "") {
        if ($list == true) {
            header('Location: listado_clientes.php?ok=1');
        } else {
            header('Location: index.php?ok=1');
        }
    } else {
        header('Location: ficha_cliente.php?error=' . $error . '&nombre=' . $nombre . '&cif=' . $cif . '&email=' . $email . '&telefono=' . $telefono . '&apellidos=' . $apellidos . '&edad=' . $edad . '&cliente_id=' . $cliente_id . '');
    }
    exit();
}

function validar(): string
{
    global $accion;
    global $nombre;
    global $cif;
    global $email;
    global $cliente_id;
    global $pdo;
    $error = "";

    switch ($accion) {
        case 'guardar':
            if (!(isset($cliente_id)) || $cliente_id == 0) {
                $error .= "Tiene que seleccionar un cliente para poder modificarlo--";
            }

            if (comprobarPatronEmail($email) == false) {
                $error .= "El email no tiene el formato correcto--";
            }

            if (comprobarDocumento($cif) == false) {
                $error .= "El cif no tiene el formato correcto--";
            }

            if (!(isset($nombre)) || $nombre == "") {
                $error .= "Es necesario rellenar el campo nombre--";
            }
            break;
        case 'eliminar':
            if (!(isset($cliente_id)) || $cliente_id == 0) {
                $error .= "Tiene que seleccionar un cliente para poder eliminarlo";
            }
            break;
        case 'anadir':
            if (comprobarPatronEmail($email) == false) {
                $error .= "El email no tiene el formato correcto--";
            }

            if (comprobarDocumento($cif) == false) {
                $error .= "El cif no tiene el formato correcto--";
            }

            if (!(isset($nombre)) || $nombre == "") {
                $error .= "Es necesario rellenar el campo nombre--";
            }
            break;

        default:
            break;
    }

    return $error;
}
