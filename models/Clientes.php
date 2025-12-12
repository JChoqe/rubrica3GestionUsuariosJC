<?php

//Incluyo el control de errores
require_once(__DIR__ . "/../error.php");

//Siempre esta bien modelar las clases
//Modelado de clase de usuario
class Cliente
{
    public $cliente_id;
    public $nombre;
    public $cif;
    public $email;
    public $telefono;
    public $apellidos;
    public $edad;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->cliente_id = $data['cliente_id'] ?? null;
            $this->nombre     = $data['nombre'] ?? null;
            $this->cif    = $data['cif'] ?? null;
            $this->email      = $data['email'] ?? null;
            $this->telefono   = $data['telefono'] ?? null;
            $this->apellidos  = $data['apellidos'] ?? null;
            $this->edad     = $data['edad'] ?? null;
        }
    }

    // ====== Getters y Setters ======

    public function getId()
    {
        return $this->cliente_id ?? 0;
    }
    public function setId($id)
    {
        $this->cliente_id = $id;
    }

    public function getNombre()
    {
        return $this->nombre ?? '';
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getCIF()
    {
        return ($this->cif ?? '');
    }
    public function setCIF($cif)
    {
        $this->cif = $cif;
    }

    public function getEmail()
    {
        return $this->email ?? '';
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getTelefono()
    {
        return $this->telefono ?? '';
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function getApellidos()
    {
        return $this->apellidos ?? '';
    }
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    }

    public function getEdad()
    {
        return $this->edad ?? 0;
    }
    public function setEdad($edad)
    {
        $this->edad = $edad;
    }


    // ====== Métodos CRUD con PDO ======

    public function guardar($pdo)
    {
        if ($this->cliente_id === null || $this->cliente_id === 0) {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre,cif, email, telefono, apellidos, edad) 
                                   VALUES (:nombre,:cif, :email, :telefono, :apellidos, :edad)");

            $stmt->execute([
                ':nombre'   => $this->nombre,
                ':cif'  => $this->cif,
                ':email'     => $this->email,
                ':telefono'    => $this->telefono,
                ':apellidos' => $this->apellidos,
                ':edad'    => $this->edad,
            ]);

            $this->cliente_id = $pdo->lastInsertId();
        } else {
            // Update
            $stmt = $pdo->prepare("UPDATE clientes SET 
                                    nombre = :nombre,
                                    cif = :cif,
                                    email = :email,
                                    telefono = :telefono,
                                    apellidos = :apellidos,
                                    edad = :edad
                                   WHERE cliente_id = :id");

            $stmt->execute([
                ':nombre'   => $this->nombre,
                ':cif'  => $this->cif,
                ':email'     => $this->email,
                ':telefono'    => $this->telefono,
                ':apellidos' => $this->apellidos,
                ':edad'    => $this->edad,
                ':id'        => $this->cliente_id
            ]);
        }
    }

    public static function obtenerPorId($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE cliente_id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new self($data) : new Cliente();
    }


    public static function obtenerTodos($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM clientes");
        $clientes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clientes[] = new self($row);
        }

        return $clientes;
    }

    public function eliminar($pdo)
    {
        if ($this->cliente_id != null) {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE cliente_id = :id");
            $stmt->execute([':id' => $this->cliente_id]);
        }
    }
}
