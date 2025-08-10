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
            $uploadDir = '../Uploads/';
            $uploadFile = $uploadDir . basename($file['name']);

            // Validar tipo de archivo
            $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
            if ($fileType != 'xls' && $fileType != 'xlsx') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos Excel (.xls, .xlsx).']);
                exit;
            }

            // Mover archivo a la carpeta uploads
            if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al subir el archivo.']);
                exit;
            }

            try {
                // Leer archivo Excel con PHPSpreadsheet
                $spreadsheet = IOFactory::load($uploadFile);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
                $header = array_shift($rows); // Quitar encabezado

                // Validar que el número de columnas sea el esperado (32)
                if (count($header) < 32) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'El archivo Excel debe tener al menos 32 columnas.']);
                    exit;
                }

                $insertedCount = 0;
                $duplicateCount = 0;

                foreach ($rows as $row) {
                    // Validar que la fila tenga suficientes columnas
                    if (count($row) < 32) {
                        error_log("Fila incompleta detectada: " . print_r($row, true));
                        continue; // Saltar filas incompletas
                    }

                    $data = [
                        'fecha_emision' => $row[0] ?: null,
                        'numero_autorizacion' => $row[1] ?: '',
                        'tipo_dte' => $row[2] ?: '',
                        'serie' => $row[3] ?: '',
                        'numero_dte' => $row[4] ?: 0,
                        'clasificacion_emisor' => $row[5] ?: 0,
                        'exportacion' => $row[6] ?: '',
                        'nit_emisor' => $row[7] ?: '',
                        'nombre_emisor' => $row[8] ?: '',
                        'codigo_establecimiento' => $row[9] ?: 0,
                        'nombre_establecimiento' => $row[10] ?: '',
                        'id_receptor' => $row[11] ?: '',
                        'nombre_receptor' => $row[12] ?: '',
                        'nit_certificador' => $row[13] ?: '',
                        'nombre_certificador' => $row[14] ?: '',
                        'estado' => $row[15] ?: '',
                        'moneda' => $row[16] ?: '',
                        'gran_total' => $row[17] ?: 0.00,
                        'iva' => $row[18] ?: 0.00,
                        'marca_anulado' => $row[19] ?: '',
                        'fecha_anulacion' => $row[20] ?: null,
                        'petroleo' => $row[21] ?: 0.00,
                        'turismo_hospedaje' => $row[22] ?: 0.00,
                        'turismo_pasajes' => $row[23] ?: 0.00,
                        'timbre_prensa' => $row[24] ?: 0.00,
                        'bomberos' => $row[25] ?: 0.00,
                        'tasa_municipal' => $row[26] ?: 0.00,
                        'bebidas_alcoholicas' => $row[27] ?: 0.00,
                        'tabaco' => $row[28] ?: 0.00,
                        'cemento' => $row[29] ?: 0.00,
                        'bebidas_no_alcoholicas' => $row[30] ?: 0.00,
                        'tarifa_portuaria' => $row[31] ?: 0.00,
                        'usado' => 'X' // Default value for usado
                    ];

                    // Verificar duplicados antes de insertar
                    if ($this->dteModel->isDteDuplicate($data['numero_autorizacion'], $data['serie'], $data['numero_dte'])) {
                        $duplicateCount++;
                        error_log("DTE duplicado omitido: numero_autorizacion={$data['numero_autorizacion']}, serie={$data['serie']}, numero_dte={$data['numero_dte']}");
                        continue;
                    }

                    if ($this->dteModel->insertDte($data)) {
                        $insertedCount++;
                    } else {
                        error_log("Error al insertar DTE para fila: " . print_r($data, true));
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Error al guardar los datos en la base de datos.']);
                        exit;
                    }
                }

                // Generar mensaje según los resultados
                if ($duplicateCount > 0 && $insertedCount == 0) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => "No se procesaron los datos. Se encontraron $duplicateCount DTEs duplicados."]);
                    exit;
                } elseif ($duplicateCount > 0) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => "Archivo procesado: $insertedCount DTEs guardados correctamente, $duplicateCount DTEs duplicados omitidos."]);
                    exit;
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => "Archivo procesado: $insertedCount DTEs guardados correctamente."]);
                    exit;
                }
            } catch (Exception $e) {
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

    public function searchByNit() {
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
            echo json_encode(['error' => 'No tienes permiso para buscar DTEs']);
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