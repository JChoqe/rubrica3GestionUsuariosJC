<?php

//Importo mi fichero de configuracion
$config = require_once "config.php";

//Incluyo mi libreria de encriptacion
require_once "encriptador.php";

//Incluyo mi control de errores
require_once "error.php";

//Incluyo mi sanetizacion
require_once "sanetizar.php";

//Incluyo mi gestion de la sesion
require_once "sesion.php";

//Me traigo la bbdd y la instancio para poder usarla
require_once "db.php";
$db = new BaseDatos();
$pdo = $db->getPdo();

function comprobarPatronEmail($email): bool
{
    $salida = true;
    $patron = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
    $salida = preg_match($patron, $email);

    return $salida;
}

function comprobarDocumento($doc): bool
{
    $salida = true;
    $patron = "/^(?:\d{8}[A-HJ-NP-TV-Z]|[XYZ]\d{7}[A-HJ-NP-TV-Z]|[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J])$/i";
    $salida = preg_match($patron, $doc);

    return $salida;
}

function comprobarPassword($password): bool
{
    $salida = true;
    if ($password != "") {
        $patron = "/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/";
        $salida = preg_match($patron, $password);
    }

    return $salida;
}

/**
 * Valida teléfonos españoles (móviles y fijos)
 * Móvil: empieza por 6 o 7 + 8 dígitos más = 9 dígitos
 * Fijo: empieza por 8 o 9 + 8 dígitos más = 9 dígitos
 */
function comprobarTelefonoEspana($telefono): bool
{
    // Eliminar espacios, guiones, paréntesis
    $telefono = preg_replace('/[\s\-\(\)]/', '', $telefono);
    
    // Patrón: 9 dígitos que empiecen por 6, 7, 8 o 9
    $patron = "/^[6-9]\d{8}$/";
    
    return preg_match($patron, $telefono) === 1;
}