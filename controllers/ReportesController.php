<?php
require_once '../models/Liquidacion.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/CajaChica.php';
require_once '../models/Usuario.php';

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

                if (empty($fechaInicio) || empty($fechaFin)) {
                    throw new Exception('Las fechas de inicio y fin son obligatorias.');
                }

                // Usar el método del modelo para obtener las liquidaciones
                $liquidaciones = $this->liquidacionModel->getLiquidacionesByFecha($fechaInicio, $fechaFin, $idCajaChica);

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
                                <td><?php echo htmlspecialchars($liquidacion['id']); ?></td>
                                <td><?php echo htmlspecialchars($liquidacion['caja_chica']); ?></td>
                                <td><?php echo htmlspecialchars($liquidacion['fecha_creacion']); ?></td>
                                <td><?php echo htmlspecialchars($liquidacion['monto_total']); ?></td>
                                <td><?php echo htmlspecialchars($liquidacion['total_gastos'] ?? '0.00'); ?></td>
                                <td><?php echo htmlspecialchars($liquidacion['estado']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                $html = ob_get_clean();
                echo $html;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
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
    
                if (empty($fechaInicio) || empty($fechaFin)) {
                    throw new Exception('Las fechas de inicio y fin son obligatorias.');
                }
    
                // Usar el método del modelo para obtener los detalles
                $detalles = $this->detalleLiquidacionModel->getDetallesByFecha($fechaInicio, $fechaFin, $idCajaChica);
    
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
                                <td><?php echo htmlspecialchars($detalle['id']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['liquidacion_fecha']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['caja_chica']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['no_factura']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['nombre_proveedor'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($detalle['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['total_factura']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['estado']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                $html = ob_get_clean();
                echo $html;
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $cajasChicas = $this->cajaChicaModel->getAllCajasChicas();
        require '../views/reportes/detalle_form.html';
        exit;
    }
}