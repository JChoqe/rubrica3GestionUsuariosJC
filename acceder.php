<?php

// Utilidades y dependencias básicas del proyecto
require_once "utils.php";

// Funciones de validación del login
require_once "ficha_guardar.php";

// Acción recibida por query string
$accion = $_GET['action'] ?? '';

// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($accion) {

        case 'cerrarsesion':
            // Cierre explícito de sesión
            cerrarSesion();
            break;

        case 'login':
            // Credenciales enviadas por el formulario
            $usuario  = $_POST['usuario'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validación de credenciales
            $error = validar();

            // Redirección según resultado
            volverLogin($error);
            break;
    }
}

/**
 * Redirección tras intento de login
 */
function volverLogin($error = "")
{
    global $usuario;

    if ($error === "") {
        header('Location: listado.php?ok=1');
    } else {
        header('Location: login.php?error=' . $error . '&usuario=' . $usuario);
    }

    exit();
}
