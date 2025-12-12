<?php
//Incluyo el control de errores 
require_once(__DIR__ . "/../error.php");

//Siempre esta bien modelar las clases
//Modelado de clase de roles
class Rol
{
    private $rol_id;
    private $rol;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->rol_id = $data['rol_id'] ?? null;
            $this->rol    = $data['rol'] ?? null;
        }
    }

    // ====== Getters y Setters ======

    public function getId() { return $this->rol_id; }
    public function setId($id) { $this->rol_id = $id; }

    public function getRol() { return $this->rol; }
    public function setRol($rol) { $this->rol = $rol; }

    // ====== CRUD con PDO ======

    public function guardar($pdo)
    {
        if ($this->rol_id === null) {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO roles (rol) VALUES (:rol)");
            $stmt->execute([
                ':rol' => $this->rol
            ]);
            $this->rol_id = $pdo->lastInsertId();
        } else {
            // Update
            $stmt = $pdo->prepare("UPDATE roles SET rol = :rol WHERE rol_id = :id");
            $stmt->execute([
                ':rol' => $this->rol,
                ':id'  => $this->rol_id
            ]);
        }
    }

    public static function obtenerPorId($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE rol_id = :id");
        $stmt->execute([ ':id' => $id ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new self($data) : null;
    }

    public static function obtenerTodos($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM roles");
        $roles = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $roles[] = new self($row);
        }

        return $roles;
    }

    public function eliminar($pdo)
    {
        if ($this->rol_id !== null) {
            $stmt = $pdo->prepare("DELETE FROM roles WHERE rol_id = :id");
            $stmt->execute([':id' => $this->rol_id]);
        }
    }
}
