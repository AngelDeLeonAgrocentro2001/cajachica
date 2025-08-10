<?php
require_once '../config/database.php';

class DteModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function isDteDuplicate($numero_autorizacion, $serie, $numero_dte) {
        try {
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
            
            $sql .= " LIMIT 10";
            
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