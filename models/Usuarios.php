<?php

// Control centralizado de errores
require_once(__DIR__ . "/../error.php");

// Modelo de dominio: Usuario
class Usuario
{
    public $usuario_id;
    public $usuario;
    public $email;
    public $nombre;
    public $password;
    public $apellidos;
    public $rol_id;

    public function __construct($data = [])
    {
        // Hidratación del objeto a partir de un array (BD / formulario)
        if (!empty($data)) {
            $this->usuario_id = $data['usuario_id'] ?? null;
            $this->usuario    = $data['usuario'] ?? null;
            $this->password   = $data['password'] ?? null;
            $this->email      = $data['email'] ?? null;
            $this->nombre     = $data['nombre'] ?? null;
            $this->apellidos  = $data['apellidos'] ?? null;
            $this->rol_id     = $data['rol_id'] ?? null;
        }
    }

    // ===== Getters y setters =====

    public function getId()
    {
        return $this->usuario_id ?? 0;
    }
    public function setId($id)
    {
        $this->usuario_id = $id;
    }

    public function getUsuario()
    {
        return $this->usuario ?? '';
    }
    public function setUsuario($u)
    {
        $this->usuario = $u;
    }

    public function getPassword()
    {
        return $this->password ?? '';
    }
    public function setPassword($p)
    {
        $this->password = $p;
    }

    public function getEmail()
    {
        return $this->email ?? '';
    }
    public function setEmail($e)
    {
        $this->email = $e;
    }

    public function getNombre()
    {
        return $this->nombre ?? '';
    }
    public function setNombre($n)
    {
        $this->nombre = $n;
    }

    public function getApellidos()
    {
        return $this->apellidos ?? '';
    }
    public function setApellidos($a)
    {
        $this->apellidos = $a;
    }

    public function getRolId()
    {
        return $this->rol_id ?? 0;
    }
    public function setRolId($r)
    {
        $this->rol_id = $r;
    }

    // ===== Persistencia =====

    public function guardar($pdo)
    {
        global $config;
        $claveEC = $config["pass"]["hash"];

        // Inserta o actualiza según exista ID
        if ($this->usuario_id === null || $this->usuario_id === 0) {

            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (usuario, password, email, nombre, apellidos, rol_id)
                 VALUES (:usuario, :password, :email, :nombre, :apellidos, :rol_id)"
            );

            $stmt->execute([
                ':usuario'   => $this->usuario,
                ':password'  => password_hash($this->password . $claveEC, PASSWORD_DEFAULT),
                ':email'     => $this->email,
                ':nombre'    => $this->nombre,
                ':apellidos' => $this->apellidos,
                ':rol_id'    => $this->rol_id,
            ]);

            $this->usuario_id = $pdo->lastInsertId();
        } else {

            $stmt = $pdo->prepare(
                "UPDATE usuarios SET
                    usuario = :usuario,
                    password = :password,
                    email = :email,
                    nombre = :nombre,
                    apellidos = :apellidos,
                    rol_id = :rol_id
                 WHERE usuario_id = :id"
            );

            $stmt->execute([
                ':usuario'   => $this->usuario,
                ':password'  => password_hash($this->password . $claveEC, PASSWORD_DEFAULT),
                ':email'     => $this->email,
                ':nombre'    => $this->nombre,
                ':apellidos' => $this->apellidos,
                ':rol_id'    => $this->rol_id,
                ':id'        => $this->usuario_id
            ]);
        }
    }

    public static function obtenerPorId($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario_id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : new self();
    }

    public static function login($pdo, $usuario, $password)
    {
        global $config;
        $claveEC = $config["pass"]["hash"];

        // Búsqueda del usuario por nombre
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }

        // Verificación de credenciales
        if (!password_verify($password . $claveEC, $data["password"])) {
            return null;
        }

        return new self($data);
    }

    public static function obtenerTodos($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM usuarios");
        $usuarios = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new self($row);
        }

        return $usuarios;
    }

    public function eliminar($pdo)
    {
        if ($this->usuario_id !== null) {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE usuario_id = :id");
            $stmt->execute([':id' => $this->usuario_id]);
        }
    }
}
