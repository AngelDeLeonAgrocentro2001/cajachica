<?php
require_once '../models/Role.php';
require_once '../models/Usuario.php';
require_once '../models/Auditoria.php';

class RoleController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listRoles() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar roles']);
            exit;
        }

        $rolModel = new Role();
        $roles = $rolModel->getAllRoles();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($roles);
        } else {
            require '../views/roles/list.html';
        }
        exit;
    }

    private function getAvailablePermissions() {
        return [
            'create_liquidaciones' => 'Gestión de Liquidaciones',
            'create_detalles' => 'Gestión de Detalles de Liquidaciones',
            'manage_cajachica' => 'Gestión de Cajas Chicas',
            'manage_impuestos' => 'Gestión de Impuestos',
            'manage_cuentas_contables' => 'Gestión de Cuentas Contables',
            'manage_tipos_gastos' => 'Gestión de Tipos de Gastos',
            'manage_roles' => 'Gestión de Roles',
            'manage_usuarios' => 'Gestión de Usuarios',
            'autorizar_liquidaciones' => 'Autorizar Liquidaciones',
            'revisar_liquidaciones' => 'Revisar Liquidaciones',
            'revisar_detalles_liquidaciones' => 'Revisar Detalles de Liquidaciones',
            'manage_reportes' => 'Generar Reportes',
            'manage_auditoria' => 'Consultar Auditoría',
            'manage_accesos' => 'Administración de Accesos',
            'manage_facturas' => 'Gestión de Facturas',
            'autorizar_facturas' => 'Autorizar Facturas',
            'revisar_facturas' => 'Revisar Facturas',
            'manage_centros_costos' => 'Gestión de Centros de Costos',
            'manage_correcciones' => 'Corrección de Liquidaciones'
        ];
    }

    private function getDefaultPermissions($rolNombre) {
        $defaultPermissions = [
            'ADMIN' => array_keys($this->getAvailablePermissions()),
            'ENCARGADO_CAJA_CHICA' => [
                'create_liquidaciones',
                'create_detalles',
                'manage_facturas',
                'manage_cajachica',
                'manage_correcciones',
                'delete_liquidaciones'
            ],
            'SUPERVISOR_AUTORIZADOR' => [
                'autorizar_liquidaciones',
                'autorizar_facturas',
                'manage_cuentas_contables',
                'manage_facturas',
                'revisar_liquidaciones',
                'revisar_detalles_liquidaciones',
                'revisar_facturas'
            ],
            'CONTABILIDAD' => [
                'revisar_liquidaciones',
                'revisar_detalles_liquidaciones',
                'revisar_facturas',
                'manage_reportes',
                'manage_auditoria',
                'manage_cuentas_contables',
                'manage_facturas',
                'manage_centros_costos',
                'manage_impuestos',
                'manage_tipos_gastos'
            ],
        ];
    
        $combinedPermissions = [];
        $rolNombreUpper = strtoupper($rolNombre);
        
        // Para roles mixtos, combinar permisos de todos los roles detectados
        foreach ($defaultPermissions as $rol => $permissions) {
            if (strpos($rolNombreUpper, $rol) !== false ||
                ($rol === 'CONTABILIDAD' && (
                    strpos($rolNombreUpper, 'CONTADOR') !== false ||
                    strpos($rolNombreUpper, 'CONTABILIDAD') !== false
                ))) {
                $combinedPermissions = array_merge($combinedPermissions, $permissions);
            }
        }
        
        return array_unique($combinedPermissions);
    }

    private function getDynamicPermissions($descripcion) {
        $roleMapping = [
            'admin' => 'ADMIN',
            'encargado' => 'ENCARGADO_CAJA_CHICA',
            'supervisor' => 'SUPERVISOR_AUTORIZADOR',
            'contador' => 'CONTABILIDAD',
            'contabilidad' => 'CONTABILIDAD',
        ];
    
        $permisosPorRol = [
            'ADMIN' => true,
            'ENCARGADO_CAJA_CHICA' => [
                'create_liquidaciones' => true,
                'create_detalles' => true,
                'manage_facturas' => true,
                'manage_cajachica' => true,
                'manage_correcciones' => true,
                'delete_liquidaciones' => true
            ],
            'SUPERVISOR_AUTORIZADOR' => [
                'autorizar_liquidaciones' => true,
                'autorizar_facturas' => true,
                'manage_cuentas_contables' => true,
                'manage_facturas' => true,
                'revisar_liquidaciones' => true,
                'revisar_detalles_liquidaciones' => true,
                'revisar_facturas' => true,
            ],
            'CONTABILIDAD' => [
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
    
        $descripcionLower = strtolower($descripcion);
        $detectedRoles = [];
    
        foreach ($roleMapping as $keyword => $role) {
            if (strpos($descripcionLower, $keyword) !== false) {
                $detectedRoles[] = $role;
            }
        }
    
        $combinedPermissions = [];
        foreach ($detectedRoles as $role) {
            if ($role === 'ADMIN') {
                $stmt = $this->pdo->query("SELECT nombre FROM permisos");
                $allPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $combinedPermissions = array_merge($combinedPermissions, $allPermissions);
            } else {
                $permissions = $permisosPorRol[$role] ?? [];
                if (is_array($permissions)) {
                    $combinedPermissions = array_merge($combinedPermissions, array_keys(array_filter($permissions)));
                }
            }
        }
    
        return array_unique($combinedPermissions);
    }

    private function assignPermissionsBasedOnDescription($rolId, $descripcion) {
        $usuarioModel = new Usuario();
        $auditoriaModel = new Auditoria();
    
        $rol = (new Role())->getRolById($rolId);
        $nombreRol = $rol['nombre'] ?? '';
    
        // Combinar permisos predeterminados y dinámicos
        $defaultPermissions = $this->getDefaultPermissions($nombreRol);
        $dynamicPermissions = $this->getDynamicPermissions($descripcion);
        $combinedPermissions = array_unique(array_merge($defaultPermissions, $dynamicPermissions));
    
        $usuarios = $usuarioModel->getUsuariosByRolId($rolId);
    
        foreach ($usuarios as $usuario) {
            // Limpiar permisos anteriores
            $this->pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND origen = ?")
                ->execute([$usuario['id'], 'ROL_DESCRIPCION']);
            error_log("Permisos dinámicos eliminados para usuario {$usuario['id']} antes de reasignar.");
    
            // Asignar permisos combinados
            foreach ($combinedPermissions as $permiso) {
                $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', 'ROL_DESCRIPCION') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                $stmt->execute([$usuario['id'], $permiso]);
            }
            $auditoriaModel->createAuditoria(null, null, $usuario['id'], 'ASIGNAR_PERMISOS', "Permisos asignados a usuario {$usuario['email']} desde descripción del rol ID $rolId: " . implode(', ', $combinedPermissions));
            error_log("Permisos dinámicos asignados a usuario {$usuario['id']}: " . implode(', ', $combinedPermissions));
        }
    }

    private function checkRoleNameExists($nombre, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM roles WHERE nombre = ?";
        if ($excludeId) {
            $query .= " AND id != ?";
        }
        $stmt = $this->pdo->prepare($query);
        if ($excludeId) {
            $stmt->execute([$nombre, $excludeId]);
        } else {
            $stmt->execute([$nombre]);
        }
        return $stmt->fetchColumn() > 0;
    }

    public function createRol() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en createRol');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $auditoriaModel = new Auditoria();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            error_log('Error: No tienes permiso para crear roles');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear roles']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';
    
                error_log("Datos recibidos para crear rol: nombre=$nombre, descripcion=$descripcion, estado=$estado");
    
                if (empty($nombre)) {
                    throw new Exception('El nombre del rol es obligatorio');
                }
    
                if ($this->checkRoleNameExists($nombre)) {
                    throw new Exception("El nombre del rol '$nombre' ya está en uso. Por favor, elige otro nombre.");
                }
    
                $rolModel = new Role();
                $result = $rolModel->createRol($nombre, $descripcion, $estado);
                if ($result === false) {
                    throw new Exception('Error al crear rol en la base de datos');
                }
    
                $rolId = $this->pdo->lastInsertId();
    
                // Combinar permisos predeterminados y dinámicos
                $defaultPermissions = $this->getDefaultPermissions($nombre);
                $dynamicPermissions = $this->getDynamicPermissions($descripcion);
                $combinedPermissions = array_unique(array_merge($defaultPermissions, $dynamicPermissions));
                error_log("Permisos combinados para el nuevo rol ID $rolId: " . print_r($combinedPermissions, true));
    
                // Asignar permisos al rol en rol_permisos
                $this->pdo->beginTransaction();
                $availablePermissions = $this->getAvailablePermissions();
                foreach ($availablePermissions as $permiso => $nombrePermiso) {
                    $estado = in_array($permiso, $combinedPermissions) ? 'ACTIVO' : 'INACTIVO';
                    $stmt = $this->pdo->prepare("INSERT INTO rol_permisos (id_rol, permiso, estado) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE estado = ?");
                    $stmt->execute([$rolId, $permiso, $estado, $estado]);
                    error_log("Permiso $permiso para rol ID $rolId establecido como $estado");
                }
    
                // Asignar permisos a los usuarios asociados (si los hay)
                $usuarios = $usuarioModel->getUsuariosByRolId($rolId);
                foreach ($usuarios as $usuario) {
                    // Limpiar permisos anteriores del rol
                    $this->pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND (origen = 'ROL_MANUAL' OR origen IS NULL)")
                        ->execute([$usuario['id']]);
                    error_log("Eliminados permisos con origen ROL_MANUAL o NULL para el usuario {$usuario['id']}");
    
                    // Asignar permisos combinados
                    foreach ($combinedPermissions as $permiso) {
                        $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', 'ROL_MANUAL') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                        $stmt->execute([$usuario['id'], $permiso]);
                        error_log("Asignado permiso $permiso al usuario {$usuario['id']} con origen ROL_MANUAL");
                    }
                    $auditoriaModel->createAuditoria(null, null, $usuario['id'], 'ASIGNAR_PERMISOS', "Permisos asignados a usuario {$usuario['email']} desde rol ID $rolId: " . implode(', ', $combinedPermissions));
                }
    
                $this->pdo->commit();
    
                $auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'CREAR_ROL', "Rol creado: {$nombre}");
    
                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode(['message' => 'Rol creado']);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error en createRol: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        ob_start();
        require '../views/roles/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function updateRol($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateRol');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $auditoriaModel = new Auditoria();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            error_log('Error: No tienes permiso para actualizar roles');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar roles']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';
    
                error_log("Datos recibidos para actualizar rol ID $id: nombre=$nombre, descripcion=$descripcion, estado=$estado");
    
                if (empty($nombre)) {
                    throw new Exception('El nombre del rol es obligatorio');
                }
    
                if ($this->checkRoleNameExists($nombre, $id)) {
                    throw new Exception("El nombre del rol '$nombre' ya está en uso por otro rol. Por favor, elige otro nombre.");
                }
    
                $rolModel = new Role();
                $result = $rolModel->updateRol($id, $nombre, $descripcion, $estado);
                if ($result === false) {
                    throw new Exception('Error al actualizar rol en la base de datos');
                }
    
                // Combinar permisos predeterminados y dinámicos
                $defaultPermissions = $this->getDefaultPermissions($nombre);
                $dynamicPermissions = $this->getDynamicPermissions($descripcion);
                $combinedPermissions = array_unique(array_merge($defaultPermissions, $dynamicPermissions));
                error_log("Nuevos permisos predeterminados y dinámicos para el rol ID $id: " . print_r($combinedPermissions, true));
    
                // Iniciar una transacción para actualizar permisos
                $this->pdo->beginTransaction();
    
                // Eliminar todos los permisos manuales existentes en rol_permisos
                $stmt = $this->pdo->prepare("DELETE FROM rol_permisos WHERE id_rol = ?");
                $stmt->execute([$id]);
                error_log("Eliminados todos los permisos manuales existentes para el rol ID $id");
    
                // Asignar los nuevos permisos combinados en rol_permisos
                $availablePermissions = $this->getAvailablePermissions();
                foreach ($availablePermissions as $permiso => $nombrePermiso) {
                    $estado = in_array($permiso, $combinedPermissions) ? 'ACTIVO' : 'INACTIVO';
                    $stmt = $this->pdo->prepare("INSERT INTO rol_permisos (id_rol, permiso, estado) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE estado = ?");
                    $stmt->execute([$id, $permiso, $estado, $estado]);
                    error_log("Permiso $permiso para rol ID $id establecido como $estado");
                }
    
                // Actualizar permisos de los usuarios asociados al rol
                $usuarios = $usuarioModel->getUsuariosByRolId($id);
                foreach ($usuarios as $usuario) {
                    // Obtener permisos individuales del usuario (origen 'MANUAL')
                    $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'ACTIVO' AND origen = 'MANUAL'");
                    $stmt->execute([$usuario['id']]);
                    $individualPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
                    // Obtener todos los permisos actuales del usuario
                    $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'ACTIVO'");
                    $stmt->execute([$usuario['id']]);
                    $currentUserPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
                    // Determinar permisos que deben ser desactivados
                    $permissionsToDeactivate = array_diff($currentUserPermissions, $combinedPermissions, $individualPermissions);
                    foreach ($permissionsToDeactivate as $permiso) {
                        $stmt = $this->pdo->prepare("UPDATE accesos_permisos SET estado = 'INACTIVO' WHERE id_usuario = ? AND permiso = ? AND estado = 'ACTIVO'");
                        $stmt->execute([$usuario['id'], $permiso]);
                        error_log("Desactivado permiso $permiso para el usuario {$usuario['id']} (ya no está en el rol)");
                    }
    
                    // Eliminar permisos anteriores del rol
                    $stmt = $this->pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND (origen = 'ROL_MANUAL' OR origen IS NULL)");
                    $stmt->execute([$usuario['id']]);
                    error_log("Eliminados permisos con origen ROL_MANUAL o NULL para el usuario {$usuario['id']}");
    
                    // Combinar permisos del rol con permisos individuales
                    $userPermissions = array_unique(array_merge($combinedPermissions, $individualPermissions));
                    foreach ($userPermissions as $permiso) {
                        $origen = in_array($permiso, $individualPermissions) ? 'MANUAL' : 'ROL_MANUAL';
                        $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', ?) ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                        $stmt->execute([$usuario['id'], $permiso, $origen]);
                        error_log("Asignado permiso $permiso al usuario {$usuario['id']} con origen $origen");
                    }
    
                    $auditoriaModel->createAuditoria(null, null, $usuario['id'], 'ASIGNAR_PERMISOS', "Permisos actualizados para usuario {$usuario['email']} desde rol ID $id: " . implode(', ', $userPermissions));
                }
    
                $this->pdo->commit();
    
                $auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'ACTUALIZAR_ROL', "Rol actualizado: {$nombre}");
    
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Rol actualizado']);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error en updateRol: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $rolModel = new Role();
        $data = $rolModel->getRolById($id);
        if ($data === false) {
            error_log("Error: No se pudo obtener el rol con ID $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Rol no encontrado']);
            exit;
        }
    
        ob_start();
        require '../views/roles/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function deleteRol($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $auditoriaModel = new Auditoria();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar roles']);
            exit;
        }

        $rolModel = new Role();
        $rol = $rolModel->getRolById($id);

        $usuarios = $usuarioModel->getUsuariosByRolId($id);
        if (!empty($usuarios)) {
            $usuarioEmails = array_map(function($u) { return $u['email']; }, $usuarios);
            $errorMessage = "Error al eliminar rol. Está en uso por los siguientes usuarios: " . implode(', ', $usuarioEmails) . ". Reasigna o elimina estos usuarios antes de eliminar el rol.";
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $errorMessage]);
            exit;
        }

        if ($rolModel->deleteRol($id)) {
            foreach ($usuarios as $usuario) {
                $this->pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND origen = ?")
                    ->execute([$usuario['id'], 'ROL_DESCRIPCION']);
                $auditoriaModel->createAuditoria(null, null, $usuario['id'], 'ELIMINAR_PERMISOS', "Permisos eliminados para usuario {$usuario['email']} al eliminar rol ID $id");
            }

            $auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'ELIMINAR_ROL', "Rol eliminado: {$rol['nombre']}");

            header('Content-Type: application/json');
            echo json_encode(['message' => 'Rol eliminado']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar rol.']);
        }
        exit;
    }

    public function managePermissions($rolId) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $rolModel = new Role();
        $auditoriaModel = new Auditoria();
    
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar roles']);
            exit;
        }
    
        $rol = $rolModel->getRolById($rolId);
        if (!$rol) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Rol no encontrado']);
            exit;
        }
    
        $defaultPermissions = $this->getDefaultPermissions($rol['nombre']);
        $dynamicPermissions = $this->getDynamicPermissions($rol['descripcion'] ?? '');
    
        // Obtener permisos manuales actuales del rol
        $stmt = $this->pdo->prepare("SELECT permiso FROM rol_permisos WHERE id_rol = ? AND estado = 'ACTIVO'");
        $stmt->execute([$rolId]);
        $currentManualPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
        $effectivePermissions = array_unique(array_merge($defaultPermissions, $dynamicPermissions, $currentManualPermissions));
        $stmt = $this->pdo->prepare("SELECT permiso, estado FROM rol_permisos WHERE id_rol = ?");
        $stmt->execute([$rolId]);
        $currentManualOverrides = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $perm) {
            $currentManualOverrides[$perm['permiso']] = $perm['estado'];
        }
        foreach ($currentManualOverrides as $permiso => $estado) {
            if ($estado === 'INACTIVO' && in_array($permiso, $effectivePermissions)) {
                $effectivePermissions = array_diff($effectivePermissions, [$permiso]);
            }
        }
        error_log("Permisos efectivos actuales para el rol $rolId: " . print_r($effectivePermissions, true));
    
        $availablePermissions = $this->getAvailablePermissions();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Solicitud POST recibida. Datos POST: " . print_r($_POST, true));
            $selectedPermissions = isset($_POST['permissions']) && is_array($_POST['permissions']) ? array_map('trim', $_POST['permissions']) : [];
            error_log("Permisos seleccionados para guardar: " . print_r($selectedPermissions, true));
    
            $this->pdo->beginTransaction();
    
            try {
                // Actualizar permisos en rol_permisos
                foreach ($availablePermissions as $permiso => $nombre) {
                    $estado = in_array($permiso, $selectedPermissions) ? 'ACTIVO' : 'INACTIVO';
                    $stmt = $this->pdo->prepare("INSERT INTO rol_permisos (id_rol, permiso, estado) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE estado = ?");
                    $stmt->execute([$rolId, $permiso, $estado, $estado]);
                    error_log("Permiso $permiso para rol $rolId actualizado a estado $estado");
                }
    
                // Actualizar permisos de los usuarios asociados al rol
                $stmt = $this->pdo->prepare("SELECT permiso FROM rol_permisos WHERE id_rol = ? AND estado = 'ACTIVO'");
                $stmt->execute([$rolId]);
                $updatedManualPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
                $updatedEffectivePermissions = array_unique(array_merge($defaultPermissions, $dynamicPermissions, $updatedManualPermissions));
                $stmt = $this->pdo->prepare("SELECT permiso, estado FROM rol_permisos WHERE id_rol = ?");
                $stmt->execute([$rolId]);
                $updatedManualOverrides = [];
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $perm) {
                    $updatedManualOverrides[$perm['permiso']] = $perm['estado'];
                }
                foreach ($updatedManualOverrides as $permiso => $estado) {
                    if ($estado === 'INACTIVO' && in_array($permiso, $updatedEffectivePermissions)) {
                        $updatedEffectivePermissions = array_diff($updatedEffectivePermissions, [$permiso]);
                    }
                }
                error_log("Permisos efectivos actualizados para el rol $rolId: " . print_r($updatedEffectivePermissions, true));
    
                $usuarios = $usuarioModel->getUsuariosByRolId($rolId);
                foreach ($usuarios as $usuario) {
                    // Obtener permisos individuales del usuario (origen 'MANUAL')
                    $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'ACTIVO' AND origen = 'MANUAL'");
                    $stmt->execute([$usuario['id']]);
                    $individualPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
                    // Obtener permisos individuales desactivados (origen 'MANUAL', estado 'INACTIVO')
                    $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'INACTIVO' AND origen = 'MANUAL'");
                    $stmt->execute([$usuario['id']]);
                    $deactivatedIndividualPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
                    // Obtener todos los permisos actuales del usuario
                    $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'ACTIVO'");
                    $stmt->execute([$usuario['id']]);
                    $currentUserPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
                    // Determinar permisos que deben ser desactivados (los que ya no están en el rol y no son individuales)
                    $permissionsToDeactivate = array_diff($currentUserPermissions, $updatedEffectivePermissions, $individualPermissions);
                    foreach ($permissionsToDeactivate as $permiso) {
                        if (!in_array($permiso, $deactivatedIndividualPermissions)) {
                            $stmt = $this->pdo->prepare("UPDATE accesos_permisos SET estado = 'INACTIVO' WHERE id_usuario = ? AND permiso = ? AND estado = 'ACTIVO'");
                            $stmt->execute([$usuario['id'], $permiso]);
                            error_log("Desactivado permiso $permiso para el usuario {$usuario['id']} (ya no está en el rol)");
                        }
                    }
    
                    // Eliminar permisos anteriores del rol (origen 'ROL_MANUAL' o NULL)
                    $stmt = $this->pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND (origen = 'ROL_MANUAL' OR origen IS NULL)");
                    $stmt->execute([$usuario['id']]);
                    error_log("Eliminados permisos con origen ROL_MANUAL o NULL para el usuario {$usuario['id']}");
    
                    // Combinar permisos del rol con permisos individuales
                    $userPermissions = array_unique(array_merge($updatedEffectivePermissions, $individualPermissions));
                    foreach ($userPermissions as $permiso) {
                        if (in_array($permiso, $deactivatedIndividualPermissions)) {
                            continue; // No reactivar permisos que el usuario ha desactivado individualmente
                        }
                        $origen = in_array($permiso, $individualPermissions) ? 'MANUAL' : 'ROL_MANUAL';
                        $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', ?) ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                        $stmt->execute([$usuario['id'], $permiso, $origen]);
                        error_log("Asignado permiso $permiso al usuario {$usuario['id']} con origen $origen");
                    }
    
                    $auditoriaModel->createAuditoria(null, null, $usuario['id'], 'ASIGNAR_PERMISOS', "Permisos actualizados para usuario {$usuario['email']} desde rol ID $rolId: " . implode(', ', $userPermissions));
                }
    
                $this->pdo->commit();
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Permisos asignados correctamente']);
                } else {
                    header('Location: index.php?controller=rol&action=managePermissions&rol_id=' . $rolId . '&success=' . urlencode('Permisos asignados correctamente'));
                }
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log("Error al guardar permisos: " . $e->getMessage());
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al guardar permisos']);
                } else {
                    header('Location: index.php?controller=rol&action=managePermissions&rol_id=' . $rolId . '&error=' . urlencode('Error al guardar permisos'));
                }
            }
            exit;
        }
    
        $success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
        $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
        $targetRol = $rol;
    
        require '../views/roles/manage_permissions.html';
        exit;
    }

    public function addPermissionToRol() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $auditoriaModel = new Auditoria();
        $rolModel = new Role();
    
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_roles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar permisos de roles']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
    
        $rolId = $_POST['rol_id'] ?? null;
        $permiso = $_POST['permiso'] ?? null;
    
        if (!$rolId || !$permiso) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros: rol_id y permiso son requeridos']);
            exit;
        }
    
        $targetRol = $rolModel->getRolById($rolId);
        if (!$targetRol) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Rol no encontrado']);
            exit;
        }
    
        $defaultPermissions = $this->getDefaultPermissions($targetRol['nombre']);
        $dynamicPermissions = $this->getDynamicPermissions($targetRol['descripcion'] ?? '');
    
        // Verificar si el permiso ya está en los predeterminados o dinámicos
        if (in_array($permiso, $defaultPermissions) || in_array($permiso, $dynamicPermissions)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El permiso ya está asignado como predeterminado o dinámico']);
            exit;
        }
    
        $this->pdo->beginTransaction();
    
        try {
            // Asignar el permiso al rol
            $stmt = $this->pdo->prepare("INSERT INTO rol_permisos (id_rol, permiso, estado) VALUES (?, ?, 'ACTIVO') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
            $stmt->execute([$rolId, $permiso]);
            error_log("Asignado permiso $permiso al rol $rolId");
    
            // Actualizar permisos de los usuarios asociados al rol
            $usuarios = $usuarioModel->getUsuariosByRolId($rolId);
            foreach ($usuarios as $usuario) {
                $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', 'ROL_MANUAL') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                $stmt->execute([$usuario['id'], $permiso]);
                $auditoriaModel->createAuditoria(null, null, $usuario['id'], 'ASIGNAR_PERMISOS', "Permiso $permiso asignado a usuario {$usuario['email']} desde rol ID $rolId");
            }
    
            $this->pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Permiso asignado al rol correctamente']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al asignar permiso al rol: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al asignar permiso al rol']);
        }
        exit;
    }
}