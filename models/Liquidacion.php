<?php
require_once '../config/database.php';

class Liquidacion {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // MÉTODO CORREGIDO: Registrar advertencia de expiración en auditoría
private function registrarAdvertenciaExpiracion($liquidacionId) {
    try {
        $userId = 0; // Sistema automático
        
        // Verificar la estructura de la tabla auditoria
        $query = "
            INSERT INTO auditoria (
                id_liquidacion, 
                id_usuario, 
                accion, 
                descripcion, 
                fecha
            ) VALUES (?, ?, 'ADVERTENCIA_EXPIRACION', ?, NOW())
        ";
        
        $descripcion = "Correo de advertencia enviado: La liquidación expirará mañana.";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$liquidacionId, $userId, $descripcion]);
        
        error_log("✅ Auditoría registrada para advertencia de expiración - Liquidación ID: $liquidacionId");
        
    } catch (PDOException $e) {
        error_log("Error al registrar auditoría de advertencia: " . $e->getMessage());
    }
}

    // NUEVO MÉTODO: Enviar correo de advertencia el día antes de expirar
    public function sendExpirationWarningEmail($liquidacionId, $liquidacionInfo) {
    try {
        error_log("🔔 Enviando correo de advertencia por expiración para liquidación ID: $liquidacionId");
        
        // 🔴 VERIFICACIÓN CRÍTICA: Verificar si ya se registró en auditoría hoy
        $checkQuery = "
            SELECT COUNT(*) as count 
            FROM auditoria 
            WHERE id_liquidacion = ? 
            AND accion = 'ADVERTENCIA_EXPIRACION'
            AND DATE(fecha) = CURDATE()
        ";
        
        $checkStmt = $this->pdo->prepare($checkQuery);
        $checkStmt->execute([$liquidacionId]);
        $yaEnviado = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if ($yaEnviado) {
            error_log("⚠️ Correo de advertencia YA ENVIADO HOY para liquidación ID: $liquidacionId. Omitiendo envío.");
            return false;
        }
        
        // Obtener información de la liquidación
        $query = "
            SELECT l.*, 
                   u.email as encargado_email, 
                   u.nombre as encargado_nombre,
                   s.email as supervisor_email,
                   s.nombre as supervisor_nombre
            FROM liquidaciones l
            LEFT JOIN usuarios u ON l.id_usuario = u.id
            LEFT JOIN usuarios s ON l.id_supervisor = s.id
            WHERE l.id = ?
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$liquidacionId]);
        $liquidacion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$liquidacion) {
            error_log("❌ Liquidación no encontrada para ID: $liquidacionId");
            return false;
        }
        
        // Calcular fecha de expiración (14 días después de fecha_creacion)
        $fechaCreacion = new DateTime($liquidacion['fecha_creacion']);
        $fechaExpiracion = clone $fechaCreacion;
        $fechaExpiracion->modify('+14 days');
        
        $fechaAdvertencia = clone $fechaCreacion;
        $fechaAdvertencia->modify('+13 days'); // Día antes de expirar
        
        // Verificar si hoy es el día de advertencia (13 días después)
        $hoy = new DateTime('today');
        
        if ($hoy->format('Y-m-d') !== $fechaAdvertencia->format('Y-m-d')) {
            error_log("⚠️ No es el día de advertencia para liquidación ID: $liquidacionId");
            return false;
        }
        
        // Preparar datos para el correo
        $datosCorreo = [
            'liquidacion_id' => $liquidacionId,
            'encargado_nombre' => $liquidacion['encargado_nombre'] ?? 'Encargado',
            'encargado_email' => $liquidacion['encargado_email'] ?? '',
            'supervisor_nombre' => $liquidacion['supervisor_nombre'] ?? 'Supervisor',
            'supervisor_email' => $liquidacion['supervisor_email'] ?? '',
            'fecha_creacion' => $fechaCreacion->format('d/m/Y'),
            'fecha_expiracion' => $fechaExpiracion->format('d/m/Y'),
            'estado_actual' => $liquidacion['estado'] ?? 'EN_PROCESO'
        ];
        
        // Enviar correos
        $loginController = new LoginController();
        $enviosExitosos = 0;
        
        // Enviar al encargado si tiene email
        if (!empty($datosCorreo['encargado_email'])) {
            $resultEncargado = $loginController->sendExpirationWarningEmail(
                $datosCorreo['encargado_email'],
                $datosCorreo['encargado_nombre'],
                $liquidacionId,
                "Tu liquidación expirará mañana. Fecha de creación: {$datosCorreo['fecha_creacion']}"
            );
            
            if ($resultEncargado) {
                $enviosExitosos++;
                error_log("✅ Correo de advertencia enviado al encargado: " . $datosCorreo['encargado_email']);
            }
        }
        
        // Enviar al supervisor si tiene email
        if (!empty($datosCorreo['supervisor_email'])) {
            $resultSupervisor = $loginController->sendExpirationWarningEmail(
                $datosCorreo['supervisor_email'],
                $datosCorreo['supervisor_nombre'],
                $liquidacionId,
                "Liquidación del encargado {$datosCorreo['encargado_nombre']} expirará mañana. Fecha de creación: {$datosCorreo['fecha_creacion']}"
            );
            
            if ($resultSupervisor) {
                $enviosExitosos++;
                error_log("✅ Correo de advertencia enviado al supervisor: " . $datosCorreo['supervisor_email']);
            }
        }
        
        // SOLO registrar en auditoría si se envió al menos un correo
        if ($enviosExitosos > 0) {
            $this->registrarAdvertenciaExpiracion($liquidacionId);
            error_log("📨 Total correos de advertencia enviados: $enviosExitosos");
            return true;
        } else {
            error_log("⚠️ No se pudo enviar ningún correo de advertencia para liquidación ID: $liquidacionId");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("❌ Error en sendExpirationWarningEmail: " . $e->getMessage());
        return false;
    }
}

private function verificarCorreoEnviadoHoy($liquidacionId) {
    try {
        $query = "
            SELECT COUNT(*) as count 
            FROM auditoria 
            WHERE id_liquidacion = ? 
            AND accion = 'ADVERTENCIA_EXPIRACION'
            AND DATE(fecha) = CURDATE()
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$liquidacionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $enviadoHoy = $result['count'] > 0;
        
        if ($enviadoHoy) {
            error_log("📊 Verificación: Liquidación $liquidacionId ya tiene auditoría de advertencia hoy");
        }
        
        return $enviadoHoy;
        
    } catch (PDOException $e) {
        error_log("Error al verificar correo enviado hoy: " . $e->getMessage());
        return true; // En caso de error, prevenir envío
    }
}
    
    // MÉTODO MODIFICADO: Ahora también verifica y envía advertencias
    public function checkAndFinalizeOldLiquidaciones() {
        try {
            // 1. Primero, verificar y enviar advertencias
            $this->checkAndSendExpirationWarnings();
            
            // 2. Luego, expirar las liquidaciones antiguas (lógica original)
            $twoWeeksAgo = date('Y-m-d 00:00:00', strtotime('-2 weeks'));
            error_log("Auto-expirado liquidaciones creadas antes de: $twoWeeksAgo");
            
            // Cambiar estado a EXPIRADO
            $query = "
                UPDATE liquidaciones 
                SET estado = 'EXPIRADO', 
                    updated_at = NOW() 
                WHERE estado IN ('EN_PROCESO', 'PENDIENTE_AUTORIZACION')
                AND fecha_creacion <= ?
            ";
            
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute([$twoWeeksAgo]);
            $rowCount = $stmt->rowCount();
            
            error_log("Auto-expiradas $rowCount liquidaciones antiguas");
            
            // 3. Eliminar liquidaciones EXPIRADAS con más de 1 hora
            $this->deleteExpiredLiquidaciones();
            
            return $rowCount;
            
        } catch (PDOException $e) {
            error_log("Error en checkAndFinalizeOldLiquidaciones: " . $e->getMessage());
            return 0;
        }
    }
     public function getDiasRestantesExpiracion($fechaCreacion) {
        $creacion = new DateTime($fechaCreacion);
        $expiracion = clone $creacion;
        $expiracion->modify('+14 days');
        $hoy = new DateTime('today');
        
        $diferencia = $hoy->diff($expiracion);
        return $diferencia->days;
    }
    // NUEVO MÉTODO: Verificar y enviar advertencias de expiración
    private function checkAndSendExpirationWarnings() {
    try {
        error_log("🔍 Verificando liquidaciones para advertencia de expiración...");
        
        // Obtener liquidaciones que están en su día 13 (mañana expiran)
        $thirteenDaysAgo = date('Y-m-d', strtotime('-13 days'));
        
        $query = "
            SELECT l.id, l.fecha_creacion, l.estado
            FROM liquidaciones l
            WHERE DATE(l.fecha_creacion) = ?
            AND l.estado IN ('EN_PROCESO', 'PENDIENTE_AUTORIZACION')
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$thirteenDaysAgo]);
        $liquidaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Liquidaciones encontradas para advertencia: " . count($liquidaciones));
        
        $advertenciasEnviadas = 0;
        
        foreach ($liquidaciones as $liquidacion) {
            error_log("Procesando liquidación ID: {$liquidacion['id']} para advertencia");
            
            // 🔴 NUEVA VERIFICACIÓN: Usar LOCK para evitar ejecución concurrente
            $lockQuery = "
                INSERT INTO task_locks (task_name, liquidacion_id, locked_at) 
                VALUES ('expiration_warning', ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    locked_at = IF(locked_at < DATE_SUB(NOW(), INTERVAL 1 HOUR), NOW(), locked_at),
                    task_name = VALUES(task_name)
            ";
            
            try {
                $lockStmt = $this->pdo->prepare($lockQuery);
                $lockStmt->execute([$liquidacion['id']]);
                
                // Verificar si realmente obtuvimos el lock
                $checkLockStmt = $this->pdo->prepare("
                    SELECT * FROM task_locks 
                    WHERE task_name = 'expiration_warning' 
                    AND liquidacion_id = ? 
                    AND locked_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
                ");
                $checkLockStmt->execute([$liquidacion['id']]);
                $lockInfo = $checkLockStmt->fetch(PDO::FETCH_ASSOC);
                
                // Si el lock fue creado/actualizado hace menos de 5 minutos, procesamos
                if ($lockInfo && (time() - strtotime($lockInfo['locked_at']) < 300)) { // 5 minutos
                    // Enviar advertencia
                    if ($this->sendExpirationWarningEmail($liquidacion['id'], $liquidacion)) {
                        $advertenciasEnviadas++;
                        error_log("✅ Advertencia enviada para liquidación ID: {$liquidacion['id']}");
                    }
                } else {
                    error_log("⚠️ Lock expirado o no obtenido para liquidación ID: {$liquidacion['id']}");
                }
                
            } catch (PDOException $e) {
                // Si hay un error de duplicado, significa que otro proceso ya está ejecutando esta tarea
                error_log("⚠️ Tarea ya en ejecución para liquidación ID: {$liquidacion['id']} - " . $e->getMessage());
                continue;
            }
        }
        
        error_log("Total advertencias de expiración enviadas: $advertenciasEnviadas");
        return $advertenciasEnviadas;
        
    } catch (PDOException $e) {
        error_log("Error en checkAndSendExpirationWarnings: " . $e->getMessage());
        return 0;
    }
}
    
    // NUEVO MÉTODO: Registrar advertencia de expiración en auditoría
    private function registrarAdvertenciaExpiración($liquidacionId) {
        try {
            $userId = 0; // Sistema automático
            
            $query = "
                INSERT INTO auditoria (
                    id_liquidacion, 
                    id_detalle, 
                    id_usuario, 
                    accion, 
                    descripcion, 
                    fecha
                ) VALUES (?, NULL, ?, 'ADVERTENCIA_EXPIRACION', ?, NOW())
            ";
            
            $descripcion = "Correo de advertencia enviado: La liquidación expirará mañana.";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$liquidacionId, $userId, $descripcion]);
            
            error_log("✅ Auditoría registrada para advertencia de expiración - Liquidación ID: $liquidacionId");
            
        } catch (PDOException $e) {
            error_log("Error al registrar auditoría de advertencia: " . $e->getMessage());
        }
    }

// NUEVO MÉTODO: Eliminar liquidaciones EXPIRADAS después de 5 minutos
public function deleteExpiredLiquidaciones() {
    try {
        $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-12 hour'));
        error_log("Eliminando liquidaciones EXPIRADAS desde: $fiveMinutesAgo");
        
        // Obtener liquidaciones EXPIRADAS con más de 5 minutos
        $query = "
            SELECT id, estado 
            FROM liquidaciones 
            WHERE estado = 'EXPIRADO'
            AND updated_at <= ?
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$fiveMinutesAgo]);
        $liquidaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Liquidaciones EXPIRADAS encontradas para eliminar: " . count($liquidaciones));
        
        $deletedCount = 0;
        
        foreach ($liquidaciones as $liquidacion) {
            $liquidacionId = $liquidacion['id'];
            error_log("Procesando liquidación EXPIRADA ID: $liquidacionId para eliminación");
            
            try {
                // Verificar estado DTE antes de eliminar
                $detallesConDte = $this->verificarEstadoDteDespuesEliminacion($liquidacionId);
                
                // Liberar y eliminar facturas asociadas
                if ($this->liberarYeliminarFacturas($liquidacionId)) {
                    // Eliminar la liquidación
                    $deleteStmt = $this->pdo->prepare("DELETE FROM liquidaciones WHERE id = ?");
                    $deleteStmt->execute([$liquidacionId]);
                    
                    if ($deleteStmt->rowCount() > 0) {
                        $deletedCount++;
                        error_log("✓ Liquidación EXPIRADA eliminada ID: $liquidacionId");
                        
                        // Registrar auditoría
                        $this->registrarAuditoriaEliminacion($liquidacionId);
                        
                        // Verificar estado DTE después de eliminar
                        $this->verificarEstadoDteDespuesEliminacionEnDte($detallesConDte);
                    }
                } else {
                    error_log("✗ No se pudo liberar facturas de liquidación ID: $liquidacionId");
                }
            } catch (Exception $e) {
                error_log("✗ Error procesando liquidación ID: $liquidacionId - " . $e->getMessage());
                continue; // Continuar con la siguiente liquidación
            }
        }
        
        error_log("Total liquidaciones EXPIRADAS eliminadas: $deletedCount");
        return $deletedCount;
        
    } catch (PDOException $e) {
        error_log("Error en deleteExpiredLiquidaciones: " . $e->getMessage());
        return 0;
    }
}

// Método para verificar estado DTE después de la operación
private function verificarEstadoDteDespuesEliminacionEnDte($detalles) {
    foreach ($detalles as $detalle) {
        if (!empty($detalle['serie']) && !empty($detalle['no_factura'])) {
            // 🔴 **Misma lógica de extracción**
            $numero_dte = $detalle['serie'] && strpos($detalle['no_factura'], $detalle['serie']) === 0 
                ? substr($detalle['no_factura'], strlen($detalle['serie'])) 
                : $detalle['no_factura'];
            
            $numero_dte_limpio = str_replace('-', '', $numero_dte);
            $numero_dte_limpio = preg_replace('/\s+/', '', $numero_dte_limpio);
            
            $checkStmt = $this->pdo->prepare("SELECT usado FROM dte WHERE serie = ? AND numero_dte = ?");
            $checkStmt->execute([$detalle['serie'], $numero_dte_limpio]);
            $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$dte) {
                // Intentar con el número exacto
                $checkStmt->execute([$detalle['serie'], $detalle['no_factura']]);
                $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if ($dte) {
                error_log("Estado DTE después de eliminar - Serie: {$detalle['serie']}, Factura: {$detalle['no_factura']}, Numero DTE: $numero_dte_limpio, Estado: {$dte['usado']}");
            } else {
                error_log("DTE no encontrado después de eliminar - Serie: {$detalle['serie']}, Factura: {$detalle['no_factura']}, Numero DTE calc: $numero_dte_limpio");
            }
        }
    }
}

// En la clase Liquidacion, añade este método:
private function liberarYeliminarFacturas($liquidacionId) {
    try {
        $this->pdo->beginTransaction();
        
        error_log("=== INICIANDO liberarYeliminarFacturas para liquidación ID: $liquidacionId ===");
        
        // 1. Obtener todas las facturas (detalles) de la liquidación
        $query = "
            SELECT id, serie, no_factura 
            FROM detalle_liquidaciones 
            WHERE id_liquidacion = ?
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$liquidacionId]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Número de detalles encontrados: " . count($detalles));
        
        // 2. Para cada factura, liberar DTE si existe
        foreach ($detalles as $index => $detalle) {
            error_log("Procesando detalle {$index}: ID={$detalle['id']}, Serie={$detalle['serie']}, No Factura={$detalle['no_factura']}");
            
            if (!empty($detalle['serie']) && !empty($detalle['no_factura'])) {
                // Usar el método liberarDte que ya existe
                $this->liberarDte($detalle['serie'], $detalle['no_factura']);
            } else {
                error_log("Detalle sin serie o número de factura, no se puede liberar DTE");
            }
        }
        
        // 3. Eliminar todos los detalles de la liquidación
        $deleteDetallesQuery = "DELETE FROM detalle_liquidaciones WHERE id_liquidacion = ?";
        $deleteStmt = $this->pdo->prepare($deleteDetallesQuery);
        $deleteStmt->execute([$liquidacionId]);
        
        $detallesEliminados = $deleteStmt->rowCount();
        error_log("Detalles eliminados: $detallesEliminados registros");
        
        // 4. Eliminar auditorías relacionadas
        $deleteAuditoriaQuery = "DELETE FROM auditoria WHERE id_liquidacion = ?";
        $deleteAuditoriaStmt = $this->pdo->prepare($deleteAuditoriaQuery);
        $deleteAuditoriaStmt->execute([$liquidacionId]);
        
        $auditoriasEliminadas = $deleteAuditoriaStmt->rowCount();
        error_log("Auditorías eliminadas: $auditoriasEliminadas registros");
        
        $this->pdo->commit();
        error_log("=== TRANSACCIÓN COMPLETADA para liquidación ID: $liquidacionId ===");
        return true;
        
    } catch (PDOException $e) {
        $this->pdo->rollBack();
        error_log("ERROR en liberarYeliminarFacturas para liquidación ID: $liquidacionId - " . $e->getMessage());
        return false;
    }
}
// NUEVO MÉTODO: Liberar y eliminar facturas asociadas
public function liberarDte($serie, $no_factura) {
    try {
        if (empty($serie) || empty($no_factura)) {
            error_log("Error: Serie o número de factura vacíos para liberar DTE");
            return false;
        }
        
        // 🔴 **IMPORTANTE: Extraer el numero_dte como en el primer ejemplo**
        $numero_dte = $serie && strpos($no_factura, $serie) === 0 
            ? substr($no_factura, strlen($serie)) 
            : $no_factura;
        
        // Limpiar el número de factura (quitar guiones y espacios)
        $numero_dte_limpio = str_replace('-', '', $numero_dte);
        $numero_dte_limpio = preg_replace('/\s+/', '', $numero_dte_limpio);
        
        error_log("=== INICIANDO liberarDte ===");
        error_log("Serie: '$serie', No Factura original: '$no_factura'");
        error_log("Numero DTE extraído: '$numero_dte'");
        error_log("Numero DTE limpio: '$numero_dte_limpio'");
        
        // Verificar si el DTE existe
        $checkStmt = $this->pdo->prepare("SELECT serie, numero_dte, usado FROM dte WHERE serie = ? AND numero_dte = ?");
        $checkStmt->execute([$serie, $numero_dte_limpio]);
        $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$dte) {
            error_log("✗ DTE no encontrado en la tabla dte con numero_dte_limpio");
            
            // 🔴 **Intentar buscar exactamente como está en la factura**
            $checkStmt->execute([$serie, $no_factura]);
            $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$dte) {
                error_log("✗ DTE no encontrado con ninguna variación");
                
                // Buscar en la tabla dte para ver qué formatos existen
                $searchStmt = $this->pdo->prepare("SELECT serie, numero_dte, usado FROM dte WHERE serie LIKE ?");
                $searchStmt->execute([$serie]);
                $allDtes = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($allDtes) > 0) {
                    error_log("DTEs encontrados con serie '$serie':");
                    foreach ($allDtes as $dteItem) {
                        error_log("  - Numero DTE: '{$dteItem['numero_dte']}', Usado: '{$dteItem['usado']}'");
                    }
                }
                
                return false;
            } else {
                $numero_dte_limpio = $no_factura; // Usar el número exacto
                error_log("✓ DTE encontrado con número exacto de factura");
            }
        }
        
        error_log("DTE encontrado - Estado actual: '{$dte['usado']}'");
        
        // Solo actualizar si está en 'Y' (usado)
        if ($dte['usado'] === 'Y') {
            $updateStmt = $this->pdo->prepare("UPDATE dte SET usado = 'X' WHERE serie = ? AND numero_dte = ?");
            $updateStmt->execute([$serie, $numero_dte_limpio]);
            $rowCount = $updateStmt->rowCount();
            
            error_log("UPDATE ejecutado - Filas afectadas: $rowCount");
            
            if ($rowCount > 0) {
                // Verificar el cambio
                $checkStmt->execute([$serie, $numero_dte_limpio]);
                $dte_actualizado = $checkStmt->fetch(PDO::FETCH_ASSOC);
                error_log("✓ DTE liberado exitosamente de 'Y' a 'X'");
                error_log("Estado DTE después: '{$dte_actualizado['usado']}'");
                return true;
            } else {
                error_log("✗ No se pudo actualizar el DTE (posiblemente ya estaba en 'X')");
                return false;
            }
        } else if ($dte['usado'] === 'X') {
            error_log("DTE ya está liberado (estado 'X')");
            return true; // Ya está liberado
        } else {
            error_log("DTE está en estado desconocido: '{$dte['usado']}'");
            return false;
        }
        
    } catch (PDOException $e) {
        error_log("✗ ERROR en liberarDte: " . $e->getMessage());
        return false;
    }
}

// Añade este método a la clase Liquidacion:
private function liberarDteIndividual($serie, $no_factura) {
    try {
        if (empty($serie) || empty($no_factura)) {
            error_log("Error: Serie o número de factura vacíos para liberar DTE");
            return false;
        }
        
        // Limpiar el número de factura (quitar guiones)
        $numero_dte_limpio = str_replace('-', '', $no_factura);
        
        error_log("Liberando DTE individual - Serie: '$serie', Numero DTE: '$numero_dte_limpio'");
        
        // Verificar si el DTE existe
        $checkStmt = $this->pdo->prepare("SELECT serie, numero_dte, usado FROM dte WHERE serie = ? AND numero_dte = ?");
        $checkStmt->execute([$serie, $numero_dte_limpio]);
        $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$dte) {
            error_log("✗ DTE no encontrado para liberar - serie: '$serie', numero_dte: '$numero_dte_limpio'");
            return false;
        }
        
        error_log("DTE encontrado - Estado actual: '{$dte['usado']}'");
        
        // Solo liberar si está en 'Y' (usado)
        if ($dte['usado'] === 'Y') {
            $updateStmt = $this->pdo->prepare("UPDATE dte SET usado = 'X' WHERE serie = ? AND numero_dte = ?");
            $updateStmt->execute([$serie, $numero_dte_limpio]);
            $rowCount = $updateStmt->rowCount();
            
            if ($rowCount > 0) {
                error_log("✓ DTE liberado de 'Y' a 'X' - serie: '$serie', numero_dte: '$numero_dte_limpio'");
                return true;
            }
        } else if ($dte['usado'] === 'X') {
            error_log("DTE ya está liberado (estado 'X')");
            return true;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error al liberar DTE individual: " . $e->getMessage());
        return false;
    }
}
// MÉTODO PARA DEPURAR - Verificar estado de DTE después de eliminar
// En la clase Liquidacion, agrega este método para debug:
public function verificarEstadoDteDespuesEliminacion($liquidacionId) {
    try {
        // Obtener detalles antes de eliminar
        $query = "
            SELECT dl.serie, dl.no_factura, d.usado as dte_estado_antes
            FROM detalle_liquidaciones dl
            LEFT JOIN dte d ON dl.serie = d.serie 
            WHERE dl.id_liquidacion = ?
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$liquidacionId]);
        $detallesConDte = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("=== VERIFICACIÓN DTE ANTES DE ELIMINAR LIQUIDACIÓN $liquidacionId ===");
        
        // Para cada detalle, verificar con la misma lógica de extracción
        foreach ($detallesConDte as &$detalle) {
            if (!empty($detalle['serie']) && !empty($detalle['no_factura'])) {
                // 🔴 **Misma lógica de extracción**
                $numero_dte = $detalle['serie'] && strpos($detalle['no_factura'], $detalle['serie']) === 0 
                    ? substr($detalle['no_factura'], strlen($detalle['serie'])) 
                    : $detalle['no_factura'];
                
                $numero_dte_limpio = str_replace('-', '', $numero_dte);
                $numero_dte_limpio = preg_replace('/\s+/', '', $numero_dte_limpio);
                
                // Verificar en la tabla dte con la misma lógica
                $checkStmt = $this->pdo->prepare("SELECT usado FROM dte WHERE serie = ? AND numero_dte = ?");
                $checkStmt->execute([$detalle['serie'], $numero_dte_limpio]);
                $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$dte) {
                    // Intentar con el número exacto
                    $checkStmt->execute([$detalle['serie'], $detalle['no_factura']]);
                    $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
                }
                
                $detalle['dte_estado_antes'] = $dte['usado'] ?? 'NO_ENCONTRADO';
                $detalle['numero_dte_calculado'] = $numero_dte_limpio;
                
                error_log("Serie: {$detalle['serie']}, Factura: {$detalle['no_factura']}, Numero DTE calc: {$detalle['numero_dte_calculado']}, Estado DTE: {$detalle['dte_estado_antes']}");
            }
        }
        
        return $detallesConDte;
        
    } catch (PDOException $e) {
        error_log("Error al verificar estado DTE: " . $e->getMessage());
        return [];
    }
}
// NUEVO MÉTODO: Registrar auditoría de eliminación automática
private function registrarAuditoriaEliminacion($liquidacionId) {
    try {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // 0 para sistema automático
        
        $query = "
            INSERT INTO auditoria (
                id_liquidacion, 
                id_detalle, 
                id_usuario, 
                accion, 
                descripcion, 
                fecha
            ) VALUES (?, NULL, ?, 'ELIMINACION_AUTOMATICA', ?, NOW())
        ";
        
        $descripcion = "Liquidación EXPIRADA eliminada automáticamente después de 1 hora. Facturas liberadas.";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$liquidacionId, $userId, $descripcion]);
        
        error_log("Auditoría registrada para eliminación automática de liquidación ID: $liquidacionId");
        
    } catch (PDOException $e) {
        error_log("Error al registrar auditoría de eliminación: " . $e->getMessage());
    }
}
public function getTiempoRestanteExpiracion($fechaExpiracion) {
    $expiracion = new DateTime($fechaExpiracion);
    $ahora = new DateTime();
    
    if ($ahora > $expiracion) {
        return "00:00:00"; // Ya expiró
    }
    
    $diferencia = $ahora->diff($expiracion);
    return sprintf(
        "%02d:%02d:%02d",
        $diferencia->h,
        $diferencia->i,
        $diferencia->s
    );
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