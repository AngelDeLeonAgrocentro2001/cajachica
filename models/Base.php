<?php
require_once '../config/database.php';

class Base {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getPdo();
        } catch (Exception $e) {
            throw new Exception('No se pudo conectar a la base de datos: ' . $e->getMessage());
        }
    }

    public function getAllBases() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM bases");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en Base.php: ' . $e->getMessage());
            throw new Exception('Error al consultar las bases: ' . $e->getMessage());
        }
    }
}