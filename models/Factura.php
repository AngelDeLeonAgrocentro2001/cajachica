<?php
require_once '../config/database.php';

class Factura {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllFacturas($searchTerm = '', $cuentaId = null) {
        $query = "SELECT f.*, c.nombre AS cuenta_nombre, b.nombre AS base_nombre 
                  FROM facturas f 
                  LEFT JOIN cuentas_contables c ON f.cuenta_id = c.id 
                  LEFT JOIN bases b ON f.base_id = b.id 
                  WHERE 1=1";
        $params = [];

        if ($searchTerm) {
            $query .= " AND (f.numero_factura LIKE ? OR f.estado LIKE ? OR f.proveedor LIKE ?)";
            $params[] = "%$searchTerm%";
            $params[] = "%$searchTerm%";
            $params[] = "%$searchTerm%";
        }

        if ($cuentaId !== null) {
            $query .= " AND f.cuenta_id = ?";
            $params[] = $cuentaId;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFacturaById($id) {
        $stmt = $this->pdo->prepare("SELECT f.*, c.nombre AS cuenta_nombre, b.nombre AS base_nombre 
                                    FROM facturas f 
                                    LEFT JOIN cuentas_contables c ON f.cuenta_id = c.id 
                                    LEFT JOIN bases b ON f.base_id = b.id 
                                    WHERE f.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function numeroFacturaExists($numeroFactura) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM facturas WHERE numero_factura = ?");
        $stmt->execute([$numeroFactura]);
        return $stmt->fetchColumn() > 0;
    }

    public function numeroFacturaExistsForOther($numeroFactura, $id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM facturas WHERE numero_factura = ? AND id != ?");
        $stmt->execute([$numeroFactura, $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function createFactura($data) {
        $query = "INSERT INTO facturas (cuenta_id, base_id, numero_factura, fecha, proveedor, monto, estado) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['cuenta_id'],
            $data['base_id'],
            $data['numero_factura'],
            $data['fecha'],
            $data['proveedor'],
            $data['monto'],
            $data['estado']
        ]);
    }

    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function updateFactura($data) {
        $query = "UPDATE facturas SET cuenta_id = ?, base_id = ?, numero_factura = ?, fecha = ?, proveedor = ?, monto = ?, estado = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            $data['cuenta_id'],
            $data['base_id'],
            $data['numero_factura'],
            $data['fecha'],
            $data['proveedor'],
            $data['monto'],
            $data['estado'],
            $data['id']
        ]);
    }

    public function updateEstadoFactura($id, $estado) {
        $stmt = $this->pdo->prepare("UPDATE facturas SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    public function deleteFactura($id) {
        $stmt = $this->pdo->prepare("DELETE FROM facturas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}