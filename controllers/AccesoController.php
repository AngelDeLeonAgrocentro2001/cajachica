<?php
require_once '../models/Usuario.php';
require_once '../models/Role.php';
require_once '../models/Auditoria.php';

class AccesoController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function list() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_accesos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar accesos']);
            exit;
        }

        $usuarios = $usuarioModel->getAllUsuarios();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } else {
            require '../views/accesos/list.html';
        }
        exit;
    }

    private function getAvailableModules() {
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

    public function manageModules($userId = null) {
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
        if (!$usuarioModel->tienePermiso($usuario, 'manage_accesos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar accesos']);
            exit;
        }
    
        $targetUser = $userId ? $usuarioModel->getUsuarioById($userId) : null;
        if ($userId && !$targetUser) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        $rol = $rolModel->getRolById($targetUser['id_rol']);
        $defaultPermissions = $this->getDefaultPermissions($rol['nombre']);
        $dynamicPermissions = $this->getDynamicPermissions($rol['descripcion'] ?? '');
    
        // Obtener permisos manuales del rol
        $stmt = $this->pdo->prepare("SELECT permiso, estado FROM rol_permisos WHERE id_rol = ?");
        $stmt->execute([$targetUser['id_rol']]);
        $manualRolPermissionsData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $manualRolPermissions = [];
        $manualOverrides = [];
        foreach ($manualRolPermissionsData as $perm) {
            if ($perm['estado'] === 'ACTIVO') {
                $manualRolPermissions[] = $perm['permiso'];
            }
            $manualOverrides[$perm['permiso']] = $perm['estado'];
        }
    
        // Combinar permisos efectivos del rol
        $rolPermissions = array_unique(array_merge($defaultPermissions, $dynamicPermissions, $manualRolPermissions));
        // Ajustar permisos efectivos según sobrescrituras manuales
        foreach ($manualOverrides as $permiso => $estado) {
            if ($estado === 'INACTIVO' && in_array($permiso, $rolPermissions)) {
                $rolPermissions = array_diff($rolPermissions, [$permiso]);
            }
        }
    
        // Obtener permisos individuales del usuario (ACTIVOS)
        $stmt = $this->pdo->prepare("SELECT permiso, origen FROM accesos_permisos WHERE id_usuario = ? AND estado = ?");
        $stmt->execute([$userId, 'ACTIVO']);
        $userPermissionsData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $userPermissions = array_column($userPermissionsData, 'permiso');
        $origins = array_column($userPermissionsData, 'origen', 'permiso');
    
        // Obtener permisos individuales desactivados (INACTIVOS, origen 'MANUAL')
        $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'INACTIVO' AND origen = 'MANUAL'");
        $stmt->execute([$userId]);
        $deactivatedUserPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
        // Ajustar permisos del usuario para respetar las sobrescrituras del rol y los permisos desactivados
        $adjustedUserPermissions = [];
        foreach ($userPermissions as $permiso) {
            if (isset($origins[$permiso]) && $origins[$permiso] === 'MANUAL') {
                $adjustedUserPermissions[] = $permiso; // Mantener permisos individuales activos
            } elseif (in_array($permiso, $rolPermissions)) {
                $adjustedUserPermissions[] = $permiso; // Mantener permisos que están activos en el rol
            }
        }
    
        // Combinar permisos del rol y del usuario ajustados, excluyendo los desactivados individualmente
        $effectivePermissions = array_unique($adjustedUserPermissions);
        // Excluir permisos que están desactivados individualmente
        $effectivePermissions = array_diff($effectivePermissions, $deactivatedUserPermissions);
        error_log("Permisos efectivos ajustados para el usuario $userId: " . print_r($effectivePermissions, true));
        error_log("Permisos desactivados individualmente para el usuario $userId: " . print_r($deactivatedUserPermissions, true));
    
        $availableModules = $this->getAvailableModules();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Solicitud POST recibida. Datos POST: " . print_r($_POST, true));
            $selectedModules = isset($_POST['modules']) && is_array($_POST['modules']) ? array_map('trim', $_POST['modules']) : [];
            error_log("Módulos seleccionados para guardar: " . print_r($selectedModules, true));
    
            // Verificar permisos que se activan y no están en el rol
            $permissionsToAskForActivation = [];
            foreach ($selectedModules as $permiso) {
                if (!in_array($permiso, $rolPermissions)) {
                    $permissionsToAskForActivation[] = $permiso;
                }
            }
    
            // Verificar permisos que se desactivan y están en el rol
            $permissionsToAskForDeactivation = [];
            foreach ($effectivePermissions as $permiso) {
                if (in_array($permiso, $rolPermissions) && !in_array($permiso, $selectedModules)) {
                    $permissionsToAskForDeactivation[] = $permiso;
                }
            }
    
            // Mostrar mensaje de confirmación para activar permisos en el rol
            if (!empty($permissionsToAskForActivation) && !isset($_POST['assign_to_role'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'action' => 'confirm_assign_to_role',
                    'permissions' => $permissionsToAskForActivation,
                    'message' => 'Los siguientes permisos no están activos en el rol ' . htmlspecialchars($rol['nombre']) . '. ¿Desea asignarlos al rol también?'
                ]);
                exit;
            }
    
            // Mostrar mensaje de confirmación para desactivar permisos del rol
            if (!empty($permissionsToAskForDeactivation) && !isset($_POST['remove_from_role'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'action' => 'confirm_remove_from_role',
                    'permissions' => $permissionsToAskForDeactivation,
                    'message' => 'Los siguientes permisos están activos en el rol ' . htmlspecialchars($rol['nombre']) . '. ¿Desea desactivarlos del rol también?'
                ]);
                exit;
            }
    
            $assignToRole = isset($_POST['assign_to_role']) && $_POST['assign_to_role'] === 'yes';
            $removeFromRole = isset($_POST['remove_from_role']) && $_POST['remove_from_role'] === 'yes';
    
            $this->pdo->beginTransaction();
    
            try {
                if ($assignToRole) {
                    // Activar los permisos en rol_permisos
                    foreach ($permissionsToAskForActivation as $permiso) {
                        $stmt = $this->pdo->prepare("INSERT INTO rol_permisos (id_rol, permiso, estado) VALUES (?, ?, 'ACTIVO') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                        $stmt->execute([$targetUser['id_rol'], $permiso]);
                        error_log("Activado permiso $permiso en rol_permisos para el rol {$targetUser['id_rol']}");
                    }
                    // Actualizar rolPermissions para incluir los permisos recién activados
                    $rolPermissions = array_unique(array_merge($rolPermissions, $permissionsToAskForActivation));
                }
    
                if ($removeFromRole) {
                    // Desactivar los permisos en rol_permisos
                    foreach ($permissionsToAskForDeactivation as $permiso) {
                        $stmt = $this->pdo->prepare("INSERT INTO rol_permisos (id_rol, permiso, estado) VALUES (?, ?, 'INACTIVO') ON DUPLICATE KEY UPDATE estado = 'INACTIVO'");
                        $stmt->execute([$targetUser['id_rol'], $permiso]);
                        error_log("Desactivado permiso $permiso en rol_permisos para el rol {$targetUser['id_rol']}");
                    }
                    // Actualizar rolPermissions para excluir los permisos desactivados
                    $rolPermissions = array_diff($rolPermissions, $permissionsToAskForDeactivation);
    
                    // Actualizar permisos de todos los usuarios asociados al rol
                    $usuarios = $usuarioModel->getUsuariosByRolId($targetUser['id_rol']);
                    foreach ($usuarios as $usuario) {
                        if ($usuario['id'] == $userId) {
                            continue; // No actualizamos el usuario actual, ya que sus permisos se manejarán a continuación
                        }
                        // Obtener permisos individuales del usuario (origen 'MANUAL')
                        $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND estado = 'ACTIVO' AND origen = 'MANUAL'");
                        $stmt->execute([$usuario['id']]);
                        $individualPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    
                        // Eliminar permisos anteriores del rol (origen 'ROL_MANUAL' o NULL)
                        $stmt = $this->pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND (origen = 'ROL_MANUAL' OR origen IS NULL)");
                        $stmt->execute([$usuario['id']]);
                        error_log("Eliminados permisos con origen ROL_MANUAL o NULL para el usuario {$usuario['id']}");
    
                        // Combinar permisos del rol con permisos individuales
                        $userPermissions = array_unique(array_merge($rolPermissions, $individualPermissions));
                        foreach ($userPermissions as $permiso) {
                            $origen = in_array($permiso, $individualPermissions) ? 'MANUAL' : 'ROL_MANUAL';
                            $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', ?) ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                            $stmt->execute([$usuario['id'], $permiso, $origen]);
                            error_log("Asignado permiso $permiso al usuario {$usuario['id']} con origen $origen");
                        }
    
                        $auditoriaModel->createAuditoria(null, null, $usuario['id'], 'ASIGNAR_PERMISOS', "Permisos actualizados para usuario {$usuario['email']} desde rol ID {$targetUser['id_rol']}: " . implode(', ', $userPermissions));
                    }
                }
    
                // Eliminar permisos individuales anteriores (origen 'MANUAL' o NULL)
                $stmt = $this->pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND (origen = 'MANUAL' OR origen IS NULL)");
                $stmt->execute([$userId]);
                error_log("Eliminados permisos con origen MANUAL o NULL para el usuario $userId");
    
                // Determinar permisos individuales (los que no están en el rol)
                $individualPermissions = array_diff($selectedModules, $rolPermissions);
                $rolBasedPermissions = array_intersect($selectedModules, $rolPermissions);
    
                // Determinar permisos desactivados que estaban en el rol (para guardarlos como individuales inactivos)
                $deactivatedPermissions = array_diff($effectivePermissions, $selectedModules);
                $deactivatedIndividualPermissions = array_intersect($deactivatedPermissions, $rolPermissions);
    
                // Asignar permisos individuales activados
                foreach ($individualPermissions as $permiso) {
                    $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', 'MANUAL') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                    $stmt->execute([$userId, $permiso]);
                    error_log("Asignado permiso individual $permiso al usuario $userId");
                }
    
                // Asignar permisos del rol
                foreach ($rolBasedPermissions as $permiso) {
                    $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', 'ROL_MANUAL') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
                    $stmt->execute([$userId, $permiso]);
                    error_log("Asignado permiso $permiso al usuario $userId con origen ROL_MANUAL");
                }
    
                // Guardar permisos desactivados como individuales (origen 'MANUAL', estado 'INACTIVO')
                foreach ($deactivatedIndividualPermissions as $permiso) {
                    $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'INACTIVO', 'MANUAL') ON DUPLICATE KEY UPDATE estado = 'INACTIVO'");
                    $stmt->execute([$userId, $permiso]);
                    error_log("Guardado permiso desactivado $permiso como individual para el usuario $userId");
                }
    
                $auditoriaModel->createAuditoria(null, null, $userId, 'ASIGNAR_MODULOS', "Módulos asignados a usuario {$targetUser['email']}: " . implode(', ', $selectedModules));
    
                $this->pdo->commit();
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Módulos asignados correctamente']);
                } else {
                    header('Location: index.php?controller=acceso&action=manageModules&user_id=' . $userId . '&success=' . urlencode('Módulos asignados correctamente'));
                }
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log("Error al guardar módulos: " . $e->getMessage());
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al guardar módulos']);
                } else {
                    header('Location: index.php?controller=acceso&action=manageModules&user_id=' . $userId . '&error=' . urlencode('Error al guardar módulos'));
                }
            }
            exit;
        }
    
        $success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
        $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
    
        require '../views/accesos/manage_modules.html';
        exit;
    }

    private function getDefaultPermissions($rolNombre) {
        $defaultPermissions = [
            'ADMIN' => array_keys($this->getAvailableModules()),
            'ENCARGADO_CAJA_CHICA' => ['create_liquidaciones', 'create_detalles', 'manage_facturas', 'manage_cajachica','manage_correcciones'],
            'SUPERVISOR_AUTORIZADOR' => ['autorizar_liquidaciones', 'autorizar_facturas', 'manage_cuentas_contables', 'manage_facturas', 'revisar_liquidaciones', 'revisar_detalles_liquidaciones', 'revisar_facturas','manage_correcciones'],
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
                'manage_tipos_gastos',
                'manage_correcciones'
            ],
        ];

        $combinedPermissions = [];
        foreach ($defaultPermissions as $rol => $permissions) {
            if (strpos($rolNombre, $rol) !== false) {
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
        ];

        $permisosPorRol = [
            'ADMIN' => true,
            'ENCARGADO_CAJA_CHICA' => [
                'create_liquidaciones' => true,
                'create_detalles' => true,
                'manage_facturas' => true,
                'manage_cajachica' => true,
                'manage_correcciones' => true,
            ],
            'SUPERVISOR_AUTORIZADOR' => [
                'autorizar_liquidaciones' => true,
                'autorizar_facturas' => true,
                'manage_cuentas_contables' => true,
                'manage_facturas' => true,
                'revisar_liquidaciones' => true,
                'revisar_detalles_liquidaciones' => true,
                'revisar_facturas' => true,
                'manage_correcciones' => true,
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
                'manage_correcciones' => true,
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
}