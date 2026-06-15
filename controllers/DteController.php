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

    /**
     * Construye el mapa de índices de columnas a partir del encabezado del Excel.
     *
     * Soporta dos formatos:
     *  - 32 columnas (formato clásico, sin "Ubicación temporal")
     *  - 33 columnas (con la columna "Ubicación temporal" insertada después de "Exportación")
     *
     * Detecta dinámicamente si la columna "Ubicación temporal" está presente
     * buscándola por nombre en el encabezado, y ajusta los índices de todas
     * las columnas siguientes en consecuencia.
     *
     * @param array $header Fila de encabezado del Excel (array de nombres de columna)
     * @return array Mapa asociativo: nombre_campo => índice de columna en $row
     */
    private function buildColumnMap(array $header): array
    {
        // Mapa base asumiendo 32 columnas (SIN "Ubicación temporal")
        $map = [
            'fecha_emision'          => 0,
            'numero_autorizacion'    => 1,
            'tipo_dte'               => 2,
            'serie'                  => 3,
            'numero_dte'             => 4,
            'clasificacion_emisor'   => 5,
            'exportacion'            => 6,
            'nit_emisor'             => 7,
            'nombre_emisor'          => 8,
            'codigo_establecimiento' => 9,
            'nombre_establecimiento' => 10,
            'id_receptor'            => 11,
            'nombre_receptor'        => 12,
            'nit_certificador'       => 13,
            'nombre_certificador'    => 14,
            'estado'                 => 15,
            'moneda'                 => 16,
            'gran_total'             => 17,
            'iva'                    => 18,
            'marca_anulado'          => 19,
            'fecha_anulacion'        => 20,
            'petroleo'               => 21,
            'turismo_hospedaje'      => 22,
            'turismo_pasajes'        => 23,
            'timbre_prensa'          => 24,
            'bomberos'               => 25,
            'tasa_municipal'         => 26,
            'bebidas_alcoholicas'    => 27,
            'tabaco'                 => 28,
            'cemento'                => 29,
            'bebidas_no_alcoholicas' => 30,
            'tarifa_portuaria'       => 31,
        ];

        // Buscar columna "Ubicación temporal" en el encabezado (búsqueda flexible,
        // ignorando acentos/mayúsculas).
        $ubicacionIndex = null;
        foreach ($header as $colIndex => $colName) {
            $normalizado = $this->normalizarTexto((string) $colName);
            if (strpos($normalizado, 'ubicacion') !== false && strpos($normalizado, 'temporal') !== false) {
                $ubicacionIndex = $colIndex;
                break;
            }
        }

        // Si existe "Ubicación temporal", todas las columnas a partir de ese punto
        // (que en el mapa base son >= 7, es decir nit_emisor en adelante) se
        // recorren una posición hacia la derecha.
        if ($ubicacionIndex !== null) {
            foreach ($map as $campo => $indice) {
                if ($indice >= 7) {
                    $map[$campo] = $indice + 1;
                }
            }
            $map['__ubicacion_temporal_index'] = $ubicacionIndex;
            $map['__total_columnas'] = 33;
        } else {
            $map['__ubicacion_temporal_index'] = null;
            $map['__total_columnas'] = 32;
        }

        return $map;
    }

    /**
     * Normaliza texto para comparación: minúsculas y sin acentos.
     */
    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        $reemplazos = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'ñ' => 'n',
        ];
        return strtr($texto, $reemplazos);
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

                // Validar que el número de columnas sea al menos el mínimo esperado (32)
                if (count($header) < 32) {
                    $filesystem->delete($spacesPath); // Eliminar archivo si hay error
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'El archivo Excel debe tener al menos 32 columnas.']);
                    exit;
                }

                // Construir el mapa de columnas dinámicamente según el encabezado.
                // Soporta archivos con 32 columnas (sin "Ubicación temporal")
                // y con 33 columnas (con "Ubicación temporal").
                $colMap = $this->buildColumnMap($header);
                $minColumnas = $colMap['__total_columnas'];

                $insertedCount = 0;
                $duplicateCount = 0;
                $errorCount = 0;
                $anuladoCount = 0;

                foreach ($rows as $index => $row) {
                    // Saltar filas vacías
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Validar que la fila tenga suficientes columnas y datos esenciales
                    if (count($row) < $minColumnas
                        || empty($row[$colMap['numero_autorizacion']])
                        || empty($row[$colMap['serie']])
                        || empty($row[$colMap['numero_dte']])
                    ) {
                        error_log("Fila $index incompleta o sin datos esenciales: " . print_r($row, true));
                        $errorCount++;
                        continue;
                    }

                    // Validar el estado del DTE: solo se procesan los "Vigente".
                    $estado = trim((string) ($row[$colMap['estado']] ?? ''));
                    if (strcasecmp($estado, 'Anulado') === 0) {
                        $anuladoCount++;
                        error_log("DTE Anulado omitido - Fila $index: numero_autorizacion={$row[$colMap['numero_autorizacion']]}, serie={$row[$colMap['serie']]}, numero_dte={$row[$colMap['numero_dte']]}");
                        continue;
                    }

                    $data = [
                        'fecha_emision' => $row[$colMap['fecha_emision']] ?: null,
                        'numero_autorizacion' => $row[$colMap['numero_autorizacion']] ?: '',
                        'tipo_dte' => $row[$colMap['tipo_dte']] ?: '',
                        'serie' => $row[$colMap['serie']] ?: '',
                        'numero_dte' => $row[$colMap['numero_dte']] ?: 0,
                        'clasificacion_emisor' => (int) ($row[$colMap['clasificacion_emisor']] ?: 0),
                        'exportacion' => $row[$colMap['exportacion']] ?: '',
                        // La columna "Ubicación temporal" (si existe) no se guarda en la BD.
                        'nit_emisor' => $row[$colMap['nit_emisor']] ?: '',
                        'nombre_emisor' => $row[$colMap['nombre_emisor']] ?: '',
                        'codigo_establecimiento' => (string) ($row[$colMap['codigo_establecimiento']] ?: ''),
                        'nombre_establecimiento' => $row[$colMap['nombre_establecimiento']] ?: '',
                        'id_receptor' => $row[$colMap['id_receptor']] ?: '',
                        'nombre_receptor' => $row[$colMap['nombre_receptor']] ?: '',
                        'nit_certificador' => $row[$colMap['nit_certificador']] ?: '',
                        'nombre_certificador' => $row[$colMap['nombre_certificador']] ?: '',
                        'estado' => $row[$colMap['estado']] ?: '',
                        'moneda' => $row[$colMap['moneda']] ?: '',
                        'gran_total' => is_numeric($row[$colMap['gran_total']]) ? (float) $row[$colMap['gran_total']] : 0.00,
                        'iva' => is_numeric($row[$colMap['iva']]) ? (float) $row[$colMap['iva']] : 0.00,
                        'marca_anulado' => $row[$colMap['marca_anulado']] ?: '',
                        'fecha_anulacion' => $row[$colMap['fecha_anulacion']] ?: null,
                        'petroleo' => is_numeric($row[$colMap['petroleo']]) ? (float) $row[$colMap['petroleo']] : 0.00,
                        'turismo_hospedaje' => is_numeric($row[$colMap['turismo_hospedaje']]) ? (float) $row[$colMap['turismo_hospedaje']] : 0.00,
                        'turismo_pasajes' => is_numeric($row[$colMap['turismo_pasajes']]) ? (float) $row[$colMap['turismo_pasajes']] : 0.00,
                        'timbre_prensa' => is_numeric($row[$colMap['timbre_prensa']]) ? (float) $row[$colMap['timbre_prensa']] : 0.00,
                        'bomberos' => is_numeric($row[$colMap['bomberos']]) ? (float) $row[$colMap['bomberos']] : 0.00,
                        'tasa_municipal' => is_numeric($row[$colMap['tasa_municipal']]) ? (float) $row[$colMap['tasa_municipal']] : 0.00,
                        'bebidas_alcoholicas' => is_numeric($row[$colMap['bebidas_alcoholicas']]) ? (float) $row[$colMap['bebidas_alcoholicas']] : 0.00,
                        'tabaco' => is_numeric($row[$colMap['tabaco']]) ? (float) $row[$colMap['tabaco']] : 0.00,
                        'cemento' => is_numeric($row[$colMap['cemento']]) ? (float) $row[$colMap['cemento']] : 0.00,
                        'bebidas_no_alcoholicas' => is_numeric($row[$colMap['bebidas_no_alcoholicas']]) ? (float) $row[$colMap['bebidas_no_alcoholicas']] : 0.00,
                        'tarifa_portuaria' => is_numeric($row[$colMap['tarifa_portuaria']]) ? (float) $row[$colMap['tarifa_portuaria']] : 0.00,
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
                if ($anuladoCount > 0) {
                    $message .= "$anuladoCount DTEs anulados omitidos. ";
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
                        'anulados' => $anuladoCount,
                        'errors' => $errorCount,
                        'formato_columnas' => $minColumnas
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