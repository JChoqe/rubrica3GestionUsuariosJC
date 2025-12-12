<?php
//Me traigo el fichero que tiene todas las librerias básicas del proyecto
require_once "utils.php";
class BaseDatos
{
    protected $pdo; //Conexion global de la db

    public function __construct()
    {
        global $config;
        //print_r($config);
        //Inicio las propiedades necesarias en el constructor y hago la conexión la BBDD
        $bbdd = $config['database']['dbname'];
        // Conectar a SQLite
        // echo  $bbdd ;
        //echo  __DIR__ ;
        //echo 'sqlite:' . __DIR__ . '/bbdd/'. $bbdd;
        //$this->pdo = new PDO('sqlite:' . __DIR__ . '/bbdd/'. $bbdd);
        $dbPath = __DIR__ . '/bbdd/usuarios.db';
        if (!file_exists($dbPath)) {
            throw new Exception("Base de datos no encontrada: $dbPath");
        }
        $this->pdo = new PDO('sqlite:' . $dbPath);
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
