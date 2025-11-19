<?php
require_once '../models/Liquidacion.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/CajaChica.php';
require_once '../models/Usuario.php';
require_once '../models/CuentaContable.php';

require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class ReportesController {
    private $liquidacionModel;
    private $detalleLiquidacionModel;
    private $cajaChicaModel;
    private $usuarioModel;
    private $baseUrl;

    public function __construct() {
        $this->liquidacionModel = new Liquidacion();
        $this->detalleLiquidacionModel = new DetalleLiquidacion();
        $this->cajaChicaModel = new CajaChica();
        $this->usuarioModel = new Usuario();
        // Define the base URL for absolute links (adjust for production)
        $this->baseUrl = 'http://localhost:8080/agrocaja-chica/';
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
    while (ob_get_level()) {
        ob_end_clean();
    }

    try {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        $mpdf->SetTitle('Reporte de Resumen - Solo Finalizadas');
        $mpdf->SetAuthor('AgroCaja Chica');

        $html = '<h1 style="text-align: center; color: #2c3e50;">Reporte de Resumen de Liquidaciones (Solo Finalizadas)</h1>';
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

        $hayDatos = false;

        foreach ($liquidaciones as $liquidacion) {
            // 1. FILTRAR SOLO LAS LIQUIDACIONES FINALIZADAS
            if (!isset($liquidacion['estado']) || strtoupper(trim($liquidacion['estado'])) !== 'FINALIZADO') {
                continue; // Saltar esta liquidación
            }

            $hayDatos = true;

            $id            = $liquidacion['id'] ?? '';
            $cajaChica     = $liquidacion['caja_chica'] ?? '';
            $fechaCreacion = $liquidacion['fecha_creacion'] ?? '';
            $montoTotal    = $liquidacion['monto_total'] ?? '';
            $totalGastos   = $liquidacion['total_gastos'] ?? '0.00';
            $estado        = $liquidacion['estado'] ?? '';

            // Fila principal de la liquidación
            $html .= '<tr>';
            $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($id) . '</td>';
            $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($cajaChica) . '</td>';
            $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($fechaCreacion) . '</td>';
            $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($montoTotal) . '</td>';
            $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($totalGastos) . '</td>';
            $html .= '<td style="padding: 8px; text-align: center; background-color:#d4edda;">' . htmlspecialchars($estado) . '</td>';
            $html .= '</tr>';

            // 2. OBTENER DETALLES Y FILTRAR SOLO LOS FINALIZADOS
            $todosDetalles = $this->detalleLiquidacionModel->getDetallesByLiquidacionId($liquidacion['id']);
            $detallesFinalizados = array_filter($todosDetalles, function($d) {
                return isset($d['estado']) && strtoupper(trim($d['estado'])) === 'FINALIZADO';
            });

            if (!empty($detallesFinalizados)) {
                // Cabecera de detalles
                $html .= '<tr>';
                $html .= '<td colspan="6" style="padding: 8px; background-color: #e9ecef; font-weight: bold;">Detalles de Liquidación #' . htmlspecialchars($id) . '</td>';
                $html .= '</tr>';

                $html .= '<tr style="background-color:#4a6a8a; color:white;">';
                $html .= '<th style="padding: 8px;">ID Detalle</th>';
                $html .= '<th style="padding: 8px;">No. Factura</th>';
                $html .= '<th style="padding: 8px;">Proveedor</th>';
                $html .= '<th style="padding: 8px;">Fecha</th>';
                $html .= '<th style="padding: 8px;">Total Factura</th>';
                $html .= '<th style="padding: 8px;">Estado</th>';
                $html .= '</tr>';

                foreach ($detallesFinalizados as $detalle) {
                    $html .= '<tr>';
                    $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($detalle['id'] ?? '') . '</td>';
                    $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($detalle['no_factura'] ?? '') . '</td>';
                    $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($detalle['nombre_proveedor'] ?? 'N/A') . '</td>';
                    $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($detalle['fecha'] ?? '') . '</td>';
                    $html .= '<td style="padding: 8px; text-align: center;">' . htmlspecialchars($detalle['total_factura'] ?? '0.00') . '</td>';
                    $html .= '<td style="padding: 8px; text-align: center; background-color:#d4edda;">Finalizado</td>';
                    $html .= '</tr>';
                }
            }
            // Si no hay detalles finalizados, puedes omitir la sección o poner un mensaje
            else {
                $html .= '<tr>';
                $html .= '<td colspan="6" style="padding: 8px; text-align: center; color:#888; font-style:italic;">No hay facturas finalizadas en esta liquidación.</td>';
                $html .= '</tr>';
            }
        }

        if (!$hayDatos) {
            $html .= '<tr>';
            $html .= '<td colspan="6" style="padding: 20px; text-align: center; color:#888;">No se encontraron liquidaciones con estado "Finalizado" en el rango seleccionado.</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_resumen_solo_finalizadas.pdf', 'D');
        exit;

    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al generar el PDF: ' . $e->getMessage()]);
        exit;
    }
}

    private function exportResumenToExcel($liquidaciones, $fechaInicio, $fechaFin, $idCajaChica) {
        if (ob_get_length()) {
            ob_end_clean();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Reporte de Resumen de Liquidaciones');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', "Fecha Inicio: $fechaInicio");
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A3', "Fecha Fin: $fechaFin");
        $sheet->mergeCells('A3:F3');
        if ($idCajaChica) {
            $caja = $this->cajaChicaModel->getCajaChicaById($idCajaChica);
            $sheet->setCellValue('A4', "Caja Chica: " . ($caja ? $caja['nombre'] : 'Desconocida'));
            $sheet->mergeCells('A4:F4');
        }

        $sheet->setCellValue('A5', 'ID');
        $sheet->setCellValue('B5', 'Caja Chica');
        $sheet->setCellValue('C5', 'Fecha Creación');
        $sheet->setCellValue('D5', 'Monto Total');
        $sheet->setCellValue('E5', 'Total Gastos');
        $sheet->setCellValue('F5', 'Estado');
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF2C3E50');
        $sheet->getStyle('A5:F5')->getFont()->getColor()->setARGB('FFFFFFFF');

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
        if (ob_get_length()) {
            ob_end_clean();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Reporte de Detalle de Liquidaciones');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', "Fecha Inicio: $fechaInicio");
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A3', "Fecha Fin: $fechaFin");
        $sheet->mergeCells('A3:H3');
        if ($idCajaChica) {
            $caja = $this->cajaChicaModel->getCajaChicaById($idCajaChica);
            $sheet->setCellValue('A4', "Caja Chica: " . ($caja ? $caja['nombre'] : 'Desconocida'));
            $sheet->mergeCells('A4:H4');
        }

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
                             foreach ($rutas as $index => $ruta) {
                                 // Asegurar que la ruta esté correctamente formateada
                                 $rutaLimpia = str_replace('\\', '/', $ruta);
                                 $fileName = basename($rutaLimpia);
                                  echo '
                                 <div class="file-item">
                                     <a href="' . htmlspecialchars($rutaLimpia) . '" 
                                        class="file-link" 
                                        data-file="' . htmlspecialchars($rutaLimpia) . '" 
                                        target="_blank" 
                                        title="Ver ' . htmlspecialchars($fileName) . '">
                                        Ver archivo
                                     </a>
                                 </div>';
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
    ini_set('memory_limit', '1024M');
    ini_set('max_execution_time', 300);
    ini_set('pcre.backtrack_limit', '10000000');
    ini_set('pcre.recursion_limit', '10000000');
    
    if (ob_get_length()) {
        ob_end_clean();
    }

    try {
        if (!is_numeric($idLiquidacion) || $idLiquidacion <= 0) {
            throw new Exception('ID de liquidación inválido: ' . $idLiquidacion);
        }

        error_log('Starting PDF generation for liquidation #' . $idLiquidacion);

        $todosDetalles = $this->detalleLiquidacionModel->getDetallesByLiquidacionId($idLiquidacion);

        $detalles = array_filter($todosDetalles, function($d) {
            return isset($d['estado']) && 
                   strtoupper(trim($d['estado'])) === 'FINALIZADO';
        });

        // $detalles = $this->detalleLiquidacionModel->getDetallesByLiquidacionId($idLiquidacion);
        if ($detalles === false) {
            throw new Exception('Error al obtener detalles de la liquidación');
        }

        $liquidacion = $this->liquidacionModel->getLiquidacionById($idLiquidacion);
        if ($liquidacion === false) {
            throw new Exception('Error al obtener liquidación');
        }

        $cajaChica = $this->cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
        $nombre_caja_chica = $cajaChica['nombre'] ?? 'N/A';

        $cuentaContableModel = new CuentaContable();
        $totalGeneral = 0;
        $gastosPorTipo = [];

        foreach ($detalles as &$detalle) {
            $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
            $detalle['cuenta_contable_nombre'] = $cuentaContable['nombre'] ?? 'N/A';
            $totalGeneral += isset($detalle['total_factura']) ? (float)$detalle['total_factura'] : 0;
            $tipoGasto = isset($detalle['t_gasto']) ? (string)$detalle['t_gasto'] : 'Sin Clasificar';
            if (!isset($gastosPorTipo[$tipoGasto])) {
                $gastosPorTipo[$tipoGasto] = 0;
            }
            $gastosPorTipo[$tipoGasto] += isset($detalle['total_factura']) ? (float)$detalle['total_factura'] : 0;
        }
        unset($detalle);

        error_log('Data fetched successfully for liquidation #' . $idLiquidacion);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_top' => 20,
            'margin_bottom' => 30,
            'margin_left' => 20,
            'margin_right' => 20,
            'default_font_size' => 10,
            'default_font' => 'Helvetica',
        ]);
        $mpdf->SetTitle('Reporte de Detalles de Liquidación');
        $mpdf->SetAuthor('AgroCaja Chica');

        $footerHtml = '
            <div style="text-align: center; color: #6b7280; font-size: 9px;">
                <p>Página {PAGENO} de {nbpg}</p>
            </div>';
        $mpdf->SetFooter($footerHtml);

        $stylesheet = '
            body {
                color: #2d3748;
                font-family: "Helvetica", sans-serif;
            }
            .content-container {
                margin-top: -10px; 
                padding-top: 10px; 
            }
            .logo-container {
                text-align: center;
                margin-bottom: 10px;
            }
            .logo-container img {
                max-width: 150px;
            }
            .header {
                background-color: #2b6cb0;
                color: #ffffff;
                padding: 15px;
                text-align: center;
                margin-bottom: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: bold;
            }
            .info {
                text-align: center;
                margin-bottom: 20px;
                color: #4a5568;
                font-size: 11px;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                page-break-inside: auto;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }
            .table th {
                background-color: #3182ce;
                color: #ffffff;
                padding: 10px;
                text-align: center;
                font-size: 10px;
                font-weight: bold;
                border: 1px solid #e2e8f0;
            }
            .table td {
                padding: 8px;
                text-align: center;
                border: 1px solid #e2e8f0;
                font-size: 9px;
            }
            .table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .table tr:nth-child(even) {
                background-color: #f7fafc;
            }
            .table tr:hover {
                background-color: #e6f0fa;
            }
            .total-row td {
                background-color: #edf2f7;
                font-weight: bold;
                font-size: 10px;
            }
            .summary-section {
                margin-top: 30px;
            }
            .summary-section h2 {
                font-size: 16px;
                color: #2b6cb0;
                margin-bottom: 10px;
                text-align: center;
            }
            
            .summary-table {
                width: 50%;
                margin: 0 auto;
                border-collapse: collapse;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }
            .summary-table th {
                background-color: #4a5568;
                color: #ffffff;
                padding: 8px;
                text-align: center;
                font-size: 10px;
                font-weight: bold;
                border: 1px solid #e2e8f0;
            }
            .summary-table td {
                padding: 8px;
                text-align: center;
                border: 1px solid #e2e8f0;
                font-size: 9px;
            }
            .summary-table tr:nth-child(even) {
                background-color: #f7fafc;
            }
            .images-section {
                margin-top: 30px;
            }
            .images-section h2 {
                font-size: 16px;
                color: #2b6cb0;
                margin-bottom: 10px;
                text-align: center;
            }
            
            .images-section{
            text-align: center;
            }
            .images-section a {
                color: #2b6cb0;
                text-decoration: underline;
                font-size: 10px;
                display: block;
                margin: 5px 0;
            }
            .images-section a:hover {
                color: #1a4971;
            }
        ';
        
        // Escribir el CSS primero
        $mpdf->WriteHTML($stylesheet, 1);

        // Generar y escribir el HTML en chunks
        $this->writePDFContentInChunks($mpdf, $idLiquidacion, $detalles, $liquidacion, $nombre_caja_chica, $totalGeneral, $gastosPorTipo);

        error_log('HTML written to PDF for liquidation #' . $idLiquidacion);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="detalles_liquidacion_' . $idLiquidacion . '.pdf"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $mpdf->Output('detalles_liquidacion_' . $idLiquidacion . '.pdf', 'D');
        error_log('PDF output completed for liquidation #' . $idLiquidacion);
        exit;
    } catch (Exception $e) {
        error_log('Error generating PDF for liquidation #' . $idLiquidacion . ': ' . $e->getMessage());
        if (headers_sent()) {
            exit;
        }
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al generar el PDF: ' . $e->getMessage()]);
        exit;
    }
}

private function writePDFContentInChunks($mpdf, $idLiquidacion, $detalles, $liquidacion, $nombre_caja_chica, $totalGeneral, $gastosPorTipo) {
    // Chunk 1: Encabezado e información general
    $htmlChunk1 = '<div class="content-container">';
    $htmlChunk1 .= '<div class="logo-container">';
    $htmlChunk1 .= '<img src="https://agrocentro.com/wp-content/uploads/2024/03/LOGO-VERTICAL.svg" alt="AgroCaja Chica Logo">';
    $htmlChunk1 .= '</div>';

    $htmlChunk1 .= '<div class="header">';
    $htmlChunk1 .= '<h1>Reporte de Detalles de Liquidación #' . htmlspecialchars($idLiquidacion) . '</h1>';
    $htmlChunk1 .= '</div>';

    $htmlChunk1 .= '<div class="info">';
    $htmlChunk1 .= '<p><strong>Caja Chica:</strong> ' . htmlspecialchars($nombre_caja_chica) . '</p>';
    $htmlChunk1 .= '<p><strong>Fecha Creación:</strong> ' . htmlspecialchars($liquidacion['fecha_creacion'] ?? 'N/A') . '</p>';
    $htmlChunk1 .= '<p><strong>Fecha de Generación:</strong> ' . date('d/m/Y H:i:s') . ' CST</p>';
    $htmlChunk1 .= '</div>';

    $mpdf->WriteHTML($htmlChunk1, 2);
    unset($htmlChunk1); // Liberar memoria

    // Chunk 2: Tabla de detalles (cabecera)
    $htmlChunk2 = '<table class="table">';
    $htmlChunk2 .= '<thead>';
    $htmlChunk2 .= '<tr>';
    $htmlChunk2 .= '<th>ID</th>';
    $htmlChunk2 .= '<th>Tipo de Documento</th>';
    $htmlChunk2 .= '<th>No. Factura</th>';
    $htmlChunk2 .= '<th>Proveedor</th>';
    $htmlChunk2 .= '<th>NIT</th>';
    $htmlChunk2 .= '<th>DPI</th>';
    $htmlChunk2 .= '<th>Cantidad</th>';
    $htmlChunk2 .= '<th>Serie</th>';
    $htmlChunk2 .= '<th>Centro de Costo</th>';
    $htmlChunk2 .= '<th>Tipo de Gasto</th>';
    $htmlChunk2 .= '<th>Tipo de Combustible</th>';
    $htmlChunk2 .= '<th>Cuenta Contable</th>';
    $htmlChunk2 .= '<th>Fecha</th>';
    $htmlChunk2 .= '<th>Subtotal</th>';
    $htmlChunk2 .= '<th>IVA</th>';
    $htmlChunk2 .= '<th>IDP</th>';
    $htmlChunk2 .= '<th>INGUAT</th>';
    $htmlChunk2 .= '<th>Total Bruto</th>';
    $htmlChunk2 .= '<th>Estado</th>';
    $htmlChunk2 .= '</tr>';
    $htmlChunk2 .= '</thead>';
    $htmlChunk2 .= '<tbody>';

    $mpdf->WriteHTML($htmlChunk2, 2);
    unset($htmlChunk2);

    // Chunk 3: Filas de la tabla (en grupos para evitar memoria excesiva)
    $chunkSize = 10; // Procesar 10 filas a la vez
    $totalRows = count($detalles);
    
    for ($i = 0; $i < $totalRows; $i += $chunkSize) {
        $htmlChunkRows = '';
        $end = min($i + $chunkSize, $totalRows);
        
        for ($j = $i; $j < $end; $j++) {
            $detalle = $detalles[$j];
            
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

            $htmlChunkRows .= '<tr>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['id']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['tipo_documento']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['no_factura']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['nombre_proveedor']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['nit_proveedor']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['dpi']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['cantidad']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['serie']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['nombre_centro_costo']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['t_gasto']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['tipo_combustible']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['cuenta_contable_nombre']) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['fecha']) . '</td>';
            $htmlChunkRows .= '<td>' . number_format($detalle['subtotal'], 2) . '</td>';
            $htmlChunkRows .= '<td>' . number_format($detalle['iva'], 2) . '</td>';
            $htmlChunkRows .= '<td>' . number_format($detalle['idp'], 2) . '</td>';
            $htmlChunkRows .= '<td>' . number_format($detalle['inguat'], 2) . '</td>';
            $htmlChunkRows .= '<td>' . number_format($detalle['total_factura'], 2) . '</td>';
            $htmlChunkRows .= '<td>' . htmlspecialchars($detalle['estado']) . '</td>';
            $htmlChunkRows .= '</tr>';
        }
        
        $mpdf->WriteHTML($htmlChunkRows, 2);
        unset($htmlChunkRows);
        
        // Pequeña pausa para liberar memoria
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    // Chunk 4: Total general y cierre de tabla
    $htmlChunk4 = '';
    if (empty($detalles)) {
        $htmlChunk4 .= '<tr><td colspan="19" style="text-align: center;">No hay detalles disponibles.</td></tr>';
    } else {
        $htmlChunk4 .= '<tr class="total-row">';
        $htmlChunk4 .= '<td colspan="17" style="text-align: right;">Total General:</td>';
        $htmlChunk4 .= '<td>' . number_format($totalGeneral, 2) . '</td>';
        $htmlChunk4 .= '<td></td>';
        $htmlChunk4 .= '</tr>';
    }
    $htmlChunk4 .= '</tbody>';
    $htmlChunk4 .= '</table>';

    $mpdf->WriteHTML($htmlChunk4, 2);
    unset($htmlChunk4);

    // Chunk 5: Resumen de gastos
    $htmlChunk5 = '<div class="summary-section">';
    $htmlChunk5 .= '<h2>Resumen de Gastos por Tipo</h2>';
    $htmlChunk5 .= '<table class="summary-table">';
    $htmlChunk5 .= '<thead>';
    $htmlChunk5 .= '<tr>';
    $htmlChunk5 .= '<th>Tipo de Gasto</th>';
    $htmlChunk5 .= '<th>Total</th>';
    $htmlChunk5 .= '</tr>';
    $htmlChunk5 .= '</thead>';
    $htmlChunk5 .= '<tbody>';

    if (empty($gastosPorTipo)) {
        $htmlChunk5 .= '<tr><td colspan="2" style="text-align: center;">No hay datos de gastos por tipo.</td></tr>';
    } else {
        ksort($gastosPorTipo);
        foreach ($gastosPorTipo as $tipo => $total) {
            $htmlChunk5 .= '<tr>';
            $htmlChunk5 .= '<td>' . htmlspecialchars($tipo) . '</td>';
            $htmlChunk5 .= '<td>' . number_format($total, 2) . '</td>';
            $htmlChunk5 .= '</tr>';
        }
    }

    $htmlChunk5 .= '</tbody>';
    $htmlChunk5 .= '</table>';
    $htmlChunk5 .= '</div>';

    $mpdf->WriteHTML($htmlChunk5, 2);
    unset($htmlChunk5);

    // Chunk 6: Archivos adjuntos (procesar uno por uno para evitar memoria excesiva)
    $htmlChunk6 = '<div class="images-section">';
    $htmlChunk6 .= '<h2>Archivos Adjuntos</h2>';
    $hasFiles = false;

    $mpdf->WriteHTML($htmlChunk6, 2);
    unset($htmlChunk6);

    // Procesar imágenes una por una
    foreach ($detalles as $detalle) {
        $rutas = !empty($detalle['rutas_archivos']) ? json_decode($detalle['rutas_archivos'], true) : [];
        if (is_array($rutas) && !empty($rutas)) {
            foreach ($rutas as $ruta) {
                $hasFiles = true;
                
                $imageContent = $this->getImageForPDF($ruta);
                
                if ($imageContent) {
                    $htmlImage = '<div style="page-break-inside: avoid; margin-bottom: 20px; text-align: center;">';
                    $htmlImage .= '<p style="font-size: 10px; margin-bottom: 5px;"><strong>Factura #' . htmlspecialchars($detalle['no_factura'] ?? 'N/A') . '</strong></p>';
                    $htmlImage .= '<p style="font-size: 9px; margin-bottom: 10px;">Archivo: ' . htmlspecialchars(basename($ruta)) . '</p>';
                    $htmlImage .= '<img src="' . $imageContent . '" style="max-width: 750px; max-height: 500px; border: 1px solid #ddd;" alt="' . htmlspecialchars(basename($ruta)) . '">';
                    $htmlImage .= '</div>';
                    
                    $mpdf->WriteHTML($htmlImage, 2);
                    unset($htmlImage);
                    unset($imageContent); // Liberar memoria de la imagen
                    
                    // Pequeña pausa entre imágenes
                    if (function_exists('gc_collect_cycles')) {
                        gc_collect_cycles();
                    }
                }
            }
        }
    }

    // Chunk final: Cierre y mensaje si no hay archivos
    if (!$hasFiles) {
        $htmlFinal = '<p style="text-align: center;">No hay archivos disponibles.</p>';
        $mpdf->WriteHTML($htmlFinal, 2);
        unset($htmlFinal);
    }

    $htmlClose = '</div></div>';
    $mpdf->WriteHTML($htmlClose, 2);
}
    private function getImageForPDF($ruta) {
    try {
        // Primero intenta con Digital Ocean Spaces
        $spacesImage = $this->getImageFromSpaces($ruta);
        if ($spacesImage) {
            return $spacesImage;
        }
        
        // Si no funciona, intenta con el sistema de archivos local
        return $this->getImageFromLocal($ruta);
        
    } catch (Exception $e) {
        error_log('Error getting image for PDF: ' . $e->getMessage());
        return null;
    }
}

private function getImageFromSpaces($ruta) {
    try {
        require_once __DIR__ . '/../config/spaces.php';
        
        $filesystem = getSpacesFilesystem();
        
        // Extraer la clave del archivo de la ruta
        $key = $this->extractKeyFromPath($ruta);
        
        if (!$key) {
            return null;
        }
        
        // Verificar si el archivo existe
        if (!$filesystem->fileExists($key)) {
            error_log("File does not exist in Spaces: " . $key);
            return null;
        }
        
        // Obtener el contenido del archivo
        $fileContent = $filesystem->read($key);
        
        // Obtener el MIME type
        $mimeType = $filesystem->mimeType($key);
        
        // Convertir a base64
        $base64 = base64_encode($fileContent);
        
        return 'data:' . $mimeType . ';base64,' . $base64;
        
    } catch (Exception $e) {
        error_log('Error getting image from Spaces: ' . $e->getMessage());
        return null;
    }
}

private function getImageFromLocal($ruta) {
    try {
        // Limpiar la ruta
        $cleanPath = str_replace('\\', '/', $ruta);
        
        // Si es una ruta relativa, construir la ruta absoluta
        if (strpos($cleanPath, '/') !== 0) {
            $basePath = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__ . '/../../public';
            $absolutePath = rtrim($basePath, '/') . '/' . ltrim($cleanPath, '/');
        } else {
            $absolutePath = $cleanPath;
        }
        
        // Verificar si el archivo existe
        if (!file_exists($absolutePath)) {
            error_log("File does not exist locally: " . $absolutePath);
            return null;
        }
        
        // Obtener el contenido y MIME type
        $fileContent = file_get_contents($absolutePath);
        $mimeType = mime_content_type($absolutePath);
        
        // Verificar que sea una imagen
        if (strpos($mimeType, 'image/') !== 0) {
            error_log("File is not an image: " . $mimeType);
            return null;
        }
        
        $base64 = base64_encode($fileContent);
        return 'data:' . $mimeType . ';base64,' . $base64;
        
    } catch (Exception $e) {
        error_log('Error getting image from local: ' . $e->getMessage());
        return null;
    }
}

private function extractKeyFromPath($ruta) {
    // Si la ruta ya es una URL completa de Spaces
    if (strpos($ruta, 'digitaloceanspaces.com') !== false) {
        $parsedUrl = parse_url($ruta);
        return ltrim($parsedUrl['path'] ?? '', '/');
    }
    
    // Si es una ruta relativa que apunta a Spaces
    $cleanPath = str_replace('\\', '/', $ruta);
    
    // Buscar patrones comunes en rutas de Spaces
    if (strpos($cleanPath, 'CAJA_CHICA/Uploads/') !== false) {
        return $cleanPath;
    }
    
    // Si no se reconoce el patrón, devolver la ruta limpia
    return $cleanPath;
}

}