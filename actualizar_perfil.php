<?php
/**
 * actualizar_perfil.php
 * Procesa las actualizaciones del perfil de usuario
 * Maneja tanto cambios de datos personales como de contraseña
 */

require_once "utils.php";
require_once "./models/Usuarios.php";

// Verificar sesión activa
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php?accion=sesioncaducada');
    exit;
}

// Solo procesar si es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

$usuario_actual = $_SESSION["usuario"];
$usuario_id = $usuario_actual->getId();
$accion = $_POST['accion'] ?? '';

// ===== ACTUALIZAR DATOS PERSONALES =====
if ($accion === 'actualizar_datos') {
    
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    $errores = [];
    
    // Validaciones
    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio';
    }
    
    if ($email === '') {
        $errores[] = 'El email es obligatorio';
    }
    
    if ($email !== '' && !comprobarPatronEmail($email)) {
        $errores[] = 'El formato del email no es válido';
    }
    
    // Verificar que el email no esté en uso por otro usuario
    if ($email !== '') {
        $stmt = $pdo->prepare("SELECT usuario_id FROM usuarios WHERE email = :email AND usuario_id != :id LIMIT 1");
        $stmt->execute([':email' => $email, ':id' => $usuario_id]);
        if ($stmt->fetch()) {
            $errores[] = 'El email ya está en uso por otro usuario';
        }
    }
    
    if (!empty($errores)) {
        $error_msg = implode('--', $errores);
        header("Location: profile.php?error=" . urlencode($error_msg));
        exit;
    }
    
    // Actualizar datos
    try {
        $usu = Usuario::obtenerPorId($pdo, $usuario_id);
        $usu->setNombre($nombre);
        $usu->setApellidos($apellidos);
        $usu->setEmail($email);
        
        // Mantener la contraseña actual (no cambiarla)
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email WHERE usuario_id = :id");
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':id' => $usuario_id
        ]);
        
        // Actualizar sesión
        $_SESSION["usuario"] = Usuario::obtenerPorId($pdo, $usuario_id);
        
        header('Location: profile.php?success=Datos actualizados correctamente');
        exit;
        
    } catch (Exception $e) {
        header('Location: profile.php?error=Error al actualizar los datos');
        exit;
    }
}

// ===== CAMBIAR CONTRASEÑA =====
if ($accion === 'cambiar_password') {
    
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';
    
    $errores = [];
    
    // Validaciones
    if ($password_actual === '' || $password_nueva === '' || $password_confirmar === '') {
        $errores[] = 'Todos los campos de contraseña son obligatorios';
    }
    
    if ($password_nueva !== $password_confirmar) {
        $errores[] = 'Las contraseñas nuevas no coinciden';
    }
    
    if ($password_nueva !== '' && !comprobarPassword($password_nueva)) {
        $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial';
    }
    
    // Verificar contraseña actual
    $usu = Usuario::obtenerPorId($pdo, $usuario_id);
    global $config;
    $claveEC = $config["pass"]["hash"];
    
    if (!password_verify($password_actual . $claveEC, $usu->getPassword())) {
        $errores[] = 'La contraseña actual es incorrecta';
    }
    
    if (!empty($errores)) {
        $error_msg = implode('--', $errores);
        header("Location: profile.php?error=" . urlencode($error_msg));
        exit;
    }
    
    // Cambiar contraseña
    try {
        $password_hash = password_hash($password_nueva . $claveEC, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE usuarios SET password = :password WHERE usuario_id = :id");
        $stmt->execute([
            ':password' => $password_hash,
            ':id' => $usuario_id
        ]);
        
        header('Location: profile.php?success=Contraseña cambiada correctamente');
        exit;
        
    } catch (Exception $e) {
        header('Location: profile.php?error=Error al cambiar la contraseña');
        exit;
    }
}

// Acción no reconocida
header('Location: profile.php');
exit;