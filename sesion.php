<?php
/**
 * sesion.php
 * 
 * Gestión de sesiones de usuario
 * Control de autenticación, timeout y seguridad
 */

require_once "./models/Usuarios.php";

// Configuración segura de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS

// Inicio de sesión PHP
session_start();

/**
 * Crea una nueva sesión de usuario
 * 
 * @param Usuario $usu Usuario autenticado
 */
function crearSesion(Usuario $usu)
{
    // Regenerar ID de sesión para prevenir session fixation
    session_regenerate_id(true);
    
    // Almacena el usuario y el momento de inicio de sesión
    $_SESSION["usuario"] = $usu;
    $_SESSION["login_time"] = time();
    $_SESSION["user_ip"] = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Cookies de estado (solo informativas, no de seguridad)
    setcookie("rol_id", $usu->getRolId(), time() + 3600, "/", "", false, true);
    setcookie("conectado", "true", time() + 3600, "/", "", false, true);
}

/**
 * Destruye la sesión actual del usuario
 */
function borrarSesion()
{
    // Destruye la sesión del servidor
    $_SESSION = array();
    session_destroy();
    
    // Elimina cookies asociadas
    setcookie("rol_id", "", time() - 3600, "/");
    setcookie("conectado", "", time() - 3600, "/");
}

/**
 * Comprueba si la sesión es válida
 * Verifica timeout y validez
 * 
 * @return bool true si es válida, false si caducó
 */
function comprobarSesion(): bool
{
    global $config;

    // Si no hay sesión activa
    if (!isset($_SESSION["usuario"])) {
        return false;
    }

    $durSesion = (int) $config['sesion']['duracion_seg'];

    // Comprueba si la sesión ha superado el tiempo permitido
    if ((time() - $_SESSION["login_time"]) > $durSesion) {
        borrarSesion();
        header('Location: login.php?accion=sesioncaducada');
        exit();
    }

    // Verificar que la IP no haya cambiado (seguridad adicional)
    $ip_actual = $_SERVER['REMOTE_ADDR'] ?? '';
    $ip_sesion = $_SESSION["user_ip"] ?? '';
    
    if ($ip_actual !== $ip_sesion) {
        borrarSesion();
        header('Location: login.php?accion=sesioninvalida');
        exit();
    }

    return true;
}

/**
 * Cierra la sesión del usuario y redirige al login
 */
function cerrarSesion()
{
    borrarSesion();
    header('Location: login.php');
    exit();
}

// Comprueba la sesión en cada página si existe la cookie de estado
$conectado = isset($_COOKIE["conectado"]) && $_COOKIE["conectado"] !== "";
if ($conectado) {
    comprobarSesion();
}