<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
// ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
session_start();
error_log("SESSION: " . print_r($_SESSION, true));

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
require_once '../controllers/TipoGastoController.php';
require_once '../controllers/AccesoController.php';
require_once '../controllers/BaseController.php';
require_once '../controllers/FacturaController.php';
require_once '../controllers/CentroCostoController.php';
require_once '../controllers/DteController.php'; // Agregado

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$subaction = isset($_GET['subaction']) ? $_GET['subaction'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$name = isset($_GET['name']) ? $_GET['name'] : null;
$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
$id_centro_costo = isset($_GET['id_centro_costo']) ? $_GET['id_centro_costo'] : null;

// Check if the request is AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Session validation
if (!isset($_SESSION['user_id']) && $controller !== 'login' && $action !== 'resetPassword' && $action !== 'resetConfirm') {
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Sesión no válida. Por favor, inicia sesión.']);
        exit;
    } else {
        header('Location: index.php?controller=login&action=login');
        exit;
    }
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
            case 'resetPassword':
                $loginController->resetPassword();
                break;
            case 'resetConfirm':
                $loginController->resetConfirm();
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
            case 'getSupervisores':
                $usuarioController->getSupervisores();
                break;
            case 'getContadores':
                $usuarioController->getContadores();
                break;
            case 'checkCardCode':
                $usuarioController->checkCardCode();
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
                        $cajaChicaController->updateCajaChica($id); // Fixed typo
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
                    $liquidacionController->listLiquidaciones();
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
                case 'manageFacturas':
                    if ($id) {
                        $liquidacionController->manageFacturas($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para gestionar facturas']);
                    }
                    break;
                case 'finalizar':
                    if ($id) {
                        $liquidacionController->finalizar($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para finalizar']);
                    }
                    break;
                case 'ver':
                    if ($id) {
                        $liquidacionController->ver($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para ver']);
                    }
                    break;
                case 'getCuentasByCentroCosto':
                    if ($id_centro_costo === null || $id_centro_costo === '') {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de centro de costo requerido para obtener cuentas contables']);
                    } else {
                        $liquidacionController->getCuentasByCentroCosto($id_centro_costo);
                    }
                    break;
                case 'fetchHanaAccounts':
                    if ($id_centro_costo === null || $id_centro_costo === '') {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de centro de costo requerido para obtener cuentas contables desde SAP HANA']);
                    } else {
                        $liquidacionController->fetchHanaAccounts($id_centro_costo);
                    }
                    break;
                case 'getImpuestosByTipoGasto':
                    if ($name) {
                        $liquidacionController->getImpuestosByTipoGasto($name);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'Nombre del tipo de gasto requerido para obtener impuestos']);
                    }
                    break;
                case 'listCorrecciones':
                    $liquidacionController->listCorrecciones();
                    break;
                case 'updateCorreccion':
                    if ($id) {
                        $liquidacionController->updateCorreccion($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para updateCorreccion']);
                    }
                    break;
                case 'submitCorreccion':
                    if ($id) {
                        $liquidacionController->submitCorreccion($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para submitCorreccion']);
                    }
                    break;
                case 'getLiquidacionState':
                    if ($id) {
                        $liquidacionController->getLiquidacionState($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para obtener el estado']);
                    }
                    break;
                case 'deleteFacturaCorreccion':
                    $detalle_id = isset($_GET['detalle_id']) ? intval($_GET['detalle_id']) : null;
                    if ($id && $detalle_id) {
                        $liquidacionController->deleteFacturaCorreccion($id, $detalle_id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación y detalle requeridos para eliminar factura']);
                    }
                    break;
                case 'getDetalleInfo':
                    if ($id) {
                        $liquidacionController->getDetalleInfo($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de detalle requerido para obtener la información']);
                    }
                    break;
                case 'getEstado':
                    if ($id) {
                        $liquidacionController->getEstado($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para obtener el estado']);
                    }
                    break;
                case 'getEnProcesoLiquidaciones':
                    $liquidacionController->getEnProcesoLiquidaciones();
                    break;
                case 'createWithDetail':
                    $liquidacionController->createWithDetail();
                    break;
                case 'addDetailToLiquidacion':
                    $liquidacionController->addDetailToLiquidacion();
                    break;
                case 'deleteDetail':
                    if ($id) {
                        $liquidacionController->deleteDetail($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de detalle requerido para eliminar']);
                    }
                    break;
                case 'autorizarDetalle':
                    if ($id) {
                        $liquidacionController->autorizarDetalle($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para autorizar detalle']);
                    }
                    break;
                case 'assignContador':
                    if ($id) {
                        $liquidacionController->assignContador($id);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        echo json_encode(['error' => 'ID de liquidación requerido para asignar contador']);
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
            $detalleLiquidacionController->revisarDetalle($id);
            break;
        case 'updateDteUsado':
            $detalleLiquidacionController->updateDteUsado();
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
            case 'getDetallesByLiquidacion':
                $reportesController->getDetallesByLiquidacion();
                break;
            case 'exportDetallesToPDF':
                $idLiquidacion = $_POST['id_liquidacion'] ?? null;
                if ($idLiquidacion) {
                    $reportesController->exportDetallesToPDF($idLiquidacion);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de liquidación requerido para exportar a PDF']);
                }
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
            case 'createForm':
                $cuentaContableController->createForm();
                break;
            case 'create':
                $cuentaContableController->createCuenta();
                break;
            case 'update':
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID requerido']);
                    exit;
                }
                $cuentaContableController->updateCuenta($id);
                break;
            case 'updateForm':
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID requerido']);
                    exit;
                }
                $cuentaContableController->updateForm($id);
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID requerido']);
                    exit;
                }
                $cuentaContableController->deleteCuenta($id);
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para cuentacontable']);
                exit;
        }
        break;

    case 'rol':
        $roleController = new RoleController();
        $rol_id = $_GET['rol_id'] ?? null;
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
            case 'managePermissions':
                if ($rol_id) {
                    $roleController->managePermissions($rol_id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de rol requerido para gestionar permisos']);
                }
                break;
            case 'addPermissionToRol':
                $roleController->addPermissionToRol();
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para rol']);
                exit;
        }
        break;

    case 'tipogasto':
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

    case 'base':
        $baseController = new BaseController();
        switch ($action) {
            case 'listBases':
                $baseController->listBases();
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para base']);
                exit;
        }
        break;

    case 'acceso':
        $accesoController = new AccesoController();
        $user_id = $_GET['user_id'] ?? null;
        switch ($action) {
            case 'list':
                $accesoController->list();
                break;
            case 'manageModules':
                $accesoController->manageModules($user_id);
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para acceso']);
                exit;
        }
        break;

    case 'factura':
        $facturaController = new FacturaController();
        switch ($action) {
            case 'list':
                $facturaController->listFacturas();
                break;
            case 'showForm':
                $facturaController->showForm();
                break;
            case 'createFactura':
                $facturaController->createFactura();
                break;
            case 'checkNumeroFactura':
                $facturaController->checkNumeroFactura();
                break;
            case 'delete':
                if ($id) {
                    $facturaController->deleteFactura();
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de factura requerido para eliminar']);
                }
                break;
            case 'getFactura':
                if ($id) {
                    $facturaController->getFactura();
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de factura requerido']);
                }
                break;
            case 'updateFactura':
                $facturaController->updateFactura();
                break;
            case 'autorizarFactura':
                if ($id) {
                    $facturaController->autorizarFactura($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de factura requerido para autorizar']);
                }
                break;
            case 'revisarFactura':
                if ($id) {
                    $facturaController->revisarFactura($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de factura requerido para revisar']);
                }
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para factura']);
                exit;
        }
        break;

    case 'centrocosto':
        $centroCostoController = new CentroCostoController();
        switch ($action) {
            case 'list':
                $centroCostoController->listCentrosCostos();
                break;
            case 'create':
                $centroCostoController->createCentroCosto();
                break;
            case 'update':
                if ($id) {
                    $centroCostoController->updateCentroCosto($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de centro de costos requerido para actualizar']);
                }
                break;
            case 'delete':
                if ($id) {
                    $centroCostoController->deleteCentroCosto($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de centro de costos requerido para eliminar']);
                }
                break;
            case 'createForm':
                $centroCostoController->createForm();
                break;
            case 'updateForm':
                if ($id) {
                    $centroCostoController->updateForm($id);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'ID de centro de costos requerido para actualizar']);
                }
                break;
            case 'checkCodigo':
                $centroCostoController->checkCodigo();
                break;
            case 'getCentrosCostos':
                $base_id = isset($_GET['base_id']) ? intval($_GET['base_id']) : null;
                $centroCostoController->getCentrosCostos($base_id);
                break;
            default:
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Acción no encontrada para centrocosto']);
                exit;
        }
        break;

        case 'dte':
            $dteController = new DteController();
            switch ($action) {
                case 'index':
                    $dteController->index();
                    break;
                case 'uploadExcel':
                    $dteController->uploadExcel();
                    break;
                case 'searchByNit':
                    $dteController->searchByNit();
                    break;
                default:
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(['error' => 'Acción no encontrada para dte']);
                    exit;
            }
            break;

    default:
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Ruta no encontrada']);
        exit;
}
?>