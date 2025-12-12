<?php
//Siempre es interesante en todo tipo de proyectos tener un fichero de configuracion 
//con las conexiones y parametrizaciones del sistema


// Los ajustes están definidos dentro de un array
return [
    // Configuración de la base de datos
    'database' => [
        'dbname'   => 'usuarios.db',
    ],

    // Configuración general de la aplicación
    'app' => [
        'name'      => 'Gestion de usuarios',
        'version'   => '1.0.0',
        'debug'     => true,
        'timezone'  => 'Europe/Madrid',
    ],
    //DURACION SESION 
    'sesion' => [
        'duracion_seg' => '3600', //esta en segundos
    ],
    'pass' => [
        'hash' => 'p3p1noM@r1n0C0nFrut@D3l@P@si0n', //esta en segundos
    ],
];