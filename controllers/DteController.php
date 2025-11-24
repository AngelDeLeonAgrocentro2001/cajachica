<?php
require_once '../config/database.php';
require_once '../models/DteModel.php';
require_once '../models/Usuario.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class DteController {
    private $dteModel;
    private $usuarioModel;

    public function __construct() {
        $this->dteModel = new DteModel();
        $this->usuarioModel = new Usuario();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$this->usuarioModel->tienePermiso($usuario, 'manage_dte')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para cargar DTE']);
            exit;
        }
        require_once '../views/dte/upload.php';
    }

    public function uploadExcel() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        
        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$this->usuarioModel->tienePermiso($usuario, 'manage_dte')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para cargar DTE']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file'];

            // Validar tipo de archivo
            $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($fileType != 'xls' && $fileType != 'xlsx') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos Excel (.xls, .xlsx).']);
                exit;
            }

            try {
                // Configurar Flysystem
                require_once '../config/spaces.php';
                $filesystem = getSpacesFilesystem();

                // Generar nombre único para el archivo en Spaces
                $fileName = uniqid() . '_' . basename($file['name']);
                $spacesPath = 'CAJA_CHICA/Uploads/' . $fileName;

                // Subir archivo a Spaces
                $stream = fopen($file['tmp_name'], 'r+');
                $filesystem->writeStream($spacesPath, $stream, [
                    'visibility' => 'public',
                    'ContentType' => $file['type'],
                ]);
                fclose($stream);

                // Obtener la URL pública
                $fileUrl = getPublicUrl($spacesPath);

                // Leer archivo Excel con PHPSpreadsheet
                $spreadsheet = IOFactory::load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
                $header = array_shift($rows); // Quitar encabezado

                // Validar que el número de columnas sea el esperado (32)
                if (count($header) < 32) {
                    $filesystem->delete($spacesPath);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'El archivo Excel debe tener al menos 32 columnas.']);
                    exit;
                }

                $insertedCount = 0;
                $duplicateCount = 0;
                $errorCount = 0;

                foreach ($rows as $index => $row) {
                    // Saltar filas vacías
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Validar que la fila tenga suficientes columnas y datos esenciales
                    if (count($row) < 32 || empty($row[1]) || empty($row[3]) || empty($row[4])) {
                        error_log("Fila $index incompleta o sin datos esenciales");
                        $errorCount++;
                        continue;
                    }

                    // Preparar datos según estructura de BD
                    $data = [
                        'fecha_emision' => $this->convertExcelDate($row[0]),
                        'numero_autorizacion' => (string)$row[1],
                        'tipo_dte' => (string)$row[2],
                        'serie' => (string)$row[3],
                        'numero_dte' => (string)$row[4], // varchar en BD
                        'clasificacion_emisor' => intval($row[5]),
                        'exportacion' => (string)$row[6],
                        'nit_emisor' => (string)$row[7],
                        'nombre_emisor' => (string)$row[8],
                        'codigo_establecimiento' => intval($row[9]),
                        'nombre_establecimiento' => (string)$row[10],
                        'id_receptor' => (string)$row[11],
                        'nombre_receptor' => (string)$row[12],
                        'nit_certificador' => (string)$row[13],
                        'nombre_certificador' => (string)$row[14],
                        'estado' => (string)$row[15],
                        'moneda' => (string)$row[16],
                        'gran_total' => floatval($row[17]),
                        'iva' => floatval($row[18]),
                        'marca_anulado' => (string)$row[19],
                        'fecha_anulacion' => $this->convertExcelDate($row[20]),
                        'petroleo' => floatval($row[21]),
                        'turismo_hospedaje' => floatval($row[22]),
                        'turismo_pasajes' => floatval($row[23]),
                        'timbre_prensa' => floatval($row[24]),
                        'bomberos' => floatval($row[25]),
                        'tasa_municipal' => floatval($row[26]),
                        'bebidas_alcoholicas' => floatval($row[27]),
                        'tabaco' => floatval($row[28]),
                        'cemento' => floatval($row[29]),
                        'bebidas_no_alcoholicas' => floatval($row[30]),
                        'tarifa_portuaria' => floatval($row[31])
                    ];

                    // Validar campos esenciales para duplicados
                    if (empty($data['numero_autorizacion']) || empty($data['serie']) || empty($data['numero_dte'])) {
                        error_log("Fila $index: Campos esenciales vacíos para verificación de duplicado");
                        $errorCount++;
                        continue;
                    }

                    // Verificar duplicados antes de insertar
                    if ($this->dteModel->isDteDuplicate($data['numero_autorizacion'], $data['serie'], $data['numero_dte'])) {
                        $duplicateCount++;
                        continue;
                    }

                    if ($this->dteModel->insertDte($data)) {
                        $insertedCount++;
                    } else {
                        $errorCount++;
                    }
                }

                // Generar mensaje según los resultados
                $message = "Archivo procesado: ";
                if ($insertedCount > 0) {
                    $message .= "$insertedCount DTEs guardados correctamente. ";
                }
                if ($duplicateCount > 0) {
                    $message .= "$duplicateCount DTEs duplicados omitidos. ";
                }
                if ($errorCount > 0) {
                    $message .= "$errorCount DTEs con errores. ";
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $insertedCount > 0,
                    'message' => $message,
                    'file_url' => $fileUrl,
                    'stats' => [
                        'inserted' => $insertedCount,
                        'duplicates' => $duplicateCount,
                        'errors' => $errorCount
                    ]
                ]);
                exit;

            } catch (Exception $e) {
                if (isset($filesystem) && $filesystem->has($spacesPath)) {
                    $filesystem->delete($spacesPath);
                }
                error_log("Error al procesar el archivo Excel: " . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al procesar el archivo Excel: ' . $e->getMessage()]);
                exit;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No se recibió ningún archivo.']);
        exit;
    }

    private function convertExcelDate($excelDate) {
        if (empty($excelDate)) {
            return null;
        }
        
        // Si es una fecha de Excel (número serial)
        if (is_numeric($excelDate)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelDate);
                return $date->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                error_log("Error convirtiendo fecha Excel: " . $e->getMessage());
            }
        }
        
        // Intentar convertir desde string
        try {
            $timestamp = strtotime($excelDate);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        } catch (Exception $e) {
            error_log("Error convirtiendo fecha string: " . $e->getMessage());
        }
        
        return null;
    }

    public function searchByNit() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $nit = isset($_GET['nit']) ? trim($_GET['nit']) : '';
        $fechaInicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
        $fechaFin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;

        if (empty($nit)) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        try {
            $dtes = $this->dteModel->getDtesByNit($nit, $fechaInicio, $fechaFin);
            header('Content-Type: application/json');
            echo json_encode($dtes);
            exit;
        } catch (Exception $e) {
            error_log("Error al buscar DTEs por NIT: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al buscar DTEs']);
            exit;
        }
    }
}