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
                $data['numero_dte'], // varchar en BD
                intval($data['clasificacion_emisor']), // int en BD
                $data['exportacion'],
                $data['nit_emisor'],
                $data['nombre_emisor'],
                intval($data['codigo_establecimiento']), // int en BD
                $data['nombre_establecimiento'],
                $data['id_receptor'],
                $data['nombre_receptor'],
                $data['nit_certificador'],
                $data['nombre_certificador'],
                $data['estado'],
                $data['moneda'],
                floatval($data['gran_total']), // decimal en BD
                floatval($data['iva']), // decimal en BD
                $data['marca_anulado'],
                $fecha_anulacion,
                floatval($data['petroleo']),
                floatval($data['turismo_hospedaje']),
                floatval($data['turismo_pasajes']),
                floatval($data['timbre_prensa']),
                floatval($data['bomberos']),
                floatval($data['tasa_municipal']),
                floatval($data['bebidas_alcoholicas']),
                floatval($data['tabaco']),
                floatval($data['cemento']),
                floatval($data['bebidas_no_alcoholicas']),
                floatval($data['tarifa_portuaria']),
                'X' // Default value for usado
            ]);
            
            if (!$result) {
                error_log("Error al ejecutar la consulta SQL: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                error_log("DTE duplicado detectado a nivel de BD: " . $e->getMessage());
                return false;
            }
            
            error_log("Error al insertar DTE: " . $e->getMessage() . " | Data: " . print_r($data, true));
            return false;
        }
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