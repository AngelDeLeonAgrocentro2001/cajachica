<?php
require_once '../config/database.php';

class Acceso {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    // Obtener los permisos asignados a un usuario para un módulo específico
    public function getPermisosByUsuarioAndModulo($id_usuario, $id_modulo) {
        $stmt = $this->pdo->prepare("SELECT permiso FROM accesos_permisos WHERE id_usuario = ? AND id_modulo = ? AND estado = 'ACTIVO'");
        $stmt->execute([$id_usuario, $id_modulo]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    // Obtener todos los módulos y permisos asignados a un usuario
    public function getUserModulesAndPermisos($id_usuario) {
        $stmt = $this->pdo->prepare("
            SELECT m.id, m.nombre, m.permiso_predeterminado, ap.permiso
            FROM modulos m
            LEFT JOIN accesos_permisos ap ON m.id = ap.id_modulo AND ap.id_usuario = ? AND ap.estado = 'ACTIVO'
            WHERE m.estado = 'ACTIVO'
        ");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Asignar un permiso a un usuario para un módulo
    public function assignPermiso($id_usuario, $id_modulo, $permiso) {
        // Verificar si ya existe el permiso
        $stmt = $this->pdo->prepare("SELECT id FROM accesos_permisos WHERE id_usuario = ? AND id_modulo = ? AND permiso = ?");
        $stmt->execute([$id_usuario, $id_modulo, $permiso]);
        if ($stmt->fetch()) {
            // Si existe, actualizar el estado a ACTIVO
            $stmt = $this->pdo->prepare("UPDATE accesos_permisos SET estado = 'ACTIVO', created_at = NOW() WHERE id_usuario = ? AND id_modulo = ? AND permiso = ?");
            return $stmt->execute([$id_usuario, $id_modulo, $permiso]);
        } else {
            // Si no existe, insertar un nuevo registro
            $stmt = $this->pdo->prepare("INSERT INTO accesos_permisos (id_usuario, id_modulo, permiso, estado, created_at) VALUES (?, ?, ?, 'ACTIVO', NOW())");
            return $stmt->execute([$id_usuario, $id_modulo, $permiso]);
        }
    }

    // Remover un permiso de un usuario para un módulo
    public function removePermiso($id_usuario, $id_modulo, $permiso) {
        $stmt = $this->pdo->prepare("UPDATE accesos_permisos SET estado = 'INACTIVO' WHERE id_usuario = ? AND id_modulo = ? AND permiso = ?");
        return $stmt->execute([$id_usuario, $id_modulo, $permiso]);
    }

    // Obtener todos los módulos disponibles
    public function getAllModules() {
        $stmt = $this->pdo->prepare("SELECT * FROM modulos WHERE estado = 'ACTIVO'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}