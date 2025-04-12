<?php
require_once '../models/Liquidacion.php';
require_once '../models/Usuario.php';

class DashboardController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en DashboardController::index');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);

        $liquidacionModel = new Liquidacion();
        $liquidaciones = $liquidacionModel->getAllLiquidaciones();

        // Resumen por estado
        $resumen = [
            'EN_PROCESO' => ['cantidad' => 0, 'monto' => 0],
            'PENDIENTE_AUTORIZACION' => ['cantidad' => 0, 'monto' => 0],
            'PENDIENTE_REVISION_CONTABILIDAD' => ['cantidad' => 0, 'monto' => 0], // Reemplazado AUTORIZADO_POR_SUPERVISOR
            'RECHAZADO_AUTORIZACION' => ['cantidad' => 0, 'monto' => 0], // Reemplazado RECHAZADO_POR_SUPERVISOR
            'AUTORIZADO_POR_CONTABILIDAD' => ['cantidad' => 0, 'monto' => 0],
            'RECHAZADO_POR_CONTABILIDAD' => ['cantidad' => 0, 'monto' => 0],
            'FINALIZADO' => ['cantidad' => 0, 'monto' => 0],
            'DESCARTADO' => ['cantidad' => 0, 'monto' => 0],
        ];

        foreach ($liquidaciones as $liquidacion) {
            $estado = $liquidacion['estado'];
            if (isset($resumen[$estado])) {
                $resumen[$estado]['cantidad']++;
                $resumen[$estado]['monto'] += (float) $liquidacion['monto_total'];
            } else {
                error_log("Estado de liquidación no reconocido: $estado");
            }
        }

        require '../views/dashboard.html';
        exit;
    }
}