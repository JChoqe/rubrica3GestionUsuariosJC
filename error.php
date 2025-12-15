<?php

// Registro global de manejadores de errores y excepciones
set_error_handler("manejaErrores");
set_exception_handler("manejaExcepciones");

function manejaErrores($nivel, $mensaje, $fichero, $linea)
{
    // Construye el mensaje de log con información contextual
    $mensaje = "Fecha: " . date("H:i d-m-Y") .
               " | Mensaje: " . $mensaje .
               " | Archivo: " . $fichero .
               " | Línea: " . $linea .
               " | Usuario: " . get_current_user() .
               " | IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;

    // Guarda el error en un log personalizado
    error_log($mensaje, 3, "C:/xampp/apache/logs/gestor_usuarios_errors.log");
}

function manejaExcepciones(Throwable $ex)
{
    // Construye el mensaje de log con información de la excepción
    $mensaje = "Fecha: " . date("H:i d-m-Y") .
               " | Mensaje: " . $ex->getMessage() .
               " | Archivo: " . $ex->getFile() .
               " | Línea: " . $ex->getLine() .
               " | Usuario: " . get_current_user() .
               " | IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;

    // Registro de la excepción en log
    error_log($mensaje, 3, "C:/xampp/apache/logs/gestor_usuarios_exceptions.log");

    // Mensaje genérico y seguro para el usuario
    echo "<b>Ocurrió un error.</b>";
}
