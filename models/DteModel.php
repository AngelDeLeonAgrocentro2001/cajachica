<?php
require_once '../config/database.php';

class DteModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
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
                $data['petroleo'] ?? 0.00,
                $data['turismo_hospedaje'] ?? 0.00,
                $data['turismo_pasajes'] ?? 0.00,
                $data['timbre_prensa'] ?? 0.00,
                $data['bomberos'] ?? 0.00,
                $data['tasa_municipal'] ?? 0.00,
                $data['bebidas_alcoholicas'] ?? 0.00,
                $data['tabaco'] ?? 0.00,
                $data['cemento'] ?? 0.00,
                $data['bebidas_no_alcoholicas'] ?? 0.00,
                $data['tarifa_portuaria'] ?? 0.00,
                'X'
            ]);
    
            return true;
            
        } catch (PDOException $e) {
            // Verificar si es error de duplicado (código 23000 para violación de restricción única)
            if ($e->getCode() == '23000') {
                error_log("DTE duplicado (manejado por BD): " . 
                         $data['numero_autorizacion'] . ", " . 
                         $data['serie'] . ", " . 
                         $data['numero_dte']);
                return false; // Indicar que es duplicado
            }
            
            error_log("Error al insertar DTE: " . $e->getMessage() . 
                     " | Código: " . $e->getCode() . 
                     " | Data: " . print_r($data, true));
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
}