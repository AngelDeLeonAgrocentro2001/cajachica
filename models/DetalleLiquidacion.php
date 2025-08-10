<?php
require_once '../config/database.php';

class DetalleLiquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAllDetallesLiquidacion() {
        $query = "
            SELECT d.*, l.id_caja_chica, l.fecha_creacion, cc.nombre as nombre_caja_chica, 
                   cc2.nombre as nombre_centro_costo, tg.name as tipo_gasto, 
                   cc3.nombre as cuenta_contable, u.nombre as nombre_usuario
            FROM detalle_liquidaciones d
            JOIN liquidaciones l ON d.id_liquidacion = l.id
            JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON d.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON d.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON d.id_cuenta_contable = cc3.id
            LEFT JOIN usuarios u ON d.id_usuario = u.id
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
            $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($detalle['id']);
        }

        return $detalles;
    }

    public function getDetalleLiquidacionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT d.*, cc.nombre as nombre_centro_costo, tg.name as tipo_gasto, 
                   cc2.nombre as cuenta_contable, u.nombre as nombre_usuario
            FROM detalle_liquidaciones d
            LEFT JOIN centros_costos cc ON d.id_centro_costo = cc.id
            LEFT JOIN tipos_gastos tg ON d.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc2 ON d.id_cuenta_contable = cc2.id
            LEFT JOIN usuarios u ON d.id_usuario = u.id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($detalle) {
            $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($id);
        }
        return $detalle;
    }

    public function createDetalleLiquidacion($id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo = null, $cantidad = null, $serie = null, $rutas_json = null, $iva = 0, $idp = 0, $inguat = 0, $id_cuenta_contable = null, $tipo_combustible = null, $id_usuario = null, $comentarios = null, $porcentaje = 100.00, $nombre_cuenta_contable = null, $es_principal = 0, $grupo_id = 0) {
        try {
            $sql = "INSERT INTO detalle_liquidaciones (
                id_liquidacion, tipo_documento, no_factura, nombre_proveedor, nit_proveedor, dpi, fecha, t_gasto, 
                p_unitario, total_factura, estado, id_centro_costo, cantidad, serie, rutas_archivos, iva, idp, 
                inguat, id_cuenta_contable, nombre_cuenta_contable, tipo_combustible, id_usuario, comentarios, porcentaje, es_principal, grupo_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, 
                $t_gasto, $p_unitario, $total_factura, $estado, $id_centro_costo, $cantidad, $serie, $rutas_json, 
                $iva, $idp, $inguat, $id_cuenta_contable, $nombre_cuenta_contable, $tipo_combustible, $id_usuario, $comentarios, $porcentaje, $es_principal, $grupo_id
            ]);
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en createDetalleLiquidacion: " . $e->getMessage());
            return false;
        }
    }

    public function updateDetalleLiquidacion($id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $p_unitario, $total_factura, $id_centro_costo, $iva, $idp, $inguat, $id_cuenta_contable, $cantidad, $serie, $rutas_json, $tipo_combustible, $comentarios, $porcentaje, $nombre_cuenta_contable, $estado = null, $grupo_id = 0) {
        try {
            $sql = "
                UPDATE detalle_liquidaciones
                SET tipo_documento = ?, no_factura = ?, nombre_proveedor = ?, nit_proveedor = ?, dpi = ?, 
                    fecha = ?, t_gasto = ?, p_unitario = ?, total_factura = ?, id_centro_costo = ?, 
                    iva = ?, idp = ?, inguat = ?, id_cuenta_contable = ?, cantidad = ?, serie = ?, 
                    rutas_archivos = ?, tipo_combustible = ?, comentarios = ?, porcentaje = ?, 
                    nombre_cuenta_contable = ?, es_principal = 1, grupo_id = ?
            ";
            $params = [
                $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto,
                $p_unitario, $total_factura, $id_centro_costo, $iva, $idp, $inguat, $id_cuenta_contable,
                $cantidad, $serie, $rutas_json, $tipo_combustible, $comentarios, $porcentaje, 
                $nombre_cuenta_contable, $grupo_id
            ];
            
            if ($estado !== null) {
                $sql .= ", estado = ?";
                $params[] = $estado;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
    
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
    
            if (!$result) {
                error_log("Error al actualizar detalle ID $id: No se afectaron filas.");
                return false;
            }
    
            error_log("Detalle ID $id actualizado con éxito. grupo_id=$grupo_id");
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar detalle ID $id: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDetalleLiquidacion($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar detalle ID $id: " . $e->getMessage());
            return false;
        }
    }

    public function createCentroCosto($id_detalle_liquidacion, $id_centro_costo, $porcentaje) {
        try {
            $sql = "INSERT INTO detalle_centros_costo (id_detalle_liquidacion, id_centro_costo, porcentaje) 
                    VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_detalle_liquidacion, $id_centro_costo, $porcentaje]);
        } catch (PDOException $e) {
            error_log("Error en createCentroCosto: " . $e->getMessage());
            return false;
        }
    }

    public function updateCentroCosto($id_detalle_liquidacion, $id_centro_costo, $porcentaje) {
        try {
            $sql = "INSERT INTO detalle_centros_costo (id_detalle_liquidacion, id_centro_costo, porcentaje) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE porcentaje = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_detalle_liquidacion, $id_centro_costo, $porcentaje, $porcentaje]);
        } catch (PDOException $e) {
            error_log("Error en updateCentroCosto: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCentrosCosto($id_detalle_liquidacion) {
        try {
            $sql = "DELETE FROM detalle_centros_costo WHERE id_detalle_liquidacion = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_detalle_liquidacion]);
        } catch (PDOException $e) {
            error_log("Error en deleteCentrosCosto: " . $e->getMessage());
            return false;
        }
    }

    public function getCentrosCostoByDetalle($id_detalle_liquidacion) {
        try {
            $sql = "SELECT dcc.*, cc.nombre AS nombre_centro_costo 
                    FROM detalle_centros_costo dcc 
                    JOIN centros_costos cc ON dcc.id_centro_costo = cc.id 
                    WHERE dcc.id_detalle_liquidacion = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_detalle_liquidacion]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getCentrosCostoByDetalle: " . $e->getMessage());
            return [];
        }
    }

    public function getDetalleById($id) {
        try {
            $id = intval($id);
            error_log("Ejecutando getDetalleById para id=$id, tipo=" . gettype($id));
            $stmt = $this->pdo->prepare("
                SELECT dl.*, cc.nombre AS nombre_centro_costo, tg.name AS tipo_gasto, 
                       cc2.nombre AS cuenta_contable, u.nombre AS nombre_usuario
                FROM detalle_liquidaciones dl
                LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id
                LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
                LEFT JOIN cuentas_contables cc2 ON dl.id_cuenta_contable = cc2.id
                LEFT JOIN usuarios u ON dl.id_usuario = u.id
                WHERE dl.id = ?
            ");
            $stmt->bindValue(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $result['centros_costo'] = $this->getCentrosCostoByDetalle($id);
                error_log("Detalle encontrado para ID $id: id_liquidacion=" . $result['id_liquidacion']);
            } else {
                error_log("No se encontró detalle para ID $id");
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
            'EN_PROCESO', 'PENDIENTE_AUTORIZACION', 'PENDIENTE_REVISION_CONTABILIDAD',
            'RECHAZADO_AUTORIZACION', 'RECHAZADO_POR_CONTABILIDAD', 'FINALIZADO',
            'DESCARTADO', 'ENVIADO_A_CORRECCION', 'EN_CORRECCION'
        ];

        if (!in_array($estado, $estadosValidos)) {
            error_log("Estado no válido para el detalle ID $id: $estado. Estados válidos: " . implode(', ', $estadosValidos));
            throw new Exception("Estado no válido: $estado. Debe ser uno de: " . implode(', ', $estadosValidos));
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
        }
    }

    public function updateEstadoWithRole($id, $estado, $rol) {
        $estadosValidos = [
            'EN_PROCESO', 'PENDIENTE_AUTORIZACION', 'PENDIENTE_REVISION_CONTABILIDAD',
            'RECHAZADO_AUTORIZACION', 'RECHAZADO_POR_CONTABILIDAD', 'FINALIZADO',
            'DESCARTADO', 'ENVIADO_A_CORRECCION', 'EN_CORRECCION'
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
        }
    }

    public function updateEstadoWithComment($id, $estado, $rol, $comment, $supervisorId = null, $contadorId = null) {
        $estadosValidos = [
            'EN_PROCESO', 'PENDIENTE_AUTORIZACION', 'PENDIENTE_REVISION_CONTABILIDAD',
            'RECHAZADO_AUTORIZACION', 'RECHAZADO_POR_CONTABILIDAD', 'FINALIZADO',
            'DESCARTADO', 'ENVIADO_A_CORRECCION', 'EN_CORRECCION'
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
            SELECT dl.*, 
                   cc.nombre AS nombre_centro_costo, 
                   cc.codigo AS codigo_centro_costo, 
                   tg.name AS tipo_gasto, 
                   cc2.nombre AS cuenta_contable,
                   s.nombre AS nombre_supervisor_correccion, 
                   c.nombre AS nombre_contador_correccion, 
                   u.nombre AS nombre_usuario
            FROM detalle_liquidaciones dl
            LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc2 ON dl.id_cuenta_contable = cc2.id
            LEFT JOIN usuarios s ON dl.id_supervisor_correccion = s.id
            LEFT JOIN usuarios c ON dl.id_contador_correccion = c.id
            LEFT JOIN usuarios u ON dl.id_usuario = u.id
            WHERE dl.id_liquidacion = ?
        ");
        $stmt->execute([$id_liquidacion]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($detalles as &$detalle) {
            $detalle['subtotal'] = floatval($detalle['p_unitario'] ?? $detalle['total_factura']);
            $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($detalle['id']);
            // Construir el formato similar al foreach
            $detalle['nombre_centro_costo'] = $detalle['nombre_centro_costo'] . ' / ' . ($detalle['codigo_centro_costo'] ?? 'N/A');
        }
        unset($detalle);
        return $detalles;
    }

    public function getDetallesByFecha($fechaInicio, $fechaFin, $idCajaChica = null) {
        $query = "
            SELECT dl.*, l.fecha_creacion as liquidacion_fecha, cc.nombre as caja_chica, 
                   cc2.nombre as nombre_centro_costo, tg.name as tipo_gasto, 
                   cc3.nombre as cuenta_contable, u.nombre as nombre_usuario
            FROM detalle_liquidaciones dl
            LEFT JOIN liquidaciones l ON dl.id_liquidacion = l.id
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON dl.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON dl.id_cuenta_contable = cc3.id
            LEFT JOIN usuarios u ON dl.id_usuario = u.id
            WHERE dl.fecha BETWEEN ? AND ?
        ";
        $params = [$fechaInicio, $fechaFin];

        if (!empty($idCajaChica)) {
            $query .= " AND l.id_caja_chica = ?";
            $params[] = $idCajaChica;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($detalles as &$detalle) {
            $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($detalle['id']);
        }
        return $detalles;
    }

    public function getDetallesByLiquidacionIdAndEstado($id_liquidacion, $estado) {
        $stmt = $this->pdo->prepare("
            SELECT dl.*, cc.nombre AS nombre_centro_costo, tg.name AS tipo_gasto, 
                   cc2.nombre AS cuenta_contable, u.nombre AS nombre_usuario
            FROM detalle_liquidaciones dl
            LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc2 ON dl.id_cuenta_contable = cc2.id
            LEFT JOIN usuarios u ON dl.id_usuario = u.id
            WHERE dl.id_liquidacion = ? AND dl.estado = ?
        ");
        $stmt->execute([$id_liquidacion, $estado]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($detalles as &$detalle) {
            $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($detalle['id']);
        }
        return $detalles;
    }

    public function getAllCorrectedDetallesForSupervisors($supervisorId = null) {
        $query = "
            SELECT dl.*, l.id as liquidacion_id, l.id_caja_chica, l.fecha_creacion, 
                   cc.nombre as nombre_caja_chica, cc2.nombre as nombre_centro_costo, 
                   tg.name as tipo_gasto, cc3.nombre as cuenta_contable, 
                   u.nombre as nombre_usuario
            FROM detalle_liquidaciones dl
            LEFT JOIN liquidaciones l ON dl.id_liquidacion = l.id
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON dl.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON dl.id_cuenta_contable = cc3.id
            LEFT JOIN usuarios u ON dl.id_usuario = u.id
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
            foreach ($detalles as &$detalle) {
                if (isset($detalle['rutas_archivos'])) {
                    $detalle['rutas_archivos'] = json_decode($detalle['rutas_archivos'], true) ?: [];
                } else {
                    $detalle['rutas_archivos'] = [];
                }
                $detalle['liquidacion'] = isset($detalle['nombre_caja_chica']) ? $detalle['nombre_caja_chica'] . ' - ' . $detalle['fecha_creacion'] : 'Independiente';
                $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($detalle['id']);
            }
    
            return $detalles;
        } catch (PDOException $e) {
            error_log("getAllCorrectedDetallesForSupervisors: Error executing query: " . $e->getMessage());
            return [];
        }
    }

    public function getCorrectedDetallesForContador($contadorId) {
        $query = "
            SELECT dl.*, l.id as liquidacion_id, l.id_caja_chica, l.fecha_creacion, 
                   cc.nombre as nombre_caja_chica, cc2.nombre as nombre_centro_costo, 
                   tg.name as tipo_gasto, cc3.nombre as cuenta_contable, 
                   u.nombre as nombre_usuario
            FROM detalle_liquidaciones dl
            JOIN liquidaciones l ON dl.id_liquidacion = l.id
            JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN centros_costos cc2 ON dl.id_centro_costo = cc2.id
            LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
            LEFT JOIN cuentas_contables cc3 ON dl.id_cuenta_contable = cc3.id
            LEFT JOIN usuarios u ON dl.id_usuario = u.id
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
            $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($detalle['id']);
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

    public function updateDteUsado($serie, $numero_dte) {
        try {
            $numero_dte = str_replace('-', '', $numero_dte);
            $stmt = $this->pdo->prepare("UPDATE dte SET usado = 'Y' WHERE serie = ? AND numero_dte = ?");
            $stmt->execute([$serie, $numero_dte]);
            $rowCount = $stmt->rowCount();
            error_log("UPDATE dte query executed: serie=$serie, numero_dte=$numero_dte, rows affected=$rowCount");
            if ($rowCount === 0) {
                error_log("No se actualizó el campo usado para serie=$serie, numero_dte=$numero_dte");
                $stmt = $this->pdo->prepare("SELECT serie, numero_dte, usado FROM dte WHERE serie = ? AND numero_dte = ?");
                $stmt->execute([$serie, $numero_dte]);
                $dte = $stmt->fetch(PDO::FETCH_ASSOC);
                error_log("DTE lookup result: " . print_r($dte, true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error al actualizar usado en dte para serie=$serie, numero_dte=$numero_dte: " . $e->getMessage());
            throw new Exception("Error al actualizar DTE: " . $e->getMessage());
        }
    }

    private function getCuentaContableNombre($id_cuenta_contable) {
        if (empty($id_cuenta_contable)) {
            return 'N/A';
        }
        $stmt = $this->pdo->prepare("SELECT nombre FROM cuentas_contables WHERE id = ?");
        $stmt->execute([$id_cuenta_contable]);
        return $stmt->fetchColumn() ?: 'N/A';
    }

    public function getDetallesByGrupoId($grupoId, $id_liquidacion) {
        try {
            $grupoId = intval($grupoId);
            $id_liquidacion = intval($id_liquidacion);
            error_log("Ejecutando getDetallesByGrupoId para grupo_id=$grupoId, id_liquidacion=$id_liquidacion");
    
            $sql = "
                SELECT dl.*, 
                       cc.nombre AS nombre_centro_costo, 
                       cc.codigo AS codigo_centro_costo, 
                       tg.name AS tipo_gasto, 
                       cc2.nombre AS cuenta_contable,
                       s.nombre AS nombre_supervisor_correccion, 
                       c.nombre AS nombre_contador_correccion, 
                       u.nombre AS nombre_usuario
                FROM detalle_liquidaciones dl
                LEFT JOIN centros_costos cc ON dl.id_centro_costo = cc.id
                LEFT JOIN tipos_gastos tg ON dl.t_gasto = tg.name
                LEFT JOIN cuentas_contables cc2 ON dl.id_cuenta_contable = cc2.id
                LEFT JOIN usuarios s ON dl.id_supervisor_correccion = s.id
                LEFT JOIN usuarios c ON dl.id_contador_correccion = c.id
                LEFT JOIN usuarios u ON dl.id_usuario = u.id
                WHERE dl.id_liquidacion = ?
            ";
            $params = [$id_liquidacion];
    
            if ($grupoId > 0) {
                $sql .= " AND dl.grupo_id = ?";
                $params[] = $grupoId;
            } else {
                $sql .= " AND dl.grupo_id = 0 AND dl.es_principal = 1";
            }
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($detalles as &$detalle) {
                $detalle['subtotal'] = floatval($detalle['p_unitario'] ?? $detalle['total_factura']);
                $detalle['centros_costo'] = $this->getCentrosCostoByDetalle($detalle['id']);
                $detalle['nombre_centro_costo'] = $detalle['nombre_centro_costo'] . ' / ' . ($detalle['codigo_centro_costo'] ?? 'N/A');
                $detalle['rutas_archivos'] = json_decode($detalle['rutas_archivos'] ?? '[]', true) ?: [];
            }
            unset($detalle);
    
            error_log("getDetallesByGrupoId: Encontrados " . count($detalles) . " detalles para grupo_id=$grupoId, id_liquidacion=$id_liquidacion");
            return $detalles;
        } catch (PDOException $e) {
            error_log("Error en getDetallesByGrupoId para grupo_id=$grupoId, id_liquidacion=$id_liquidacion: " . $e->getMessage());
            return [];
        }
    }
}
?>