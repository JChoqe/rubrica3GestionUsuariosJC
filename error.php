<?php
/**
 * error.php
 * 
 * Gestor centralizado de errores y excepciones
 * Registra todos los errores en archivos de log con información contextual
 */

// Directorio de logs (relativo al proyecto)
$log_dir = __DIR__ . '/logs';

// Crear directorio de logs si no existe
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Rutas de los archivos de log
define('LOG_ERRORES', $log_dir . '/errors.log');
define('LOG_EXCEPCIONES', $log_dir . '/exceptions.log');

// Registro global de manejadores de errores y excepciones
set_error_handler("manejaErrores");
set_exception_handler("manejaExcepciones");

function manejaErrores($nivel, $mensaje, $fichero, $linea)
{
    // Construye el mensaje de log con información contextual
    $log_mensaje = sprintf(
        "[%s] NIVEL: %d | MENSAJE: %s | ARCHIVO: %s | LÍNEA: %d | IP: %s" . PHP_EOL,
        date("Y-m-d H:i:s"),
        $nivel,
        $mensaje,
        $fichero,
        $linea,
        $_SERVER['REMOTE_ADDR'] ?? 'CLI'
    );

    // Guarda el error en el log
    error_log($log_mensaje, 3, LOG_ERRORES);
}

function manejaExcepciones(Throwable $ex)
{
    // Construye el mensaje de log con información de la excepción
    $log_mensaje = sprintf(
        "[%s] EXCEPCIÓN: %s | MENSAJE: %s | ARCHIVO: %s | LÍNEA: %d | IP: %s | TRACE: %s" . PHP_EOL,
        date("Y-m-d H:i:s"),
        get_class($ex),
        $ex->getMessage(),
        $ex->getFile(),
        $ex->getLine(),
        $_SERVER['REMOTE_ADDR'] ?? 'CLI',
        $ex->getTraceAsString()
    );

    // Registro de la excepción en log
    error_log($log_mensaje, 3, LOG_EXCEPCIONES);

    // Mensaje genérico y seguro para el usuario
    echo "<b>Ocurrió un error. Por favor, contacte al administrador.</b>";
}