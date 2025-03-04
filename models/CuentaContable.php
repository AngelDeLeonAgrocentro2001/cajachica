<?php
require_once '../config/database.php';

class CuentaContable {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllCuentas() {
        $stmt = $this->pdo->query("SELECT * FROM cuentas_contables");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCuentaById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM cuentas_contables WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCuenta($codigo, $nombre, $estado) {
        $stmt = $this->pdo->prepare("INSERT INTO cuentas_contables (codigo, nombre, estado) VALUES (?, ?, ?)");
        return $stmt->execute([$codigo, $nombre, $estado]);
    }

    public function updateCuenta($id, $codigo, $nombre, $estado) {
        $stmt = $this->pdo->prepare("UPDATE cuentas_contables SET codigo = ?, nombre = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$codigo, $nombre, $estado, $id]);
    }

    public function deleteCuenta($id) {
        $stmt = $this->pdo->prepare("DELETE FROM cuentas_contables WHERE id = ?");
        return $stmt->execute([$id]);
    }
}