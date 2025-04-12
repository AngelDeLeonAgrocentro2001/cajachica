<?php
require_once '../config/database.php';

class DetalleLiquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllDetallesLiquidacion() {
        $query = "
            SELECT d.*, l.id_caja_chica, l.fecha_creacion, cc.nombre as nombre_caja_chica
            FROM detalle_liquidaciones d
            JOIN liquidaciones l ON d.id_liquidacion = l.id
            JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            ORDER BY d.id ASC
        ";
        $stmt = $this->pdo->query($query);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($detalles as &$detalle) {
            if (isset($detalle['rutas_archivos'])) {
                $detalle['rutas_archivos'] = json_decode($detalle['rutas_archivos'], true) ?: [];
                foreach ($detalle['rutas_archivos'] as &$ruta) {
                    if ($ruta && strpos($ruta, 'uploads/') === 0) {
                        $ruta = substr($ruta, 7);
                    }
                }
            } else {
                $detalle['rutas_archivos'] = [];
            }
            $detalle['liquidacion'] = $detalle['nombre_caja_chica'] . ' - ' . $detalle['fecha_creacion'];
        }
    
        return $detalles;
    }

    public function getDetalleLiquidacionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM detalle_liquidaciones WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['rutas_archivos'])) {
            $row['rutas_archivos'] = json_decode($row['rutas_archivos'], true) ?: [];
        }
        return $row;
    }

    public function createDetalleLiquidacion($id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo, $cantidad = null, $serie = null, $rutas_archivos = '[]') {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO detalle_liquidaciones (id_liquidacion, tipo_documento, no_factura, nombre_proveedor, nit_proveedor, dpi, fecha, t_gasto, p_unitario, total_factura, estado, id_centro_costo, cantidad, serie, rutas_archivos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo, $cantidad, $serie, $rutas_archivos]);
        } catch (PDOException $e) {
            error_log("Error al crear detalle de liquidación: " . $e->getMessage());
            return false;
        }
    }

    public function updateDetalleLiquidacion($id, $id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo, $cantidad = null, $serie = null, $rutas_archivos = '[]') {
        try {
            $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET id_liquidacion = ?, tipo_documento = ?, no_factura = ?, nombre_proveedor = ?, nit_proveedor = ?, dpi = ?, fecha = ?, t_gasto = ?, p_unitario = ?, total_factura = ?, estado = ?, id_centro_costo = ?, cantidad = ?, serie = ?, rutas_archivos = ? WHERE id = ?");
            return $stmt->execute([$id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo, $cantidad, $serie, $rutas_archivos, $id]);
        } catch (PDOException $e) {
            error_log("Error al actualizar detalle de liquidación: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDetalleLiquidacion($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar detalle de liquidación: " . $e->getMessage());
            return false;
        }
    }

    public function updateEstado($id, $estado) {
        $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }
    
    public function getDetallesByLiquidacionId($id_liquidacion) {
        $query = "SELECT dl.*, cc.nombre as nombre_centro_costo FROM detalle_liquidaciones dl LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id WHERE dl.id_liquidacion = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id_liquidacion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetallesByFecha($fechaInicio, $fechaFin, $idCajaChica = null) {
        $query = "
            SELECT dl.*, l.fecha_creacion as liquidacion_fecha, cc.nombre as caja_chica
            FROM detalle_liquidaciones dl
            LEFT JOIN liquidaciones l ON dl.id_liquidacion = l.id
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE dl.fecha BETWEEN ? AND ?
        ";
        $params = [$fechaInicio, $fechaFin];
    
        if (!empty($idCajaChica)) {
            $query .= " AND l.id_caja_chica = ?";
            $params[] = $idCajaChica;
        }
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}