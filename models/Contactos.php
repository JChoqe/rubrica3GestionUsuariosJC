<?php
/**
 * Modelo Contacto
 *
 * Gestiona la entidad contacto y su persistencia en base de datos.
 * Cada contacto pertenece a un cliente.
 */

require_once(__DIR__ . "/../error.php");

class Contacto
{
    // Identificador del contacto (PK)
    private $contacto_id;

    // Cliente al que pertenece el contacto (FK)
    private $cliente_id;

    private $nombre;
    private $apellidos;
    private $email;
    private $telefono;

    /**
     * Constructor con hidratación opcional desde array (BD)
     */
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->contacto_id = $data['contacto_id'] ?? null;
            $this->cliente_id  = $data['cliente_id'] ?? null;
            $this->nombre      = $data['nombre'] ?? '';
            $this->apellidos   = $data['apellidos'] ?? '';
            $this->email       = $data['email'] ?? '';
            $this->telefono    = $data['telefono'] ?? '';
        }
    }

    // ===== GETTERS =====

    public function getId()
    {
        return $this->contacto_id ?? 0;
    }

    public function getClienteId()
    {
        return $this->cliente_id ?? 0;
    }

    public function getNombre()
    {
        return $this->nombre ?? '';
    }

    public function getApellidos()
    {
        return $this->apellidos ?? '';
    }

    public function getEmail()
    {
        return $this->email ?? '';
    }

    public function getTelefono()
    {
        return $this->telefono ?? '';
    }

    // ===== SETTERS =====

    public function setId($id)
    {
        $this->contacto_id = $id;
    }

    public function setClienteId($cliente_id)
    {
        $this->cliente_id = $cliente_id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = trim($nombre);
    }

    public function setApellidos($apellidos)
    {
        $this->apellidos = trim($apellidos);
    }

    public function setEmail($email)
    {
        $this->email = strtolower(trim($email));
    }

    public function setTelefono($telefono)
    {
        $this->telefono = trim($telefono);
    }

    // ===== PERSISTENCIA =====

    /**
     * Inserta o actualiza el contacto según tenga ID
     */
    public function guardar($pdo)
    {
        try {
            if (empty($this->contacto_id)) {
                $stmt = $pdo->prepare(
                    "INSERT INTO contactos (cliente_id, nombre, apellidos, email, telefono)
                     VALUES (:cliente_id, :nombre, :apellidos, :email, :telefono)"
                );

                $stmt->execute([
                    ':cliente_id' => $this->cliente_id,
                    ':nombre'     => $this->nombre,
                    ':apellidos'  => $this->apellidos,
                    ':email'      => $this->email,
                    ':telefono'   => $this->telefono
                ]);

                $this->contacto_id = $pdo->lastInsertId();
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE contactos SET
                        cliente_id = :cliente_id,
                        nombre = :nombre,
                        apellidos = :apellidos,
                        email = :email,
                        telefono = :telefono
                     WHERE contacto_id = :id"
                );

                $stmt->execute([
                    ':cliente_id' => $this->cliente_id,
                    ':nombre'     => $this->nombre,
                    ':apellidos'  => $this->apellidos,
                    ':email'      => $this->email,
                    ':telefono'   => $this->telefono,
                    ':id'         => $this->contacto_id
                ]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error guardando contacto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Devuelve un contacto por su ID
     */
    public static function obtenerPorId($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM contactos WHERE contacto_id = :id");
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    /**
     * Devuelve todos los contactos
     */
    public static function obtenerTodos($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM contactos ORDER BY apellidos, nombre");
        $contactos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contactos[] = new self($row);
        }

        return $contactos;
    }

    /**
     * Devuelve los contactos de un cliente concreto
     */
    public static function obtenerPorCliente($pdo, $cliente_id)
    {
        $stmt = $pdo->prepare(
            "SELECT * FROM contactos WHERE cliente_id = :cliente_id ORDER BY apellidos, nombre"
        );
        $stmt->execute([':cliente_id' => $cliente_id]);

        $contactos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contactos[] = new self($row);
        }

        return $contactos;
    }

    /**
     * Cuenta los contactos asociados a un cliente
     */
    public static function contarPorCliente($pdo, $cliente_id)
    {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS total FROM contactos WHERE cliente_id = :cliente_id"
        );
        $stmt->execute([':cliente_id' => $cliente_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['total'];
    }

    /**
     * Elimina el contacto
     */
    public function eliminar($pdo)
    {
        try {
            if (!empty($this->contacto_id)) {
                $stmt = $pdo->prepare(
                    "DELETE FROM contactos WHERE contacto_id = :id"
                );
                $stmt->execute([':id' => $this->contacto_id]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error eliminando contacto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Devuelve nombre y apellidos concatenados
     */
    public function getNombreCompleto()
    {
        return trim($this->nombre . ' ' . $this->apellidos);
    }
}
