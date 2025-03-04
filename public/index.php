<?php
// En public/index.php, al inicio del archivo
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Habilitar sesiones al inicio
session_start();
error_log(print_r($_SESSION, true));
require_once '../config/database.php';
require '../controllers/UsuarioController.php';
require '../controllers/CajaChicaController.php';
require '../controllers/LiquidacionController.php';
require '../controllers/DetalleLiquidacionController.php';
require '../controllers/AuditoriaController.php';
require '../controllers/TipoGastoController.php';
require '../controllers/ReportesController.php';
require '../controllers/DashboardController.php';



$controller = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

if (!isset($_SESSION['user_id']) && $controller !== 'login') {
    header('Location: index.php?controller=login&action=login');
    exit;
}

switch ($controller) {
    case 'dashboard':
        require_once '../controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->index();
        break;

    case 'login':
        require_once '../controllers/LoginController.php';
        $loginController = new LoginController();
        $loginController->login();
        break;

    case 'usuario':
        require_once '../controllers/UsuarioController.php';
        $usuarioController = new UsuarioController();
        switch ($action) {
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
                default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para detalleliquidacion']);
                exit;
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
                default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para detalleliquidacion']);
                exit;
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
        case 'autorizar':
            $liquidacionController->autorizar($id);
            break;
        case 'revisar':
            $liquidacionController->revisar($id);
            break;
        case 'exportar':
            $liquidacionController->exportar($id);
            break;
        default:
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Acción no encontrada para liquidacion']);
            exit;
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
            case 'revisar':
                $detalleLiquidacionController->revisarDetalle($id);
                break;
                default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para detalleliquidacion']);
                exit;
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
                default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para detalleliquidacion']);
                exit;
        }
        break;

        case 'auditoria':
            require_once '../controllers/AuditoriaController.php';
            $auditoriaController = new AuditoriaController();
            switch ($action) {
                case 'list':
                    $auditoriaController->list();
                    break;
                case 'getAuditoria':
                    $auditoriaController->getAuditoria();
                    break;
                default:
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(['error' => 'Acción no encontrada para auditoria']);
                    exit;
            }
            break;

        case 'reportes':
            require_once '../controllers/ReportesController.php';
            $reportesController = new ReportesController();
            switch ($action) {
                case 'list':
                    $reportesController->list();
                    break;
                case 'generarResumen':
                    $reportesController->generarResumen();
                    break;
                case 'generarDetalle':
                    $reportesController->generarDetalle();
                    break;
                default:
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(['error' => 'Acción no encontrada para reportes']);
                    exit;
            }
            break;

    default:
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Ruta no encontrada']);
        exit;
}