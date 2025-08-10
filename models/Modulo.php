<?php
require_once '../config/database.php';

class Modulo {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllModulos() {
        $stmt = $this->pdo->query("SELECT * FROM modulos WHERE estado = 'ACTIVO'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModuloById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM modulos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}