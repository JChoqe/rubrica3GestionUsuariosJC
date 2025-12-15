<?php
/**
 * ficha_contacto_guardar.php
 * 
 * Controlador que procesa el formulario de contactos
 * Valida datos y guarda/actualiza en base de datos
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

// Recogida de datos del formulario
$contacto_id = (int)($_POST['contacto_id'] ?? 0);
$cliente_id = (int)($_POST['cliente_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$volver_cliente = ($_POST['volver_cliente'] ?? 'false') === 'true';

$nuevo = ($contacto_id == 0);

// ===== VALIDACIÓN DE DATOS =====

$errores = [];

// 1. Cliente seleccionado
if ($cliente_id == 0) {
    $errores[] = 'Debe seleccionar un cliente';
}

// 2. Nombre obligatorio y válido
if ($nombre === '') {
    $errores[] = 'El nombre es obligatorio';
} elseif (!comprobarNombre($nombre)) {
    $errores[] = 'El nombre solo puede contener letras y espacios';
}

// 3. Apellidos obligatorios y válidos
if ($apellidos === '') {
    $errores[] = 'Los apellidos son obligatorios';
} elseif (!comprobarNombre($apellidos)) {
    $errores[] = 'Los apellidos solo pueden contener letras y espacios';
}

// 4. Email obligatorio y válido
if ($email === '') {
    $errores[] = 'El email es obligatorio';
} elseif (!comprobarPatronEmail($email)) {
    $errores[] = 'El formato del email no es válido';
}

// 5. Teléfono obligatorio y español válido
if ($telefono === '') {
    $errores[] = 'El teléfono es obligatorio';
} elseif (!comprobarTelefonoEspanol($telefono)) {
    $errores[] = 'El teléfono debe ser español (9 dígitos empezando por 6, 7, 8 o 9)';
}

// 6. Verificar que el cliente existe
if ($cliente_id > 0) {
    $stmt = $pdo->prepare("SELECT cliente_id FROM clientes WHERE cliente_id = :id");
    $stmt->execute([':id' => $cliente_id]);
    if (!$stmt->fetch()) {
        $errores[] = 'El cliente seleccionado no existe';
    }
}

// Si hay errores, volver al formulario
if (!empty($errores)) {
    $error_msg = implode('--', $errores);
    $params = http_build_query([
        'error' => $error_msg,
        'contacto_id' => $contacto_id,
        'cliente_id' => $cliente_id,
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'email' => $email,
        'telefono' => $telefono,
        'volver_cliente' => $volver_cliente ? 'true' : 'false'
    ]);
    header("Location: ficha_contacto.php?$params");
    exit;
}

// ===== TODO VÁLIDO: GUARDAR CONTACTO =====

try {
    // Crear o cargar el contacto
    if ($nuevo) {
        $contacto = new Contacto();
    } else {
        $contacto = Contacto::obtenerPorId($pdo, $contacto_id);
        if (!$contacto) {
            header('Location: listado_contactos.php?error=Contacto no encontrado');
            exit;
        }
    }

    // Asignar datos
    $contacto->setClienteId($cliente_id);
    $contacto->setNombre($nombre);
    $contacto->setApellidos($apellidos);
    $contacto->setEmail($email);
    $contacto->setTelefono($telefono);

    // Guardar en base de datos
    if ($contacto->guardar($pdo)) {
        $mensaje = $nuevo ? 'Contacto creado correctamente' : 'Contacto actualizado correctamente';
        
        // Redirigir según de dónde venga
        if ($volver_cliente && $cliente_id > 0) {
            header("Location: listado_contactos.php?cliente_id=$cliente_id&success=" . urlencode($mensaje));
        } else {
            header("Location: listado_contactos.php?success=" . urlencode($mensaje));
        }
        exit;
    } else {
        throw new Exception('Error al guardar en base de datos');
    }

} catch (Exception $e) {
    error_log("Error al guardar contacto: " . $e->getMessage());
    header('Location: ficha_contacto.php?error=Error al guardar el contacto&contacto_id=' . $contacto_id);
    exit;
}