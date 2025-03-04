<?php
require_once '../models/Usuario.php';

class UsuarioController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listUsuarios() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = new Usuario();
        $usuarioData = $usuario->getUsuarioById($_SESSION['user_id']);
        if (!$usuario->tienePermiso($usuarioData, 'manage_usuarios')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar usuarios']);
            exit;
        }

        $usuarios = $usuario->getAllUsuarios();

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
            error_log('Error: No hay session user_id en createUsuario');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false) {
            error_log('Error: No se pudo obtener el usuario de la sesión');
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener el usuario de la sesión']);
            exit;
        }
    
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            error_log('Error: No tienes permiso para crear usuarios');
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
                $rol = $_POST['rol'] ?? '';
    
                error_log("Datos recibidos para crear usuario: nombre=$nombre, email=$email, rol=$rol, password=" . (empty($password) ? 'vacío' : 'proporcionado'));
    
                if (empty($nombre) || empty($email) || empty($password) || empty($rol)) {
                    throw new Exception('Nombre, email, contraseña y rol son obligatorios');
                }
    
                // Validar que el rol sea válido
                $validRoles = [
                    'ADMIN',
                    'ENCARGADO_CAJA_CHICA',
                    'SUPERVISOR_AUTORIZADOR',
                    'CONTABILIDAD'
                ];
                if (!in_array($rol, $validRoles, true)) {
                    error_log("Rol inválido recibido: $rol");
                    throw new Exception('Rol inválido');
                }
    
                // Verificar que el email no esté duplicado
                $existingUserStmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
                if (!$existingUserStmt) {
                    error_log("Error al preparar la consulta de email duplicado: " . implode(", ", $this->pdo->errorInfo()));
                    throw new Exception('Error al verificar el email');
                }
                $existingUserStmt->execute([$email]);
                $existingUser = $existingUserStmt->fetch();
                if ($existingUser) {
                    error_log("Email duplicado detectado: $email");
                    throw new Exception('El email ya está en uso');
                }
    
                $usuarioModel = new Usuario();
                $result = $usuarioModel->createUsuario($nombre, $email, $password, $rol);
                if ($result === false) {
                    error_log("Error al ejecutar createUsuario en el modelo");
                    throw new Exception('Error al crear usuario en la base de datos');
                }
    
                header('Content-Type: application/json');
                http_response_code(201);
                echo json_encode(['message' => 'Usuario creado']);
            } catch (Exception $e) {
                error_log('Error en createUsuario: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            } catch (Throwable $t) {
                error_log('Error inesperado en createUsuario: ' . $t->getMessage());
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error inesperado en el servidor']);
            }
            exit;
        }
    
        // Verificar si la solicitud es AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            // Si no es una solicitud AJAX, redirigir a la lista de usuarios
            header('Location: index.php?controller=usuario&action=list');
            exit;
        }
    
        ob_start();
        require '../views/usuarios/form.html';
        $html = ob_get_clean();
        echo $html;
    }
    
    public function updateUsuario($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateUsuario');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false) {
            error_log('Error: No se pudo obtener el usuario de la sesión');
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener el usuario de la sesión']);
            exit;
        }
    
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
            error_log('Error: No tienes permiso para actualizar usuarios');
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
                $rol = $_POST['rol'] ?? '';
    
                error_log("Datos recibidos para actualizar usuario ID $id: nombre=$nombre, email=$email, rol=$rol, password=" . (empty($password) ? 'vacío' : 'proporcionado'));
    
                if (empty($nombre) || empty($email) || empty($rol)) {
                    throw new Exception('Nombre, email y rol son obligatorios');
                }
    
                // Validar que el rol sea válido
                $validRoles = [
                    'ADMIN',
                    'ENCARGADO_CAJA_CHICA',
                    'SUPERVISOR_AUTORIZADOR',
                    'CONTABILIDAD'
                ];
                if (!in_array($rol, $validRoles, true)) {
                    error_log("Rol inválido recibido: $rol");
                    throw new Exception('Rol inválido');
                }
    
                // Verificar que el email no esté duplicado (excepto para el usuario actual)
                $existingUserStmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
                if (!$existingUserStmt) {
                    error_log("Error al preparar la consulta de email duplicado: " . implode(", ", $this->pdo->errorInfo()));
                    throw new Exception('Error al verificar el email');
                }
                $existingUserStmt->execute([$email, $id]);
                $existingUser = $existingUserStmt->fetch();
                if ($existingUser) {
                    error_log("Email duplicado detectado: $email");
                    throw new Exception('El email ya está en uso');
                }
    
                $usuarioModel = new Usuario();
                $result = $usuarioModel->updateUsuario($id, $nombre, $email, $password, $rol);
                if ($result === false) {
                    error_log("Error al ejecutar updateUsuario en el modelo para ID $id");
                    throw new Exception('Error al actualizar usuario en la base de datos');
                }
    
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Usuario actualizado']);
            } catch (Exception $e) {
                error_log('Error en updateUsuario: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            } catch (Throwable $t) {
                error_log('Error inesperado en updateUsuario: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error inesperado en el servidor']);
            }
            exit;
        }
    
        // Verificar si la solicitud es AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            // Si no es una solicitud AJAX, redirigir a la lista de usuarios
            header('Location: index.php?controller=usuario&action=list');
            exit;
        }
    
        $usuarioModel = new Usuario();
        $data = $usuarioModel->getUsuarioById($id);
        if ($data === false) {
            error_log("Error: No se pudo obtener el usuario con ID $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        ob_start();
        require '../views/usuarios/form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function deleteUsuario($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = new Usuario();
        $usuarioData = $usuario->getUsuarioById($_SESSION['user_id']);
        if (!$usuario->tienePermiso($usuarioData, 'manage_usuarios')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar usuarios']);
            exit;
        }

        if ($usuario->deleteUsuario($id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Usuario eliminado']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar usuario']);
        }
        exit;
    }
}