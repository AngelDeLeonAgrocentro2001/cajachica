<?php
require_once '../config/database.php';
require_once '../models/Liquidacion.php';
require_once '../models/DetalleLiquidacion.php';
require_once '../models/CajaChica.php';
require_once '../models/TipoDocumento.php';
require_once '../models/TipoGasto.php';
require_once '../models/CentroCosto.php';
require_once '../models/CuentaContable.php';
require_once '../models/Auditoria.php';

class LiquidacionController {
    private $pdo;
    private $liquidacionModel;
    private $detalleModel;
    private $cajaChicaModel;
    private $tipoDocumentoModel;
    private $tipoGastoModel;
    private $centroCostoModel;
    private $cuentaContableModel;
    private $auditoriaModel;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
        $this->liquidacionModel = new Liquidacion();
        $this->detalleModel = new DetalleLiquidacion();
        $this->cajaChicaModel = new CajaChica();
        $this->tipoDocumentoModel = new TipoDocumento();
        $this->tipoGastoModel = new TipoGasto();
        $this->centroCostoModel = new CentroCosto();
        $this->cuentaContableModel = new CuentaContable();
        $this->auditoriaModel = new Auditoria();
    }

    public function listLiquidaciones() {
        error_log('Iniciando listLiquidaciones');
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $liquidacion = new Liquidacion();
        $liquidaciones = $liquidacion->getAllLiquidaciones();
        error_log('Liquidaciones obtenidas: ' . print_r($liquidaciones, true));

        $urlParams = $_GET['mode'] ?? '';
        $isRevisarMode = $urlParams === 'revisar';
        if ($isRevisarMode) {
            $liquidaciones = array_filter($liquidaciones, function($liquidacion) {
                return $liquidacion['estado'] !== 'EN_PROCESO';
            });
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(array_values($liquidaciones));
        } else {
            require '../views/liquidaciones/list.html';
        }
        exit;
    }

    public function createLiquidacion() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en createLiquidacion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log('Error: No tienes permiso para crear liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear liquidaciones']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_caja_chica = $_POST['id_caja_chica'] ?? '';
                $fecha_creacion = $_POST['fecha_creacion'] ?? '';
                $fecha_inicio = $_POST['fecha_inicio'] ?? null;
                $fecha_fin = $_POST['fecha_fin'] ?? null;
                $monto_total = 0; // Inicializamos en 0, se calculará después si hay detalles
                $estado = 'EN_PROCESO'; // Nuevo estado inicial
    
                if (empty($id_caja_chica) || empty($fecha_creacion)) {
                    throw new Exception('Campos obligatorios (id_caja_chica, fecha_creacion) son requeridos y deben ser válidos.');
                }
    
                if ($this->liquidacionModel->createLiquidacion($id_caja_chica, $fecha_creacion, $fecha_inicio, $fecha_fin, $monto_total, $estado)) {
                    $lastInsertId = $this->pdo->lastInsertId();
                    $this->auditoriaModel->createAuditoria($lastInsertId, null, $_SESSION['user_id'], 'CREADO', 'Liquidación creada por encargado');
                    header('Content-Type: application/json');
                    http_response_code(201);
                    echo json_encode(['message' => 'Liquidación creada']);
                } else {
                    throw new Exception('Error al crear liquidación en la base de datos.');
                }
            } catch (Exception $e) {
                error_log('Error en createLiquidacion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $cajaChica = new CajaChica();
        $cajasChicas = $cajaChica->getAllCajasChicas();
        $selectCajasChicas = '';
        if (empty($cajasChicas)) {
            $selectCajasChicas = '<option value="">No hay cajas chicas disponibles</option>';
        } else {
            foreach ($cajasChicas as $cc) {
                $selectCajasChicas .= "<option value='{$cc['id']}'>{$cc['nombre']}</option>";
            }
        }
    
        ob_start();
        require '../views/liquidaciones/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_cajas_chicas}}', $selectCajasChicas, $html);
        echo $html;
        exit;
    }
    
    public function updateLiquidacion($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateLiquidacion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') && 
            !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones') && 
            !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
            error_log('Error: No tienes permiso para actualizar liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar liquidaciones']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_caja_chica = $_POST['id_caja_chica'] ?? '';
                $fecha_creacion = $_POST['fecha_creacion'] ?? '';
                $fecha_inicio = $_POST['fecha_inicio'] ?? null;
                $fecha_fin = $_POST['fecha_fin'] ?? null;
                $estado = $_POST['estado'] ?? 'EN_PROCESO';
    
                // Obtener la liquidación actual para preservar el monto_total
                $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
                if (!$liquidacion) {
                    throw new Exception('Liquidación no encontrada');
                }
                $monto_total = $liquidacion['monto_total']; // Mantener el monto_total actual
    
                // Solo usuarios con permisos de autorización pueden cambiar el estado
                if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
                    $estado = $liquidacion['estado']; // Mantener el estado actual
                } else {
                    // Transformar el estado según el rol del usuario
                    $rol = strtoupper($usuario['rol']);
                    if ($estado === 'APROBADO') {
                        if ($rol === 'SUPERVISOR') {
                            $estado = 'PENDIENTE_REVISION_CONTABILIDAD';
                        } elseif ($rol === 'CONTABILIDAD') {
                            $estado = 'FINALIZADO';
                        } else {
                            throw new Exception("Rol no permitido para autorizar: {$rol}");
                        }
                    } elseif ($estado === 'RECHAZADO') {
                        if ($rol === 'SUPERVISOR') {
                            $estado = 'RECHAZADO_AUTORIZACION';
                        } elseif ($rol === 'CONTABILIDAD') {
                            $estado = 'RECHAZADO_POR_CONTABILIDAD';
                        } else {
                            throw new Exception("Rol no permitido para rechazar: {$rol}");
                        }
                    }
    
                    // Validar que el estado transformado sea un valor permitido en el ENUM
                    $allowedEstados = [
                        'EN_PROCESO',
                        'PENDIENTE_AUTORIZACION',
                        'PENDIENTE_REVISION_CONTABILIDAD',
                        'FINALIZADO',
                        'RECHAZADO_AUTORIZACION',
                        'RECHAZADO_POR_CONTABILIDAD'
                    ];
                    if (!in_array($estado, $allowedEstados)) {
                        throw new Exception("Estado no permitido: {$estado}. Contacta al administrador del sistema.");
                    }
                }
    
                // Validaciones existentes
                if (empty($id_caja_chica) || empty($fecha_creacion)) {
                    throw new Exception('Campos obligatorios (id_caja_chica, fecha_creacion) son requeridos.');
                }
    
                // Nueva validación: fecha_inicio <= fecha_fin
                if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                    $fechaInicioDate = new DateTime($fecha_inicio);
                    $fechaFinDate = new DateTime($fecha_fin);
                    if ($fechaInicioDate > $fechaFinDate) {
                        throw new Exception('La fecha de inicio no puede ser mayor que la fecha de fin.');
                    }
                }
    
                if ($this->liquidacionModel->updateLiquidacion($id, $id_caja_chica, $fecha_creacion, $fecha_inicio, $fecha_fin, $monto_total, $estado)) {
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ACTUALIZADO', 'Liquidación actualizada por usuario');
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Liquidación actualizada']);
                } else {
                    throw new Exception('Error al actualizar liquidación');
                }
            } catch (Exception $e) {
                error_log('Error en updateLiquidacion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $liquidacion = new Liquidacion();
        $data = $liquidacion->getLiquidacionById($id);
    
        $cajaChica = new CajaChica();
        $cajasChicas = $cajaChica->getAllCajasChicas();
        $selectCajasChicas = '';
        if (empty($cajasChicas)) {
            $selectCajasChicas = '<option value="">No hay cajas chicas disponibles</option>';
        } else {
            foreach ($cajasChicas as $cc) {
                $selected = $data['id_caja_chica'] == $cc['id'] ? 'selected' : '';
                $selectCajasChicas .= "<option value='{$cc['id']}' {$selected}>{$cc['nombre']}</option>";
            }
        }
    
        ob_start();
        require '../views/liquidaciones/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_cajas_chicas}}', $selectCajasChicas, $html);
        echo $html;
        exit;
    }
    
    public function deleteLiquidacion($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar liquidaciones']);
            exit;
        }
    
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($liquidacion['estado'] !== 'EN_PROCESO') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden eliminar liquidaciones en estado EN_PROCESO']);
            exit;
        }
    
        // Inicializar el modelo de detalles
        $this->detalleModel = new DetalleLiquidacion();
        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id);
        if (!empty($detalles)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar la liquidación porque tiene facturas asociadas']);
            exit;
        }
    
        try {
            $this->pdo->beginTransaction();
    
            // Registrar auditoría antes de eliminar
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ELIMINADO', 'Liquidación eliminada');
            error_log("Auditoría registrada para la liquidación ID $id antes de eliminarla");
    
            // Eliminar registros de auditoría asociados
            $stmt = $this->pdo->prepare("DELETE FROM auditoria WHERE id_liquidacion = ?");
            $stmt->execute([$id]);
            error_log("Registros de auditoría eliminados para la liquidación ID $id");
    
            // Eliminar la liquidación
            if (!$this->liquidacionModel->deleteLiquidation($id)) {
                throw new Exception('Error al eliminar la liquidación en la base de datos');
            }
    
            $this->pdo->commit();
    
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Liquidación eliminada correctamente']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar liquidación ID $id: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar la liquidación: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function autorizar($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario) {
            error_log("Usuario no encontrado para ID: " . $_SESSION['user_id']);
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para autorizar liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para autorizar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            error_log("Liquidación no encontrada para ID: $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        $rol = strtoupper($usuario['rol']);
        $expectedEstado = $rol === 'SUPERVISOR' ? 'PENDIENTE_AUTORIZACION' : 'PENDIENTE_REVISION_CONTABILIDAD';
        if ($liquidacion['estado'] !== $expectedEstado) {
            error_log("Estado de la liquidación no válido. Esperado: $expectedEstado, Actual: " . $liquidacion['estado']);
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => "Solo se pueden autorizar liquidaciones en estado $expectedEstado"]);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $motivo = $_POST['motivo'] ?? '';
            $detallesSeleccionados = $_POST['detalles'] ?? [];
    
            error_log("Rol del usuario: $rol");
            error_log("Acción recibida en autorizar: " . $accion);
            error_log("Estado actual de la liquidación: " . $liquidacion['estado']);
    
            $allowedAcciones = ['APROBADO', 'RECHAZADO', 'DESCARTADO'];
            if (!in_array($accion, $allowedAcciones)) {
                error_log("Acción no válida: " . $accion . ". Acciones permitidas: " . implode(', ', $allowedAcciones));
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
            try {
                $detalleModel = new DetalleLiquidacion();
                $detalles = $detalleModel->getDetallesByLiquidacionId($id);
    
                $nuevoEstado = '';
                $auditoriaAccion = '';
                if ($accion === 'APROBADO') {
                    if ($rol === 'SUPERVISOR') {
                        $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                        $auditoriaAccion = 'AUTORIZADO_POR_SUPERVISOR';
                    } elseif ($rol === 'CONTABILIDAD') {
                        $nuevoEstado = 'FINALIZADO';
                        $auditoriaAccion = 'AUTORIZADO_POR_CONTABILIDAD';
                    }
                } elseif ($accion === 'RECHAZADO') {
                    if ($rol === 'SUPERVISOR') {
                        $nuevoEstado = 'RECHAZADO_AUTORIZACION';
                        $auditoriaAccion = 'RECHAZADO_POR_SUPERVISOR';
                    } elseif ($rol === 'CONTABILIDAD') {
                        $nuevoEstado = 'RECHAZADO_POR_CONTABILIDAD';
                        $auditoriaAccion = 'RECHAZADO_POR_CONTABILIDAD';
                    }
                } elseif ($accion === 'DESCARTADO') {
                    $nuevoEstado = 'EN_PROCESO';
                    $auditoriaAccion = 'DESCARTADO';
                }
                
                error_log("Nuevo estado asignado: $nuevoEstado");
    
                if ($accion === 'DESCARTADO') {
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleModel->updateEstado($detalleId, 'DESCARTADO');
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'DESCARTADO', $motivo);
                        }
                    }
                    if (!$liquidacionModel->updateEstado($id, $nuevoEstado)) {
                        throw new Exception("Error al actualizar el estado de la liquidación a $nuevoEstado");
                    }
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'DESCARTADO', 'Liquidación marcada para corrección');
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Detalles descartados y liquidación marcada para corrección']);
                } else {
                    if (!$liquidacionModel->updateEstado($id, $nuevoEstado)) {
                        throw new Exception("Error al actualizar el estado de la liquidación a $nuevoEstado");
                    }
    
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleModel->updateEstado($detalleId, $nuevoEstado);
                        }
                    }
    
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
    
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Autorización registrada correctamente']);
                }
            } catch (Exception $e) {
                error_log('Error al registrar autorización: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error al registrar la autorización: ' . $e->getMessage()]);
            }
            exit;
        }
    
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
        $data = $liquidacion;
        $mode = 'autorizar';
        require '../views/liquidaciones/autorizar_individual.html';
        exit;
    }
    
    public function revisar($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para revisar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($liquidacion['estado'] !== 'PENDIENTE_REVISION_CONTABILIDAD') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden revisar liquidaciones en estado PENDIENTE_REVISION_CONTABILIDAD']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $motivo = $_POST['motivo'] ?? '';
            $detallesSeleccionados = $_POST['detalles'] ?? [];
    
            $rol = strtoupper($usuario['rol']);
            $allowedAcciones = ['APROBADO', 'RECHAZADO', 'DESCARTADO'];
    
            error_log("Acción recibida en revisar: " . $accion);
            error_log("Acciones permitidas en revisar: " . print_r($allowedAcciones, true));
    
            if (!in_array($accion, $allowedAcciones)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
            try {
                $detalleModel = new DetalleLiquidacion();
                $detalles = $detalleModel->getDetallesByLiquidacionId($id);
    
                $nuevoEstado = '';
                $auditoriaAccion = '';
                if ($accion === 'APROBADO') {
                    $nuevoEstado = 'FINALIZADO';
                    $auditoriaAccion = 'AUTORIZADO_POR_CONTABILIDAD';
                } elseif ($accion === 'RECHAZADO') {
                    $nuevoEstado = 'RECHAZADO_POR_CONTABILIDAD';
                    $auditoriaAccion = 'RECHAZADO_POR_CONTABILIDAD';
                } elseif ($accion === 'DESCARTADO') {
                    $nuevoEstado = 'EN_PROCESO';
                    $auditoriaAccion = 'DESCARTADO';
                }
    
                if ($accion === 'DESCARTADO') {
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleModel->updateEstado($detalleId, 'DESCARTADO');
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'DESCARTADO', $motivo);
                        }
                    }
                    $liquidacionModel->updateEstado($id, 'EN_PROCESO');
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'DESCARTADO', 'Liquidación marcada para corrección');
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Detalles descartados y liquidación marcada para corrección']);
                } else {
                    $liquidacionModel->updateEstado($id, $nuevoEstado);
    
                    foreach ($detalles as $detalle) {
                        $detalleId = $detalle['id'];
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleEstado = $nuevoEstado;
                            $detalleModel->updateEstado($detalleId, $detalleEstado);
                        }
                    }
    
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
    
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Revisión registrada correctamente']);
                }
            } catch (Exception $e) {
                error_log('Error al registrar revisión: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error al registrar la revisión: ' . $e->getMessage()]);
            }
            exit;
        }
    
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
        $data = $liquidacion;
        $mode = 'revisar';
        require '../views/liquidaciones/autorizar_individual.html';
        exit;
    }
    
    public function exportar($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en exportar');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log('Error: No tienes permiso para exportar liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para exportar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($liquidacion['estado'] !== 'FINALIZADO') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden exportar liquidaciones en estado FINALIZADO']);
            exit;
        }
    
        $forceExport = isset($_GET['force']) && $_GET['force'] === 'true';
    
        if ($liquidacion['exportado'] == 1 && !$forceExport) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Esta liquidación ya ha sido exportada']);
            exit;
        }
    
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
    
        $filename = "liquidacion_{$id}_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Liquidacion_ID', 'Caja_Chica', 'Fecha_Creacion', 'Monto_Total', 'Estado_Liquidacion',
            'Exportado', 'Detalle_ID', 'Numero_Factura', 'Proveedor', 'Fecha_Detalle',
            'Bien_Servicio', 'Tipo_Gasto', 'Precio_Unitario', 'Total_Factura', 'Estado_Detalle'
        ]);
    
        foreach ($detalles as $detalle) {
            fputcsv($output, [
                $liquidacion['id'],
                $liquidacion['nombre_caja_chica'],
                $liquidacion['fecha_creacion'],
                $liquidacion['monto_total'],
                $liquidacion['estado'],
                $liquidacion['exportado'],
                $detalle['id'],
                $detalle['no_factura'],
                $detalle['nombre_proveedor'],
                $detalle['fecha'],
                $detalle['bien_servicio'],
                $detalle['t_gasto'],
                $detalle['p_unitario'],
                $detalle['total_factura'],
                $detalle['estado']
            ]);
        }
    
        fclose($output);
    
        if ($forceExport) {
            $liquidacionModel->markAsExported($id);
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO', 'Liquidación reexportada a SAP como ' . $filename);
        } else {
            $liquidacionModel->markAsExported($id);
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO', 'Liquidación exportada a SAP como ' . $filename);
        }
    
        exit;
    }
    
    public function manageFacturas($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            die("Liquidación no encontrada.");
        }
    
        $cajaChica = $this->cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
        if (!$cajaChica) {
            die("Caja chica no encontrada.");
        }
    
        // Obtener el nombre del Centro de Costo asociado a id_centros_de_costos (de la liquidación)
        $centroCostoModel = new CentroCosto();
        $centroCostoLiquidacion = $centroCostoModel->getCentroCostoById($liquidacion['id_centros_de_costos']);
        $nombreCentroCostoLiquidacion = $centroCostoLiquidacion ? $centroCostoLiquidacion['nombre'] : 'N/A';
    
        // Obtener el nombre del Centro de Costo al que pertenece la Caja Chica
        $centroCostoCajaChica = $centroCostoModel->getCentroCostoById($cajaChica['id_centro_costo']);
        $nombreCentroCostoCajaChica = $centroCostoCajaChica ? $centroCostoCajaChica['nombre'] : 'N/A';
    
        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id);
        $tiposDocumentos = $this->tipoDocumentoModel->getAllTiposDocumentos();
        $tiposGastos = $this->tipoGastoModel->getAllTiposGastos();
        $centrosCostos = $this->centroCostoModel->getAllCentrosCostos();
    
        // Generar opciones para tipos de documentos
        $select_tipos_documentos = '';
        if (empty($tiposDocumentos)) {
            $select_tipos_documentos = "<option value=''>No hay tipos de documentos disponibles</option>";
        } else {
            foreach ($tiposDocumentos as $tipo) {
                $select_tipos_documentos .= "<option value='{$tipo['name']}'>{$tipo['name']}</option>";
            }
        }
    
        $select_tipos_gastos = '';
        foreach ($tiposGastos as $tipo) {
            $select_tipos_gastos .= "<option value='{$tipo['name']}'>{$tipo['name']}</option>";
        }
    
        $select_centros_costos = '';
        foreach ($centrosCostos as $centro) {
            $select_centros_costos .= "<option value='{$centro['id']}'>{$centro['nombre']}</option>";
        }
    
        $suggestedCentroCostoId = $cajaChica['id_centro_costo'] ?? $centrosCostos[0]['id'] ?? null;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                $this->pdo->beginTransaction();
    
                // Prepare file upload handling
                $rutas_archivos = [];
                $uploadDir = '../uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
                $maxFileSize = 5 * 1024 * 1024; // 5 MB
    
                if (isset($_FILES['archivos']) && !empty($_FILES['archivos']['name'][0])) {
                    foreach ($_FILES['archivos']['name'] as $key => $name) {
                        if ($_FILES['archivos']['error'][$key] === UPLOAD_ERR_OK) {
                            $fileType = $_FILES['archivos']['type'][$key];
                            $fileSize = $_FILES['archivos']['size'][$key];
    
                            if (!in_array($fileType, $allowedTypes)) {
                                throw new Exception('Tipo de archivo no permitido: ' . $name . '. Solo se permiten PDF, PNG, JPG y JPEG.');
                            }
    
                            if ($fileSize > $maxFileSize) {
                                throw new Exception('El archivo ' . $name . ' excede el tamaño máximo permitido de 5 MB.');
                            }
    
                            $fileName = basename($name);
                            $filePath = $uploadDir . uniqid() . '_' . $fileName;
                            if (move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $filePath)) {
                                $rutas_archivos[] = 'uploads/' . basename($filePath);
                            } else {
                                throw new Exception('Error al subir el archivo: ' . $name);
                            }
                        } elseif ($_FILES['archivos']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                            throw new Exception('Error al subir el archivo: ' . $name);
                        }
                    }
                }
                $rutas_json = json_encode($rutas_archivos);
    
                if ($action === 'create') {
                    $tipo_documento = $_POST['tipo_documento'] ?? '';
                    $no_factura = $_POST['no_factura'] ?? '';
                    $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                    $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                    $dpi = $_POST['dpi'] ?? null;
                    $fecha = $_POST['fecha'] ?? '';
                    $t_gasto = $_POST['t_gasto'] ?? '';
                    $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                    $subtotal = $_POST['subtotal'] ?? 0;
                    $total_factura = $_POST['total_factura'] ?? 0;
                    $iva = isset($_POST['iva']) && $_POST['iva'] !== '' ? floatval($_POST['iva']) : null;
                    $idp = isset($_POST['idp']) && $_POST['idp'] !== '' ? floatval($_POST['idp']) : null;
                    $inguat = isset($_POST['inguat']) && $_POST['inguat'] !== '' ? floatval($_POST['inguat']) : null;
                    $id_centro_costo = $_POST['id_centro_costo'] ?? $suggestedCentroCostoId;
                    $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                    $cantidad = $_POST['cantidad'] ?? null;
                    $serie = $_POST['serie'] ?? null;
                    $estado = 'EN_PROCESO';
    
                    // Ajustar nit_proveedor y dpi según el tipo de documento
                    if ($tipo_documento === 'RECIBO') {
                        $nit_proveedor = null;
                    } else {
                        $dpi = null;
                    }
    
                    // Validaciones
                    if (empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                        throw new Exception('Los campos obligatorios (tipo_documento, no_factura, nombre_proveedor, fecha, t_gasto, subtotal, total_factura) deben ser válidos.');
                    }
    
                    if (empty($id_centro_costo)) {
                        throw new Exception('El Centro de Costo es obligatorio.');
                    }
    
                    if ($tipo_documento === 'COMPROBANTE' && (empty($cantidad) || empty($serie))) {
                        throw new Exception('Cantidad y Serie son obligatorios para el tipo de documento Comprobante.');
                    }
    
                    if ($tipo_documento === 'RECIBO' && empty($dpi)) {
                        throw new Exception('DPI es obligatorio para el tipo de documento Recibo.');
                    }
    
                    if (in_array($tipo_documento, ['FACTURA', 'COMPROBANTE']) && empty($nit_proveedor)) {
                        throw new Exception('NIT es obligatorio para el tipo de documento Factura o Comprobante.');
                    }
    
                    // Validar tipo_combustible y cantidad para Combustible y Gasto Operativo
                    if ($tipo_documento === 'FACTURA') {
                        if ($t_gasto === 'Combustible' && empty($tipo_combustible)) {
                            throw new Exception('El tipo de combustible es obligatorio para el tipo de gasto Combustible.');
                        }
                        if (in_array($t_gasto, ['Combustible', 'Gasto Operativo']) && (empty($cantidad) || $cantidad <= 0)) {
                            throw new Exception('La cantidad de galones es obligatoria y debe ser mayor a 0 para el tipo de gasto ' . $t_gasto . '.');
                        }
                    }
    
                    // Ajustar tipo_combustible según el tipo de gasto
                    if ($t_gasto === 'Gasto Operativo') {
                        $tipo_combustible = 'Gasolina'; // Gasto Operativo siempre es gasolina
                    } elseif ($t_gasto !== 'Combustible') {
                        $tipo_combustible = null; // Para otros tipos de gasto, tipo_combustible debe ser null
                    }
    
                    $fechaFactura = new DateTime($fecha);
                    $fechaInicio = new DateTime($liquidacion['fecha_inicio']);
                    $fechaFin = new DateTime($liquidacion['fecha_fin']);
                    if ($fechaFactura < $fechaInicio || $fechaFactura > $fechaFin) {
                        throw new Exception("La fecha de la factura debe estar entre {$liquidacion['fecha_inicio']} y {$liquidacion['fecha_fin']}.");
                    }
    
                    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detalle_liquidaciones WHERE id_liquidacion = ? AND no_factura = ?");
                    $stmt->execute([$id, $no_factura]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("El número de factura '$no_factura' ya existe para esta liquidación.");
                    }
    
                    // Establecer valores predeterminados si no se calcularon impuestos
                    $iva = $iva ?? 0;
                    $idp = $idp ?? 0;
                    $inguat = $inguat ?? 0;
    
                    $detalleModel = new DetalleLiquidacion();
                    if ($detalleModel->createDetalleLiquidacion($id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $subtotal, $total_factura, $estado, $id_centro_costo, $cantidad, $serie, $rutas_json, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible)) {
                        $lastInsertId = $this->pdo->lastInsertId();
                        $this->auditoriaModel->createAuditoria($id, $lastInsertId, $_SESSION['user_id'], 'CREAR_DETALLE', "Factura creada: $no_factura");
    
                        // Recalcular el monto_total después de crear el detalle
                        $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                        $this->liquidacionModel->updateMontoTotal($id, $monto_total);
    
                        $response = [
                            'message' => 'Factura creada correctamente',
                            'detalle_id' => $lastInsertId,
                            'rutas_archivos' => $rutas_archivos,
                            'monto_total' => $monto_total // Opcional: para depuración
                        ];
                    } else {
                        throw new Exception('Error al crear la factura.');
                    }
                } elseif ($action === 'update') {
                    $detalle_id = $_POST['detalle_id'] ?? '';
                    $tipo_documento = $_POST['tipo_documento'] ?? '';
                    $no_factura = $_POST['no_factura'] ?? '';
                    $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                    $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                    $dpi = $_POST['dpi'] ?? null;
                    $fecha = $_POST['fecha'] ?? '';
                    $t_gasto = $_POST['t_gasto'] ?? '';
                    $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                    $subtotal = $_POST['subtotal'] ?? 0;
                    $total_factura = $_POST['total_factura'] ?? 0;
                    $iva = isset($_POST['iva']) && $_POST['iva'] !== '' ? floatval($_POST['iva']) : null;
                    $idp = isset($_POST['idp']) && $_POST['idp'] !== '' ? floatval($_POST['idp']) : null;
                    $inguat = isset($_POST['inguat']) && $_POST['inguat'] !== '' ? floatval($_POST['inguat']) : null;
                    $id_centro_costo = $_POST['id_centro_costo'] ?? $suggestedCentroCostoId;
                    $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                    $cantidad = $_POST['cantidad'] ?? null;
                    $serie = $_POST['serie'] ?? null;
    
                    if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                        throw new Exception('Los campos obligatorios deben ser válidos.');
                    }
    
                    if (empty($id_centro_costo)) {
                        throw new Exception('El Centro de Costo es obligatorio.');
                    }
    
                    // Validar tipo_combustible y cantidad para Combustible y Gasto Operativo
                    if ($tipo_documento === 'FACTURA') {
                        if ($t_gasto === 'Combustible' && empty($tipo_combustible)) {
                            throw new Exception('El tipo de combustible es obligatorio para el tipo de gasto Combustible.');
                        }
                        if (in_array($t_gasto, ['Combustible', 'Gasto Operativo']) && (empty($cantidad) || $cantidad <= 0)) {
                            throw new Exception('La cantidad de galones es obligatoria y debe ser mayor a 0 para el tipo de gasto ' . $t_gasto . '.');
                        }
                    }
    
                    // Ajustar tipo_combustible según el tipo de gasto
                    if ($t_gasto === 'Gasto Operativo') {
                        $tipo_combustible = 'Gasolina'; // Gasto Operativo siempre es gasolina
                    } elseif ($t_gasto !== 'Combustible') {
                        $tipo_combustible = null; // Para otros tipos de gasto, tipo_combustible debe ser null
                    }
    
                    $detalle = $this->detalleModel->getDetalleById($detalle_id);
                    if (!$detalle) {
                        throw new Exception('Detalle no encontrado.');
                    }
    
                    $existing_rutas = json_decode($detalle['rutas_archivos'], true) ?? [];
                    $rutas_archivos = array_merge($existing_rutas, $rutas_archivos);
                    $rutas_json = json_encode($rutas_archivos);
    
                    // Ajustar nit_proveedor y dpi según el tipo de documento
                    if ($tipo_documento === 'RECIBO') {
                        $nit_proveedor = null;
                    } else {
                        $dpi = null;
                    }
    
                    $fechaFactura = new DateTime($fecha);
                    $fechaInicio = new DateTime($liquidacion['fecha_inicio']);
                    $fechaFin = new DateTime($liquidacion['fecha_fin']);
                    if ($fechaFactura < $fechaInicio || $fechaFactura > $fechaFin) {
                        throw new Exception("La fecha de la factura debe estar entre {$liquidacion['fecha_inicio']} y {$liquidacion['fecha_fin']}.");
                    }
    
                    // Establecer valores predeterminados si no se calcularon impuestos
                    $iva = $iva ?? 0;
                    $idp = $idp ?? 0;
                    $inguat = $inguat ?? 0;
    
                    if ($this->detalleModel->updateDetalleLiquidacion($detalle_id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $subtotal, $total_factura, $id_centro_costo, $cantidad, $serie, $rutas_json, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible)) {
                        $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE', "Factura actualizada: $no_factura");
    
                        // Recalcular el monto_total después de actualizar el detalle
                        $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                        $this->liquidacionModel->updateMontoTotal($id, $monto_total);
    
                        $response = [
                            'message' => 'Factura actualizada correctamente',
                            'detalle_id' => $detalle_id,
                            'rutas_archivos' => $rutas_archivos,
                            'monto_total' => $monto_total // Opcional: para depuración
                        ];
                    } else {
                        throw new Exception('Error al actualizar la factura.');
                    }
                } elseif ($action === 'delete') {
                    $detalle_id = $_POST['detalle_id'] ?? '';
                    if (empty($detalle_id)) {
                        throw new Exception('ID de detalle no proporcionado.');
                    }
    
                    $detalle = $this->detalleModel->getDetalleById($detalle_id);
                    if (!$detalle) {
                        throw new Exception('Detalle no encontrado.');
                    }
    
                    if ($this->detalleModel->deleteDetalleLiquidacion($detalle_id)) {
                        $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ELIMINAR_DETALLE', "Factura eliminada: {$detalle['no_factura']}");
    
                        // Recalcular el monto_total después de eliminar el detalle
                        $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                        $this->liquidacionModel->updateMontoTotal($id, $monto_total);
    
                        $response = [
                            'message' => 'Factura eliminada correctamente',
                            'monto_total' => $monto_total // Opcional: para depuración
                        ];
                    } else {
                        throw new Exception('Error al eliminar la factura.');
                    }
                } else {
                    throw new Exception('Acción no válida.');
                }
    
                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } catch (Exception $e) {
                $this->pdo->rollBack();
                if (!empty($rutas_archivos)) {
                    foreach ($rutas_archivos as $ruta) {
                        $filePath = '../' . $ruta;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
        }
    
        // Recalcular el monto_total para pasar a la vista
        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id); // Refrescar los detalles
        $monto_total = array_sum(array_column($detalles, 'total_factura'));
    
        $data = [
            'id' => $liquidacion['id'],
            'nombre_caja_chica' => $cajaChica['nombre'],
            'id_caja_chica' => $liquidacion['id_caja_chica'],
            'centro_costo_caja_chica_id' => $cajaChica['id_centro_costo'], // ID del Centro de Costo de la Caja Chica
            'centro_costo_caja_chica_nombre' => $nombreCentroCostoCajaChica,
            'centro_costo_liquidacion_id' => $liquidacion['id_centros_de_costos'], // ID del Centro de Costo de la Liquidación
            'centro_costo_liquidacion_nombre' => $nombreCentroCostoLiquidacion,
            'fecha_inicio' => $liquidacion['fecha_inicio'],
            'fecha_fin' => $liquidacion['fecha_fin'],
            'updated_at' => $liquidacion['updated_at'],
            'suggested_centro_costo_id' => $suggestedCentroCostoId,
            'monto_total' => $monto_total,
        ];
    
        require_once '../views/liquidaciones/manage_facturas.html';
    }

    public function getCuentasByCentroCosto($id_centro_costo) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Sesión no válida. Por favor, inicia sesión.']);
            exit;
        }
    
        try {
            $cuentaContableModel = new CuentaContable();
            $cuentas = $cuentaContableModel->getCuentasByCentroCosto($id_centro_costo);
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($cuentas);
        } catch (Exception $e) {
            error_log("Error in getCuentasByCentroCosto: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener cuentas contables']);
        }
    }

    public function getImpuestosByTipoGasto($name) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $tipoGasto = $this->tipoGastoModel->getTipoGastoByName($name);
        if (!$tipoGasto) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Tipo de gasto no encontrado']);
            exit;
        }
    
        $impuestos = $tipoGasto['impuestos'] ?? [];
        header('Content-Type: application/json');
        echo json_encode($impuestos);
        exit;
    }

    public function finalizar($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para finalizar liquidaciones']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        error_log("Estado actual de la liquidación con ID $id: " . $liquidacion['estado']);
    
        if ($liquidacion['estado'] !== 'EN_PROCESO') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden finalizar liquidaciones en estado EN_PROCESO']);
            exit;
        }
    
        try {
            $liquidacionModel->updateEstado($id, 'PENDIENTE_AUTORIZACION');
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'FINALIZADO', 'Liquidación finalizada por encargado');
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Liquidación finalizada correctamente']);
        } catch (Exception $e) {
            error_log('Error al finalizar liquidación: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al finalizar la liquidación: ' . $e->getMessage()]);
        }
        exit;
    }

    public function ver($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario) {
            error_log("Usuario no encontrado para ID: " . $_SESSION['user_id']);
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        // Verificar permisos: el usuario debe haber creado la liquidación o tener permisos de autorización/revisión
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') && 
            !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && 
            !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para ver liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para ver esta liquidación']);
            exit;
        }
    
        $liquidacionModel = new Liquidacion();
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            error_log("Liquidación no encontrada para ID: $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        // Solo permitir ver liquidaciones que no estén en EN_PROCESO (o sea, después de finalizar)
        // if ($liquidacion['estado'] === 'EN_PROCESO') {
        //     error_log("No se puede ver la liquidación en estado EN_PROCESO. ID: $id");
        //     header('Content-Type: application/json');
        //     http_response_code(400);
        //     echo json_encode(['error' => 'No se puede ver una liquidación en estado EN_PROCESO']);
        //     exit;
        // }
    
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
        $data = $liquidacion;
        $mode = 'ver'; // Modo de solo lectura
        require '../views/liquidaciones/ver_liquidacion.html';
        exit;
    }
}



// tipo d gasto agregar la columna impuesto y cuenta contable 

// liquidacion a un centro de costo donde sera visto a una factura id centro de costo liquidaciones 