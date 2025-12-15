<?php

// Carga de utilidades y dependencias básicas del proyecto
require_once "utils.php";

class BaseDatos
{
    protected $pdo; // Conexión PDO a la base de datos

    public function __construct()
    {
        global $config;

        // Nombre de la base de datos definido en la configuración
        $bbdd = $config['database']['dbname'];

        // Conexión a base de datos SQLite
        $this->pdo = new PDO(
            'sqlite:' . __DIR__ . '/bbdd/' . $bbdd
        );
    }

    // Devuelve la conexión PDO para su reutilización
    public function getPdo()
    {
        return $this->pdo;
    }
}
