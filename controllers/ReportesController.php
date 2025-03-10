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
                'format' => 'A4',
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
}