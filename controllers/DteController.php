<?php
require_once '../config/database.php';
require_once '../models/DteModel.php';
require_once '../models/Usuario.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class DteController
{
    private $dteModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->dteModel = new DteModel();
        $this->usuarioModel = new Usuario();
    }

    public function index()
    {
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

    public function uploadExcel()
    {
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
                    'visibility' => 'public', // Hacer el archivo público
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
                    $filesystem->delete($spacesPath); // Eliminar archivo si hay error
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
                        error_log("Fila $index incompleta o sin datos esenciales: " . print_r($row, true));
                        $errorCount++;
                        continue;
                    }

                    $data = [
                        'fecha_emision' => $row[0] ?: null,
                        'numero_autorizacion' => $row[1] ?: '',
                        'tipo_dte' => $row[2] ?: '',
                        'serie' => $row[3] ?: '',
                        'numero_dte' => $row[4] ?: 0,
                        'clasificacion_emisor' => (int) ($row[5] ?: 0),
                        'exportacion' => $row[6] ?: '',
                        'nit_emisor' => $row[7] ?: '',
                        'nombre_emisor' => $row[8] ?: '',
                        'codigo_establecimiento' => (string) ($row[9] ?: ''),
                        'nombre_establecimiento' => $row[10] ?: '',
                        'id_receptor' => $row[11] ?: '',
                        'nombre_receptor' => $row[12] ?: '',
                        'nit_certificador' => $row[13] ?: '',
                        'nombre_certificador' => $row[14] ?: '',
                        'estado' => $row[15] ?: '',
                        'moneda' => $row[16] ?: '',           // Columna 16: Moneda ("GTQ", "USD")
                        'gran_total' => is_numeric($row[17]) ? (float) $row[17] : 0.00, // Columna 17: Gran Total (debe ser número)
                        'iva' => is_numeric($row[18]) ? (float) $row[18] : 0.00,        // Columna 18: IVA (debe ser número)
                        'marca_anulado' => $row[19] ?: '',
                        'fecha_anulacion' => $row[20] ?: null,
                        'petroleo' => is_numeric($row[21]) ? (float) $row[21] : 0.00,
                        'turismo_hospedaje' => is_numeric($row[22]) ? (float) $row[22] : 0.00,
                        'turismo_pasajes' => is_numeric($row[23]) ? (float) $row[23] : 0.00,
                        'timbre_prensa' => is_numeric($row[24]) ? (float) $row[24] : 0.00,
                        'bomberos' => is_numeric($row[25]) ? (float) $row[25] : 0.00,
                        'tasa_municipal' => is_numeric($row[26]) ? (float) $row[26] : 0.00,
                        'bebidas_alcoholicas' => is_numeric($row[27]) ? (float) $row[27] : 0.00,
                        'tabaco' => is_numeric($row[28]) ? (float) $row[28] : 0.00,
                        'cemento' => is_numeric($row[29]) ? (float) $row[29] : 0.00,
                        'bebidas_no_alcoholicas' => is_numeric($row[30]) ? (float) $row[30] : 0.00,
                        'tarifa_portuaria' => is_numeric($row[31]) ? (float) $row[31] : 0.00,
                        'usado' => 'X',
                        'file_url' => $fileUrl
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
                        error_log("DTE duplicado omitido - Fila $index: numero_autorizacion={$data['numero_autorizacion']}, serie={$data['serie']}, numero_dte={$data['numero_dte']}");
                        continue;
                    }

                    if ($this->dteModel->insertDte($data)) {
                        $insertedCount++;
                    } else {
                        $errorCount++;
                        error_log("Error al insertar DTE para fila $index: " . print_r($data, true));
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
                    $filesystem->delete($spacesPath); // Eliminar archivo en caso de error
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

    public function searchByNit()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $nit = isset($_GET['nit']) ? trim($_GET['nit']) : '';
        $serie = isset($_GET['serie']) ? trim($_GET['serie']) : ''; // Agregar parámetro serie
        $fechaInicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
        $fechaFin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;

        if (empty($nit) && empty($serie)) { // Validar ambos campos
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        try {
            // Modificar el modelo para aceptar ambos parámetros
            $dtes = $this->dteModel->getDtesByNitOrSerie($nit, $serie, $fechaInicio, $fechaFin);
            header('Content-Type: application/json');
            echo json_encode($dtes);
            exit;
        } catch (Exception $e) {
            error_log("Error al buscar DTEs: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al buscar DTEs']);
            exit;
        }
    }

    public function searchDtes()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $nit = isset($_GET['nit']) ? trim($_GET['nit']) : '';
        $serie = isset($_GET['serie']) ? trim($_GET['serie']) : '';
        $fechaInicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : null;
        $fechaFin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : null;

        if (empty($nit) && empty($serie)) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        try {
            $dtes = $this->dteModel->searchDtes($nit, $serie, $fechaInicio, $fechaFin);
            header('Content-Type: application/json');
            echo json_encode($dtes);
            exit;
        } catch (Exception $e) {
            error_log("Error al buscar DTEs: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al buscar DTEs']);
            exit;
        }
    }
}