<?php
require_once '../config/database.php';

class DteModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function isDteDuplicate($numero_autorizacion, $serie, $numero_dte) {
        try {
            if (empty($numero_autorizacion) || empty($serie) || empty($numero_dte)) {
                error_log("Campos incompletos para verificación de duplicado: numero_autorizacion=$numero_autorizacion, serie=$serie, numero_dte=$numero_dte");
                return false;
            }
            
            $sql = "SELECT COUNT(*) FROM dte WHERE numero_autorizacion = ? AND serie = ? AND numero_dte = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$numero_autorizacion, $serie, $numero_dte]);
            $count = $stmt->fetchColumn();
            
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar duplicado de DTE: " . $e->getMessage());
            return false;
        }
    }

    public function insertDte($data) {
    try {
        // VERIFICAR VALORES NUMÉRICOS ANTES DE INSERTAR
        error_log("Validando valores numéricos para DTE:");
        error_log("gran_total: " . $data['gran_total'] . " | Tipo: " . gettype($data['gran_total']));
        error_log("iva: " . $data['iva'] . " | Tipo: " . gettype($data['iva']));
        
        // Validar y limitar valores numéricos
        $gran_total = $this->safeDecimalValue($data['gran_total']);
        $iva = $this->safeDecimalValue($data['iva']);
        
        error_log("Valores después de validación:");
        error_log("gran_total: $gran_total");
        error_log("iva: $iva");

        // Verificar duplicados
        if ($this->isDteDuplicate($data['numero_autorizacion'], $data['serie'], $data['numero_dte'])) {
            error_log("DTE duplicado detectado: numero_autorizacion={$data['numero_autorizacion']}, serie={$data['serie']}, numero_dte={$data['numero_dte']}");
            return false;
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
        
        // Convertir valores para coincidir con la estructura de la BD
        $fecha_emision = !empty($data['fecha_emision']) ? date('Y-m-d H:i:s', strtotime($data['fecha_emision'])) : null;
        $fecha_anulacion = !empty($data['fecha_anulacion']) ? date('Y-m-d H:i:s', strtotime($data['fecha_anulacion'])) : null;
        
        $result = $stmt->execute([
            $fecha_emision,
            $data['numero_autorizacion'],
            $data['tipo_dte'],
            $data['serie'],
            $data['numero_dte'],
            intval($data['clasificacion_emisor']),
            $data['exportacion'],
            $data['nit_emisor'],
            $data['nombre_emisor'],
            intval($data['codigo_establecimiento']),
            $data['nombre_establecimiento'],
            $data['id_receptor'],
            $data['nombre_receptor'],
            $data['nit_certificador'],
            $data['nombre_certificador'],
            $data['estado'],
            $data['moneda'],
            $gran_total, // Usar valor validado
            $iva, // Usar valor validado
            $data['marca_anulado'],
            $fecha_anulacion,
            $this->safeDecimalValue($data['petroleo']),
            $this->safeDecimalValue($data['turismo_hospedaje']),
            $this->safeDecimalValue($data['turismo_pasajes']),
            $this->safeDecimalValue($data['timbre_prensa']),
            $this->safeDecimalValue($data['bomberos']),
            $this->safeDecimalValue($data['tasa_municipal']),
            $this->safeDecimalValue($data['bebidas_alcoholicas']),
            $this->safeDecimalValue($data['tabaco']),
            $this->safeDecimalValue($data['cemento']),
            $this->safeDecimalValue($data['bebidas_no_alcoholicas']),
            $this->safeDecimalValue($data['tarifa_portuaria']),
            'X'
        ]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            error_log("Error al ejecutar la consulta SQL: " . print_r($errorInfo, true));
            error_log("Datos problemáticos: " . print_r($data, true));
            return false;
        }
        
        error_log("DTE insertado exitosamente: {$data['numero_autorizacion']}");
        return true;
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            error_log("DTE duplicado detectado a nivel de BD: " . $e->getMessage());
            return false;
        }
        
        error_log("Error al insertar DTE: " . $e->getMessage());
        error_log("Datos: " . print_r($data, true));
        error_log("Trace: " . $e->getTraceAsString());
        return false;
    }
}

// AGREGAR ESTA FUNCIÓN PARA VALIDAR VALORES DECIMALES
private function safeDecimalValue($value) {
    if ($value === null || $value === '' || $value === 'NULL') {
        return 0.00;
    }
    
    // Convertir a float
    $floatValue = floatval($value);
    
    // Limitar a un rango seguro para DECIMAL(30,2)
    $maxValue = 999999999999999999.99; // Máximo para DECIMAL(30,2)
    $minValue = -999999999999999999.99; // Mínimo para DECIMAL(30,2)
    
    if ($floatValue > $maxValue) {
        error_log("Valor $floatValue excede el máximo permitido. Se limitará a $maxValue");
        return $maxValue;
    }
    
    if ($floatValue < $minValue) {
        error_log("Valor $floatValue es menor que el mínimo permitido. Se limitará a $minValue");
        return $minValue;
    }
    
    // Redondear a 2 decimales
    return round($floatValue, 2);
}

    public function getDtesByNit($nit, $fechaInicio = null, $fechaFin = null) {
        try {
            $sql = "SELECT 
                    d.numero_autorizacion, 
                    d.serie, 
                    d.numero_dte, 
                    d.nombre_emisor, 
                    DATE_FORMAT(d.fecha_emision, '%Y-%m-%d') as fecha_emision, 
                    d.gran_total, 
                    d.iva, 
                    d.nit_emisor,
                    d.tipo_dte,
                    d.estado,
                    d.usado
                FROM dte d
                WHERE d.nit_emisor LIKE :nit 
                AND d.usado = 'X'";
            
            $params = ['nit' => "%$nit%"];
            
            if ($fechaInicio && $fechaFin) {
                $sql .= " AND DATE(d.fecha_emision) BETWEEN :fecha_inicio AND :fecha_fin";
                $params['fecha_inicio'] = $fechaInicio;
                $params['fecha_fin'] = $fechaFin;
            }
            
            $sql .= " ORDER BY d.fecha_emision DESC LIMIT 100";
            
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
            $numero_dte = trim($numero_dte);
            
            $sql = "UPDATE dte SET usado = 'Y' WHERE serie = ? AND numero_dte = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$serie, $numero_dte]);
            
            if ($stmt->rowCount() > 0) {
                error_log("DTE actualizado: serie=$serie, numero_dte=$numero_dte, usado=Y");
                return true;
            } else {
                error_log("No se encontró DTE para actualizar: serie=$serie, numero_dte=$numero_dte");
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar DTE usado: " . $e->getMessage());
            return false;
        }
    }
}