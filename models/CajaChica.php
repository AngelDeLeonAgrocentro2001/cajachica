<?php
require_once '../config/database.php';

class CajaChica {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllCajasChicas() {
        $stmt = $this->pdo->query("SELECT cc.*, u1.nombre AS nombre_encargado, u2.nombre AS nombre_supervisor 
                                 FROM cajas_chicas cc 
                                 JOIN usuarios u1 ON cc.id_usuario_encargado = u1.id 
                                 JOIN usuarios u2 ON cc.id_supervisor = u2.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCajaChicaById($id) {
        $stmt = $this->pdo->prepare("SELECT cc.*, u1.nombre AS nombre_encargado, u2.nombre AS nombre_supervisor 
                                   FROM cajas_chicas cc 
                                   JOIN usuarios u1 ON cc.id_usuario_encargado = u1.id 
                                   JOIN usuarios u2 ON cc.id_supervisor = u2.id 
                                   WHERE cc.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCajaChica($nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor) {
        $stmt = $this->pdo->prepare("INSERT INTO cajas_chicas (nombre, monto_asignado, monto_disponible, id_usuario_encargado, id_supervisor) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor]);
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