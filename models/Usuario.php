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
        if (!$usuario || !isset($usuario['rol'])) {
            error_log("Usuario no válido o sin rol: " . print_r($usuario, true));
            return false;
        }
    
        // Permisos predeterminados por rol
        $permisosPorRol = [
            self::ROL_ADMIN => true, // Admin tiene acceso a todo
            self::ROL_ENCARGADO_CAJA_CHICA => [
                'create_liquidaciones' => true,
                'create_detalles' => true,
                'manage_facturas' => true,
                'manage_cajachica' => true, // Agregado para que el encargado pueda gestionar cajas chicas
            ],
            self::ROL_SUPERVISOR => [
                'autorizar_liquidaciones' => true,
                'autorizar_facturas' => true,
                'manage_cuentas_contables' => true,
                'manage_facturas' => true,
                'revisar_liquidaciones' => true, // Agregado
                'revisar_detalles_liquidaciones' => true, // Agregado
                'revisar_facturas' => true, // Agregado
            ],
            self::ROL_CONTABILIDAD => [
                'revisar_liquidaciones' => true,
                'revisar_detalles_liquidaciones' => true,
                'revisar_facturas' => true,
                'manage_reportes' => true,
                'manage_auditoria' => true,
                'manage_cuentas_contables' => true,
                'manage_facturas' => true,
                'manage_centros_costos' => true,
                'manage_impuestos' => true, // Agregado
                'manage_tipos_gastos' => true, // Agregado
            ],
        ];
    
        // Verificar permisos predeterminados
        $rol = $usuario['rol'];
        error_log("Verificando permiso '$permiso' para rol '$rol'");
        if ($rol === self::ROL_ADMIN) {
            error_log("Permiso concedido: Rol ADMIN tiene acceso a todo");
            return true; // Admin siempre tiene permiso
        }
    
        $permisosPredeterminados = $permisosPorRol[$rol] ?? [];
        $tienePermisoPredeterminado = isset($permisosPredeterminados[$permiso]) && $permisosPredeterminados[$permiso];
        error_log("Permiso predeterminado para '$permiso': " . ($tienePermisoPredeterminado ? 'Sí' : 'No'));
    
        // Consultar permisos asignados dinámicamente desde accesos_permisos
        $permisosAsignados = [];
        if (isset($usuario['id'])) {
            $query = "SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'ACTIVO'";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$usuario['id']]);
            $permisosAsignados = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
            error_log("Permisos asignados para usuario " . $usuario['id'] . ": " . print_r($permisosAsignados, true));
        }
    
        // Combinar permisos predeterminados con permisos asignados
        $tienePermiso = $tienePermisoPredeterminado || in_array($permiso, $permisosAsignados);
        error_log("Resultado final de permiso '$permiso': " . ($tienePermiso ? 'Concedido' : 'Denegado'));
        return $tienePermiso;
    }

    public function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles WHERE estado = 'ACTIVO'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}