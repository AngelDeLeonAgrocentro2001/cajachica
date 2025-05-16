<?php
require_once '../models/Liquidacion.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/CajaChica.php';
require_once '../models/Usuario.php';

// Asegúrate de incluir las librerías
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class ReportesController {
    private $liquidacionModel;
    private $detalleLiquidacionModel;
    private $cajaChicaModel;
    private $usuarioModel;

    public function __construct() {
        $this->liquidacionModel = new Liquidacion();
        $this->detalleLiquidacionModel = new DetalleLiquidacion();
        $this->cajaChicaModel = new CajaChica();
        $this->usuarioModel = new Usuario();
    }

    public function list() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para generar reportes']);
            exit;
        }

        require '../views/reportes/list.html';
        exit;
    }

    public function generarResumen() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para generar reportes']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $fechaInicio = $_POST['fecha_inicio'] ?? '';
                $fechaFin = $_POST['fecha_fin'] ?? '';
                $idCajaChica = $_POST['id_caja_chica'] ?? '';
                $formato = $_POST['formato'] ?? 'html';

                if (empty($fechaInicio) || empty($fechaFin)) {
                    throw new Exception('Las fechas de inicio y fin son obligatorias.');
                }

                $liquidaciones = $this->liquidacionModel->getLiquidacionesByFecha($fechaInicio, $fechaFin, $idCajaChica);

                if ($formato === 'pdf') {
                    $this->exportResumenToPDF($liquidaciones, $fechaInicio, $fechaFin, $idCajaChica);
                    exit;
                } elseif ($formato === 'excel') {
                    $this->exportResumenToExcel($liquidaciones, $fechaInicio, $fechaFin, $idCajaChica);
                    exit;
                }

                // Generar HTML para el reporte
                ob_start();
                ?>
                <h2>Reporte de Resumen</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Caja Chica</th>
                            <th>Fecha Creación</th>
                            <th>Monto Total</th>
                            <th>Total Gastos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($liquidaciones as $liquidacion): ?>
                            <tr>
                                <td data-label="ID"><?php echo htmlspecialchars($liquidacion['id']); ?></td>
                                <td data-label="Caja Chica"><?php echo htmlspecialchars($liquidacion['caja_chica']); ?></td>
                                <td data-label="Fecha Creación"><?php echo htmlspecialchars($liquidacion['fecha_creacion']); ?></td>
                                <td data-label="Monto Total"><?php echo htmlspecialchars($liquidacion['monto_total']); ?></td>
                                <td data-label="Total Gastos"><?php echo htmlspecialchars($liquidacion['total_gastos'] ?? '0.00'); ?></td>
                                <td data-label="Estado"><?php echo htmlspecialchars($liquidacion['estado']); ?></td>
                                <td data-label="Acciones">
                                    <button class="btn-primary" onclick="showDetalles(<?php echo $liquidacion['id']; ?>)">Ver Detalles</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="export-buttons">
                    <button class="btn-export btn-export-pdf" onclick="exportReport('resumen', 'pdf')">Exportar a PDF</button>
                    <button class="btn-export" onclick="exportReport('resumen', 'excel')">Exportar a Excel</button>
                </div>
                <?php
                $html = ob_get_clean();
                echo $html;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al generar el reporte: ' . $e->getMessage()]);
            }
            exit;
        }

        $cajasChicas = $this->cajaChicaModel->getAllCajasChicas();
        require '../views/reportes/resumen_form.html';
        exit;
    }

    public function generarDetalle() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para generar reportes']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $fechaInicio = $_POST['fecha_inicio'] ?? '';
                $fechaFin = $_POST['fecha_fin'] ?? '';
                $idCajaChica = $_POST['id_caja_chica'] ?? '';
                $formato = $_POST['formato'] ?? 'html';

                if (empty($fechaInicio) || empty($fechaFin)) {
                    throw new Exception('Las fechas de inicio y fin son obligatorias.');
                }

                $detalles = $this->detalleLiquidacionModel->getDetallesByFecha($fechaInicio, $fechaFin, $idCajaChica);

                if ($formato === 'pdf') {
                    $this->exportDetalleToPDF($detalles, $fechaInicio, $fechaFin, $idCajaChica);
                    exit;
                } elseif ($formato === 'excel') {
                    $this->exportDetalleToExcel($detalles, $fechaInicio, $fechaFin, $idCajaChica);
                    exit;
                }

                // Generar HTML para el reporte
                ob_start();
                ?>
                <h2>Reporte de Detalle</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Liquidación Fecha</th>
                            <th>Caja Chica</th>
                            <th>No. Factura</th>
                            <th>Nombre Proveedor</th>
                            <th>Fecha</th>
                            <th>Total Factura</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td data-label="ID"><?php echo htmlspecialchars($detalle['id']); ?></td>
                                <td data-label="Liquidación Fecha"><?php echo htmlspecialchars($detalle['liquidacion_fecha']); ?></td>
                                <td data-label="Caja Chica"><?php echo htmlspecialchars($detalle['caja_chica']); ?></td>
                                <td data-label="No. Factura"><?php echo htmlspecialchars($detalle['no_factura']); ?></td>
                                <td data-label="Nombre Proveedor"><?php echo htmlspecialchars($detalle['nombre_proveedor'] ?? 'N/A'); ?></td>
                                <td data-label="Fecha"><?php echo htmlspecialchars($detalle['fecha']); ?></td>
                                <td data-label="Total Factura"><?php echo htmlspecialchars($detalle['total_factura']); ?></td>
                                <td data-label="Estado"><?php echo htmlspecialchars($detalle['estado']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="export-buttons">
                    <button class="btn-export" onclick="exportReport('detalle', 'pdf')">Exportar a PDF</button>
                    <button class="btn-export" onclick="exportReport('detalle', 'excel')">Exportar a Excel</button>
                </div>
                <?php
                $html = ob_get_clean();
                echo $html;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al generar el reporte: ' . $e->getMessage()]);
            }
            exit;
        }

        $cajasChicas = $this->cajaChicaModel->getAllCajasChicas();
        require '../views/reportes/detalle_form.html';
        exit;
    }

    private function exportResumenToPDF($liquidaciones, $fechaInicio, $fechaFin, $idCajaChica) {
        // Limpiar cualquier buffer de salida previo
        while (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Landscape orientation
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_left' => 15,
                'margin_right' => 15,
            ]);
            $mpdf->SetTitle('Reporte de Resumen');
            $mpdf->SetAuthor('AgroCaja Chica');

            // Generar el contenido HTML para el PDF
            $html = '<h1 style="text-align: center; color: #2c3e50;">Reporte de Resumen de Liquidaciones</h1>';
            $html .= "<p style='text-align: center; color: #555;'>Fecha Inicio: " . htmlspecialchars($fechaInicio) . " | Fecha Fin: " . htmlspecialchars($fechaFin) . "</p>";
            if ($idCajaChica) {
                $caja = $this->cajaChicaModel->getCajaChicaById($idCajaChica);
                $cajaNombre = $caja ? $caja['nombre'] : 'Desconocida';
                $html .= "<p style='text-align: center; color: #555;'>Caja Chica: " . htmlspecialchars($cajaNombre) . "</p>";
            }

            $html .= '<table border="1" style="width:100%; border-collapse:collapse; font-size: 12px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color:#2c3e50; color:white;">';
            $html .= '<th style="padding: 8px;">ID</th>';
            $html .= '<th style="padding: 8px;">Caja Chica</th>';
            $html .= '<th style="padding: 8px;">Fecha Creación</th>';
            $html .= '<th style="padding: 8px;">Monto Total</th>';
            $html .= '<th style="padding: 8px;">Total Gastos</th>';
            $html .= '<th style="padding: 8px;">Estado</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($liquidaciones as $liquidacion) {
                $id = isset($liquidacion['id']) ? (string)$liquidacion['id'] : '';
                $cajaChica = isset($liquidacion['caja_chica']) ? (string)$liquidacion['caja_chica'] : '';
                $fechaCreacion = isset($liquidacion['fecha_creacion']) ? (string)$liquidacion['fecha_creacion'] : '';
                $montoTotal = isset($liquidacion['monto_total']) ? (string)$liquidacion['monto_total'] : '';
                $totalGastos = isset($liquidacion['total_gastos']) ? (string)$liquidacion['total_gastos'] : '0.00';
                $estado = isset($liquidacion['estado']) ? (string)$liquidacion['estado'] : '';

                $html .= '<tr>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($id) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($cajaChica) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($fechaCreacion) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($montoTotal) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($totalGastos) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($estado) . '</td>';
                $html .= '</tr>';

                // Fetch and add detalles for this liquidation
                $detalles = $this->detalleLiquidacionModel->getDetallesByLiquidacionId($liquidacion['id']);
                if (!empty($detalles)) {
                    $html .= '<tr>';
                    $html .= '<td colspan="6" style="padding: 8px; background-color: #f0f0f0; font-weight: bold;">Detalles de Liquidación #' . htmlspecialchars($id) . '</td>';
                    $html .= '</tr>';
                    $html .= '<tr style="background-color:#4a6a8a; color:white;">';
                    $html .= '<th style="padding: 8px;">ID Detalle</th>';
                    $html .= '<th style="padding: 8px;">No. Factura</th>';
                    $html .= '<th style="padding: 8px;">Nombre Proveedor</th>';
                    $html .= '<th style="padding: 8px;">Fecha</th>';
                    $html .= '<th style="padding: 8px;">Total Factura</th>';
                    $html .= '<th style="padding: 8px;">Estado</th>';
                    $html .= '</tr>';

                    foreach ($detalles as $detalle) {
                        $detalleId = isset($detalle['id']) ? (string)$detalle['id'] : '';
                        $noFactura = isset($detalle['no_factura']) ? (string)$detalle['no_factura'] : '';
                        $nombreProveedor = isset($detalle['nombre_proveedor']) ? (string)$detalle['nombre_proveedor'] : 'N/A';
                        $fecha = isset($detalle['fecha']) ? (string)$detalle['fecha'] : '';
                        $totalFactura = isset($detalle['total_factura']) ? (string)$detalle['total_factura'] : '';
                        $estadoDetalle = isset($detalle['estado']) ? (string)$detalle['estado'] : '';

                        $html .= '<tr>';
                        $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($detalleId) . '</td>';
                        $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($noFactura) . '</td>';
                        $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($nombreProveedor) . '</td>';
                        $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($fecha) . '</td>';
                        $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($totalFactura) . '</td>';
                        $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($estadoDetalle) . '</td>';
                        $html .= '</tr>';
                    }
                }
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $mpdf->WriteHTML($html);

            // Enviar el PDF al navegador
            $mpdf->Output('reporte_resumen.pdf', 'D');
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al generar el PDF con mPDF: ' . $e->getMessage()]);
            exit;
        }
    }

    private function exportResumenToExcel($liquidaciones, $fechaInicio, $fechaFin, $idCajaChica) {
        // Limpiar cualquier buffer de salida previo
        if (ob_get_length()) {
            ob_end_clean();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de Resumen de Liquidaciones');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Filtros
        $sheet->setCellValue('A2', "Fecha Inicio: $fechaInicio");
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A3', "Fecha Fin: $fechaFin");
        $sheet->mergeCells('A3:F3');
        if ($idCajaChica) {
            $caja = $this->cajaChicaModel->getCajaChicaById($idCajaChica);
            $sheet->setCellValue('A4', "Caja Chica: " . ($caja ? $caja['nombre'] : 'Desconocida'));
            $sheet->mergeCells('A4:F4');
        }

        // Encabezados de la tabla
        $sheet->setCellValue('A5', 'ID');
        $sheet->setCellValue('B5', 'Caja Chica');
        $sheet->setCellValue('C5', 'Fecha Creación');
        $sheet->setCellValue('D5', 'Monto Total');
        $sheet->setCellValue('E5', 'Total Gastos');
        $sheet->setCellValue('F5', 'Estado');
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF2C3E50');
        $sheet->getStyle('A5:F5')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Contenido de la tabla
        $row = 6;
        foreach ($liquidaciones as $liquidacion) {
            $sheet->setCellValue("A$row", $liquidacion['id']);
            $sheet->setCellValue("B$row", $liquidacion['caja_chica']);
            $sheet->setCellValue("C$row", $liquidacion['fecha_creacion']);
            $sheet->setCellValue("D$row", $liquidacion['monto_total']);
            $sheet->setCellValue("E$row", $liquidacion['total_gastos'] ?? '0.00');
            $sheet->setCellValue("F$row", $liquidacion['estado']);
            $row++;
        }

        // Ajustar el ancho de las columnas
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_resumen.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    private function exportDetalleToPDF($detalles, $fechaInicio, $fechaFin, $idCajaChica) {
        // Limpiar cualquier buffer de salida previo
        while (ob_get_level()) {
            ob_end_clean();
        }

        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_left' => 15,
                'margin_right' => 15,
            ]);
            $mpdf->SetTitle('Reporte de Detalle');
            $mpdf->SetAuthor('AgroCaja Chica');

            // Generar el contenido HTML para el PDF
            $html = '<h1 style="text-align: center; color: #2c3e50;">Reporte de Detalle de Liquidaciones</h1>';
            $html .= "<p style='text-align: center; color: #555;'>Fecha Inicio: " . htmlspecialchars($fechaInicio) . " | Fecha Fin: " . htmlspecialchars($fechaFin) . "</p>";
            if ($idCajaChica) {
                $caja = $this->cajaChicaModel->getCajaChicaById($idCajaChica);
                $cajaNombre = $caja ? $caja['nombre'] : 'Desconocida';
                $html .= "<p style='text-align: center; color: #555;'>Caja Chica: " . htmlspecialchars($cajaNombre) . "</p>";
            }

            $html .= '<table border="1" style="width:100%; border-collapse:collapse; font-size: 12px;">';
            $html .= '<thead>';
            $html .= '<tr style="background-color:#2c3e50; color:white;">';
            $html .= '<th style="padding: 8px;">ID</th>';
            $html .= '<th style="padding: 8px;">Liquidación Fecha</th>';
            $html .= '<th style="padding: 8px;">Caja Chica</th>';
            $html .= '<th style="padding: 8px;">No. Factura</th>';
            $html .= '<th style="padding: 8px;">Nombre Proveedor</th>';
            $html .= '<th style="padding: 8px;">Fecha</th>';
            $html .= '<th style="padding: 8px;">Total Factura</th>';
            $html .= '<th style="padding: 8px;">Estado</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($detalles as $detalle) {
                $id = isset($detalle['id']) ? (string)$detalle['id'] : '';
                $liquidacionFecha = isset($detalle['liquidacion_fecha']) ? (string)$detalle['liquidacion_fecha'] : '';
                $cajaChica = isset($detalle['caja_chica']) ? (string)$detalle['caja_chica'] : '';
                $noFactura = isset($detalle['no_factura']) ? (string)$detalle['no_factura'] : '';
                $nombreProveedor = isset($detalle['nombre_proveedor']) ? (string)$detalle['nombre_proveedor'] : 'N/A';
                $fecha = isset($detalle['fecha']) ? (string)$detalle['fecha'] : '';
                $totalFactura = isset($detalle['total_factura']) ? (string)$detalle['total_factura'] : '';
                $estado = isset($detalle['estado']) ? (string)$detalle['estado'] : '';

                $html .= '<tr>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($id) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($liquidacionFecha) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($cajaChica) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($noFactura) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($nombreProveedor) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($fecha) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($totalFactura) . '</td>';
                $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($estado) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $mpdf->WriteHTML($html);

            // Enviar el PDF al navegador
            $mpdf->Output('reporte_detalle.pdf', 'D');
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al generar el PDF con mPDF: ' . $e->getMessage()]);
            exit;
        }
    }

    private function exportDetalleToExcel($detalles, $fechaInicio, $fechaFin, $idCajaChica) {
        // Limpiar cualquier buffer de salida previo
        if (ob_get_length()) {
            ob_end_clean();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'Reporte de Detalle de Liquidaciones');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Filtros
        $sheet->setCellValue('A2', "Fecha Inicio: $fechaInicio");
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A3', "Fecha Fin: $fechaFin");
        $sheet->mergeCells('A3:H3');
        if ($idCajaChica) {
            $caja = $this->cajaChicaModel->getCajaChicaById($idCajaChica);
            $sheet->setCellValue('A4', "Caja Chica: " . ($caja ? $caja['nombre'] : 'Desconocida'));
            $sheet->mergeCells('A4:H4');
        }

        // Encabezados de la tabla
        $sheet->setCellValue('A5', 'ID');
        $sheet->setCellValue('B5', 'Liquidación Fecha');
        $sheet->setCellValue('C5', 'Caja Chica');
        $sheet->setCellValue('D5', 'No. Factura');
        $sheet->setCellValue('E5', 'Nombre Proveedor');
        $sheet->setCellValue('F5', 'Fecha');
        $sheet->setCellValue('G5', 'Total Factura');
        $sheet->setCellValue('H5', 'Estado');
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF2C3E50');
        $sheet->getStyle('A5:H5')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Contenido de la tabla
        $row = 6;
        foreach ($detalles as $detalle) {
            $sheet->setCellValue("A$row", $detalle['id']);
            $sheet->setCellValue("B$row", $detalle['liquidacion_fecha']);
            $sheet->setCellValue("C$row", $detalle['caja_chica']);
            $sheet->setCellValue("D$row", $detalle['no_factura']);
            $sheet->setCellValue("E$row", $detalle['nombre_proveedor'] ?? 'N/A');
            $sheet->setCellValue("F$row", $detalle['fecha']);
            $sheet->setCellValue("G$row", $detalle['total_factura']);
            $sheet->setCellValue("H$row", $detalle['estado']);
            $row++;
        }

        // Ajustar el ancho de las columnas
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_detalle.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // New method to fetch detalles for a specific liquidation
    public function getDetallesByLiquidacion() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $idLiquidacion = $_POST['id_liquidacion'] ?? null;
        if (!$idLiquidacion) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'ID de liquidación no proporcionado']);
            exit;
        }
    
        try {
            $detalles = $this->detalleLiquidacionModel->getDetallesByLiquidacionId($idLiquidacion);
            ob_start();
            ?>
            <h2>Detalles de Liquidación #<?php echo htmlspecialchars($idLiquidacion); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Documento</th>
                        <th>No. Factura</th>
                        <th>Proveedor</th>
                        <th>NIT</th>
                        <th>DPI</th>
                        <th>Cantidad</th>
                        <th>Serie</th>
                        <th>Centro de Costo</th>
                        <th>Tipo de Gasto</th>
                        <th>Tipo de Combustible</th>
                        <th>Cuenta Contable</th>
                        <th>Fecha</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>IDP</th>
                        <th>INGUAT</th>
                        <th>Total Bruto</th>
                        <th>Estado</th>
                        <th>Archivos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($detalles)): ?>
                        <tr>
                            <td colspan="20" style="text-align: center;">No hay detalles disponibles.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td data-label="ID"><?php echo htmlspecialchars($detalle['id']); ?></td>
                                <td data-label="Tipo de Documento"><?php echo htmlspecialchars($detalle['tipo_documento']); ?></td>
                                <td data-label="No. Factura"><?php echo htmlspecialchars($detalle['no_factura']); ?></td>
                                <td data-label="Proveedor"><?php echo htmlspecialchars($detalle['nombre_proveedor']); ?></td>
                                <td data-label="NIT"><?php echo htmlspecialchars($detalle['nit_proveedor'] ?? 'N/A'); ?></td>
                                <td data-label="DPI"><?php echo htmlspecialchars($detalle['dpi'] ?? 'N/A'); ?></td>
                                <td data-label="Cantidad"><?php echo htmlspecialchars($detalle['cantidad'] ?? 'N/A'); ?></td>
                                <td data-label="Serie"><?php echo htmlspecialchars($detalle['serie'] ?? 'N/A'); ?></td>
                                <td data-label="Centro de Costo"><?php echo htmlspecialchars($detalle['nombre_centro_costo'] ?? 'N/A'); ?></td>
                                <td data-label="Tipo de Gasto"><?php echo htmlspecialchars($detalle['t_gasto']); ?></td>
                                <td data-label="Tipo de Combustible"><?php echo htmlspecialchars($detalle['tipo_combustible'] ?? 'N/A'); ?></td>
                                <td data-label="Cuenta Contable"><?php echo htmlspecialchars($detalle['cuenta_contable_nombre'] ?? 'N/A'); ?></td>
                                <td data-label="Fecha"><?php echo htmlspecialchars($detalle['fecha']); ?></td>
                                <td data-label="Subtotal"><?php echo number_format($detalle['subtotal'], 2); ?></td>
                                <td data-label="IVA"><?php echo number_format($detalle['iva'] ?? 0, 2); ?></td>
                                <td data-label="IDP"><?php echo number_format($detalle['idp'] ?? 0, 2); ?></td>
                                <td data-label="INGUAT"><?php echo number_format($detalle['inguat'] ?? 0, 2); ?></td>
                        <td data-label="Total Bruto"><?php echo number_format($detalle['total_factura'], 2); ?></td>
                        <td data-label="Estado"><?php echo htmlspecialchars($detalle['estado']); ?></td>
                        <td data-label="Archivos">
                            <?php
                            $rutas = !empty($detalle['rutas_archivos']) ? json_decode($detalle['rutas_archivos'], true) : [];
                            if (is_array($rutas) && !empty($rutas)) {
                                foreach ($rutas as $ruta) {
                                    echo '<div><a href="../' . htmlspecialchars($ruta) . '" target="_blank">Ver Archivo</a></div>';
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="export-buttons">
            <button class="btn-export btn-export-pdf" onclick="exportDetallesToPDF(<?php echo $idLiquidacion; ?>)">Exportar a PDF</button>
        </div>
        <?php
        $html = ob_get_clean();
        echo $html;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener los detalles: ' . $e->getMessage()]);
        }
        exit;
    }

    public function exportDetallesToPDF($idLiquidacion) {
        // Ensure no output occurs before PDF generation
        if (ob_get_length()) {
            ob_end_clean();
        }
    
        try {
            // Validate input
            if (!is_numeric($idLiquidacion) || $idLiquidacion <= 0) {
                throw new Exception('ID de liquidación inválido: ' . $idLiquidacion);
            }
    
            // Log the start of the process
            error_log('Starting PDF generation for liquidation #' . $idLiquidacion);
    
            // Fetch data
            $detalles = $this->detalleLiquidacionModel->getDetallesByLiquidacionId($idLiquidacion);
            if ($detalles === false) {
                throw new Exception('Error al obtener detalles de la liquidación: método getDetallesByLiquidacionId devolvió false');
            }
    
            $liquidacion = $this->liquidacionModel->getLiquidacionById($idLiquidacion);
            if ($liquidacion === false) {
                throw new Exception('Error al obtener liquidación: método getLiquidacionById devolvió false');
            }
    
            // Fetch Caja Chica name
            $cajaChica = $this->cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
            $nombre_caja_chica = $cajaChica['nombre'] ?? 'N/A';
    
            // Enrich detalles with cuenta_contable_nombre
            $cuentaContableModel = new CuentaContable();
            foreach ($detalles as &$detalle) {
                $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
                $detalle['cuenta_contable_nombre'] = $cuentaContable['nombre'] ?? 'N/A';
            }
            unset($detalle);
    
            error_log('Data fetched successfully for liquidation #' . $idLiquidacion);
    
            // Initialize mPDF with custom settings
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Landscape orientation
                'margin_top' => 40,
                'margin_bottom' => 50, // Enough space for the logo and page number
                'margin_left' => 20,
                'margin_right' => 20,
                'default_font_size' => 10,
                'default_font' => 'Helvetica',
            ]);
            $mpdf->SetTitle('Reporte de Detalles de Liquidación');
            $mpdf->SetAuthor('AgroCaja Chica');
    
            // Set footer content for every page
            $footerHtml = '
                <div style="text-align: center; color: #7f8c8d; font-size: 9px;">
                    <img src="https://agrocentro.com/wp-content/uploads/2024/03/LOGO-VERTICAL.svg" alt="AgroCaja Chica Logo" style="max-width: 120px; margin-bottom: 5px;">
                    <p>Página {PAGENO} de {nbpg}</p>
                </div>';
            $mpdf->SetFooter($footerHtml);
    
            // Add custom CSS for styling
            $stylesheet = '
                body {
                    color: #333;
                }
                .header {
                    background-color: #2980b9;
                    color: #fff;
                    padding: 20px;
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #1abc9c;
                }
                .header h1 {
                    margin: 0;
                    font-size: 22px;
                    font-weight: bold;
                }
                .info {
                    text-align: center;
                    margin-bottom: 20px;
                    color: #7f8c8d;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                    page-break-inside: auto;
                }
                .table th {
                    background-color: #3498db;
                    color: #fff;
                    padding: 12px;
                    text-align: center;
                    font-size: 11px;
                    font-weight: bold;
                    border: 1px solid #ddd;
                }
                .table td {
                    padding: 8px;
                    text-align: center;
                    border: 1px solid #ddd;
                    font-size: 9px;
                }
                .table tr {
                    page-break-inside: avoid;
                    page-break-after: auto;
                }
                .table tr:nth-child(even) {
                    background-color: #ecf0f1;
                }
                .table tr:hover {
                    background-color: #d5f0f3;
                }
            ';
            $mpdf->WriteHTML($stylesheet, 1); // 1 = stylesheet only
    
            // Build HTML for PDF
            $html = '<div class="header">';
            $html .= '<h1>Reporte de Detalles de Liquidación #' . htmlspecialchars($idLiquidacion) . '</h1>';
            $html .= '</div>';
    
            $html .= '<div class="info">';
            $html .= '<p><strong>Caja Chica:</strong> ' . htmlspecialchars($nombre_caja_chica) . '</p>';
            $html .= '<p><strong>Fecha Creación:</strong> ' . htmlspecialchars($liquidacion['fecha_creacion'] ?? 'N/A') . '</p>';
            $html .= '<p><strong>Fecha de Generación:</strong> ' . date('d/m/Y H:i:s') . ' CST</p>'; // 04:14 PM CST, May 13, 2025
            $html .= '</div>';
    
            $html .= '<table class="table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>ID</th>';
            $html .= '<th>Tipo de Documento</th>';
            $html .= '<th>No. Factura</th>';
            $html .= '<th>Proveedor</th>';
            $html .= '<th>NIT</th>';
            $html .= '<th>DPI</th>';
            $html .= '<th>Cantidad</th>';
            $html .= '<th>Serie</th>';
            $html .= '<th>Centro de Costo</th>';
            $html .= '<th>Tipo de Gasto</th>';
            $html .= '<th>Tipo de Combustible</th>';
            $html .= '<th>Cuenta Contable</th>';
            $html .= '<th>Fecha</th>';
            $html .= '<th>Subtotal</th>';
            $html .= '<th>IVA</th>';
            $html .= '<th>IDP</th>';
            $html .= '<th>INGUAT</th>';
            $html .= '<th>Total Bruto</th>';
            $html .= '<th>Estado</th>';
            $html .= '<th>Archivos</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
    
            if (empty($detalles)) {
                $html .= '<tr><td colspan="20" style="text-align: center;">No hay detalles disponibles.</td></tr>';
            } else {
                foreach ($detalles as $detalle) {
                    $detalle['id'] = isset($detalle['id']) ? (string)$detalle['id'] : '';
                    $detalle['tipo_documento'] = isset($detalle['tipo_documento']) ? (string)$detalle['tipo_documento'] : '';
                    $detalle['no_factura'] = isset($detalle['no_factura']) ? (string)$detalle['no_factura'] : '';
                    $detalle['nombre_proveedor'] = isset($detalle['nombre_proveedor']) ? (string)$detalle['nombre_proveedor'] : '';
                    $detalle['nit_proveedor'] = isset($detalle['nit_proveedor']) ? (string)$detalle['nit_proveedor'] : 'N/A';
                    $detalle['dpi'] = isset($detalle['dpi']) ? (string)$detalle['dpi'] : 'N/A';
                    $detalle['cantidad'] = isset($detalle['cantidad']) ? (string)$detalle['cantidad'] : 'N/A';
                    $detalle['serie'] = isset($detalle['serie']) ? (string)$detalle['serie'] : 'N/A';
                    $detalle['nombre_centro_costo'] = isset($detalle['nombre_centro_costo']) ? (string)$detalle['nombre_centro_costo'] : 'N/A';
                    $detalle['t_gasto'] = isset($detalle['t_gasto']) ? (string)$detalle['t_gasto'] : '';
                    $detalle['tipo_combustible'] = isset($detalle['tipo_combustible']) ? (string)$detalle['tipo_combustible'] : 'N/A';
                    $detalle['cuenta_contable_nombre'] = isset($detalle['cuenta_contable_nombre']) ? (string)$detalle['cuenta_contable_nombre'] : 'N/A';
                    $detalle['fecha'] = isset($detalle['fecha']) ? (string)$detalle['fecha'] : '';
                    $detalle['subtotal'] = isset($detalle['subtotal']) ? (float)$detalle['subtotal'] : 0;
                    $detalle['iva'] = isset($detalle['iva']) ? (float)$detalle['iva'] : 0;
                    $detalle['idp'] = isset($detalle['idp']) ? (float)$detalle['idp'] : 0;
                    $detalle['inguat'] = isset($detalle['inguat']) ? (float)$detalle['inguat'] : 0;
                    $detalle['total_factura'] = isset($detalle['total_factura']) ? (float)$detalle['total_factura'] : 0;
                    $detalle['estado'] = isset($detalle['estado']) ? (string)$detalle['estado'] : '';
                    $detalle['rutas_archivos'] = isset($detalle['rutas_archivos']) ? $detalle['rutas_archivos'] : '';
    
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($detalle['id']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['tipo_documento']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['no_factura']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['nombre_proveedor']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['nit_proveedor']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['dpi']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['cantidad']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['serie']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['nombre_centro_costo']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['t_gasto']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['tipo_combustible']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['cuenta_contable_nombre']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['fecha']) . '</td>';
                    $html .= '<td>' . number_format($detalle['subtotal'], 2) . '</td>';
                    $html .= '<td>' . number_format($detalle['iva'], 2) . '</td>';
                    $html .= '<td>' . number_format($detalle['idp'], 2) . '</td>';
                    $html .= '<td>' . number_format($detalle['inguat'], 2) . '</td>';
                    $html .= '<td>' . number_format($detalle['total_factura'], 2) . '</td>';
                    $html .= '<td>' . htmlspecialchars($detalle['estado']) . '</td>';
                    $html .= '<td>';
                    $rutas = !empty($detalle['rutas_archivos']) ? json_decode($detalle['rutas_archivos'], true) : [];
                    if (is_array($rutas) && !empty($rutas)) {
                        foreach ($rutas as $ruta) {
                            $html .= htmlspecialchars((string)$ruta) . '<br>';
                        }
                    } else {
                        $html .= 'N/A';
                    }
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }
    
            $html .= '</tbody>';
            $html .= '</table>';
    
            // Write HTML to PDF
            $mpdf->WriteHTML($html, 2); // 2 = HTML + content
    
            error_log('HTML written to PDF for liquidation #' . $idLiquidacion);
    
            // Set headers for PDF download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="detalles_liquidacion_' . $idLiquidacion . '.pdf"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
    
            // Output the PDF
            $mpdf->Output('detalles_liquidacion_' . $idLiquidacion . '.pdf', 'D');
            error_log('PDF output completed for liquidation #' . $idLiquidacion);
            exit;
    
        } catch (Exception $e) {
            // Log the error
            error_log('Error generating PDF for liquidation #' . $idLiquidacion . ': ' . $e->getMessage());
    
            // Ensure no PDF data has been sent before switching to JSON
            if (headers_sent()) {
                exit;
            }
    
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al generar el PDF: ' . $e->getMessage()]);
            exit;
        }
    }
}