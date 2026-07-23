<?php
require_once '../models/Liquidacion.php';
require_once '../models/Usuario.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/DteModel.php';

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
        $userId = $_SESSION['user_id'];
        $rol = $usuario['rol'];

        $liquidacionModel = new Liquidacion();

        // Initialize summary array - INCLUIR EXPIRADO
        $resumen = [
            'EN_PROCESO' => ['cantidad' => 0, 'monto' => 0],
            'PENDIENTE_AUTORIZACION' => ['cantidad' => 0, 'monto' => 0],
            'PENDIENTE_REVISION_CONTABILIDAD' => ['cantidad' => 0, 'monto' => 0],
            'RECHAZADO_AUTORIZACION' => ['cantidad' => 0, 'monto' => 0],
            'AUTORIZADO_POR_CONTABILIDAD' => ['cantidad' => 0, 'monto' => 0],
            'RECHAZADO_POR_CONTABILIDAD' => ['cantidad' => 0, 'monto' => 0],
            'FINALIZADO' => ['cantidad' => 0, 'monto' => 0],
            'EXPIRADO' => ['cantidad' => 0, 'monto' => 0], // NUEVO ESTADO
            'DESCARTADO' => ['cantidad' => 0, 'monto' => 0],
            'EN_CORRECCION' => ['cantidad' => 0, 'monto' => 0],
        ];

        // Fetch liquidations based on user role
        $liquidaciones = [];
        $liquidacionesEnCorreccion = [];

        // ✅ NUEVO: Si el usuario es CONTADOR, obtener TODAS las liquidaciones
        if ($this->esContador($usuario)) {
            error_log("🔍 Usuario CONTADOR detectado - Obteniendo TODAS las liquidaciones");
            $liquidaciones = $liquidacionModel->getAllLiquidaciones();
            
            // Para contadores, también obtener todas las liquidaciones en corrección
            $correccionQuery = "
                SELECT DISTINCT l.*, cc.nombre AS nombre_caja_chica
                FROM liquidaciones l
                JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
                JOIN detalle_liquidaciones dl ON dl.id_liquidacion = l.id
                WHERE dl.estado = 'EN_CORRECCION'
                ORDER BY l.fecha_creacion DESC
            ";
            $stmt = $this->pdo->prepare($correccionQuery);
            $stmt->execute();
            $liquidacionesEnCorreccion = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } else {
            // ✅ COMPORTAMIENTO ORIGINAL para otros roles
            error_log("🔍 Usuario NO contador - Aplicando lógica original");

            // Fetch liquidations created by the user
            $userLiquidaciones = $liquidacionModel->getLiquidacionesByUsuario($userId);
            $liquidaciones = array_merge($liquidaciones, $userLiquidaciones);

            // Fetch liquidations where the user is supervisor
            if ($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
                $supervisorLiquidaciones = $liquidacionModel->getAllLiquidaciones(null, $userId);
                $liquidaciones = array_merge($liquidaciones, $supervisorLiquidaciones);
            }

            // Fetch liquidations where the user is accountant
            if ($usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
                $contadorLiquidaciones = $liquidacionModel->getAllLiquidaciones(null, null, null, $userId);
                $liquidaciones = array_merge($liquidaciones, $contadorLiquidaciones);
            }

            // Fetch liquidations in EN_CORRECCION state where user is creator, supervisor, or accountant
            $correccionQuery = "
                SELECT DISTINCT l.*, cc.nombre AS nombre_caja_chica
                FROM liquidaciones l
                JOIN cajas_chicas cc ON l.id_caja_chica = cc.id
                JOIN detalle_liquidaciones dl ON dl.id_liquidacion = l.id
                WHERE dl.estado = 'EN_CORRECCION'
                AND (
                    l.id_usuario = :userId
                    OR l.id_supervisor = :userId
                    OR l.id_contador = :userId
                )
                ORDER BY l.fecha_creacion DESC
            ";
            $stmt = $this->pdo->prepare($correccionQuery);
            $stmt->execute(['userId' => $userId]);
            $liquidacionesEnCorreccion = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Remove duplicates by ID (solo para usuarios no contadores)
        if (!$this->esContador($usuario)) {
            $uniqueLiquidaciones = [];
            foreach ($liquidaciones as $liquidacion) {
                $uniqueLiquidaciones[$liquidacion['id']] = $liquidacion;
            }
            $liquidaciones = array_values($uniqueLiquidaciones);
        }

        // Calculate summary for visible liquidations
        foreach ($liquidaciones as $liquidacion) {
            $estado = $liquidacion['estado'];
            if (isset($resumen[$estado])) {
                $resumen[$estado]['cantidad']++;
                $resumen[$estado]['monto'] += (float) $liquidacion['monto_total'];
            } else {
                error_log("Estado de liquidación no reconocido: $estado");
            }
        }

        // Calculate summary for EN_CORRECCION
        foreach ($liquidacionesEnCorreccion as $liquidacion) {
            $resumen['EN_CORRECCION']['cantidad']++;
            $resumen['EN_CORRECCION']['monto'] += (float) $liquidacion['monto_total'];
        }

        $isAdmin = $this->esAdmin($usuario);

        // ✅ Pasar información del rol al frontend para lógica adicional si es necesario
        echo "<script>const usuarioRol = '" . $rol . "'; const esContador = " . ($this->esContador($usuario) ? 'true' : 'false') . ";</script>";

        require '../views/dashboard.html';
        exit;
    }

    /**
     * Método auxiliar para verificar si el usuario es contador
     */
    private function esContador($usuario) {
        $rol = strtoupper($usuario['rol'] ?? '');
        $esContador = (strpos($rol, 'CONTADOR') !== false ||
                      strpos($rol, 'CONTABILIDAD') !== false);

        error_log("🔍 Verificando rol contador - Rol: $rol, Es contador: " . ($esContador ? 'SÍ' : 'NO'));
        return $esContador;
    }

    /**
     * Método auxiliar para verificar si el usuario es administrador
     */
    private function esAdmin($usuario) {
        $rol = strtoupper($usuario['rol'] ?? '');
        return strpos($rol, Usuario::ROL_ADMIN) !== false;
    }

    /**
     * Estadisticas mensuales para el tab de graficas del dashboard. Solo ADMIN.
     */
    public function estadisticas() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario || !$this->esAdmin($usuario)) {
            error_log('Acceso denegado a estadisticas del dashboard para user_id: ' . $_SESSION['user_id']);
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para ver las estadísticas'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $meses = 6;

        $liquidacionModel = new Liquidacion();
        $detalleModel = new DetalleLiquidacion();
        $dteModel = new DteModel();

        $enCorreccionPorUsuario = $liquidacionModel->getEnCorreccionPorUsuario();
        $rechazadoAutorizacionPorUsuario = $liquidacionModel->getLiquidacionesPorEstadoPorUsuario('RECHAZADO_AUTORIZACION');
        $expiradoPorUsuario = $liquidacionModel->getLiquidacionesPorEstadoPorUsuario('EXPIRADO');

        $data = [
            'meses' => $meses,
            'volumen_liquidaciones' => $liquidacionModel->getEstadisticasMensuales($meses),
            'volumen_detalles' => $detalleModel->getEstadisticasMensuales($meses),
            'pendientes_revision' => [
                'liquidaciones' => $liquidacionModel->contarPorEstado('PENDIENTE_REVISION_CONTABILIDAD'),
                'facturas' => $detalleModel->contarPorEstado('PENDIENTE_REVISION_CONTABILIDAD'),
            ],
            'en_correccion' => [
                'liquidaciones' => array_sum(array_column($enCorreccionPorUsuario, 'liquidaciones')),
                'facturas' => array_sum(array_column($enCorreccionPorUsuario, 'facturas')),
                'por_usuario' => $enCorreccionPorUsuario,
            ],
            'rechazado_autorizacion' => [
                'liquidaciones' => array_sum(array_column($rechazadoAutorizacionPorUsuario, 'liquidaciones')),
                'facturas' => array_sum(array_column($rechazadoAutorizacionPorUsuario, 'facturas')),
                'por_usuario' => $rechazadoAutorizacionPorUsuario,
            ],
            'expirado' => [
                'liquidaciones' => array_sum(array_column($expiradoPorUsuario, 'liquidaciones')),
                'facturas' => array_sum(array_column($expiradoPorUsuario, 'facturas')),
                'por_usuario' => $expiradoPorUsuario,
            ],
            'tiempo_ciclo' => $liquidacionModel->getTiempoPromedioCicloPorMes($meses),
            'dte' => [
                'hoy' => $dteModel->contarHoy(),
                'por_mes' => $dteModel->getEstadisticasMensuales($meses),
            ],
        ];

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>