<?php
require_once '../models/Usuario.php';
require_once '../models/Role.php';
require_once '../models/Auditoria.php';

class UsuarioController {
    private $usuarioModel;
    private $rolModel;
    private $auditoriaModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->rolModel = new Role();
        $this->auditoriaModel = new Auditoria();
    }

    private function assignPermissionsBasedOnRole($usuarioId, $rolId) {
        $usuarioModel = new Usuario();
        $rol = $this->rolModel->getRolById($rolId);
        if (!$rol) {
            error_log("Rol no encontrado para id_rol: $rolId");
            return;
        }
    
        $nombreRol = strtoupper($rol['nombre'] ?? '');
        $descripcion = $rol['descripcion'] ?? '';
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
            if (strpos($descripcionLower, $keyword) !== false || strpos(strtolower($nombreRol), $keyword) !== false) {
                $detectedRoles[] = $role;
            }
        }
    
        $pdo = Database::getInstance()->getPdo();
    
        $stmt = $pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ? AND origen = ?");
        $stmt->execute([$usuarioId, 'ROL_DESCRIPCION']);
        error_log("Permisos dinámicos anteriores eliminados para usuario $usuarioId");
    
        if (empty($detectedRoles)) {
            error_log("No se detectaron roles dinámicos para usuario $usuarioId con nombre: $nombreRol, descripción: $descripcion");
            return;
        }
    
        $combinedPermissions = [];
        foreach ($detectedRoles as $role) {
            if ($role === 'ADMIN') {
                $stmt = $pdo->query("SELECT nombre FROM permisos");
                $allPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $combinedPermissions = array_merge($combinedPermissions, $allPermissions);
                error_log("Asignando todos los permisos a usuario $usuarioId (ADMIN): " . implode(', ', $allPermissions));
            } else {
                $permissions = $permisosPorRol[$role] ?? [];
                if (is_array($permissions)) {
                    $combinedPermissions = array_merge($combinedPermissions, array_keys(array_filter($permissions)));
                    error_log("Asignando permisos de rol $role a usuario $usuarioId: " . implode(', ', array_keys(array_filter($permissions))));
                }
            }
        }
    
        $combinedPermissions = array_unique($combinedPermissions);
    
        foreach ($combinedPermissions as $permiso) {
            $stmt = $pdo->prepare("INSERT INTO accesos_permisos (id_usuario, permiso, estado, origen) VALUES (?, ?, 'ACTIVO', 'ROL_DESCRIPCION') ON DUPLICATE KEY UPDATE estado = 'ACTIVO'");
            $stmt->execute([$usuarioId, $permiso]);
        }
    
        $usuario = $usuarioModel->getUsuarioById($usuarioId);
        $this->auditoriaModel->createAuditoria(null, null, $usuarioId, 'ASIGNAR_PERMISOS', "Permisos asignados a usuario {$usuario['email']} desde rol ID $rolId: " . implode(', ', $combinedPermissions));
    }

    public function listUsuarios() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar usuarios']);
            exit;
        }

        $usuarios = $this->usuarioModel->getAllUsuarios();
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($usuarios);
        } else {
            require '../views/usuarios/list.html';
        }
        exit;
    }

    public function createUsuario() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear usuarios']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $id_rol = $_POST['id_rol'] ?? '';
    
                if (empty($nombre) || empty($email) || empty($password) || empty($id_rol)) {
                    throw new Exception('Todos los campos son obligatorios');
                }
    
                if ($this->usuarioModel->getUsuarioByEmail($email)) {
                    throw new Exception("El email '$email' ya está registrado. Por favor, usa un email diferente.");
                }
    
                $result = $this->usuarioModel->createUsuario($nombre, $email, $password, $id_rol);
                if ($result === false) {
                    throw new Exception('Error al crear usuario');
                }

                $usuarioId = Database::getInstance()->getPdo()->lastInsertId();
                $this->assignPermissionsBasedOnRole($usuarioId, $id_rol);
    
                $this->auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'CREAR_USUARIO', "Usuario creado: {$email}");
    
                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode(['message' => 'Usuario creado']);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                $errorMessage = 'Error al crear usuario';
                if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errorMessage = "El email '$email' ya está registrado. Por favor, usa un email diferente.";
                } else {
                    $errorMessage .= ': ' . $e->getMessage();
                }
                echo json_encode(['error' => $errorMessage]);
            }
            exit;
        }
    
        $usuario = [];
        $roles = $this->usuarioModel->getAllRoles();
        ob_start();
        require '../views/usuarios/form.html';
        $html = ob_get_clean();
        echo $html;
    }
    
    public function updateUsuario($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar usuarios']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $id_rol = $_POST['id_rol'] ?? '';
    
                if (empty($nombre) || empty($email) || empty($id_rol)) {
                    throw new Exception('Nombre, email e ID de rol son obligatorios');
                }
    
                $existingUser = $this->usuarioModel->getUsuarioByEmail($email);
                if ($existingUser && $existingUser['id'] != $id) {
                    throw new Exception("El email '$email' ya está registrado por otro usuario. Por favor, usa un email diferente.");
                }
    
                $result = $this->usuarioModel->updateUsuario($id, $nombre, $email, $password, $id_rol);
                if ($result === false) {
                    throw new Exception('Error al actualizar usuario');
                }

                $this->assignPermissionsBasedOnRole($id, $id_rol);
    
                $this->auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'ACTUALIZAR_USUARIO', "Usuario actualizado: {$email}");
    
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Usuario actualizado']);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                $errorMessage = 'Error al actualizar usuario';
                if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errorMessage = "El email '$email' ya está registrado por otro usuario. Por favor, usa un email diferente.";
                } else {
                    $errorMessage .= ': ' . $e->getMessage();
                }
                echo json_encode(['error' => $errorMessage]);
            }
            exit;
        }
    
        $usuario = $this->usuarioModel->getUsuarioById($id);
        if ($usuario === false) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        $roles = $this->usuarioModel->getAllRoles();
        ob_start();
        require '../views/usuarios/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function deleteUsuario($id) {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(E_ALL);
    
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar usuarios']);
            exit;
        }
    
        if ($id == $_SESSION['user_id']) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No puedes eliminar tu propio usuario']);
            exit;
        }
    
        $targetUser = $this->usuarioModel->getUsuarioById($id);
        if ($targetUser === false) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        $email = $targetUser['email'] ?? 'Usuario desconocido';
    
        try {
            $pdo = Database::getInstance()->getPdo();
            $pdo->beginTransaction();
    
            $this->auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'ELIMINAR_USUARIO', "Usuario eliminado: {$email}");
    
            $stmt = $pdo->prepare("DELETE FROM auditoria WHERE id_usuario = ?");
            $stmt->execute([$id]);
            error_log("Registros de auditoría eliminados para el usuario $id");
    
            $stmt = $pdo->prepare("DELETE FROM accesos_permisos WHERE id_usuario = ?");
            $stmt->execute([$id]);
            error_log("Permisos eliminados para el usuario $id");
    
            if ($this->usuarioModel->deleteUsuario($id)) {
                $pdo->commit();
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Usuario eliminado']);
            } else {
                $pdo->rollBack();
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al eliminar usuario']);
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error al eliminar usuario $id: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar usuario: ' . $e->getMessage()]);
        } catch (Exception $e) {
            error_log("Error inesperado al eliminar usuario $id: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error inesperado al eliminar usuario: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function getSupervisores() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para acceder a la lista de supervisores']);
            exit;
        }

        $supervisores = $usuarioModel->getUsuariosByRol('SUPERVISOR');
        $supervisoresList = array_map(function($supervisor) {
            return [
                'id' => $supervisor['id'],
                'nombre' => $supervisor['nombre'],
                'email' => $supervisor['email']
            ];
        }, $supervisores);

        header('Content-Type: application/json');
        echo json_encode($supervisoresList);
        exit;
    }

    public function getContadores() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        if (!$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones') && strtoupper($usuario['rol']) !== 'ADMIN') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para acceder a la lista de contadores']);
            exit;
        }

        // Fetch all users with the CONTABILIDAD role (corrected from CONTADOR)
        $contadores = $usuarioModel->getUsuariosByRol('CONTABILIDAD');
        $contadoresList = array_map(function($contador) {
            return [
                'id' => $contador['id'],
                'nombre' => $contador['nombre'],
                'email' => $contador['email']
            ];
        }, $contadores);

        header('Content-Type: application/json');
        echo json_encode($contadoresList);
        exit;
    }
}
?>