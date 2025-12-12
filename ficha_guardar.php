<?php

//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Incluyo mi clases necesarias
require_once "./models/Usuarios.php";
require_once "./models/Roles.php";

//Me instancio mi clase de usuario
$usu = new Usuario();

// Obtenemos la acción del query string
$accion = $_GET['action'] ?? '';
$list = $_GET['listado'] ?? false;

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol_id = (int)($_POST['rol_id'] ?? 2);
    $usuario_id = (int)($_POST['usuario_id']  ?? 0);

    //Creo mis dos clases de las entidades que necesito
    if ($usuario_id > 0) {
        $usu = $usu->obtenerPorId($pdo, (int)$usuario_id);
    } else {
        $usu = new Usuario();
    }

    $usu->setEmail($email);
    $usu->setNombre($nombre);
    $usu->setApellidos($apellidos);
    $usu->setUsuario($usuario);
    $usu->setPassword($password);
    $usu->setRolId($rol_id);
    $usu->setId($usuario_id);

    // Llamamos a la función correspondiente
    switch ($accion) {
        case 'guardar':
            $error = validar();
            if ($error == "") {
                $usu->guardar($pdo);
            }
            volver($error, $list);
            break;
        case 'eliminar':
            $error = validar();
            if ($error == "") {
                $usu->eliminar($pdo);
            }
            volver($error, $list);
            break;
        case 'anadir':
            $error = validar();
            if ($error == "") {
                $usu->guardar($pdo);
            }
            volver($error, $list);
            break;

        default:
    }
}

function volver($error = "", $list = false)
{
    global $email;
    global $nombre;
    global $apellidos;
    global $usuario;
    global $rol_id;
    global $usuario_id;

    //Volvemos a la página que ha hecho el submit en caso de error
    if ($error == "") {
        if ($list == true) {
            header('Location: listado.php?ok=1');
        } else {
            header('Location: index.php?ok=1');
        }
    } else {
        header('Location: ficha.php?error=' . urlencode($error) . '&email=' . urlencode($email) . '&nombre=' . urlencode($nombre) . '&apellidos=' . urlencode($apellidos) . '&usuario=' . urlencode($usuario) . '&rol_id=' . $rol_id . '&usuario_id=' . $usuario_id);
    }
    exit();
}

function validar(): string
{
    global $accion;
    global $usuario_id;
    global $email;
    global $usuario;
    global $password;
    global $pdo;
    $error = "";

    switch ($accion) {
        case 'guardar':
            if (!(isset($usuario_id)) || $usuario_id == 0) {
                $error .= "Tiene que seleccionar un usuario para poder modificarlo--";
            }
            if (comprobarPatronEmail($email) == false) {
                $error .= "El email no tiene el formato correcto--";
            }

            if (!(isset($usuario)) || $usuario == "") {
                $error .= "Es necesario rellenar el campo usuario--";
            }
            if (!(isset($password)) || $password == "") {
                $error .= "Es necesario rellenar el campo password--";
            }

            if (comprobarPassword($password) == false) {
                $error .= "La password tiene que tener al menos una mayúscula, un número, un carácter alfanumérico y al menos 8 caracteres--";
            }
            break;
        case 'eliminar':
            if (!(isset($usuario_id)) || $usuario_id == 0) {
                $error .= "Tiene que seleccionar un usuario para poder eliminarlo";
            }
            break;
        case 'anadir':
            if (comprobarPatronEmail($email) == false) {
                $error .= "El email no tiene el formato correcto--";
            }

            if (!(isset($usuario)) || $usuario == "") {
                $error .= "Es necesario rellenar el campo usuario--";
            }

            if (!(isset($password)) || $password == "") {
                $error .= "Es necesario rellenar el campo password--";
            }

            if (comprobarPassword($password) == false) {
                $error .= "La password tiene que tener al menos una mayúscula, un número, un carácter alfanumérico y al menos 8 caracteres--";
            }
            break;

        default:
            break;
    }

    return $error;
}