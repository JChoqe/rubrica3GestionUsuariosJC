<?php

//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";

//Me traigo el php de ficha guardar para usar la funcion de validar
require_once "ficha_guardar.php";

// Obtenemos la acción del query string
$accion = $_GET['action'] ?? '';

// echo "accion";
// echo $accion;

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($accion) {
        case 'cerrarsesion':
            //Cerramos sesion 
            cerrarSesion();
            break;
        case 'login':
            //Reocojo los valores del usuario
            $usuario = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';

            //Validamos credenciales
            $error = validar();

            volverLogin($error);
            break;

        default:
            break;
    }
    //echo "entra";

}

function volverLogin($error = "")
{
    global $usuario;

    //Volvemos a la página que ha hecho el submit en caso de error
    if ($error == "") {
        header('Location: listado.php?ok=1');
    } else {
        header('Location: login.php?error=' . $error . '&usuario=' . $usuario . '');
    }
    exit();
}
