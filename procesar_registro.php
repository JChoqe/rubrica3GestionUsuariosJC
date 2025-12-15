<?php
/**
 * procesar_registro.php
 * Procesa el formulario de registro de nuevos usuarios
 */

require_once "utils.php";
require_once "./models/Usuarios.php";

// Solo procesar si es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// Recogida de datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Array para acumular errores
$errores = [];

// ===== VALIDACIONES =====

// 1. Campos obligatorios
if ($nombre === '') {
    $errores[] = 'El nombre es obligatorio';
}
if ($usuario === '') {
    $errores[] = 'El nombre de usuario es obligatorio';
}
if ($email === '') {
    $errores[] = 'El correo electrónico es obligatorio';
}
if ($password === '') {
    $errores[] = 'La contraseña es obligatoria';
}

// 2. Validar formato de email
if ($email !== '' && !comprobarPatronEmail($email)) {
    $errores[] = 'El formato del correo electrónico no es válido';
}

// 3. Validar fortaleza de contraseña
if ($password !== '' && !comprobarPassword($password)) {
    $errores[] = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial';
}

// 4. Verificar que las contraseñas coincidan
if ($password !== $password_confirm) {
    $errores[] = 'Las contraseñas no coinciden';
}

// 5. Comprobar que el nombre de usuario no exista
if ($usuario !== '') {
    $stmt = $pdo->prepare("SELECT usuario_id FROM usuarios WHERE usuario = :usuario LIMIT 1");
    $stmt->execute([':usuario' => $usuario]);
    if ($stmt->fetch()) {
        $errores[] = 'El nombre de usuario ya está en uso';
    }
}

// 6. Comprobar que el email no exista
if ($email !== '') {
    $stmt = $pdo->prepare("SELECT usuario_id FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        $errores[] = 'El correo electrónico ya está registrado';
    }
}

// Si hay errores, volver al formulario
if (!empty($errores)) {
    $error_msg = implode('--', $errores);
    $params = http_build_query([
        'error' => $error_msg,
        'nombre' => $nombre,
        'usuario' => $usuario,
        'email' => $email
    ]);
    header("Location: register.php?$params");
    exit;
}

// ===== TODO CORRECTO: CREAR USUARIO =====

try {
    $nuevo_usuario = new Usuario();
    $nuevo_usuario->setNombre($nombre);
    $nuevo_usuario->setUsuario($usuario);
    $nuevo_usuario->setEmail($email);
    $nuevo_usuario->setPassword($password);
    $nuevo_usuario->setApellidos(''); // Opcional
    $nuevo_usuario->setRolId(2); // Rol de usuario normal por defecto
    
    $nuevo_usuario->guardar($pdo);
    
    // Registro exitoso: redirigir a login con mensaje
    header('Location: login.php?registro=exitoso&usuario=' . urlencode($usuario));
    exit;
    
} catch (Exception $e) {
    // Error al guardar en base de datos
    $error_msg = 'Error al crear la cuenta. Por favor, inténtelo de nuevo.';
    header('Location: register.php?error=' . urlencode($error_msg));
    exit;
}