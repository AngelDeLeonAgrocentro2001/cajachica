<?php
require_once '../config/database.php';

class Usuario {
    private $pdo;

    const ROL_ADMIN = 'ADMIN';
    const ROL_ENCARGADO_CAJA_CHICA = 'ENCARGADO_CAJA_CHICA';
    const ROL_SUPERVISOR = 'SUPERVISOR_AUTORIZADOR';
    const ROL_CONTABILIDAD = 'CONTABILIDAD';

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getUsuarioByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsuarioById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsuarios() {
        $stmt = $this->pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nuevo método para obtener usuarios por rol
    public function getUsuariosByRol($rol) {
        error_log("Buscando usuarios con rol: $rol");
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE rol = ?");
        $stmt->execute([$rol]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Usuarios encontrados para rol $rol: " . print_r($result, true));
        return $result;
    }

    public function createUsuario($nombre, $email, $password, $rol) {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_BCRYPT), $rol]);
    }

    public function updateUsuario($id, $nombre, $email, $password, $rol) {
        try {
            if (!empty($password)) {
                $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id = ?");
                if (!$stmt) {
                    error_log("Error al preparar la consulta de actualización: " . implode(', ', $this->pdo->errorInfo()));
                    return false;
                }
                $result = $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_BCRYPT), $rol, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
                if (!$stmt) {
                    error_log("Error al preparar la consulta de actualización: " . implode(', ', $this->pdo->errorInfo()));
                    return false;
                }
                $result = $stmt->execute([$nombre, $email, $rol, $id]);
            }
            if ($result === false) {
                error_log("Error al ejecutar UPDATE en updateUsuario: " . implode(', ', $stmt->errorInfo()));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error PDO en updateUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUsuario($id) {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function tienePermiso($usuario, $permiso) {
        $rol = $usuario['rol'] ?? '';
        if ($rol === self::ROL_ADMIN) {
            return true; // ADMIN tiene acceso a todo
        }

        switch ($permiso) {
            case 'create_liquidaciones':
            case 'create_detalles':
                return in_array($rol, [self::ROL_ADMIN, self::ROL_ENCARGADO_CAJA_CHICA]);
            case 'autorizar_liquidaciones':
                return in_array($rol, [self::ROL_ADMIN, self::ROL_SUPERVISOR]);
            case 'revisar_liquidaciones':
                return in_array($rol, [self::ROL_ADMIN, self::ROL_CONTABILIDAD]);
            default:
                return false;
        }
    }
}