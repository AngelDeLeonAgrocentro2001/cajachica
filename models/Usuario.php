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

    public function checkCardCode($cardCode) {
        $stmt = $this->pdo->prepare("SELECT CardCode, CardName FROM codigo WHERE LOWER(CardCode) = LOWER(?)");
        $stmt->execute([$cardCode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? ['exists' => true, 'CardCode' => $result['CardCode'], 'CardName' => $result['CardName']] : ['exists' => false];
    }

    public function getUsuarioByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nombre AS rol, cc.nombre AS nombre_caja_chica FROM usuarios u JOIN roles r ON u.id_rol = r.id LEFT JOIN cajas_chicas cc ON u.id_caja_chica = cc.id WHERE LOWER(u.email) = LOWER(?)");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsuarioById($id) {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nombre AS rol, cc.nombre AS nombre_caja_chica FROM usuarios u JOIN roles r ON u.id_rol = r.id LEFT JOIN cajas_chicas cc ON u.id_caja_chica = cc.id WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsuarios() {
        $stmt = $this->pdo->query("SELECT u.*, r.nombre AS rol, cc.nombre AS nombre_caja_chica FROM usuarios u JOIN roles r ON u.id_rol = r.id LEFT JOIN cajas_chicas cc ON u.id_caja_chica = cc.id");
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

    public function getUsuariosBySupervisorRole() {
        error_log("Buscando usuarios con roles de tipo supervisor");
        $stmt = $this->pdo->prepare("
            SELECT u.* 
            FROM usuarios u 
            JOIN roles r ON u.id_rol = r.id 
            WHERE UPPER(r.nombre) LIKE '%SUPERVISOR%' 
            OR UPPER(r.descripcion) LIKE '%SUPERVISOR%'
        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Usuarios encontrados con roles de tipo supervisor: " . print_r($result, true));
        return $result;
    }

    public function getUsuariosByContadorRole() {
        error_log("Buscando usuarios con roles de tipo contador");
        $stmt = $this->pdo->prepare("
            SELECT u.* 
            FROM usuarios u 
            JOIN roles r ON u.id_rol = r.id 
            WHERE UPPER(r.nombre) LIKE '%CONTADOR%' 
            OR UPPER(r.nombre) LIKE '%CONTABILIDAD%' 
            OR UPPER(r.descripcion) LIKE '%CONTADOR%' 
            OR UPPER(r.descripcion) LIKE '%CONTABILIDAD%'
        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Usuarios encontrados con roles de tipo contador: " . print_r($result, true));
        return $result;
    }

    public function createUsuario($nombre, $email, $password, $id_rol, $card_code, $id_caja_chica = null) {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email, password, id_rol, clientes, id_caja_chica) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_BCRYPT), $id_rol, $card_code, $id_caja_chica]);
    }

    public function updateUsuario($id, $nombre, $email, $password, $id_rol, $card_code, $id_caja_chica = null) {
        try {
            if (!empty($password)) {
                $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ?, id_rol = ?, clientes = ?, id_caja_chica = ? WHERE id = ?");
                $result = $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_BCRYPT), $id_rol, $card_code, $id_caja_chica, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, id_rol = ?, clientes = ?, id_caja_chica = ? WHERE id = ?");
                $result = $stmt->execute([$nombre, $email, $id_rol, $card_code, $id_caja_chica, $id]);
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

        $permisosPorRol = [
            self::ROL_ADMIN => true,
            self::ROL_ENCARGADO_CAJA_CHICA => [
                'create_liquidaciones' => true,
                'create_detalles' => true,
                'manage_facturas' => true,
                'manage_cajachica' => true,
                'manage_correcciones' => true,
                'delete_liquidaciones' => true
            ],
            self::ROL_SUPERVISOR => [
                'autorizar_liquidaciones' => true,
                'autorizar_facturas' => true,
                'manage_cuentas_contables' => true,
                'manage_facturas' => true,
                'revisar_liquidaciones' => true,
                'revisar_detalles_liquidaciones' => true,
                'revisar_facturas' => true
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
                'manage_impuestos' => true,
                'manage_tipos_gastos' => true,
            ],
        ];

        $permisosPredeterminados = [];
        $tienePermisoPredeterminado = false;

        $stmt = $this->pdo->prepare("SELECT descripcion FROM roles WHERE id = ?");
        $stmt->execute([$rolId]);
        $rolData = $stmt->fetch(PDO::FETCH_ASSOC);
        $descripcion = $rolData['descripcion'] ?? '';

        $isContabilidadRole = strpos(strtoupper($rol), 'CONTADOR') !== false || 
                             strpos(strtoupper($rol), 'CONTABILIDAD') !== false ||
                             strpos(strtoupper($descripcion), 'CONTADOR') !== false || 
                             strpos(strtoupper($descripcion), 'CONTABILIDAD') !== false;

        if (strpos($rol, self::ROL_ADMIN) !== false) {
            $stmt = $this->pdo->query("SELECT nombre FROM permisos");
            $allPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $permisosPredeterminados = array_fill_keys($allPermissions, true);
            $tienePermisoPredeterminado = isset($permisosPredeterminados[$permiso]) && $permisosPredeterminados[$permiso];
            error_log("Permisos predeterminados para ADMIN: " . print_r($permisosPredeterminados, true));
        } else {
            $combinedPredeterminados = [];
            if (strpos($rol, self::ROL_ENCARGADO_CAJA_CHICA) !== false) {
                $combinedPredeterminados = array_merge($combinedPredeterminados, $permisosPorRol[self::ROL_ENCARGADO_CAJA_CHICA]);
            }
            if (strpos($rol, self::ROL_SUPERVISOR) !== false || 
                strpos(strtoupper($descripcion), 'SUPERVISOR') !== false) {
                $combinedPredeterminados = array_merge($combinedPredeterminados, $permisosPorRol[self::ROL_SUPERVISOR]);
            }
            if ($isContabilidadRole) {
                $combinedPredeterminados = array_merge($combinedPredeterminados, $permisosPorRol[self::ROL_CONTABILIDAD]);
            }
            $permisosPredeterminados = $combinedPredeterminados;
            $tienePermisoPredeterminado = isset($permisosPredeterminados[$permiso]) && $permisosPredeterminados[$permiso];
            error_log("Permisos predeterminados para rol $rol: " . print_r($permisosPredeterminados, true));
        }

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
        if ($isContabilidadRole) {
            $permisosDinamicos = array_merge($permisosDinamicos, array_keys($permisosPorRol[self::ROL_CONTABILIDAD]));
        }
        $permisosDinamicos = array_unique($permisosDinamicos);
        $tienePermisoDinamico = in_array($permiso, $permisosDinamicos);
        error_log("Permisos dinámicos: " . print_r($permisosDinamicos, true));

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
        error_log("Permisos manuales del rol: " . print_r($manualRolPermissions, true));
        error_log("Overrides del rol: " . print_r($manualOverrides, true));

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
        error_log("Permisos combinados del rol: " . print_r($rolPermissions, true));

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
        error_log("Permisos específicos del usuario: " . print_r($userPermissions, true));
        error_log("Overrides del usuario: " . print_r($userOverrides, true));

        $effectivePermissions = array_unique(array_merge($rolPermissions, $userPermissions));
        foreach ($userOverrides as $perm => $estado) {
            if ($estado === 'INACTIVO' && in_array($perm, $effectivePermissions)) {
                $effectivePermissions = array_diff($effectivePermissions, [$perm]);
            }
        }
        $tienePermiso = in_array($permiso, $effectivePermissions);
        error_log("Permisos efectivos finales: " . print_r($effectivePermissions, true));
        error_log("Resultado final para permiso $permiso: " . ($tienePermiso ? 'true' : 'false'));

        return $tienePermiso;
    }

    public function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles WHERE estado = 'ACTIVO'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}