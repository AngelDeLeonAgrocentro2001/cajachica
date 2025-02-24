<?php
require_once '../config/database.php';

class HistorialAprobacion {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllHistorialAprobaciones() {
        $stmt = $this->pdo->query("SELECT ha.*, u.nombre AS nombre_usuario, l.id_caja_chica, dl.no_factura 
                                 FROM historial_aprobaciones ha 
                                 JOIN usuarios u ON ha.id_usuario = u.id 
                                 LEFT JOIN liquidaciones l ON ha.id_liquidacion = l.id 
                                 LEFT JOIN detalle_liquidaciones dl ON ha.id_detalle_liquidacion = dl.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createHistorialAprobacion($id_liquidacion, $id_detalle_liquidacion, $id_usuario, $accion, $comentario) {
        $stmt = $this->pdo->prepare("INSERT INTO historial_aprobaciones (id_liquidacion, id_detalle_liquidacion, id_usuario, accion, comentario) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$id_liquidacion, $id_detalle_liquidacion, $id_usuario, $accion, $comentario]);
    }
}