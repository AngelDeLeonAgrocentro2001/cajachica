<?php
require_once '../config/database.php';

class DetalleLiquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllDetallesLiquidacion() {
        $query = "
            SELECT d.*, l.id_caja_chica, l.fecha_creacion, cc.nombre as nombre_caja_chica, cc2.nombre as nombre_centro_costo, tg.name as tipo_gasto, cc3.nombre as cuenta_contable
            FROM detalle_liquidaciones d
            JOIN liquidaciones l ON d.id_liquidacion = l.id
            JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON d.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON d.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON d.id_cuenta_contable = cc3.id
            ORDER BY d.id ASC
        ";
        $stmt = $this->pdo->query($query);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detalles as &$detalle) {
            if (isset($detalle['rutas_archivos'])) {
                $detalle['rutas_archivos'] = json_decode($detalle['rutas_archivos'], true) ?: [];
            } else {
                $detalle['rutas_archivos'] = [];
            }
            $detalle['liquidacion'] = $detalle['nombre_caja_chica'] . ' - ' . $detalle['fecha_creacion'];
        }

        return $detalles;
    }

    public function getDetalleLiquidacionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT d.*, cc.nombre as nombre_centro_costo, tg.name as tipo_gasto, cc2.nombre as cuenta_contable
            FROM detalle_liquidaciones d
            LEFT JOIN centros_costos cc ON d.id_centro_costo = cc.id
            LEFT JOIN tipos_gastos tg ON d.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc2 ON d.id_cuenta_contable = cc2.id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createDetalleLiquidacion($id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo = null, $cantidad = null, $serie = null, $rutas_json = null, $iva = 0, $idp = 0, $inguat = 0, $id_cuenta_contable = null, $tipo_combustible = null, $id_usuario = null, $comentarios = null) {
        try {
            $sql = "INSERT INTO detalle_liquidaciones (
                id_liquidacion, tipo_documento, no_factura, nombre_proveedor, nit_proveedor, dpi, fecha, t_gasto, p_unitario, total_factura, estado, id_centro_costo, cantidad, serie, rutas_archivos, iva, idp, inguat, id_cuenta_contable, tipo_combustible, id_usuario, comentarios
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo, $cantidad, $serie, $rutas_json, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible, $id_usuario, $comentarios
            ]);
        } catch (PDOException $e) {
            error_log("Error en createDetalleLiquidacion: " . $e->getMessage());
            return false;
        }
    }

    public function updateDetalleLiquidacion(
        $id,
        $tipo_documento,
        $no_factura,
        $nombre_proveedor,
        $nit_proveedor,
        $dpi,
        $fecha,
        $t_gasto,
        $subtotal,
        $total_factura,
        $id_centro_costo,
        $iva,
        $idp,
        $inguat,
        $id_cuenta_contable,
        $cantidad = null,
        $serie = null,
        $rutas_archivos = '[]',
        $tipo_combustible = null,
        $comentarios = null
    ) {
        try {
            // Validate id_cuenta_contable
            if (!empty($id_cuenta_contable)) {
                $stmt = $this->pdo->prepare("SELECT id FROM cuentas_contables WHERE id = ? AND id_centro_costo = ? AND estado = 'ACTIVO'");
                $stmt->execute([$id_cuenta_contable, $id_centro_costo]);
                if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                    error_log("Invalid id_cuenta_contable=$id_cuenta_contable for id_centro_costo=$id_centro_costo or inactive.");
                    return false;
                }
            }
    
            $stmt = $this->pdo->prepare("
                UPDATE detalle_liquidaciones
                SET tipo_documento = ?, no_factura = ?, nombre_proveedor = ?, nit_proveedor = ?, dpi = ?, fecha = ?, t_gasto = ?,
                    p_unitario = ?, total_factura = ?, id_centro_costo = ?, cantidad = ?, serie = ?, rutas_archivos = ?, iva = ?, idp = ?, inguat = ?, id_cuenta_contable = ?, tipo_combustible = ?, comentarios = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto,
                $subtotal, $total_factura, $id_centro_costo, $cantidad, $serie, $rutas_archivos, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible, $comentarios, $id
            ]);
            if (!$result) {
                error_log("Failed to update detalle_liquidaciones ID $id. No rows affected.");
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error al actualizar detalle de liquidación ID $id: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDetalleLiquidacion($id) {
        $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getDetalleById($id) {
        try {
            $id = intval($id);
            error_log("Ejecutando getDetalleById para id=$id, tipo=" . gettype($id));
            $stmt = $this->pdo->prepare("
                SELECT dl.*, cc.nombre AS nombre_centro_costo, tg.name AS tipo_gasto, cc2.nombre AS cuenta_contable
                FROM detalle_liquidaciones dl
                LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id
                LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
                LEFT JOIN cuentas_contables cc2 ON dl.id_cuenta_contable = cc2.id
                WHERE dl.id = ?
            ");
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                error_log("No se encontró detalle para ID $id");
            } else {
                error_log("Detalle encontrado para ID $id: id_liquidacion=" . $result['id_liquidacion']);
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error en getDetalleById para ID $id: " . $e->getMessage());
            return false;
        }
    }

    public function updateLiquidacionId($detalleId, $liquidacionId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET id_liquidacion = ? WHERE id = ?");
            return $stmt->execute([$liquidacionId, $detalleId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar id_liquidacion para detalle ID $detalleId: " . $e->getMessage());
            throw new Exception("Error al actualizar id_liquidacion: " . $e->getMessage());
        }
    }

    public function updateEstado($id, $estado) {
        $estadosValidos = [
            'EN_PROCESO',
            'PENDIENTE_AUTORIZACION',
            'PENDIENTE_REVISION_CONTABILIDAD',
            'RECHAZADO_AUTORIZACION',
            'RECHAZADO_POR_CONTABILIDAD',
            'FINALIZADO',
            'DESCARTADO',
            'ENVIADO_A_CORRECCION',
            'EN_CORRECCION'
        ];

        if (!in_array($estado, $estadosValidos)) {
            error_log("Estado no válido para el detalle ID $id: $estado. Estados válidos: " . implode(', ', $estadosValidos));
            throw new Exception("Estado no válido: $estado. Debe be uno de: " . implode(', ', $estadosValidos));
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET estado = ? WHERE id = ?");
            $result = $stmt->execute([$estado, $id]);
            if (!$result) {
                error_log("Fallo al actualizar el estado del detalle ID $id a $estado. No se afectaron filas.");
                throw new Exception("No se pudo actualizar el estado del detalle ID $id a $estado");
            }
            error_log("Estado del detalle ID $id actualizado a $estado con éxito.");
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar el estado del detalle ID $id a $estado: " . $e->getMessage());
            throw new Exception("Error al actualizar el estado del detalle: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error general al actualizar el estado del detalle ID $id a $estado: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateEstadoWithRole($id, $estado, $rol) {
        $estadosValidos = [
            'EN_PROCESO',
            'PENDIENTE_AUTORIZACION',
            'PENDIENTE_REVISION_CONTABILIDAD',
            'RECHAZADO_AUTORIZACION',
            'RECHAZADO_POR_CONTABILIDAD',
            'FINALIZADO',
            'DESCARTADO',
            'ENVIADO_A_CORRECCION',
            'EN_CORRECCION'
        ];

        if (!in_array($estado, $estadosValidos)) {
            error_log("Estado no válido para el detalle ID $id: $estado. Estados válidos: " . implode(', ', $estadosValidos));
            throw new Exception("Estado no válido: $estado. Debe ser uno de: " . implode(', ', $estadosValidos));
        }

        try {
            if ($rol === null) {
                $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET estado = ?, original_role = NULL WHERE id = ?");
                $result = $stmt->execute([$estado, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE detalle_liquidaciones SET estado = ?, original_role = ? WHERE id = ?");
                $result = $stmt->execute([$estado, $rol, $id]);
            }

            if (!$result) {
                error_log("Fallo al actualizar el estado del detalle ID $id a $estado con rol $rol. No se afectaron filas.");
                throw new Exception("No se pudo actualizar el estado del detalle ID $id a $estado con rol $rol");
            }
            error_log("Estado del detalle ID $id actualizado a $estado con rol $rol con éxito.");
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar el estado del detalle ID $id a $estado con rol $rol: " . $e->getMessage());
            throw new Exception("Error al actualizar el estado del detalle: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error general al actualizar el estado del detalle ID $id a $estado con rol $rol: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateEstadoWithComment($id, $estado, $rol, $comment, $supervisorId = null, $contadorId = null) {
        $estadosValidos = [
            'EN_PROCESO',
            'PENDIENTE_AUTORIZACION',
            'PENDIENTE_REVISION_CONTABILIDAD',
            'RECHAZADO_AUTORIZACION',
            'RECHAZADO_POR_CONTABILIDAD',
            'FINALIZADO',
            'DESCARTADO',
            'ENVIADO_A_CORRECCION',
            'EN_CORRECCION'
        ];
    
        if (!in_array($estado, $estadosValidos)) {
            error_log("Estado no válido para el detalle ID $id: $estado. Estados válidos: " . implode(', ', $estadosValidos));
            throw new Exception("Estado no válido: $estado. Debe ser uno de: " . implode(', ', $estadosValidos));
        }
    
        $rolUpper = strtoupper($rol);
        $isContabilidadRole = strpos($rolUpper, 'CONTADOR') !== false || strpos($rolUpper, 'CONTABILIDAD') !== false;
        $isSupervisorRole = strpos($rolUpper, 'SUPERVISOR') !== false;
    
        error_log("updateEstadoWithComment: detalle ID=$id, estado=$estado, rol=$rol, isContabilidadRole=" . ($isContabilidadRole ? 'SÍ' : 'NO') . ", isSupervisorRole=" . ($isSupervisorRole ? 'SÍ' : 'NO') . ", contadorId=" . ($contadorId ?? 'NULL'));
    
        try {
            if ($isContabilidadRole) {
                error_log("Ejecutando rama CONTABILIDAD para detalle ID $id con contadorId=$contadorId");
                $stmt = $this->pdo->prepare("
                    UPDATE detalle_liquidaciones
                    SET estado = ?, original_role = ?, correccion_comentario = ?, id_supervisor_correccion = NULL, id_contador_correccion = ?
                    WHERE id = ?
                ");
                $result = $stmt->execute([$estado, 'CONTABILIDAD', $comment, $contadorId, $id]);
            } elseif ($isSupervisorRole) {
                error_log("Ejecutando rama SUPERVISOR para detalle ID $id con supervisorId=$supervisorId");
                $stmt = $this->pdo->prepare("
                    UPDATE detalle_liquidaciones
                    SET estado = ?, original_role = ?, correccion_comentario = ?, id_supervisor_correccion = ?, id_contador_correccion = NULL
                    WHERE id = ?
                ");
                $result = $stmt->execute([$estado, 'SUPERVISOR', $comment, $supervisorId, $id]);
            } else {
                error_log("Ejecutando rama DEFAULT para detalle ID $id");
                $stmt = $this->pdo->prepare("
                    UPDATE detalle_liquidaciones
                    SET estado = ?, original_role = NULL, correccion_comentario = ?, id_supervisor_correccion = NULL, id_contador_correccion = NULL
                    WHERE id = ?
                ");
                $result = $stmt->execute([$estado, $comment, $id]);
            }
    
            if (!$result) {
                error_log("Fallo al actualizar el detalle ID $id a $estado con comentario. No se afectaron filas.");
                throw new Exception("No se pudo actualizar el detalle ID $id a $estado con comentario");
            }
            error_log("Detalle ID $id actualizado a $estado con comentario, supervisorId=" . ($supervisorId ?? 'N/A') . ", contadorId=" . ($contadorId ?? 'N/A') . " con éxito.");
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar el detalle ID $id a $estado con comentario: " . $e->getMessage());
            throw new Exception("Error al actualizar el detalle: " . $e->getMessage());
        }
    }

    public function getDetallesByLiquidacionId($id_liquidacion) {
        $stmt = $this->pdo->prepare("
            SELECT dl.*, cc.nombre AS nombre_centro_costo, tg.name AS tipo_gasto, cc2.nombre AS cuenta_contable,
                   s.nombre AS nombre_supervisor_correccion, c.nombre AS nombre_contador_correccion
            FROM detalle_liquidaciones dl
            LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc2 ON dl.id_cuenta_contable = cc2.id
            LEFT JOIN usuarios s ON dl.id_supervisor_correccion = s.id
            LEFT JOIN usuarios c ON dl.id_contador_correccion = c.id
            WHERE dl.id_liquidacion = ?
        ");
        $stmt->execute([$id_liquidacion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetallesByFecha($fechaInicio, $fechaFin, $idCajaChica = null) {
        $query = "
            SELECT dl.*, l.fecha_creacion as liquidacion_fecha, cc.nombre as caja_chica, cc2.nombre as nombre_centro_costo, tg.name as tipo_gasto, cc3.nombre as cuenta_contable
            FROM detalle_liquidaciones dl
            LEFT JOIN liquidaciones l ON dl.id_liquidacion = l.id
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON dl.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON dl.id_cuenta_contable = cc3.id
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

    public function getDetallesByLiquidacionIdAndEstado($id_liquidacion, $estado) {
        $stmt = $this->pdo->prepare("
            SELECT dl.*, cc.nombre AS nombre_centro_costo, tg.name AS tipo_gasto, cc2.nombre AS cuenta_contable
            FROM detalle_liquidaciones dl
            LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc2 ON dl.id_cuenta_contable = cc2.id
            WHERE dl.id_liquidacion = ? AND dl.estado = ?
        ");
        $stmt->execute([$id_liquidacion, $estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCorrectedDetallesForSupervisors($supervisorId = null) {
        $query = "
            SELECT dl.*, 
                   l.id as liquidacion_id, 
                   l.id_caja_chica, 
                   l.fecha_creacion, 
                   cc.nombre as nombre_caja_chica, 
                   cc2.nombre as nombre_centro_costo, 
                   tg.name as tipo_gasto, 
                   cc3.nombre as cuenta_contable
            FROM detalle_liquidaciones dl
            LEFT JOIN liquidaciones l ON dl.id_liquidacion = l.id
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON dl.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON dl.id_cuenta_contable = cc3.id
            WHERE dl.estado = 'PENDIENTE_AUTORIZACION'
            AND UPPER(dl.original_role) LIKE '%SUPERVISOR%'
            AND dl.correccion_comentario IS NOT NULL
        ";
        $params = [];
    
        if ($supervisorId !== null) {
            $query .= " AND dl.id_supervisor_correccion = ?";
            $params[] = $supervisorId;
            error_log("getAllCorrectedDetallesForSupervisors: Filtering for supervisorId=$supervisorId");
        } else {
            error_log("getAllCorrectedDetallesForSupervisors: No supervisorId filter applied");
        }
    
        $query .= " ORDER BY dl.id ASC";
    
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("getAllCorrectedDetallesForSupervisors: Retrieved " . count($detalles) . " details");
            foreach ($detalles as $detalle) {
                error_log("Detail ID: {$detalle['id']}, Liquidacion ID: {$detalle['liquidacion_id']}, Estado: {$detalle['estado']}, Supervisor Correccion: {$detalle['id_supervisor_correccion']}");
            }
    
            foreach ($detalles as &$detalle) {
                if (isset($detalle['rutas_archivos'])) {
                    $detalle['rutas_archivos'] = json_decode($detalle['rutas_archivos'], true) ?: [];
                } else {
                    $detalle['rutas_archivos'] = [];
                }
                $detalle['liquidacion'] = isset($detalle['nombre_caja_chica']) ? $detalle['nombre_caja_chica'] . ' - ' . $detalle['fecha_creacion'] : 'Independiente';
            }
    
            return $detalles;
        } catch (PDOException $e) {
            error_log("getAllCorrectedDetallesForSupervisors: Error executing query: " . $e->getMessage());
            return [];
        }
    }

    public function getCorrectedDetallesForContador($contadorId) {
        $query = "
            SELECT dl.*, 
                   l.id as liquidacion_id, 
                   l.id_caja_chica, 
                   l.fecha_creacion, 
                   cc.nombre as nombre_caja_chica, 
                   cc2.nombre as nombre_centro_costo, 
                   tg.name as tipo_gasto, 
                   cc3.nombre as cuenta_contable
            FROM detalle_liquidaciones dl
            JOIN liquidaciones l ON dl.id_liquidacion = l.id
            JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON dl.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON dl.id_cuenta_contable = cc3.id
            WHERE dl.estado = 'PENDIENTE_REVISION_CONTABILIDAD'
            AND dl.original_role = 'CONTABILIDAD'
            AND dl.id_contador_correccion = ?
            AND dl.correccion_comentario IS NOT NULL
            ORDER BY dl.id ASC
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$contadorId]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detalles as &$detalle) {
            if (isset($detalle['rutas_archivos'])) {
                $detalle['rutas_archivos'] = json_decode($detalle['rutas_archivos'], true) ?: [];
            } else {
                $detalle['rutas_archivos'] = [];
            }
            $detalle['liquidacion'] = $detalle['nombre_caja_chica'] . ' - ' . $detalle['fecha_creacion'];
        }

        return $detalles;
    }

    public function updateSapFields($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE detalle_liquidaciones
                SET sap_doc_entry = ?, sap_doc_num = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['sap_doc_entry'],
                $data['sap_doc_num'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar SAP fields para detalle ID $id: " . $e->getMessage());
            return false;
        }
    }
}
?>