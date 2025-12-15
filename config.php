<?php
/**
 * Archivo de configuración global del proyecto
 * 
 * Centraliza los parámetros principales de la aplicación:
 *  - Conexión a base de datos
 *  - Ajustes generales de la app
 *  - Configuración de sesión
 *  - Parámetros relacionados con seguridad
 * 
 * Facilita el mantenimiento y evita valores “hardcodeados”
 */

// Todos los ajustes se devuelven dentro de un array asociativo
return [

    /**
     * Configuración de la base de datos
     * Define el nombre del fichero de la base de datos (SQLite)
     */
    'database' => [
        'dbname' => 'usuarios.db',
    ],

    /**
     * Configuración general de la aplicación
     */
    'app' => [
        'name'     => 'Gestión de usuarios', // Nombre de la aplicación
        'version'  => '1.0.0',               // Versión actual
        'debug'    => true,                  // Modo debug (desactivar en producción)
        'timezone' => 'Europe/Madrid',       // Zona horaria del sistema
    ],

    /**
     * Configuración de sesión
     */
    'sesion' => [
        // Duración máxima de la sesión en segundos (3600 = 1 hora)
        'duracion_seg' => 3600,
    ],

    /**
     * Configuración relacionada con contraseñas
     */
    'pass' => [
        // Clave "salt" utilizada para el hash de contraseñas
        // Importante: no debe cambiarse en producción
        'hash' => 'p3p1noM@r1n0C0nFrut@D3l@P@si0n',
    ],
];
