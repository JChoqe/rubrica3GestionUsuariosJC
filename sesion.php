<?php

require_once "./models/Usuarios.php";

// Inicio de sesión PHP (necesario en todas las páginas con sesión)
session_start();

function crearSesion(Usuario $usu)
{
    // Almacena el usuario y el momento de inicio de sesión
    $_SESSION["usuario"]    = $usu;
    $_SESSION["login_time"] = time();

    // Cookies de estado (no recomendadas para control de seguridad)
    setcookie("rol_id", $usu->getRolId());
    setcookie("conectado", "true");
}

function borrarSesion()
{
    // Destruye la sesión del servidor
    session_destroy();

    // Elimina cookies asociadas
    setcookie("rol_id");
    setcookie("conectado");
}

function comprobarSesion(): bool
{
    global $config;

    $durSesion = (int) $config['sesion']['duracion_seg'];

    // Comprueba si la sesión ha superado el tiempo permitido
    if ((time() - $_SESSION["login_time"]) > $durSesion) {

        borrarSesion();

        // Redirección por sesión caducada
        header('Location: login.php?accion=sesioncaducada');
        return false;
    }

    return true;
}

function cerrarSesion()
{
    borrarSesion();

    // Redirección al login
    header('Location: login.php');
}

// Comprueba la sesión en cada página si existe la cookie de estado
$conectado = isset($_COOKIE["conectado"]) && $_COOKIE["conectado"] !== "";
if ($conectado) {
    comprobarSesion();
}
