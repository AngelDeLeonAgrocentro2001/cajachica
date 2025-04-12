<?php
require_once '../config/database.php';

class Usuario {
    const ROL_ADMIN = 'ADMIN';
    const ROL_ENCARGADO_CAJA_CHICA = 'ENCARGADO_CAJA_CHICA';
    const ROL_SUPERVISOR = 'SUPERVISOR';
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

    public function getUsuariosByRolId($rolId) {
        error_log("Buscando usuarios con id_rol: $rolId");
        $stmt = $this->pdo->prepare("SELECT u.* FROM usuarios u WHERE u.id_rol = ?");
        $stmt->execute([$rolId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Usuarios encontrados para id_rol $rolId: " . print_r($result, true));
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
        if (!$usuario || !isset($usuario['rol']) || !isset($usuario['id_rol'])) {
            error_log("Usuario no válido o sin rol: " . print_r($usuario, true));
            return false;
        }
    
        $rol = $usuario['rol'];
        $rolId = $usuario['id_rol'];
        $usuarioId = $usuario['id'];
        error_log("Verificando permiso '$permiso' para usuario ID $usuarioId con rol '$rol' (id_rol: $rolId)");
    
        // 1. Obtener permisos predeterminados según el rol
        $permisosPorRol = [
            self::ROL_ADMIN => true,
            self::ROL_ENCARGADO_CAJA_CHICA => [
                'create_liquidaciones' => true,
                'create_detalles' => true,
                'manage_facturas' => true,
                'manage_cajachica' => true,
            ],
            self::ROL_SUPERVISOR => [
                'autorizar_liquidaciones' => true,
                'autorizar_facturas' => true,
                'manage_cuentas_contables' => true,
                'manage_facturas' => true,
                // 'revisar_liquidaciones' => true,
                // 'revisar_detalles_liquidaciones' => true,
                'revisar_facturas' => true,
            ],
            self::ROL_CONTABILIDAD => [
                'autorizar_liquidaciones' => true, // Agregado
                'revisar_liquidaciones' => true,
                'revisar_detalles_liquidaciones' => true,
                'revisar_facturas' => true,
                'manage_reportes' => true,
                'manage_auditoria' => true,
                'manage_cuentas_contables' => true,
                'manage_facturas' => true,
                'manage_centros_costos' => true,
                'manage_impuestos' => true,
                'manage_tipos_gastos' => true,
            ],
        ];
    
        $permisosPredeterminados = [];
        $tienePermisoPredeterminado = false;
    
        if (strpos($rol, self::ROL_ADMIN) !== false) {
            $stmt = $this->pdo->query("SELECT nombre FROM permisos");
            $allPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $permisosPredeterminados = array_fill_keys($allPermissions, true);
            $tienePermisoPredeterminado = isset($permisosPredeterminados[$permiso]) && $permisosPredeterminados[$permiso];
            error_log("Permiso predeterminado para '$permiso' (ADMIN): " . ($tienePermisoPredeterminado ? 'Sí' : 'No'));
            error_log("Todos los permisos predeterminados para ADMIN: " . print_r($allPermissions, true));
        } else {
            $combinedPredeterminados = [];
            if (strpos($rol, self::ROL_ENCARGADO_CAJA_CHICA) !== false) {
                $combinedPredeterminados = array_merge($combinedPredeterminados, $permisosPorRol[self::ROL_ENCARGADO_CAJA_CHICA]);
            }
            if (strpos($rol, self::ROL_SUPERVISOR) !== false) {
                $combinedPredeterminados = array_merge($combinedPredeterminados, $permisosPorRol[self::ROL_SUPERVISOR]);
            }
            if (strpos($rol, self::ROL_CONTABILIDAD) !== false) {
                $combinedPredeterminados = array_merge($combinedPredeterminados, $permisosPorRol[self::ROL_CONTABILIDAD]);
            }
            $permisosPredeterminados = $combinedPredeterminados;
            $tienePermisoPredeterminado = isset($permisosPredeterminados[$permiso]) && $permisosPredeterminados[$permiso];
            error_log("Permiso predeterminado para '$permiso': " . ($tienePermisoPredeterminado ? 'Sí' : 'No'));
            error_log("Permisos predeterminados combinados: " . print_r($combinedPredeterminados, true));
        }
    
        // 2. Obtener permisos dinámicos del rol (basados en la descripción)
        $stmt = $this->pdo->prepare("SELECT descripcion FROM roles WHERE id = ?");
        $stmt->execute([$rolId]);
        $rolData = $stmt->fetch(PDO::FETCH_ASSOC);
        $descripcion = $rolData['descripcion'] ?? '';
        error_log("Descripción del rol $rolId: $descripcion");
    
        $permisosDinamicos = [];
        if (strpos(strtoupper($descripcion), self::ROL_ADMIN) !== false) {
            $stmt = $this->pdo->query("SELECT nombre FROM permisos");
            $allPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $permisosDinamicos = $allPermissions;
        }
        if (strpos(strtoupper($descripcion), self::ROL_ENCARGADO_CAJA_CHICA) !== false) {
            $permisosDinamicos = array_merge($permisosDinamicos, array_keys($permisosPorRol[self::ROL_ENCARGADO_CAJA_CHICA]));
        }
        if (strpos(strtoupper($descripcion), self::ROL_SUPERVISOR) !== false) {
            $permisosDinamicos = array_merge($permisosDinamicos, array_keys($permisosPorRol[self::ROL_SUPERVISOR]));
        }
        if (strpos(strtoupper($descripcion), self::ROL_CONTABILIDAD) !== false) {
            $permisosDinamicos = array_merge($permisosDinamicos, array_keys($permisosPorRol[self::ROL_CONTABILIDAD]));
        }
        $permisosDinamicos = array_unique($permisosDinamicos);
        $tienePermisoDinamico = in_array($permiso, $permisosDinamicos);
        error_log("Permiso dinámico para '$permiso': " . ($tienePermisoDinamico ? 'Sí' : 'No'));
        error_log("Permisos dinámicos: " . print_r($permisosDinamicos, true));
    
        // 3. Obtener permisos manuales del rol desde rol_permisos
        $stmt = $this->pdo->prepare("SELECT permiso, estado FROM rol_permisos WHERE id_rol = ?");
        $stmt->execute([$rolId]);
        $manualRolPermissionsData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $manualRolPermissions = [];
        $manualOverrides = [];
        foreach ($manualRolPermissionsData as $perm) {
            if ($perm['estado'] === 'ACTIVO') {
                $manualRolPermissions[] = $perm['permiso'];
            }
            $manualOverrides[$perm['permiso']] = $perm['estado'];
        }
        error_log("Permisos manuales del rol $rolId: " . print_r($manualRolPermissions, true));
        error_log("Sobrescrituras manuales del rol $rolId: " . print_r($manualOverrides, true));
    
        // 4. Combinar permisos efectivos del rol
        $rolPermissions = array_unique(array_merge(
            $permisosPredeterminados ? array_keys($permisosPredeterminados) : [],
            $permisosDinamicos,
            $manualRolPermissions
        ));
        foreach ($manualOverrides as $perm => $estado) {
            if ($estado === 'INACTIVO' && in_array($perm, $rolPermissions)) {
                $rolPermissions = array_diff($rolPermissions, [$perm]);
            }
        }
        $tienePermisoRol = in_array($permiso, $rolPermissions);
        error_log("Permiso efectivo del rol para '$permiso': " . ($tienePermisoRol ? 'Sí' : 'No'));
        error_log("Permisos efectivos del rol: " . print_r($rolPermissions, true));
    
        // 5. Obtener permisos individuales del usuario desde accesos_permisos
        $stmt = $this->pdo->prepare("SELECT permiso, estado FROM accesos_permisos WHERE id_usuario = ?");
        $stmt->execute([$usuarioId]);
        $userPermissionsData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $userPermissions = [];
        $userOverrides = [];
        foreach ($userPermissionsData as $perm) {
            if ($perm['estado'] === 'ACTIVO') {
                $userPermissions[] = $perm['permiso'];
            }
            $userOverrides[$perm['permiso']] = $perm['estado'];
        }
        error_log("Permisos individuales del usuario $usuarioId: " . print_r($userPermissions, true));
        error_log("Sobrescrituras individuales del usuario $usuarioId: " . print_r($userOverrides, true));
    
        // 6. Combinar permisos efectivos finales
        $effectivePermissions = array_unique(array_merge($rolPermissions, $userPermissions));
        foreach ($userOverrides as $perm => $estado) {
            if ($estado === 'INACTIVO' && in_array($perm, $effectivePermissions)) {
                $effectivePermissions = array_diff($effectivePermissions, [$perm]);
            }
        }
        $tienePermiso = in_array($permiso, $effectivePermissions);
        error_log("Permisos efectivos finales para usuario $usuarioId: " . print_r($effectivePermissions, true));
        error_log("Resultado final de permiso '$permiso': " . ($tienePermiso ? 'Concedido' : 'Denegado'));
    
        return $tienePermiso;
    }

    public function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles WHERE estado = 'ACTIVO'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}