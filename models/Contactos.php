<?php
require_once(__DIR__ . "/../error.php");

/*
 * Clase Contacto
 * Representa un contacto de un cliente en el sistema
 * Cada contacto pertenece a un cliente (relación FK)
 */
class Contacto
{
    // Propiedades públicas (igual que en tus otras clases)
    public $contacto_id;
    public $nombre;
    public $apellidos;
    public $email;
    public $telefono;
    public $cliente_id; // FK - a qué cliente pertenece este contacto

    /**
     * Constructor: crea un contacto desde un array (datos de BD)
     * @param array $data - Array asociativo con los datos del contacto
     */
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->contacto_id = $data['contacto_id'] ?? null;
            $this->nombre      = $data['nombre'] ?? null;
            $this->apellidos   = $data['apellidos'] ?? null;
            $this->email       = $data['email'] ?? null;
            $this->telefono    = $data['telefono'] ?? null;
            $this->cliente_id  = $data['cliente_id'] ?? null;
        }
    }

    // ====== Getters y Setters ======

    public function getId()
    {
        return $this->contacto_id ?? 0;
    }
    public function setId($id)
    {
        $this->contacto_id = $id;
    }

    public function getNombre()
    {
        return $this->nombre ?? '';
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getApellidos()
    {
        return $this->apellidos ?? '';
    }
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
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

    public function getClienteId()
    {
        return $this->cliente_id ?? 0;
    }
    public function setClienteId($cliente_id)
    {
        $this->cliente_id = $cliente_id;
    }

    // ====== Métodos CRUD con PDO ======

    /**
     * Guarda el contacto en la BD
     * Si NO tiene ID -> INSERT (nuevo contacto)
     * Si tiene ID -> UPDATE (modificar existente)
     * @param PDO $pdo - Conexión a la base de datos
     */
    public function guardar($pdo)
    {
        if ($this->contacto_id === null || $this->contacto_id === 0) {
            // INSERT - Crear nuevo contacto
            $stmt = $pdo->prepare("INSERT INTO contactos (nombre, apellidos, email, telefono, cliente_id) 
                                   VALUES (:nombre, :apellidos, :email, :telefono, :cliente_id)");

            $stmt->execute([
                ':nombre'     => $this->nombre,
                ':apellidos'  => $this->apellidos,
                ':email'      => $this->email,
                ':telefono'   => $this->telefono,
                ':cliente_id' => $this->cliente_id,
            ]);

            // Guardamos el ID generado automáticamente
            $this->contacto_id = $pdo->lastInsertId();
        } else {
            // UPDATE - Modificar contacto existente
            $stmt = $pdo->prepare("UPDATE contactos SET 
                                    nombre = :nombre,
                                    apellidos = :apellidos,
                                    email = :email,
                                    telefono = :telefono,
                                    cliente_id = :cliente_id
                                   WHERE contacto_id = :id");

            $stmt->execute([
                ':nombre'     => $this->nombre,
                ':apellidos'  => $this->apellidos,
                ':email'      => $this->email,
                ':telefono'   => $this->telefono,
                ':cliente_id' => $this->cliente_id,
                ':id'         => $this->contacto_id
            ]);
        }
    }

    /**
     * Obtiene un contacto por su ID
     * @param PDO $pdo - Conexión a la BD
     * @param int $id - ID del contacto a buscar
     * @return Contacto - Objeto Contacto (vacío si no existe)
     */
    public static function obtenerPorId($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM contactos WHERE contacto_id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si encuentra datos, crea el objeto; si no, devuelve uno vacío
        return $data ? new self($data) : new Contacto();
    }

    /**
     * Obtiene TODOS los contactos de la BD
     * @param PDO $pdo - Conexión a la BD
     * @return array - Array de objetos Contacto
     */
    public static function obtenerTodos($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM contactos");
        $contactos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contactos[] = new self($row);
        }

        return $contactos;
    }

    /**
     * Obtiene SOLO los contactos de un cliente específico
     * @param PDO $pdo - Conexión a la BD
     * @param int $cliente_id - ID del cliente
     * @return array - Array de objetos Contacto
     */
    public static function obtenerPorCliente($pdo, $cliente_id)
    {
        $stmt = $pdo->prepare("SELECT * FROM contactos WHERE cliente_id = :cliente_id");
        $stmt->execute([':cliente_id' => $cliente_id]);

        $contactos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contactos[] = new self($row);
        }

        return $contactos;
    }

    /**
     * Elimina el contacto de la BD
     * @param PDO $pdo - Conexión a la BD
     */
    public function eliminar($pdo)
    {
        if ($this->contacto_id != null) {
            $stmt = $pdo->prepare("DELETE FROM contactos WHERE contacto_id = :id");
            $stmt->execute([':id' => $this->contacto_id]);
        }
    }
}
