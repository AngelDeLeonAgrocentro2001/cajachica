<?php
require_once '../models/Usuario.php';
require_once '../models/Auditoria.php'; // Asegúrate de incluir el modelo Auditoria

class UsuarioController {
    private $usuarioModel;
    private $auditoriaModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->auditoriaModel = new Auditoria(); // Inicializar el modelo de auditoría
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
    
                // Verificar si el email ya existe
                if ($this->usuarioModel->getUsuarioByEmail($email)) {
                    throw new Exception("El email '$email' ya está registrado. Por favor, usa un email diferente.");
                }
    
                $result = $this->usuarioModel->createUsuario($nombre, $email, $password, $id_rol);
                if ($result === false) {
                    throw new Exception('Error al crear usuario');
                }
    
                // Registrar auditoría
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
    
                // Verificar si el email ya existe (excluyendo el usuario actual)
                $existingUser = $this->usuarioModel->getUsuarioByEmail($email);
                if ($existingUser && $existingUser['id'] != $id) {
                    throw new Exception("El email '$email' ya está registrado por otro usuario. Por favor, usa un email diferente.");
                }
    
                $result = $this->usuarioModel->updateUsuario($id, $nombre, $email, $password, $id_rol);
                if ($result === false) {
                    throw new Exception('Error al actualizar usuario');
                }
    
                // Registrar auditoría
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

        $email = $this->usuarioModel->getUsuarioById($id)['email'] ?? 'Usuario desconocido';
        if ($this->usuarioModel->deleteUsuario($id)) {
            // Registrar auditoría
            $this->auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'ELIMINAR_USUARIO', "Usuario eliminado: {$email}");
            
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



// <?php
// require_once '../models/Usuario.php';

// class UsuarioController {
//     private $usuarioModel;

//     public function __construct() {
//         $this->usuarioModel = new Usuario();
//     }

//     public function listUsuarios() {
//         if (!isset($_SESSION['user_id'])) {
//             header('Content-Type: application/json');
//             http_response_code(401);
//             echo json_encode(['error' => 'No autorizado']);
//             exit;
//         }

//         $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
//         if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
//             header('Content-Type: application/json');
//             http_response_code(403);
//             echo json_encode(['error' => 'No tienes permiso para gestionar usuarios']);
//             exit;
//         }

//         $usuarios = $this->usuarioModel->getAllUsuarios();
//         if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
//             header('Content-Type: application/json');
//             echo json_encode($usuarios);
//         } else {
//             require '../views/usuarios/list.html';
//         }
//         exit;
//     }

//     public function createUsuario() {
//         if (!isset($_SESSION['user_id'])) {
//             header('Content-Type: application/json');
//             http_response_code(401);
//             echo json_encode(['error' => 'No autorizado']);
//             exit;
//         }
    
//         $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
//         if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
//             header('Content-Type: application/json');
//             http_response_code(403);
//             echo json_encode(['error' => 'No tienes permiso para crear usuarios']);
//             exit;
//         }
    
//         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//             try {
//                 $nombre = $_POST['nombre'] ?? '';
//                 $email = $_POST['email'] ?? '';
//                 $password = $_POST['password'] ?? '';
//                 $id_rol = $_POST['id_rol'] ?? '';
    
//                 if (empty($nombre) || empty($email) || empty($password) || empty($id_rol)) {
//                     throw new Exception('Todos los campos son obligatorios');
//                 }
    
//                 // Verificar si el email ya existe
//                 if ($this->usuarioModel->getUsuarioByEmail($email)) {
//                     throw new Exception("El email '$email' ya está registrado. Por favor, usa un email diferente.");
//                 }
    
//                 $result = $this->usuarioModel->createUsuario($nombre, $email, $password, $id_rol);
//                 if ($result === false) {
//                     throw new Exception('Error al crear usuario');
//                 }
    
//                 header('Content-Type: application/json');
//                 http_response_code(201);
//                 echo json_encode(['message' => 'Usuario creado']);
//             } catch (Exception $e) {
//                 header('Content-Type: application/json');
//                 http_response_code(400);
//                 echo json_encode(['error' => $e->getMessage()]);
//             } catch (PDOException $e) {
//                 header('Content-Type: application/json');
//                 http_response_code(400);
//                 $errorMessage = 'Error al crear usuario';
//                 if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
//                     $errorMessage = "El email '$email' ya está registrado. Por favor, usa un email diferente.";
//                 } else {
//                     $errorMessage .= ': ' . $e->getMessage();
//                 }
//                 echo json_encode(['error' => $errorMessage]);
//             }
//             exit;
//         }
    
//         $usuario = [];
//         $roles = $this->usuarioModel->getAllRoles();
//         ob_start();
//         require '../views/usuarios/form.html';
//         $html = ob_get_clean();
//         echo $html;
//     }
    
//     public function updateUsuario($id) {
//         if (!isset($_SESSION['user_id'])) {
//             header('Content-Type: application/json');
//             http_response_code(401);
//             echo json_encode(['error' => 'No autorizado']);
//             exit;
//         }
    
//         $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
//         if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
//             header('Content-Type: application/json');
//             http_response_code(403);
//             echo json_encode(['error' => 'No tienes permiso para actualizar usuarios']);
//             exit;
//         }
    
//         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//             try {
//                 $nombre = $_POST['nombre'] ?? '';
//                 $email = $_POST['email'] ?? '';
//                 $password = $_POST['password'] ?? '';
//                 $id_rol = $_POST['id_rol'] ?? '';
    
//                 if (empty($nombre) || empty($email) || empty($id_rol)) {
//                     throw new Exception('Nombre, email e ID de rol son obligatorios');
//                 }
    
//                 // Verificar si el email ya existe (excluyendo el usuario actual)
//                 $existingUser = $this->usuarioModel->getUsuarioByEmail($email);
//                 if ($existingUser && $existingUser['id'] != $id) {
//                     throw new Exception("El email '$email' ya está registrado por otro usuario. Por favor, usa un email diferente.");
//                 }
    
//                 $result = $this->usuarioModel->updateUsuario($id, $nombre, $email, $password, $id_rol);
//                 if ($result === false) {
//                     throw new Exception('Error al actualizar usuario');
//                 }
    
//                 header('Content-Type: application/json');
//                 echo json_encode(['message' => 'Usuario actualizado']);
//             } catch (Exception $e) {
//                 header('Content-Type: application/json');
//                 http_response_code(400);
//                 echo json_encode(['error' => $e->getMessage()]);
//             } catch (PDOException $e) {
//                 header('Content-Type: application/json');
//                 http_response_code(400);
//                 $errorMessage = 'Error al actualizar usuario';
//                 if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
//                     $errorMessage = "El email '$email' ya está registrado por otro usuario. Por favor, usa un email diferente.";
//                 } else {
//                     $errorMessage .= ': ' . $e->getMessage();
//                 }
//                 echo json_encode(['error' => $errorMessage]);
//             }
//             exit;
//         }
    
//         $usuario = $this->usuarioModel->getUsuarioById($id);
//         if ($usuario === false) {
//             header('Content-Type: application/json');
//             http_response_code(404);
//             echo json_encode(['error' => 'Usuario no encontrado']);
//             exit;
//         }
    
//         $roles = $this->usuarioModel->getAllRoles();
//         ob_start();
//         require '../views/usuarios/form.html';
//         $html = ob_get_clean();
//         echo $html;
//     }

//     public function deleteUsuario($id) {
//         if (!isset($_SESSION['user_id'])) {
//             header('Content-Type: application/json');
//             http_response_code(401);
//             echo json_encode(['error' => 'No autorizado']);
//             exit;
//         }

//         $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
//         if ($usuario === false || !isset($usuario['rol']) || $usuario['rol'] !== 'ADMIN') {
//             header('Content-Type: application/json');
//             http_response_code(403);
//             echo json_encode(['error' => 'No tienes permiso para eliminar usuarios']);
//             exit;
//         }

//         if ($this->usuarioModel->deleteUsuario($id)) {
//             header('Content-Type: application/json');
//             echo json_encode(['message' => 'Usuario eliminado']);
//         } else {
//             header('Content-Type: application/json');
//             http_response_code(400);
//             echo json_encode(['error' => 'Error al eliminar usuario']);
//         }
//         exit;
//     }
// }