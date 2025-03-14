<?php
require_once '../config/database.php';

class Acceso {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAccesosByCuenta($cuenta_id) {
        $stmt = $this->pdo->prepare("SELECT u.id, u.email, c.id as cuenta_id FROM usuarios u JOIN accesos a ON u.id = a.id_usuario JOIN cuentas_contables c ON a.id_cuenta_contable = c.id WHERE c.id = ?");
        $stmt->execute([$cuenta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignUsuario($id_usuario, $id_cuenta_contable) {
        $stmt = $this->pdo->prepare("INSERT INTO accesos (id_usuario, id_cuenta_contable) VALUES (?, ?)");
        return $stmt->execute([$id_usuario, $id_cuenta_contable]);
    }

    public function removeUsuario($id_usuario, $id_cuenta_contable) {
        $stmt = $this->pdo->prepare("DELETE FROM accesos WHERE id_usuario = ? AND id_cuenta_contable = ?");
        return $stmt->execute([$id_usuario, $id_cuenta_contable]);
    }
}