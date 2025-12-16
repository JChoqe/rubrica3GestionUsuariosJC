<?php
/**
 * utils.php
 * 
 * Archivo central de utilidades del proyecto
 * Carga todas las dependencias y proporciona funciones de validación
 * 
 * Este archivo debe ser incluido al inicio de cada página PHP
 * 
 * @author Sistema de Gestión
 * @version 1.2
 */

// Carga la configuración global del proyecto
$config = require_once "config.php";

// Carga de dependencias principales del sistema
require_once "encriptador.php";  // Funciones de cifrado AES-256 (opcional)
require_once "error.php";        // Gestión centralizada de errores
require_once "sanetizar.php";    // Sanitización automática de inputs
require_once "sesion.php";       // Control de sesiones de usuario

// Inicialización de la conexión a base de datos
require_once "db.php";
$db  = new BaseDatos();
$pdo = $db->getPdo();

// Habilitar foreign keys en SQLite
$pdo->exec("PRAGMA foreign_keys = ON");

// ===== FUNCIONES DE PROTECCIÓN CSRF =====

/**
 * Genera un token CSRF y lo guarda en sesión
 * 
 * @return string Token CSRF generado
 */
function generarTokenCSRF(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica que el token CSRF sea válido
 * 
 * @param string $token Token recibido del formulario
 * @return bool true si es válido, false si no
 */
function verificarTokenCSRF($token): bool
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Genera el campo hidden HTML con el token CSRF
 * 
 * @return string HTML del campo hidden
 */
function campoCSRF(): string
{
    $token = generarTokenCSRF();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// ===== FUNCIONES DE VALIDACIÓN =====

/**
 * Valida el formato de un email
 * 
 * @param string $email Email a validar
 * @return bool true si es válido, false si no
 */
function comprobarPatronEmail($email): bool
{
    if (empty(trim($email))) {
        return false;
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida documentos españoles (DNI, NIE y CIF)
 * 
 * Formatos aceptados:
 * - DNI: 8 dígitos + letra (12345678A)
 * - NIE: X/Y/Z + 7 dígitos + letra (X1234567A)
 * - CIF: Letra + 7 dígitos + dígito/letra (B12345678)
 * 
 * @param string $doc Documento a validar
 * @return bool true si es válido, false si no
 */
function comprobarDocumento($doc): bool
{
    if (empty(trim($doc))) {
        return false;
    }
    $patron = "/^(?:\d{8}[A-HJ-NP-TV-Z]|[XYZ]\d{7}[A-HJ-NP-TV-Z]|[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J])$/i";
    return (bool) preg_match($patron, strtoupper(trim($doc)));
}

/**
 * Valida que la contraseña cumpla requisitos de seguridad
 * 
 * Requisitos:
 * - Mínimo 8 caracteres
 * - Al menos una letra mayúscula
 * - Al menos un número
 * - Al menos un carácter especial
 * 
 * @param string $password Contraseña a validar
 * @return bool true si cumple requisitos, false si no
 */
function comprobarPassword($password): bool
{
    // Si está vacía, es inválida
    if ($password === '' || $password === null) {
        return false;
    }

    // Patrón: al menos 8 caracteres, una mayúscula, un número y un carácter especial
    $patron = "/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    return (bool) preg_match($patron, $password);
}

/**
 * Valida contraseña opcional (para edición de usuario)
 * Permite contraseña vacía o que cumpla requisitos
 * 
 * @param string $password Contraseña a validar
 * @return bool true si es válida o vacía, false si no cumple requisitos
 */
function comprobarPasswordOpcional($password): bool
{
    // Si está vacía, es válida (no se cambia)
    if ($password === '' || $password === null) {
        return true;
    }

    // Si tiene contenido, debe cumplir requisitos
    return comprobarPassword($password);
}

/**
 * Valida número de teléfono español
 * 
 * Formatos aceptados:
 * - Móvil: 6XX XXX XXX o 7XX XXX XXX (9 dígitos)
 * - Fijo: 9XX XXX XXX (9 dígitos)
 * - Con espacios, guiones o sin separadores
 * 
 * @param string $telefono Teléfono a validar
 * @return bool true si es válido, false si no
 */
function comprobarTelefonoEspanol($telefono): bool
{
    if (empty(trim($telefono))) {
        return false;
    }
    
    // Eliminar espacios, guiones y paréntesis
    $telefono_limpio = preg_replace('/[\s\-\(\)]/', '', $telefono);
    
    // Patrón: 9 dígitos que empiecen por 6, 7, 8 o 9
    $patron = "/^[6789]\d{8}$/";
    return (bool) preg_match($patron, $telefono_limpio);
}

/**
 * Formatea un teléfono español para mostrar
 * Convierte 612345678 en 612 345 678
 * 
 * @param string $telefono Teléfono a formatear
 * @return string Teléfono formateado
 */
function formatearTelefono($telefono): string
{
    if (empty($telefono)) {
        return '';
    }
    
    $telefono_limpio = preg_replace('/[\s\-\(\)]/', '', $telefono);
    
    if (strlen($telefono_limpio) === 9) {
        return substr($telefono_limpio, 0, 3) . ' ' . 
               substr($telefono_limpio, 3, 3) . ' ' . 
               substr($telefono_limpio, 6, 3);
    }
    
    return $telefono;
}

/**
 * Valida que el nombre contenga solo letras, espacios y caracteres acentuados
 * 
 * @param string $nombre Nombre a validar
 * @return bool true si es válido, false si no
 */
function comprobarNombre($nombre): bool
{
    if (trim($nombre) === '') {
        return false;
    }
    
    // Permite letras (incluidas acentuadas), espacios y apóstrofes
    $patron = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s']+$/u";
    return (bool) preg_match($patron, $nombre);
}

/**
 * Verifica si el usuario actual es administrador
 * 
 * @return bool true si es admin, false si no
 */
function esAdmin(): bool
{
    if (isset($_SESSION['usuario'])) {
        $usuario = $_SESSION['usuario'];
        return $usuario->getRolId() == 1;
    }
    return false;
}

/**
 * Redirige si el usuario no es administrador
 * Protege páginas que solo pueden ver admins
 */
function requerirAdmin(): void
{
    if (!esAdmin()) {
        header('Location: listado.php?error=' . urlencode('No tienes permisos de administrador'));
        exit;
    }
}