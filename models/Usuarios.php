<?php
$config = require_once(__DIR__ . "/../config.php");
//Incluyo el control de errores
require_once(__DIR__ . "/../error.php");

//Siempre esta bien modelar las clases
//Modelado de clase de usuario
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

    // ====== Getters y Setters ======

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
        return ($this->usuario ?? '');
    }
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function getPassword()
    {
        return $this->password ?? '';
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getEmail()
    {
        return $this->email ?? '';
    }
    public function setEmail($email)
    {
        $this->email = $email;
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

    public function getRolId()
    {
        return $this->rol_id ?? 0;
    }
    public function setRolId($rol_id)
    {
        $this->rol_id = $rol_id;
    }


    // ====== Métodos CRUD con PDO ======

    public function guardar($pdo)
    {
        global $config;
        $claveEC = $config["pass"]["hash"];
        if ($this->usuario_id === null || $this->usuario_id === 0) {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO usuarios (usuario,password, email, nombre, apellidos, rol_id) 
                                   VALUES (:usuario,:password, :email, :nombre, :apellidos, :rol_id)");

            $stmt->execute([
                ':usuario'   => $this->usuario,
                ':password'  => password_hash($this->password. $claveEC, PASSWORD_DEFAULT),
                ':email'     => $this->email,
                ':nombre'    => $this->nombre,
                ':apellidos' => $this->apellidos,
                ':rol_id'    => $this->rol_id,
            ]);

            $this->usuario_id = $pdo->lastInsertId();
        } else {
            // Update
            $stmt = $pdo->prepare("UPDATE usuarios SET 
                                    usuario = :usuario,
                                    password = :password,
                                    email = :email,
                                    nombre = :nombre,
                                    apellidos = :apellidos,
                                    rol_id = :rol_id
                                   WHERE usuario_id = :id");

            $stmt->execute([
                ':usuario'   => $this->usuario,
                ':password'  => password_hash($this->password. $claveEC, PASSWORD_DEFAULT),
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

        return $data ? new self($data) : new Usuario();
    }

    public static function login($pdo, $usuario, $password)
    {
        global $config;
        $claveEC = $config["pass"]["hash"];

        //print_r($claveEC);
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            // Usuario no existe
            return null;
        }

        // Contraseña guardada en la BD
        $hashGuardado = $data["password"];

        // Verificar contraseña ingresada
        if (!password_verify($password . $claveEC, $hashGuardado)) {
            // Contraseña incorrecta
            return null;
        }

        return $data ? new self($data) : null;
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
        if ($this->usuario_id != null) {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE usuario_id = :id");
            $stmt->execute([':id' => $this->usuario_id]);
        }
    }
}
