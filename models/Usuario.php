<?php
require_once '../config/database.php';

class Usuario {
    const ROL_ADMIN = 'ADMIN';
    const ROL_ENCARGADO_CAJA_CHICA = 'ENCARGADO_CAJA_CHICA';
    const ROL_SUPERVISOR = 'SUPERVISOR_AUTORIZADOR';
    const ROL_CONTABILIDAD = 'CONTABILIDAD';
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getUsuarioByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nombre AS rol FROM usuarios u JOIN roles r ON u.id_rol = r.id WHERE LOWER(u.email) = LOWER(?)");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsuarioById($id) {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nombre AS rol FROM usuarios u JOIN roles r ON u.id_rol = r.id WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsuarios() {
        $stmt = $this->pdo->query("SELECT u.*, r.nombre AS rol FROM usuarios u JOIN roles r ON u.id_rol = r.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsuariosByRol($rol) {
        error_log("Buscando usuarios con rol: $rol");
        $stmt = $this->pdo->prepare("SELECT u.* FROM usuarios u JOIN roles r ON u.id_rol = r.id WHERE r.nombre = ?");
        $stmt->execute([$rol]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Usuarios encontrados para rol $rol: " . print_r($result, true));
        return $result;
    }

    public function createUsuario($nombre, $email, $password, $id_rol) {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email, password, id_rol) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_BCRYPT), $id_rol]);
    }

    public function updateUsuario($id, $nombre, $email, $password, $id_rol) {
        try {
            if (!empty($password)) {
                $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ?, id_rol = ? WHERE id = ?");
                $result = $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_BCRYPT), $id_rol, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, id_rol = ? WHERE id = ?");
                $result = $stmt->execute([$nombre, $email, $id_rol, $id]);
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
        if ($rol === 'ADMIN') {
            return true;
        }

        switch ($permiso) {
            case 'create_liquidaciones':
            case 'create_detalles':
                return in_array($rol, ['ADMIN', 'ENCARGADO_CAJA_CHICA']);
            case 'autorizar_liquidaciones':
            case 'autorizar_facturas':
                return in_array($rol, ['ADMIN', 'SUPERVISOR_AUTORIZADOR']);
            case 'revisar_liquidaciones':
            case 'revisar_facturas':
                return in_array($rol, ['ADMIN', 'CONTABILIDAD']);
            case 'manage_usuarios':
            case 'manage_impuestos':
            case 'manage_tipos_gastos':
            case 'manage_roles':
            case 'manage_cajachica':
            case 'manage_reportes':
            case 'manage_auditoria':
            case 'manage_accesos':
                return $rol === 'ADMIN';
            case 'manage_cuentas_contables':
                // Permitir a SUPERVISOR_AUTORIZADOR listar cuentas (solo lectura)
                return in_array($rol, ['ADMIN', 'CONTABILIDAD', 'SUPERVISOR_AUTORIZADOR']);
            case 'manage_facturas':
                // Permitir a SUPERVISOR_AUTORIZADOR listar facturas, cuentas y bases (necesario para autorizar)
                return in_array($rol, ['ADMIN', 'ENCARGADO_CAJA_CHICA', 'CONTABILIDAD', 'SUPERVISOR_AUTORIZADOR']);
            default:
                return false;
        }
    }

    public function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles WHERE estado = 'ACTIVO'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}