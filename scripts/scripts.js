/**
 * scripts.js
 * 
 * Funciones JavaScript para navegación y acciones del sistema
 * 
 * @author Sistema de Gestión
 * @version 1.0
 */

// ==========================================
// FUNCIONES DE NAVEGACIÓN GENERAL
// ==========================================

/**
 * Redirige a la página de login
 */
function IrLogin() {
    window.location.href = 'login.php';
}

/**
 * Envía el formulario para iniciar sesión
 */
function login() {
    let frm = document.forms[0];
    frm.action = 'acceder.php?action=login';
    frm.submit();
}

/**
 * Cierra la sesión del usuario actual
 */
function cerrarSesion() {
    let frm = document.forms[0];
    if (!frm) {
        // Si no hay formulario, crear uno
        frm = document.createElement('form');
        frm.method = 'post';
        document.body.appendChild(frm);
    }
    frm.action = 'acceder.php?action=cerrarsesion';
    frm.submit();
}

// ==========================================
// FUNCIONES DE GESTIÓN DE USUARIOS
// ==========================================

/**
 * Redirige al listado de usuarios
 */
function IrListadoUsuarios() {
    window.location.href = 'listado.php';
}

/**
 * Abre la ficha de usuario (nuevo o editar)
 * @param {boolean} list - Si viene desde el listado
 */
function IrFicha(list = false) {
    let qList = list ? '?listado=true' : '';
    window.location.href = `ficha.php${qList}`;
}

/**
 * Envía el formulario para crear un nuevo usuario
 * @param {boolean} list - Si debe volver al listado
 */
function anadirUsuario(list = false) {
    let qList = list ? '&listado=true' : '';
    let frm = document.forms[0];
    frm.action = `ficha_guardar.php?action=anadir${qList}`;
    frm.submit();
}

/**
 * Envía el formulario para modificar un usuario existente
 * @param {boolean} list - Si debe volver al listado
 */
function modificarUsuario(list = false) {
    let qList = list ? '&listado=true' : '';
    let frm = document.forms[0];
    frm.action = `ficha_guardar.php?action=guardar${qList}`;
    frm.submit();
}

/**
 * Elimina un usuario tras confirmación
 * @param {number} id - ID del usuario a eliminar
 */
function eliminarUsuario(id) {
    if (confirm(`¿Seguro que deseas eliminar este usuario con ID ${id}?`)) {
        let hid = document.getElementById('usuario_id');
        if (hid) hid.value = id;

        let frm = document.forms[0];
        frm.action = 'ficha_guardar.php?action=eliminar&listado=true';
        frm.submit();
    }
}

// ==========================================
// FUNCIONES DE GESTIÓN DE CLIENTES
// ==========================================

/**
 * Redirige al listado de clientes
 */
function IrListadoClientes() {
    window.location.href = 'listado_clientes.php';
}

/**
 * Abre la ficha de cliente (nuevo o editar)
 * @param {boolean} list - Si viene desde el listado
 */
function IrFichaCliente(list = false) {
    let qList = list ? '?listado=true' : '';
    window.location.href = `ficha_cliente.php${qList}`;
}

/**
 * Envía el formulario para crear un nuevo cliente
 * @param {boolean} list - Si debe volver al listado
 */
function anadirCliente(list = false) {
    let qList = list ? '&listado=true' : '';
    let frm = document.forms[0];
    frm.action = `ficha_cliente_guardar.php?action=anadir${qList}`;
    frm.submit();
}

/**
 * Envía el formulario para modificar un cliente existente
 * @param {boolean} list - Si debe volver al listado
 */
function modificarCliente(list = false) {
    let qList = list ? '&listado=true' : '';
    let frm = document.forms[0];
    frm.action = `ficha_cliente_guardar.php?action=guardar${qList}`;
    frm.submit();
}

/**
 * Elimina un cliente tras confirmación
 * @param {number} id - ID del cliente a eliminar
 */
function eliminarCliente(id) {
    if (confirm(`¿Seguro que deseas eliminar este cliente con ID ${id}?\n\nADVERTENCIA: También se eliminarán todos sus contactos.`)) {
        let hid = document.getElementById('cliente_id');
        if (hid) hid.value = id;

        let frm = document.forms[0];
        frm.action = 'ficha_cliente_guardar.php?action=eliminar&listado=true';
        frm.submit();
    }
}

// ==========================================
// FUNCIONES DE GESTIÓN DE CONTACTOS
// ==========================================

/**
 * Redirige al listado completo de contactos
 */
function IrListadoContactos() {
    window.location.href = 'listado_contactos.php';
}

/**
 * Redirige al listado de contactos de un cliente específico
 * @param {number} clienteId - ID del cliente
 */
function IrContactosCliente(clienteId) {
    window.location.href = `listado_contactos.php?cliente_id=${clienteId}`;
}

/**
 * Abre la ficha de contacto (nuevo o editar)
 * @param {number} contactoId - ID del contacto (0 para nuevo)
 * @param {number} clienteId - ID del cliente predeterminado
 */
function IrFichaContacto(contactoId = 0, clienteId = 0) {
    let url = 'ficha_contacto.php';
    let params = [];
    
    if (contactoId > 0) {
        params.push(`contacto_id=${contactoId}`);
    }
    if (clienteId > 0) {
        params.push(`cliente_id=${clienteId}`);
        params.push('volver_cliente=true');
    }
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

/**
 * Elimina un contacto tras confirmación
 * @param {number} id - ID del contacto a eliminar
 * @param {number} clienteId - ID del cliente (para filtro de retorno)
 */
function eliminarContacto(id, clienteId = 0) {
    if (confirm(`¿Seguro que deseas eliminar este contacto con ID ${id}?`)) {
        // Crear formulario dinámicamente
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'eliminar_contacto.php';
        
        // Campo contacto_id
        let inputContacto = document.createElement('input');
        inputContacto.type = 'hidden';
        inputContacto.name = 'contacto_id';
        inputContacto.value = id;
        form.appendChild(inputContacto);
        
        // Campo cliente_id_filtro (para volver al listado correcto)
        let inputCliente = document.createElement('input');
        inputCliente.type = 'hidden';
        inputCliente.name = 'cliente_id_filtro';
        inputCliente.value = clienteId;
        form.appendChild(inputCliente);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// ==========================================
// FUNCIONES DE VALIDACIÓN EN CLIENTE
// ==========================================

/**
 * Valida formato de teléfono español en tiempo real
 * @param {HTMLInputElement} input - Campo de teléfono
 */
function validarTelefono(input) {
    // Eliminar caracteres no numéricos excepto espacios
    let valor = input.value.replace(/[^\d\s]/g, '');
    
    // Limitar a 11 caracteres (9 dígitos + 2 espacios)
    if (valor.length > 11) {
        valor = valor.substring(0, 11);
    }
    
    input.value = valor;
}

/**
 * Formatea teléfono automáticamente (XXX XXX XXX)
 * @param {HTMLInputElement} input - Campo de teléfono
 */
function formatearTelefonoInput(input) {
    let valor = input.value.replace(/\s/g, ''); // Quitar espacios
    
    if (valor.length >= 3) {
        let formatted = valor.substring(0, 3);
        if (valor.length >= 6) {
            formatted += ' ' + valor.substring(3, 6);
            if (valor.length >= 9) {
                formatted += ' ' + valor.substring(6, 9);
            } else if (valor.length > 6) {
                formatted += ' ' + valor.substring(6);
            }
        } else if (valor.length > 3) {
            formatted += ' ' + valor.substring(3);
        }
        input.value = formatted;
    }
}

// ==========================================
// EVENTOS AL CARGAR LA PÁGINA
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // Auto-formateo de teléfono si existe el campo
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function() {
            validarTelefono(this);
        });
        
        telefonoInput.addEventListener('blur', function() {
            formatearTelefonoInput(this);
        });
    }
});