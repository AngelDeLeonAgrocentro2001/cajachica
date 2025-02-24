<?php
require_once '../models/DetalleLiquidacion.php';
require_once '../config/jwt.php';

class DetalleLiquidacionController {
    public function listDetallesLiquidacion() {
        $detalle = new DetalleLiquidacion();
        $detalles = $detalle->getAllDetallesLiquidacion();
        header('Content-Type: application/json');
        echo json_encode($detalles);
        exit;
    }

    public function createDetalleLiquidacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_liquidacion = $_POST['id_liquidacion'] ?? '';
            $no_factura = $_POST['no_factura'] ?? '';
            $regimen = $_POST['regimen'] ?? NULL;
            $c_costo = $_POST['c_costo'] ?? NULL;
            $nit_proveedor = $_POST['nit_proveedor'] ?? NULL;
            $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $bien_servicio = $_POST['bien_servicio'] ?? '';
            $t_gasto = $_POST['t_gasto'] ?? '';
            $codigo_ccta = $_POST['codigo_ccta'] ?? NULL;
            $descripcion_factura = $_POST['descripcion_factura'] ?? '';
            $p_unitario = $_POST['p_unitario'] ?? 0;
            $iva = $_POST['iva'] ?? 0;
            $total_factura = $_POST['total_factura'] ?? 0;
            $idp = $_POST['idp'] ?? 0;
            $inguat = $_POST['inguat'] ?? 0;
            $rutaimagen = NULL;
            $rutarchivopdf = NULL;
            $porcentajeiva = $_POST['porcentajeiva'] ?? 0;
            $porcentajeidp = $_POST['porcentajeidp'] ?? 0;
            $tipo_combustible = $_POST['tipo_combustible'] ?? NULL;

            // Manejo de subida de archivos con depuración
            $uploadDir = '../uploads/'; // Ruta relativa desde controllers/
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    http_response_code(500);
                    echo json_encode(['error' => 'No se pudo crear la carpeta uploads/']);
                    exit;
                }
            }

            if (isset($_FILES['rutaimagen']) && $_FILES['rutaimagen']['error'] === UPLOAD_ERR_OK) {
                $imageName = basename($_FILES['rutaimagen']['name']);
                $imagePath = $uploadDir . $imageName;
                if (move_uploaded_file($_FILES['rutaimagen']['tmp_name'], $imagePath)) {
                    $rutaimagen = 'uploads/' . $imageName;
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Error al subir la imagen']);
                    exit;
                }
            }

            if (isset($_FILES['rutarchivopdf']) && $_FILES['rutarchivopdf']['error'] === UPLOAD_ERR_OK) {
                $pdfName = basename($_FILES['rutarchivopdf']['name']);
                $pdfPath = $uploadDir . $pdfName;
                if (move_uploaded_file($_FILES['rutarchivopdf']['tmp_name'], $pdfPath)) {
                    $rutarchivopdf = 'uploads/' . $pdfName;
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Error al subir el PDF']);
                    exit;
                }
            }

            $detalle = new DetalleLiquidacion();
            if ($detalle->createDetalleLiquidacion($id_liquidacion, $no_factura, $regimen, $c_costo, $nit_proveedor, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $codigo_ccta, $descripcion_factura, $p_unitario, $iva, $total_factura, $idp, $inguat, $rutaimagen, $rutarchivopdf, $porcentajeiva, $porcentajeidp, $tipo_combustible)) {
                http_response_code(201);
                echo json_encode(['message' => 'Detalle de liquidación creado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear detalle de liquidación']);
            }
            exit;
        }
        require '../views/detalle_liquidaciones/form.html';
    }

    public function updateDetalleLiquidacion($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_liquidacion = $_POST['id_liquidacion'] ?? '';
            $no_factura = $_POST['no_factura'] ?? '';
            $regimen = $_POST['regimen'] ?? NULL;
            $c_costo = $_POST['c_costo'] ?? NULL;
            $nit_proveedor = $_POST['nit_proveedor'] ?? NULL;
            $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
            $fecha = $_POST['fecha'] ?? '';
            $bien_servicio = $_POST['bien_servicio'] ?? '';
            $t_gasto = $_POST['t_gasto'] ?? '';
            $codigo_ccta = $_POST['codigo_ccta'] ?? NULL;
            $descripcion_factura = $_POST['descripcion_factura'] ?? '';
            $p_unitario = $_POST['p_unitario'] ?? 0;
            $iva = $_POST['iva'] ?? 0;
            $total_factura = $_POST['total_factura'] ?? 0;
            $idp = $_POST['idp'] ?? 0;
            $inguat = $_POST['inguat'] ?? 0;
            $rutaimagen = NULL;
            $rutarchivopdf = NULL;
            $porcentajeiva = $_POST['porcentajeiva'] ?? 0;
            $porcentajeidp = $_POST['porcentajeidp'] ?? 0;
            $tipo_combustible = $_POST['tipo_combustible'] ?? NULL;
            $estado = $_POST['estado'] ?? 'PENDIENTE';

            $uploadDir = '../uploads/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    http_response_code(500);
                    echo json_encode(['error' => 'No se pudo crear la carpeta uploads/']);
                    exit;
                }
            }

            if (isset($_FILES['rutaimagen']) && $_FILES['rutaimagen']['error'] === UPLOAD_ERR_OK) {
                $imageName = basename($_FILES['rutaimagen']['name']);
                $imagePath = $uploadDir . $imageName;
                if (move_uploaded_file($_FILES['rutaimagen']['tmp_name'], $imagePath)) {
                    $rutaimagen = 'uploads/' . $imageName;
                }
            }

            if (isset($_FILES['rutarchivopdf']) && $_FILES['rutarchivopdf']['error'] === UPLOAD_ERR_OK) {
                $pdfName = basename($_FILES['rutarchivopdf']['name']);
                $pdfPath = $uploadDir . $pdfName;
                if (move_uploaded_file($_FILES['rutarchivopdf']['tmp_name'], $pdfPath)) {
                    $rutarchivopdf = 'uploads/' . $pdfName;
                }
            }

            $detalle = new DetalleLiquidacion();
            if ($detalle->updateDetalleLiquidacion($id, $id_liquidacion, $no_factura, $regimen, $c_costo, $nit_proveedor, $nombre_proveedor, $fecha, $bien_servicio, $t_gasto, $codigo_ccta, $descripcion_factura, $p_unitario, $iva, $total_factura, $idp, $inguat, $rutaimagen, $rutarchivopdf, $porcentajeiva, $porcentajeidp, $tipo_combustible, $estado)) {
                echo json_encode(['message' => 'Detalle de liquidación actualizado']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar detalle de liquidación']);
            }
            exit;
        }
        $detalle = new DetalleLiquidacion();
        $data = $detalle->getDetalleLiquidacionById($id);
        require '../views/detalle_liquidaciones/form.html';
    }

    public function deleteDetalleLiquidacion($id) {
        $detalle = new DetalleLiquidacion();
        if ($detalle->deleteDetalleLiquidacion($id)) {
            echo json_encode(['message' => 'Detalle de liquidación eliminado']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar detalle de liquidación']);
        }
        exit;
    }
}