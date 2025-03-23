<?php
require_once '../models/Acceso.php';
require_once '../models/Usuario.php';

class AccesoController {
    private $accesoModel;
    private $usuarioModel;

    public function __construct() {
        $this->accesoModel = new Acceso();
        $this->usuarioModel = new Usuario();
    }

    public function list() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== Usuario::ROL_ADMIN) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar accesos']);
            exit;
        }

        $usuarios = $this->usuarioModel->getAllUsuarios();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } else {
            require '../views/accesos/list.html';
        }
        exit;
    }

    public function manageModules($user_id = null) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== Usuario::ROL_ADMIN) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar módulos']);
            exit;
        }
    
        $targetUser = $user_id ? $this->usuarioModel->getUsuarioById($user_id) : null;
        if ($user_id && !$targetUser) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        $modules = $this->accesoModel->getAllModules();
        $availableModules = $this->getAvailableModules();
        $defaultModules = $this->getDefaultModules($targetUser['rol'] ?? '', $availableModules);
        $userModules = $this->accesoModel->getUserModulesAndPermisos($user_id);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Solicitud POST recibida. Datos POST: " . print_r($_POST, true));
            $selectedModules = isset($_POST['modules']) && is_array($_POST['modules']) ? array_map('trim', $_POST['modules']) : [];
            error_log("Módulos seleccionados para guardar: " . print_r($selectedModules, true));
    
            $pdo = Database::getInstance()->getPdo();
            $pdo->beginTransaction();
    
            try {
                // Obtener los módulos actuales del usuario (permisos asignados dinámicamente)
                $currentModules = [];
                foreach ($userModules as $userModule) {
                    if ($userModule['permiso']) {
                        $currentModules[] = $userModule['permiso'];
                    }
                }
                error_log("Módulos actuales del usuario: " . print_r($currentModules, true));
    
                // Asegurar que los permisos predeterminados estén incluidos en los módulos seleccionados
                $selectedModules = array_unique(array_merge($selectedModules, $defaultModules));
    
                // Determinar módulos a asignar y a remover
                $modulesToAssign = array_diff($selectedModules, $currentModules);
                $modulesToRemove = array_diff($currentModules, $selectedModules, $defaultModules);
    
                // Asignar nuevos módulos
                foreach ($modulesToAssign as $module) {
                    $moduleData = array_filter($modules, function($m) use ($module) {
                        return $m['permiso_predeterminado'] === $module;
                    });
                    $moduleData = reset($moduleData);
                    if ($moduleData) {
                        $moduleId = $moduleData['id'];
                        $this->accesoModel->assignPermiso($user_id, $moduleId, $module);
                        error_log("Asignado módulo $module (ID: $moduleId) al usuario $user_id");
                    }
                }
    
                // Remover módulos deseleccionados
                foreach ($modulesToRemove as $module) {
                    $moduleData = array_filter($modules, function($m) use ($module) {
                        return $m['permiso_predeterminado'] === $module;
                    });
                    $moduleData = reset($moduleData);
                    if ($moduleData) {
                        $moduleId = $moduleData['id'];
                        $this->accesoModel->removePermiso($user_id, $moduleId, $module);
                        error_log("Removido módulo $module (ID: $moduleId) del usuario $user_id");
                    }
                }
    
                $pdo->commit();
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Módulos asignados correctamente']);
                } else {
                    header('Location: index.php?controller=acceso&action=manageModules&user_id=' . $user_id . '&success=' . urlencode('Módulos asignados correctamente'));
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Error al guardar módulos: " . $e->getMessage());
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al guardar módulos']);
                } else {
                    header('Location: index.php?controller=acceso&action=manageModules&user_id=' . $user_id . '&error=' . urlencode('Error al guardar módulos'));
                }
            }
            exit;
        }
    
        $success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
        $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
    
        require '../views/accesos/manage_modules.html';
        exit;
    }

    private function getDefaultModules($rol, $modules) {
        $defaultPermissions = [
            Usuario::ROL_ADMIN => array_keys($modules), // El administrador tiene todos los permisos
            Usuario::ROL_ENCARGADO_CAJA_CHICA => ['create_liquidaciones', 'create_detalles', 'manage_facturas'],
            Usuario::ROL_SUPERVISOR => ['autorizar_liquidaciones', 'autorizar_facturas', 'manage_cuentas_contables', 'manage_facturas'],
            Usuario::ROL_CONTABILIDAD => [
                'revisar_liquidaciones',
                'revisar_detalles_liquidaciones',
                'manage_reportes',
                'manage_auditoria',
                'manage_cuentas_contables',
                'manage_facturas',
                'manage_centros_costos',
                'revisar_facturas' // Aseguramos que esté incluido
            ],
        ];
        $defaultModules = $defaultPermissions[$rol] ?? [];
        return array_intersect($defaultModules, array_keys($modules));
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
            'manage_centros_costos' => 'Gestión de Centros de Costos' // Nuevo permiso agregado
        ];
    }
}