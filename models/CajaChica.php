<?php
require_once '../config/database.php';

class CajaChica {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCajasChicas() {
        $stmt = $this->pdo->query("SELECT * FROM cajas_chicas");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log('Cajas Chicas obtenidas: ' . print_r($result, true));
        return $result;
    }

    public function getCajaChicaById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM cajas_chicas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCajaChica($nombre, $monto_asignado, $id_usuario_encargado, $id_supervisor, $estado) {
        $stmt = $this->pdo->prepare("INSERT INTO cajas_chicas (nombre, monto_asignado, monto_disponible, id_usuario_encargado, id_supervisor, estado) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, $monto_asignado, $monto_asignado, $id_usuario_encargado, $id_supervisor, $estado]);
    }

    public function updateCajaChica($id, $nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor, $estado) {
        $stmt = $this->pdo->prepare("UPDATE cajas_chicas SET nombre = ?, monto_asignado = ?, monto_disponible = ?, id_usuario_encargado = ?, id_supervisor = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor, $estado, $id]);
    }

    public function deleteCajaChica($id) {
        $stmt = $this->pdo->prepare("DELETE FROM cajas_chicas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}