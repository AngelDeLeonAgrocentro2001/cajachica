<?php
require_once '../config/database.php';

class DetalleLiquidacion {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllDetallesLiquidacion() {
        $stmt = $this->pdo->query("SELECT dl.*, l.id_caja_chica 
                                 FROM detalle_liquidaciones dl 
                                 JOIN liquidaciones l ON dl.id_liquidacion = l.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetalleLiquidacionById($id) {
        $stmt = $this->pdo->prepare("SELECT dl.*, l.id_caja_chica 
                                   FROM detalle_liquidaciones dl 
                                   JOIN liquidaciones l ON dl.id_liquidacion = l.id 
                                   WHERE dl.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createDetalleLiquidacion($id_liquidacion, $no_factura, $regimen, $c_costo, $nit_proveedor, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $codigo_ccta, $descripcion_factura, $p_unitario, $iva, $total_factura, $idp, $inguat, $rutaimagen, $rutarchivopdf, $porcentajeiva, $porcentajeidp, $tipo_combustible) {
        $stmt = $this->pdo->prepare("INSERT INTO detalle_liquidaciones (id_liquidacion, no_factura, regimen, c_costo, nit_proveedor, nombre_proveedor, fecha, bien_servicio, t_gasto, codigo_ccta, descripcion_factura, p_unitario, iva, total_factura, idp, inguat, rutaimagen, rutarchivopdf, porcentajeiva, porcentajeidp, tipo_combustible) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id_liquidacion, $no_factura, $regimen, $c_costo, $nit_proveedor, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $codigo_ccta, $descripcion_factura, $p_unitario, $iva, $total_factura, $idp, $inguat, $rutaimagen, $rutarchivopdf, $porcentajeiva, $porcentajeidp, $tipo_combustible]);
    }

    public function updateDetalleLiquidacion($id, $id_liquidacion, $no_factura, $regimen, $c_costo, $nit_proveedor, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $codigo_ccta, $descripcion_factura, $p_unitario, $iva, $total_factura, $idp, $inguat, $rutaimagen, $rutarchivopdf, $porcentajeiva, $porcentajeidp, $tipo_combustible, $estado) {
        $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET id_liquidacion = ?, no_factura = ?, regimen = ?, c_costo = ?, nit_proveedor = ?, nombre_proveedor = ?, fecha = ?, bien_servicio = ?, t_gasto = ?, codigo_ccta = ?, descripcion_factura = ?, p_unitario = ?, iva = ?, total_factura = ?, idp = ?, inguat = ?, rutaimagen = ?, rutarchivopdf = ?, porcentajeiva = ?, porcentajeidp = ?, tipo_combustible = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$id_liquidacion, $no_factura, $regimen, $c_costo, $nit_proveedor, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $codigo_ccta, $descripcion_factura, $p_unitario, $iva, $total_factura, $idp, $inguat, $rutaimagen, $rutarchivopdf, $porcentajeiva, $porcentajeidp, $tipo_combustible, $estado, $id]);
    }

    public function deleteDetalleLiquidacion($id) {
        $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}