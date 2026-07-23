<?php
require_once '../config/database.php';

class DteModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    // Cantidad de DTE con fecha_emision de hoy (para el KPI del dashboard de estadisticas)
    public function contarHoy() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM dte WHERE DATE(fecha_emision) = CURDATE()");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    // Cantidad de DTE por mes segun fecha_emision, para los ultimos $meses meses (incluye el actual)
    public function getEstadisticasMensuales($meses = 6) {
        $stmt = $this->pdo->prepare("
            SELECT DATE_FORMAT(fecha_emision, '%Y-%m') AS mes, COUNT(*) AS cantidad
            FROM dte
            WHERE fecha_emision >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL ? MONTH)
            GROUP BY mes
            ORDER BY mes ASC
        ");
        $stmt->execute([$meses - 1]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isDteDuplicate($numero_autorizacion, $serie, $numero_dte) {
        try {
            // Verificar que todos los campos necesarios estén presentes
            if (empty($numero_autorizacion) || empty($serie) || empty($numero_dte)) {
                error_log("Campos incompletos para verificación de duplicado: numero_autorizacion=$numero_autorizacion, serie=$serie, numero_dte=$numero_dte");
                return false;
            }
            
            $sql = "SELECT COUNT(*) FROM dte WHERE numero_autorizacion = ? AND serie = ? AND numero_dte = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$numero_autorizacion, $serie, $numero_dte]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                error_log("DTE DUPLICADO ENCONTRADO: numero_autorizacion=$numero_autorizacion, serie=$serie, numero_dte=$numero_dte");
            }
            
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar duplicado de DTE: " . $e->getMessage());
            return false;
        }
    }

    public function insertDte($data) {
        try {
            // Verificar duplicados
            if ($this->isDteDuplicate($data['numero_autorizacion'], $data['serie'], $data['numero_dte'])) {
                error_log("DTE duplicado detectado: numero_autorizacion={$data['numero_autorizacion']}, serie={$data['serie']}, numero_dte={$data['numero_dte']}");
                return false; // Indicar que no se insertó por duplicado
            }

            $sql = "INSERT INTO dte (
                fecha_emision, numero_autorizacion, tipo_dte, serie, numero_dte, 
                clasificacion_emisor, exportacion, nit_emisor, nombre_emisor, 
                codigo_establecimiento, nombre_establecimiento, id_receptor, 
                nombre_receptor, nit_certificador, nombre_certificador, estado, 
                moneda, gran_total, iva, marca_anulado, fecha_anulacion, 
                petroleo, turismo_hospedaje, turismo_pasajes, timbre_prensa, 
                bomberos, tasa_municipal, bebidas_alcoholicas, tabaco, cemento, 
                bebidas_no_alcoholicas, tarifa_portuaria, usado
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['fecha_emision'],
                $data['numero_autorizacion'],
                $data['tipo_dte'],
                $data['serie'],
                $data['numero_dte'],
                $data['clasificacion_emisor'],
                $data['exportacion'],
                $data['nit_emisor'],
                $data['nombre_emisor'],
                $data['codigo_establecimiento'],
                $data['nombre_establecimiento'],
                $data['id_receptor'],
                $data['nombre_receptor'],
                $data['nit_certificador'],
                $data['nombre_certificador'],
                $data['estado'],
                $data['moneda'],
                $data['gran_total'],
                $data['iva'],
                $data['marca_anulado'],
                $data['fecha_anulacion'] ?: null,
                $data['petroleo'],
                $data['turismo_hospedaje'],
                $data['turismo_pasajes'],
                $data['timbre_prensa'],
                $data['bomberos'],
                $data['tasa_municipal'],
                $data['bebidas_alcoholicas'],
                $data['tabaco'],
                $data['cemento'],
                $data['bebidas_no_alcoholicas'],
                $data['tarifa_portuaria'],
                'X' // Default value for usado
            ]);
            if (!$result) {
                error_log("Error al ejecutar la consulta SQL: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            // Verificar si es error de duplicado
            if ($e->getCode() == 23000) { // Código de error para violación de restricción única
                error_log("DTE duplicado detectado a nivel de BD: " . $e->getMessage());
                return false;
            }
            
            error_log("Error al insertar DTE: " . $e->getMessage() . " | Data: " . print_r($data, true));
            return false;
        }
    }

    public function getDtesByNit($nit, $fechaInicio = null, $fechaFin = null) {
        try {
            $sql = "SELECT d.numero_autorizacion, d.serie, CAST(d.numero_dte AS CHAR) AS numero_dte, 
                           d.nombre_emisor, d.fecha_emision, d.gran_total, d.iva, d.nit_emisor 
                    FROM dte d
                    WHERE d.nit_emisor LIKE :nit 
                    AND d.usado = 'X'";
            
            $params = ['nit' => "%$nit%"];
            
            if ($fechaInicio && $fechaFin) {
                $sql .= " AND d.fecha_emision BETWEEN :fecha_inicio AND :fecha_fin";
                $params['fecha_inicio'] = $fechaInicio;
                $params['fecha_fin'] = $fechaFin;
            }
            
            // $sql .= " LIMIT 10";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $dtes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("DTEs encontrados para NIT $nit: " . count($dtes));
            return $dtes;
        } catch (PDOException $e) {
            error_log("Error al buscar DTEs por NIT: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateDteUsado($serie, $numero_dte) {
        try {
            $serie = trim($serie);
            $numero_dte = trim(str_replace(['-', ' '], '', $numero_dte));
            
            $sql = "UPDATE dte SET usado = 'Y' WHERE serie = ? AND numero_dte = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$serie, $numero_dte]);
            
            if ($stmt->rowCount() > 0) {
                error_log("DTE actualizado: serie=$serie, numero_dte=$numero_dte, usado=Y");
                return true;
            } else {
                // Verificar si ya estaba en 'Y'
                $checkSql = "SELECT usado FROM dte WHERE serie = ? AND numero_dte = ?";
                $checkStmt = $this->pdo->prepare($checkSql);
                $checkStmt->execute([$serie, $numero_dte]);
                $dte = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($dte && $dte['usado'] === 'Y') {
                    error_log("DTE ya estaba en estado 'Y': serie=$serie, numero_dte=$numero_dte");
                    return true;
                }
                
                error_log("No se encontró DTE para actualizar: serie=$serie, numero_dte=$numero_dte");
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar DTE usado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Usado en la vista de búsqueda de DTE (upload.php).
     * Incluye nombre_emisor (necesario para la tabla de resultados)
     * y la información de liquidación/detalle/usuario cuando el DTE está usado.
     */
    public function searchDtes($nit = '', $serie = '', $fechaInicio = null, $fechaFin = null) {
    try {
        $sql = "SELECT d.id, d.numero_autorizacion, d.serie, CAST(d.numero_dte AS CHAR) AS numero_dte, 
                       d.nombre_emisor, d.fecha_emision, d.gran_total, d.iva, 
                       d.nit_emisor, d.usado,
                       MAX(u.id) AS id_usuario_uso,
                       MAX(u.nombre) AS nombre_usuario_uso,
                       MAX(dl.id_liquidacion) AS id_liquidacion,
                       MAX(dl.id) AS id_detalle_liquidacion
                FROM dte d
                LEFT JOIN detalle_liquidaciones dl ON dl.no_factura LIKE CONCAT('%', d.serie, '%')
                LEFT JOIN liquidaciones l ON l.id = dl.id_liquidacion
                LEFT JOIN usuarios u ON u.id = dl.id_usuario
                WHERE 1=1";
        
        $params = [];
        $conditions = [];
        
        if (!empty($nit)) {
            $conditions[] = "d.nit_emisor LIKE ?";
            $params[] = "%$nit%";
        }
        
        if (!empty($serie)) {
            $conditions[] = "d.serie LIKE ?";
            $params[] = "%$serie%";
        }
        
        if (!empty($conditions)) {
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        }
        
        if ($fechaInicio && $fechaFin) {
            $sql .= " AND d.fecha_emision BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }
        
        // GROUP BY incluye nombre_emisor para que ONLY_FULL_GROUP_BY no se queje.
        $sql .= " GROUP BY d.id, d.numero_autorizacion, d.serie, d.numero_dte, 
                            d.nombre_emisor, d.fecha_emision, d.gran_total, d.iva, 
                            d.nit_emisor, d.usado";
        $sql .= " ORDER BY d.fecha_emision DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $dtes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("DTEs encontrados: nit=$nit, serie=$serie, total=" . count($dtes));
        return $dtes;
    } catch (PDOException $e) {
        error_log("Error al buscar DTEs: " . $e->getMessage());
        throw $e;
    }
}

    /**
     * Usado tanto en la búsqueda del formulario de facturas (fetchDteSuggestions en JS)
     * como en la vista upload.php.
     * 
     * IMPORTANTE: nombre_emisor DEBE estar en el SELECT porque el JS de manageFacturas
     * lo usa en selectDte() para rellenar el campo "Nombre Proveedor" del formulario:
     *   document.getElementById('nombre_proveedor').value = dte.nombre_emisor || '';
     *
     * También incluye la información de liquidación/detalle/usuario para upload.php.
     */
    public function getDtesByNitOrSerie($nit = '', $serie = '', $fechaInicio = null, $fechaFin = null) {
    try {
        $sql = "SELECT d.id, d.numero_autorizacion, d.serie, CAST(d.numero_dte AS CHAR) AS numero_dte, 
                       d.nombre_emisor, d.fecha_emision, d.gran_total, d.iva, 
                       d.nit_emisor, d.usado,
                       MAX(u.id) AS id_usuario_uso,
                       MAX(u.nombre) AS nombre_usuario_uso,
                       MAX(dl.id_liquidacion) AS id_liquidacion,
                       MAX(dl.id) AS id_detalle_liquidacion
                FROM dte d
                LEFT JOIN detalle_liquidaciones dl ON dl.no_factura LIKE CONCAT('%', d.serie, '%')
                LEFT JOIN liquidaciones l ON l.id = dl.id_liquidacion
                LEFT JOIN usuarios u ON u.id = dl.id_usuario
                WHERE 1=1";
        
        $params = [];
        $conditions = [];
        
        if (!empty($nit)) {
            $conditions[] = "d.nit_emisor LIKE ?";
            $params[] = "%$nit%";
        }
        
        if (!empty($serie)) {
            $conditions[] = "d.serie LIKE ?";
            $params[] = "%$serie%";
        }
        
        if (!empty($conditions)) {
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        }
        
        if ($fechaInicio && $fechaFin) {
            $sql .= " AND d.fecha_emision BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }
        
        // GROUP BY incluye nombre_emisor para que ONLY_FULL_GROUP_BY no se queje.
        $sql .= " GROUP BY d.id, d.numero_autorizacion, d.serie, d.numero_dte, 
                            d.nombre_emisor, d.fecha_emision, d.gran_total, d.iva, 
                            d.nit_emisor, d.usado";
        $sql .= " ORDER BY d.fecha_emision DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $dtes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("DTEs encontrados: nit=$nit, serie=$serie, total=" . count($dtes));
        return $dtes;
    } catch (PDOException $e) {
        error_log("Error al buscar DTEs: " . $e->getMessage());
        throw $e;
    }
}
}