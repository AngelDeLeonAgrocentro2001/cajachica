<?php
require_once '../config/database.php';

class Liquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function checkAndFinalizeOldLiquidaciones() {
    try {
        $twoWeeksAgo = date('Y-m-d 00:00:00', strtotime('-2 weeks'));
        error_log("Auto-expirado liquidaciones creadas antes de: $twoWeeksAgo");
        
        // Cambiar estado a EXPIRADO en lugar de FINALIZADO
        $query = "
            UPDATE liquidaciones 
            SET estado = 'EXPIRADO', 
                updated_at = NOW() 
            WHERE estado IN ('EN_PROCESO', 'PENDIENTE_REVISION_CONTABILIDAD', 'PENDIENTE_AUTORIZACION')
            AND fecha_creacion <= ?
        ";
        
        $stmt = $this->pdo->prepare($query);
        $result = $stmt->execute([$twoWeeksAgo]);
        $rowCount = $stmt->rowCount();
        
        error_log("Auto-expiradas $rowCount liquidaciones antiguas");
        return $rowCount;
        
    } catch (PDOException $e) {
        error_log("Error en checkAndFinalizeOldLiquidaciones: " . $e->getMessage());
        return 0;
    }
}

public function getLiquidacionAgeInWeeks($fechaCreacion) {
    $creacion = new DateTime($fechaCreacion);
    $hoy = new DateTime();
    $diferencia = $creacion->diff($hoy);
    return floor($diferencia->days / 7);
}

public function hasRecentMovements($liquidacionId, $weeks = 2) {
    $dateLimit = date('Y-m-d H:i:s', strtotime("-$weeks weeks"));
    
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as count 
        FROM detalle_liquidaciones 
        WHERE id_liquidacion = ? 
        AND (created_at > ? OR updated_at > ?)
    ");
    $stmt->execute([$liquidacionId, $dateLimit, $dateLimit]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'] > 0;
}

    public function getAllLiquidaciones($idUsuario = null, $idSupervisor = null, $estado = null, $idContador = null) {
        $query = "
            SELECT l.*, 
                   cc.nombre AS nombre_caja_chica,
                   u.nombre AS nombre_usuario,
                   s.nombre AS nombre_supervisor,
                   c.nombre AS nombre_contador
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN usuarios u ON l.id_usuario = u.id
            LEFT JOIN usuarios s ON l.id_supervisor = s.id
            LEFT JOIN usuarios c ON l.id_contador = c.id
            WHERE 1=1
        ";
        $params = [];
    
        if ($idUsuario !== null) {
            $query .= " AND l.id_usuario = ?";
            $params[] = $idUsuario;
        }
    
        if ($idSupervisor !== null) {
            $query .= " AND l.id_supervisor = ?";
            $params[] = $idSupervisor;
        }
    
        if ($estado !== null) {
            $query .= " AND l.estado = ?";
            $params[] = $estado;
        }
    
        if ($idContador !== null) {
            $query .= " AND l.id_contador = ?";
            $params[] = $idContador;
        }
    
        $query .= " ORDER BY l.id DESC";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        error_log("getAllLiquidaciones - Query: $query");
        error_log("getAllLiquidaciones - Params: " . json_encode($params));
        error_log("getAllLiquidaciones - ID Usuario: " . ($idUsuario ?? 'N/A') . 
                  ", ID Supervisor: " . ($idSupervisor ?? 'N/A') . 
                  ", ID Contador: " . ($idContador ?? 'N/A') . 
                  ", Estado: " . ($estado ?? 'N/A') . 
                  ", Registros: " . count($result));
        foreach ($result as $liquidacion) {
            error_log("Liquidacion ID: " . $liquidacion['id'] . 
                      ", id_contador: " . ($liquidacion['id_contador'] ?? 'N/A') . 
                      ", Estado: " . ($liquidacion['estado'] ?? 'N/A'));
        }
    
        return $result;
    }

    public function getLiquidacionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT l.*, 
                   cc.nombre AS nombre_caja_chica,
                   u.nombre AS nombre_usuario,
                   s.nombre AS nombre_supervisor,
                   c.nombre AS nombre_contador
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            LEFT JOIN usuarios u ON l.id_usuario = u.id
            LEFT JOIN usuarios s ON l.id_supervisor = s.id
            LEFT JOIN usuarios c ON l.id_contador = c.id
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCajaChicaByUsuario($idUsuario) {
        $stmt = $this->pdo->prepare("
            SELECT id, id_supervisor, id_contador 
            FROM cajas_chicas 
            WHERE id_usuario_encargado = ? AND estado = 'ACTIVA'
        ");
        $stmt->execute([$idUsuario]);
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$caja) {
            throw new Exception('No se encontró una caja chica activa para el usuario');
        }
        return $caja;
    }

    public function createLiquidacion($idUsuario, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado) {
        $caja = $this->getCajaChicaByUsuario($idUsuario);
        $idCajaChica = $caja['id'];

        $stmt = $this->pdo->prepare("
            INSERT INTO liquidaciones (id_caja_chica, fecha_creacion, fecha_inicio, fecha_fin, monto_total, estado, id_usuario, id_supervisor, id_contador)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$idCajaChica, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado, $idUsuario, $caja['id_supervisor'], $caja['id_contador']]);
    }

    public function createLiquidacionFromArray($data) {
        return $this->createLiquidacion(
            $data['id_usuario'],
            $data['fecha_creacion'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $data['monto_total'],
            $data['estado']
        );
    }

    public function updateLiquidacion($id, $idUsuario, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado) {
        $caja = $this->getCajaChicaByUsuario($idUsuario);
        $idCajaChica = $caja['id'];

        $stmt = $this->pdo->prepare("
            UPDATE liquidaciones
            SET id_caja_chica = ?, fecha_creacion = ?, fecha_inicio = ?, fecha_fin = ?, monto_total = ?, estado = ?, id_supervisor = ?, id_contador = ?
            WHERE id = ?
        ");
        return $stmt->execute([$idCajaChica, $fechaCreacion, $fechaInicio, $fechaFin, $montoTotal, $estado, $caja['id_supervisor'], $caja['id_contador'], $id]);
    }

    public function deleteLiquidation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM liquidaciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function markAsExported($id) {
        try {
            error_log("Attempting to mark liquidation as exported for ID: $id");
            error_log("Transaction status: " . ($this->pdo->inTransaction() ? 'In transaction' : 'Not in transaction'));
            $stmt = $this->pdo->prepare("UPDATE liquidaciones SET exportado = 1 WHERE id = ?");
            $result = $stmt->execute([$id]);
            $rowCount = $stmt->rowCount();
            error_log("markAsExported - ID: $id, Result: " . ($result ? 'true' : 'false') . ", Rows affected: $rowCount");
            if ($result === false) {
                error_log("markAsExported failed: " . implode(', ', $stmt->errorInfo()));
                return false;
            }
            if ($rowCount === 0) {
                error_log("markAsExported - No rows updated for ID: $id");
            }
            return true;
        } catch (PDOException $e) {
            error_log("PDOException in markAsExported for ID $id: " . $e->getMessage());
            return false;
        }
    }

    public function updateEstado($id, $estado, $supervisorId = null, $contadorId = null) {
        try {
            if ($contadorId !== null) {
                $stmt = $this->pdo->prepare("UPDATE liquidaciones SET estado = ?, id_contador = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$estado, $contadorId, $id]);
            } elseif ($supervisorId !== null) {
                $stmt = $this->pdo->prepare("UPDATE liquidaciones SET estado = ?, id_supervisor = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$estado, $supervisorId, $id]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE liquidaciones SET estado = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$estado, $id]);
            }

            if ($result === false) {
                error_log("Error al ejecutar UPDATE en updateEstado: " . implode(', ', $stmt->errorInfo()));
                return false;
            }
            $rowCount = $stmt->rowCount();
            error_log("updateEstado ejecutado - ID: $id, Estado: $estado, SupervisorID: " . ($supervisorId ?? 'N/A') . ", ContadorID: " . ($contadorId ?? 'N/A') . ", Filas afectadas: $rowCount");
            return $rowCount > 0;
        } catch (PDOException $e) {
            error_log("Error PDO en updateEstado: " . $e->getMessage());
            return false;
        }
    }

    public function getLiquidacionesByFecha($fechaInicio, $fechaFin, $idCajaChica = null) {
        $query = "
            SELECT l.*, cc.nombre as caja_chica, SUM(dl.total_factura) as total_gastos
            FROM liquidaciones l
            LEFT JOIN detalle_liquidaciones dl ON l.id = dl.id_liquidacion
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE l.fecha_inicio >= ? AND l.fecha_fin <= ?
        ";
        $params = [$fechaInicio, $fechaFin];

        if (!empty($idCajaChica)) {
            $query .= " AND l.id_caja_chica = ?";
            $params[] = $idCajaChica;
        }

        $query .= " GROUP BY l.id";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMontoTotal($id, $montoTotal) {
        $stmt = $this->pdo->prepare("UPDATE liquidaciones SET monto_total = ? WHERE id = ?");
        return $stmt->execute([$montoTotal, $id]);
    }

    public function getLiquidacionesWithCorrections() {
        $query = "
            SELECT l.*, cc.nombre as nombre_caja_chica
            FROM liquidaciones l
            JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE EXISTS (
                SELECT 1 
                FROM detalle_liquidaciones dl 
                WHERE dl.id_liquidacion = l.id 
                AND dl.estado = 'EN_CORRECCION'
            )
            ORDER BY l.fecha_creacion DESC
        ";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLiquidacionesByUsuario($idUsuario) {
        $stmt = $this->pdo->prepare("
            SELECT l.*, 
                   cc.nombre AS nombre_caja_chica
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE l.id_usuario = ?
            ORDER BY l.fecha_creacion DESC
        ");
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLiquidacionesByEstado($estado) {
        $stmt = $this->pdo->prepare("
            SELECT l.*, 
                   cc.nombre AS nombre_caja_chica
            FROM liquidaciones l
            LEFT JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
            WHERE l.estado = ?
            ORDER BY l.fecha_creacion DESC
        ");
        $stmt->execute([$estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isExported($id) {
        $stmt = $this->pdo->prepare("SELECT exportado FROM liquidaciones WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['exportado'] ?? 0;
    }
}


?>