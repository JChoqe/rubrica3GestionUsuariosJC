<?php
/**
 * eliminar_contacto.php
 * 
 * Controlador para eliminar contactos
 * Solo accesible por administradores
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

require_once "utils.php";
require_once "./models/Contactos.php";

// Verificar sesión activa
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php?accion=sesioncaducada');
    exit;
}

// Solo administradores
requerirAdmin();

// Solo procesar si es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listado_contactos.php');
    exit;
}

// Obtener ID del contacto a eliminar
$contacto_id = (int)($_POST['contacto_id'] ?? 0);
$cliente_id_filtro = (int)($_POST['cliente_id_filtro'] ?? 0);

if ($contacto_id > 0) {
    try {
        // Cargar el contacto
        $contacto = Contacto::obtenerPorId($pdo, $contacto_id);
        
        if ($contacto) {
            // Eliminar contacto
            if ($contacto->eliminar($pdo)) {
                $mensaje = 'Contacto eliminado correctamente';
                
                // Redirigir al listado correspondiente
                if ($cliente_id_filtro > 0) {
                    header("Location: listado_contactos.php?cliente_id=$cliente_id_filtro&success=" . urlencode($mensaje));
                } else {
                    header("Location: listado_contactos.php?success=" . urlencode($mensaje));
                }
                exit;
            } else {
                throw new Exception('Error al eliminar de la base de datos');
            }
        } else {
            header('Location: listado_contactos.php?error=Contacto no encontrado');
            exit;
        }
    } catch (Exception $e) {
        error_log("Error al eliminar contacto: " . $e->getMessage());
        header('Location: listado_contactos.php?error=Error al eliminar el contacto');
        exit;
    }
} else {
    header('Location: listado_contactos.php?error=ID de contacto inválido');
    exit;
}