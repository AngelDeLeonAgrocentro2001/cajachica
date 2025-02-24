<?php
require_once '../config/database.php';
require_once '../config/jwt.php';

$controller = $_GET['controller'] ?? 'usuario';
$action = $_GET['action'] ?? 'login';
$id = $_GET['id'] ?? null;

$token = null;
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }
}

if ($token && $controller !== 'usuario') { // Cambié 'login' por 'usuario' para usar el login del usuario
    $decoded = validateJWT($token);
    if (!$decoded) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'Token inválido']);
        exit;
    }
}

switch ($controller) {
    case 'usuario':
        require_once '../controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        switch ($action) {
            case 'login':
                $usuarioController->login();
                break;
            case 'list':
                $usuarioController->listUsuarios();
                break;
            case 'create':
                if ($id) {
                    $usuarioController->updateUsuario($id);
                } else {
                    $usuarioController->createUsuario();
                }
                break;
            case 'update':
                $usuarioController->updateUsuario($id);
                break;
            case 'delete':
                $usuarioController->deleteUsuario($id);
                break;
        }
        break;
    case 'cajachica':
        require_once '../controllers/CajaChicaController.php';
        $cajaChicaController = new CajaChicaController();
        switch ($action) {
            case 'list':
                $cajaChicaController->listCajasChicas();
                break;
            case 'create':
                if ($id) {
                    $cajaChicaController->updateCajaChica($id);
                } else {
                    $cajaChicaController->createCajaChica();
                }
                break;
            case 'update':
                $cajaChicaController->updateCajaChica($id);
                break;
            case 'delete':
                $cajaChicaController->deleteCajaChica($id);
                break;
        }
        break;
    case 'liquidacion':
        require_once '../controllers/LiquidacionController.php';
        $liquidacionController = new LiquidacionController();
        switch ($action) {
            case 'list':
                $liquidacionController->listLiquidaciones();
                break;
            case 'create':
                if ($id) {
                    $liquidacionController->updateLiquidacion($id);
                } else {
                    $liquidacionController->createLiquidacion();
                }
                break;
            case 'update':
                $liquidacionController->updateLiquidacion($id);
                break;
            case 'delete':
                $liquidacionController->deleteLiquidacion($id);
                break;
        }
        break;
    case 'detalleliquidacion':
        require_once '../controllers/DetalleLiquidacionController.php';
        $detalleLiquidacionController = new DetalleLiquidacionController();
        switch ($action) {
            case 'list':
                $detalleLiquidacionController->listDetallesLiquidacion();
                break;
            case 'create':
                if ($id) {
                    $detalleLiquidacionController->updateDetalleLiquidacion($id);
                } else {
                    $detalleLiquidacionController->createDetalleLiquidacion();
                }
                break;
            case 'update':
                $detalleLiquidacionController->updateDetalleLiquidacion($id);
                break;
            case 'delete':
                $detalleLiquidacionController->deleteDetalleLiquidacion($id);
                break;
        }
        break;
    case 'tipogasto':
        require_once '../controllers/TipoGastoController.php';
        $tipoGastoController = new TipoGastoController();
        switch ($action) {
            case 'list':
                $tipoGastoController->listTiposGastos();
                break;
            case 'create':
                if ($id) {
                    $tipoGastoController->updateTipoGasto($id);
                } else {
                    $tipoGastoController->createTipoGasto();
                }
                break;
            case 'update':
                $tipoGastoController->updateTipoGasto($id);
                break;
            case 'delete':
                $tipoGastoController->deleteTipoGasto($id);
                break;
        }
        break;
    case 'historialaprobacion':
        require_once '../controllers/HistorialAprobacionController.php';
        $historialAprobacionController = new HistorialAprobacionController();
        switch ($action) {
            case 'list':
                $historialAprobacionController->listHistorialAprobaciones();
                break;
            case 'create':
                $historialAprobacionController->createHistorialAprobacion();
                break;
        }
        break;
    default:
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Ruta no encontrada']);
        exit;
}