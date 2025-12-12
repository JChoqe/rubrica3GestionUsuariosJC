<?php

// Activar manejador de errores
set_error_handler("manejaErrores");
set_exception_handler("manejaExcepciones");
function manejaErrores($nivel, $mensaje, $fichero, $linea)
{
    $mensaje = "Fecha: " . date("H:i d-m-Y") .
               " | Mensaje: " . $mensaje .
               " | Archivo: " . $fichero .
               " | Línea: " . $linea .
               " | Usuario: " . get_current_user() .
               " | IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;

    // Registra el error en un archivo log personalizado
    error_log($mensaje, 3, "C:/xampp/apache/logs/gestor_usuarios_errors.log");
}


function manejaExcepciones(Throwable $ex)
{
    $mensaje = "Fecha: " . date("H:i d-m-Y") .
               " | Mensaje: " . $ex->getMessage() .
               " | Archivo: " . $ex->getFile() .
               " | Línea: " . $ex->getLine() .
               " | Usuario: " . get_current_user() .
               " | IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;

    // Guardar en el log (siempre recomendable)
    error_log($mensaje, 3, "C:/xampp/apache/logs/gestor_usuarios_exceptions.log");

    // Mostrar un mensaje controlado al usuario
    echo "<b>Ocurrió un error:</b> " . htmlspecialchars($ex->getMessage());

    // También podrías redirigir, por ejemplo:
    // header("Location: error.php?msg=" . urlencode($ex->getMessage()));
    // exit;
}
