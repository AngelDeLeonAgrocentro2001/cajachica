<?php
// En public/index.php, al inicio del archivo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Habilitar sesiones al inicio
session_start();
error_log(print_r($_SESSION, true));

// Incluir configuraciones y controladores necesarios
require_once '../config/database.php';
require_once '../controllers/AuditoriaController.php';
require_once '../controllers/CajaChicaController.php';
require_once '../controllers/CuentaContableController.php';
require_once '../controllers/DetalleLiquidacionController.php';
require_once '../controllers/ImpuestoController.php';
require_once '../controllers/LiquidacionController.php';
require_once '../controllers/LoginController.php';
require_once '../controllers/ReportesController.php';
require_once '../controllers/RoleController.php';
require_once '../controllers/UsuarioController.php';
require_once '../controllers/DashboardController.php';
require_once '../controllers/TipoGastoController.php'; // Añadido

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Redirigir a login si no hay sesión y no estamos en el controlador de login
if (!isset($_SESSION['user_id']) && $controller !== 'login' && $action !== 'login') {
    header('Location: index.php?controller=login&action=login');
    exit;
}

switch ($controller) {
    case 'dashboard':
        $dashboardController = new DashboardController();
        switch ($action) {
            case 'index':
                $dashboardController->index();
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para dashboard']);
                exit;
        }
        break;

    case 'login':
        $loginController = new LoginController();
        switch ($action) {
            case 'login':
                $loginController->login();
                break;
            case 'logout':
                $loginController->logout();
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para login']);
                exit;
        }
        break;

    case 'usuario':
        $usuarioController = new UsuarioController();
        switch ($action) {
            case 'list':
                $usuarioController->listUsuarios();
                break;
            case 'create':
                $usuarioController->createUsuario();
                break;
            case 'update':
                if ($id) {
                    $usuarioController->updateUsuario($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de usuario requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $usuarioController->deleteUsuario($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de usuario requerido para eliminar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para usuario']);
                exit;
        }
        break;

    case 'cajachica':
        $cajaChicaController = new CajaChicaController();
        switch ($action) {
            case 'list':
                $cajaChicaController->listCajasChicas();
                break;
            case 'create':
                $cajaChicaController->createCajaChica();
                break;
            case 'update':
                if ($id) {
                    $cajaChicaController->updateCajaChica($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de caja chica requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $cajaChicaController->deleteCajaChica($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de caja chica requerido para eliminar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para cajachica']);
                exit;
        }
        break;

    case 'liquidacion':
        $liquidacionController = new LiquidacionController();
        switch ($action) {
            case 'list':
                $mode = $_GET['mode'] ?? '';
                $liquidacionController->listLiquidaciones($mode);
                break;
            case 'create':
                $liquidacionController->createLiquidacion();
                break;
            case 'update':
                if ($id) {
                    $liquidacionController->updateLiquidacion($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de liquidación requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $liquidacionController->deleteLiquidacion($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de liquidación requerido para eliminar']);
                }
                break;
            case 'autorizar':
                if ($id) {
                    $liquidacionController->autorizar($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de liquidación requerido para autorizar']);
                }
                break;
            case 'revisar':
                if ($id) {
                    $liquidacionController->revisar($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de liquidación requerido para revisar']);
                }
                break;
            case 'exportar':
                if ($id) {
                    $liquidacionController->exportar($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de liquidación requerido para exportar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para liquidacion']);
                exit;
        }
        break;

    case 'detalleliquidacion':
        $detalleLiquidacionController = new DetalleLiquidacionController();
        switch ($action) {
            case 'list':
                $detalleLiquidacionController->listDetallesLiquidacion();
                break;
            case 'create':
                $detalleLiquidacionController->createDetalleLiquidacion();
                break;
            case 'update':
                if ($id) {
                    $detalleLiquidacionController->updateDetalleLiquidacion($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de detalle requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $detalleLiquidacionController->deleteDetalleLiquidacion($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de detalle requerido para eliminar']);
                }
                break;
            case 'revisar':
                if ($id) {
                    $detalleLiquidacionController->revisarDetalle($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de detalle requerido para revisar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para detalleliquidacion']);
                exit;
        }
        break;

    case 'auditoria':
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

    case 'impuesto':
        $impuestoController = new ImpuestoController();
        switch ($action) {
            case 'list':
                $impuestoController->listImpuestos();
                break;
            case 'create':
                $impuestoController->createImpuesto();
                break;
            case 'update':
                if ($id) {
                    $impuestoController->updateImpuesto($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de impuesto requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $impuestoController->deleteImpuesto($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de impuesto requerido para eliminar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para impuesto']);
                exit;
        }
        break;

    case 'cuentacontable':
        $cuentaContableController = new CuentaContableController();
        switch ($action) {
            case 'list':
                $cuentaContableController->listCuentas();
                break;
            case 'create':
                $cuentaContableController->createCuenta();
                break;
            case 'update':
                if ($id) {
                    $cuentaContableController->updateCuenta($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de cuenta contable requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $cuentaContableController->deleteCuenta($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de cuenta contable requerido para eliminar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para cuentacontable']);
                exit;
        }
        break;

    case 'rol':
        $roleController = new RoleController();
        switch ($action) {
            case 'list':
                $roleController->listRoles();
                break;
            case 'create':
                $roleController->createRol();
                break;
            case 'update':
                if ($id) {
                    $roleController->updateRol($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de rol requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $roleController->deleteRol($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de rol requerido para eliminar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para rol']);
                exit;
        }
        break;

    case 'tipogasto': // Añadido
        $tipoGastoController = new TipoGastoController();
        switch ($action) {
            case 'list':
                $tipoGastoController->listTiposGastos();
                break;
            case 'create':
                $tipoGastoController->createTipoGasto();
                break;
            case 'update':
                if ($id) {
                    $tipoGastoController->updateTipoGasto($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de tipo de gasto requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $tipoGastoController->deleteTipoGasto($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de tipo de gasto requerido para eliminar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para tipogasto']);
                exit;
        }
        break;

    default:
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Ruta no encontrada']);
        exit;
}