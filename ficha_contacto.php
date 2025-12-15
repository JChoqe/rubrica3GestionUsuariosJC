<?php
/**
 * ficha_contacto.php
 * 
 * Formulario para crear o editar un contacto
 * Solo accesible por administradores
 * 
 * Parámetros GET:
 * - contacto_id: ID del contacto a editar (0 o ausente = nuevo)
 * - cliente_id: ID del cliente predeterminado
 * - volver_cliente: true si debe volver al listado del cliente
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

require_once "utils.php";
require_once "./models/Contactos.php";
require_once "./models/Clientes.php";

// Verificar sesión activa
if (!isset($_SESSION["usuario"])) {
    header('Location: login.php?accion=sesioncaducada');
    exit;
}

// Solo administradores pueden gestionar contactos
$usu_conectado = $_SESSION["usuario"];
if ($usu_conectado->getRolId() != 1) {
    header('Location: listado_contactos.php?error=Solo administradores pueden gestionar contactos');
    exit;
}

// Parámetros GET
$contacto_id = (int)($_GET['contacto_id'] ?? 0);
$cliente_id_param = (int)($_GET['cliente_id'] ?? 0);
$volver_cliente = ($_GET['volver_cliente'] ?? '') === 'true';
$error = $_GET['error'] ?? '';

// Determinar si es nuevo o edición
$nuevo = ($contacto_id == 0);
$titulo = $nuevo ? 'Nuevo Contacto' : 'Modificar Contacto';

// Valores por defecto
$nombre = '';
$apellidos = '';
$email = '';
$telefono = '';
$cliente_id = $cliente_id_param;

// Cargar datos si hay error o es edición
if ($error !== '') {
    // Recuperar datos tras error de validación
    $nombre = htmlspecialchars($_GET['nombre'] ?? '');
    $apellidos = htmlspecialchars($_GET['apellidos'] ?? '');
    $email = htmlspecialchars($_GET['email'] ?? '');
    $telefono = htmlspecialchars($_GET['telefono'] ?? '');
    $cliente_id = (int)($_GET['cliente_id'] ?? 0);
    
    // Mostrar errores
    echo "<script>alert('" . str_replace("--", "\\n", $error) . "')</script>";
} else {
    if (!$nuevo) {
        // Cargar datos del contacto existente
        $contacto = Contacto::obtenerPorId($pdo, $contacto_id);
        if ($contacto) {
            $nombre = htmlspecialchars($contacto->getNombre());
            $apellidos = htmlspecialchars($contacto->getApellidos());
            $email = htmlspecialchars($contacto->getEmail());
            $telefono = htmlspecialchars($contacto->getTelefono());
            $cliente_id = $contacto->getClienteId();
        } else {
            header('Location: listado_contactos.php?error=Contacto no encontrado');
            exit;
        }
    }
}

// Cargar lista de clientes para el select
$clientes = Cliente::obtenerTodos($pdo);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= $titulo ?></title>
    <link rel="stylesheet" href="./estilos/estilos.css">
    <script src="./scripts/scripts.js"></script>
</head>

<body>
    <div class="container">
        <h1><?= $titulo ?></h1>

        <form method="post" action="ficha_contacto_guardar.php">
            <input type="hidden" name="contacto_id" value="<?= $contacto_id ?>">
            <input type="hidden" name="volver_cliente" value="<?= $volver_cliente ? 'true' : 'false' ?>">

            <!-- Selector de cliente -->
            <div class="campo">
                <label for="cliente_id">Cliente *</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">-- Seleccione un cliente --</option>
                    <?php foreach ($clientes as $cli): ?>
                        <option value="<?= $cli->getId() ?>" 
                                <?= $cliente_id == $cli->getId() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cli->getNombre()) ?> 
                            (<?= htmlspecialchars($cli->getCIF()) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>Seleccione el cliente al que pertenece este contacto</small>
            </div>

            <!-- Nombre -->
            <div class="campo">
                <label for="nombre">Nombre *</label>
                <input type="text" 
                       id="nombre" 
                       name="nombre" 
                       placeholder="Nombre del contacto" 
                       value="<?= $nombre ?>"
                       required>
            </div>

            <!-- Apellidos -->
            <div class="campo">
                <label for="apellidos">Apellidos *</label>
                <input type="text" 
                       id="apellidos" 
                       name="apellidos" 
                       placeholder="Apellidos del contacto" 
                       value="<?= $apellidos ?>"
                       required>
            </div>

            <!-- Email -->
            <div class="campo">
                <label for="email">Email *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       placeholder="contacto@empresa.com" 
                       value="<?= $email ?>"
                       required>
                <small>Email de contacto corporativo</small>
            </div>

            <!-- Teléfono -->
            <div class="campo">
                <label for="telefono">Teléfono *</label>
                <input type="tel" 
                       id="telefono" 
                       name="telefono" 
                       placeholder="612 345 678" 
                       value="<?= $telefono ?>"
                       pattern="[6789]\d{8}|[6789]\d{2}\s\d{3}\s\d{3}"
                       maxlength="11"
                       required>
                <small>Teléfono español (9 dígitos, puede incluir espacios)</small>
            </div>

            <!-- Botones de acción -->
            <div class="acciones">
                <button type="submit" class="btn primary">
                    <?= $nuevo ? 'Crear Contacto' : 'Guardar Cambios' ?>
                </button>
                
                <?php if ($volver_cliente && $cliente_id > 0): ?>
                    <a href="listado_contactos.php?cliente_id=<?= $cliente_id ?>" 
                       class="btn secondary">
                        Cancelar
                    </a>
                <?php else: ?>
                    <a href="listado_contactos.php" class="btn secondary">
                        Cancelar
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>

</html>