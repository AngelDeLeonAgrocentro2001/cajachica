<?php
require_once '../models/Role.php';

class RoleController {
    public function listRoles() {
        $role = new Role();
        $roles = $role->getAllRoles();
        header('Content-Type: application/json');
        echo json_encode($roles);
        exit;
    }

    public function createRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $role = new Role();
            if ($role->createRole($name, $description)) {
                http_response_code(201);
                echo json_encode(['message' => 'Rol creado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear rol']);
            }
            exit;
        }
        require '../views/roles/form.html';
    }

    public function updateRole($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $role = new Role();
            if ($role->updateRole($id, $name, $description)) {
                echo json_encode(['message' => 'Rol actualizado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar rol']);
            }
            exit;
        }
        require '../views/roles/form.html';
    }

    public function deleteRole($id) {
        $role = new Role();
        if ($role->deleteRole($id)) {
            echo json_encode(['message' => 'Rol eliminado']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar rol']);
        }
        exit;
    }

    public function assignRoleToUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? '';
            $roleId = $_POST['role_id'] ?? '';

            $role = new Role();
            if ($role->assignRoleToUser($userId, $roleId)) {
                echo json_encode(['message' => 'Rol asignado al usuario']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al asignar rol']);
            }
            exit;
        }
    }
}