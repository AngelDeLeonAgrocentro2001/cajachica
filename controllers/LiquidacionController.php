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

    public function CONEXION_HANA($db_name) {
        $driver = "HDBODBC";
        $servername = "192.168.1.9:30015";
        $username = "SAPDBA";
        $password = "B1Adminh";
        $conn = odbc_connect("Driver=$driver;ServerNode=$servername;Database=$db_name;", $username, $password, SQL_CUR_USE_ODBC);
        if (!$conn) {
            error_log("Error al conectar a HANA: " . odbc_errormsg());
            throw new Exception("Error al conectar a la base de datos HANA.");
        }
        return $conn;
    }

    public function ctrObtenerCuentas($Sociedad, $cuenta) {
        if ($Sociedad != 'EC_AGROCENTRO_2015') {
            $segmento2 = '';
            if ($cuenta == 1) {
                $segmento = 64;
                $segmento1 = 65;
                $sub = 2;
            } else if ($cuenta == 2) {
                $segmento = 61;
                $segmento1 = 65;
                $sub = 2;
            } else if ($cuenta == 3) {
                $segmento = 62;
                $segmento1 = 65;
                $sub = 2;
            } else if ($cuenta == 4) {
                $segmento = 63;
                $segmento1 = 65;
                $sub = 2;
            } else if ($cuenta == 5) {
                $segmento = 52;
                $sub = 2;
            } else if ($cuenta == 6) {
                $segmento = 52;
                $sub = 2;
            } else if ($cuenta == 7) {
                $segmento = 52;
                $sub = 2;
            } else if ($cuenta == 8) {
                $segmento = 52;
                $sub = 2;
            } else if ($cuenta == 9) {
                $segmento = 52;
                $sub = 2;
            } else if ($cuenta == 10) {
                $segmento = 52;
                $sub = 2;
            }
    
            if ($segmento == 81) {
                $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND (LEFT("AcctCode", ' . $sub . ') = ' . "'" . $segmento . "'" . ' OR LEFT("AcctCode", ' . $sub . ') = ' . "'" . $segmento1 . "'" . ' OR LEFT("AcctCode", ' . $sub . ') = ' . "'" . $segmento2 . "'" . ') OR "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) IN (\'65\', \'72\', \'83\', \'74\')';
            } else if ($cuenta == 7) {
                $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) IN (\'51\', \'52\', \'54\', \'65\', \'72\', \'83\', \'74\')';
            } else {
                $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", ' . $sub . ') = ' . "'" . $segmento . "'" . ' OR "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) IN (\'65\', \'72\', \'83\', \'74\')';
            }
        } else {
            $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) = \'81\'';
        }
    
        $respuesta = $this->mdlObtenerCuentas($qry);
        return $respuesta;
    }

    public function mdlObtenerCuentas($query) {
        try {
            $conexion = $this->CONEXION_HANA('GT_AGROCENTRO_2016');
            error_log("Executing HANA query: " . $query);
            $prov = odbc_exec($conexion, $query);
            $json = "";
            if ($prov) {
                while ($proveedor = odbc_fetch_object($prov)) {
                    $json .= "|" . $proveedor->AcctCode . '-' . utf8_encode($proveedor->AcctName);
                    error_log("Fetched account: " . $proveedor->AcctCode . '-' . $proveedor->AcctName);
                }
            } else {
                error_log("Error al ejecutar la consulta HANA: " . odbc_errormsg($conexion));
                throw new Exception("Error al ejecutar la consulta en la base de datos HANA.");
            }
            odbc_free_result($prov);
            odbc_close($conexion);
            return $json;
        } catch (Exception $e) {
            error_log("Error en mdlObtenerCuentas: " . $e->getMessage());
            throw $e;
        }
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
        if (!$usuario) {
            error_log('Usuario no encontrado para ID: ' . $_SESSION['user_id']);
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        $rol = strtoupper($usuario['rol']);
        $urlParams = $_GET['mode'] ?? '';
        $isRevisarMode = $urlParams === 'revisar';
    
        $liquidaciones = $this->liquidacionModel->getAllLiquidaciones();
        error_log('Liquidaciones obtenidas: ' . print_r($liquidaciones, true));
    
        foreach ($liquidaciones as &$liquidacion) {
            $liquidacion['detalles'] = $this->detalleModel->getDetallesByLiquidacionId($liquidacion['id']);
        }
        unset($liquidacion);
    
        if ($isRevisarMode && $rol === 'CONTABILIDAD') {
            $liquidaciones = array_filter($liquidaciones, function($liquidacion) {
                return in_array($liquidacion['estado'], [
                    'PENDIENTE_REVISION_CONTABILIDAD',
                    'FINALIZADO',
                    'RECHAZADO_POR_CONTABILIDAD',
                    'EN_PROCESO'
                ]);
            });
            error_log('Liquidaciones filtradas para CONTABILIDAD: ' . print_r($liquidaciones, true));
        } elseif ($urlParams === 'autorizar' && $rol === 'SUPERVISOR') {
            $liquidaciones = array_filter($liquidaciones, function($liquidacion) {
                return $liquidacion['estado'] === 'PENDIENTE_AUTORIZACION';
            });
            error_log('Liquidaciones filtradas para SUPERVISOR: ' . print_r($liquidaciones, true));
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
    
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
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
    
        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
    
            if ($action === 'send_to_correction') {
                $detalleIds = json_decode($_POST['detalle_ids'] ?? '[]', true);
                $correccionComentarios = json_decode($_POST['correccion_comentarios'] ?? '{}', true);
    
                if (empty($detalleIds)) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => 'No se proporcionaron IDs de detalle']);
                    exit;
                }
    
                try {
                    $this->pdo->beginTransaction();
    
                    $numCorrections = 0;
                    foreach ($detalleIds as $detalleId) {
                        $comment = $correccionComentarios[$detalleId] ?? '';
                        if (empty($comment)) {
                            throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                        }
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment");
                        $numCorrections++;
                    }
    
                    // Check if all details are in correction after this action
                    $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
                    $allInCorrection = true;
                    foreach ($allDetalles as $detalle) {
                        if ($detalle['estado'] !== 'EN_CORRECCION') {
                            $allInCorrection = false;
                            break;
                        }
                    }
    
                    if ($allInCorrection) {
                        $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                        $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
                    }
    
                    $this->pdo->commit();
                    header('Content-Type: application/json');
                    echo json_encode(['message' => "$numCorrections detalle(s) enviado(s) a corrección correctamente"]);
                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    error_log('Error al enviar detalle a corrección: ' . $e->getMessage());
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => 'Error al enviar detalle a corrección: ' . $e->getMessage()]);
                }
                exit;
            }
    
            $accion = $_POST['accion'] ?? '';
            $motivo = $_POST['motivo'] ?? '';
            $detallesSeleccionados = $_POST['detalles'] ?? [];
            $detallesNoSeleccionados = json_decode($_POST['unselected_detalles'] ?? '[]', true);
            $correccionComentarios = json_decode($_POST['correccion_comentarios'] ?? '{}', true);
    
            error_log("Rol del usuario: $rol");
            error_log("Acción recibida en autorizar: " . $accion);
            error_log("Estado actual de la liquidación: " . $liquidacion['estado']);
            error_log("Detalles seleccionados: " . json_encode($detallesSeleccionados));
            error_log("Detalles no seleccionados: " . json_encode($detallesNoSeleccionados));
    
            $allowedAcciones = ['APROBADO', 'RECHAZADO', 'DESCARTADO'];
            if (!in_array($accion, $allowedAcciones)) {
                error_log("Acción no válida: " . $accion . ". Acciones permitidas: " . implode(', ', $allowedAcciones));
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
            try {
                $this->pdo->beginTransaction();
    
                $nuevoEstado = '';
                $auditoriaAccion = '';
                $message = '';
    
                $hasCorrections = false;
                foreach ($detalles as $detalle) {
                    if ($detalle['estado'] === 'EN_CORRECCION') {
                        $hasCorrections = true;
                        break;
                    }
                }
    
                if ($rol === 'CONTABILIDAD' && $hasCorrections) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => 'No se puede autorizar/rechazar/descartar hasta que todas las correcciones estén resueltas']);
                    exit;
                }
    
                if ($accion === 'APROBADO') {
                    if ($rol === 'SUPERVISOR') {
                        $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                        $auditoriaAccion = 'AUTORIZADO_POR_SUPERVISOR';
                        $message = 'Liquidación autorizada por supervisor';
                    } elseif ($rol === 'CONTABILIDAD') {
                        $nuevoEstado = 'FINALIZADO';
                        $auditoriaAccion = 'AUTORIZADO_POR_CONTABILIDAD';
                        $message = 'Liquidación finalizada por contabilidad';
                    }
                } elseif ($accion === 'RECHAZADO') {
                    if ($rol === 'SUPERVISOR') {
                        $nuevoEstado = 'RECHAZADO_AUTORIZACION';
                        $auditoriaAccion = 'RECHAZADO_POR_SUPERVISOR';
                        $message = 'Liquidación rechazada por supervisor';
                    } elseif ($rol === 'CONTABILIDAD') {
                        $nuevoEstado = 'RECHAZADO_POR_CONTABILIDAD';
                        $auditoriaAccion = 'RECHAZADO_POR_CONTABILIDAD';
                        $message = 'Liquidación rechazada por contabilidad';
                    }
                } elseif ($accion === 'DESCARTADO') {
                    $nuevoEstado = 'DESCARTADO';
                    $auditoriaAccion = 'DESCARTADO';
                    $message = 'Liquidación descartada';
                }
    
                error_log("Nuevo estado asignado: $nuevoEstado");
    
                // Process unselected details (send to correction)
                $detailsToCorrect = [];
                foreach ($detalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                        $detailsToCorrect[] = $detalleId;
                        $comment = $correccionComentarios[$detalleId] ?? '';
                        if (empty($comment)) {
                            throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                        }
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment");
                    }
                }
    
                // Update selected details (if any)
                $anySelected = !empty($detallesSeleccionados);
                foreach ($detalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesSeleccionados)) {
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                    } elseif (!in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                        // Update unselected details that were not sent to correction to match the new state
                        // This ensures all non-corrected details are in the same state
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                    }
                }
    
                // Update liquidation state based on the state of details
                $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
                $allInCorrection = true;
                $hasApprovedDetails = false;
    
                foreach ($allDetalles as $detalle) {
                    if ($detalle['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD') {
                        $hasApprovedDetails = true;
                    }
                    if ($detalle['estado'] !== 'EN_CORRECCION') {
                        $allInCorrection = false;
                    }
                }
    
                if ($allInCorrection && !$anySelected) {
                    $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
                } elseif ($hasApprovedDetails) {
                    $this->liquidacionModel->updateEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD');
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                } else {
                    $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                }
    
                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode(['message' => $message, 'num_corrections' => count($detailsToCorrect)]);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error al registrar autorización: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al registrar la autorización: ' . $e->getMessage()]);
            }
            exit;
        }
    
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
        if (!$usuario) {
            error_log("Usuario no encontrado para ID: " . $_SESSION['user_id']);
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }
    
        if (!$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para revisar liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para revisar liquidaciones']);
            exit;
        }
    
        $rol = strtoupper($usuario['rol']);
        if ($rol !== 'CONTABILIDAD') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Solo el rol CONTABILIDAD puede revisar liquidaciones']);
            exit;
        }
    
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            error_log("Liquidación no encontrada para ID: $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($liquidacion['estado'] !== 'PENDIENTE_REVISION_CONTABILIDAD') {
            error_log("Estado de la liquidación no válido. Esperado: PENDIENTE_REVISION_CONTABILIDAD, Actual: " . $liquidacion['estado']);
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden revisar liquidaciones en estado PENDIENTE_REVISION_CONTABILIDAD']);
            exit;
        }
    
        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $motivo = $_POST['motivo'] ?? '';
            $detallesSeleccionados = $_POST['detalles'] ?? [];
            $detallesNoSeleccionados = json_decode($_POST['unselected_detalles'] ?? '[]', true);
            $correccionComentarios = json_decode($_POST['correccion_comentarios'] ?? '{}', true);
    
            $allowedAcciones = ['APROBADO', 'RECHAZADO', 'DESCARTADO'];
            if (!in_array($accion, $allowedAcciones)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
            try {
                $this->pdo->beginTransaction();
    
                $nuevoEstado = '';
                $auditoriaAccion = '';
                $message = '';
    
                $hasCorrections = false;
                foreach ($detalles as $detalle) {
                    if ($detalle['estado'] === 'EN_CORRECCION') {
                        $hasCorrections = true;
                        break;
                    }
                }
    
                if ($hasCorrections) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => 'No se puede autorizar/rechazar/descartar hasta que todas las correcciones estén resueltas']);
                    exit;
                }
    
                if ($accion === 'APROBADO') {
                    $nuevoEstado = 'FINALIZADO';
                    $auditoriaAccion = 'AUTORIZADO_POR_CONTABILIDAD';
                    $message = 'Se autorizaron por contabilidad.';
                } elseif ($accion === 'RECHAZADO') {
                    $nuevoEstado = 'RECHAZADO_POR_CONTABILIDAD';
                    $auditoriaAccion = 'RECHAZADO_POR_CONTABILIDAD';
                    $message = 'Rechazado por contabilidad.';
                } elseif ($accion === 'DESCARTADO') {
                    $nuevoEstado = 'DESCARTADO';
                    $auditoriaAccion = 'DESCARTADO_POR_CONTABILIDAD';
                    $message = 'Descartado por contabilidad.';
                }
    
                // Process unselected details (send to correction)
                $detailsToCorrect = [];
                foreach ($detalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                        $detailsToCorrect[] = $detalleId;
                        $comment = $correccionComentarios[$detalleId] ?? '';
                        if (empty($comment)) {
                            throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                        }
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment");
                    }
                }
    
                // Update selected and non-corrected details to the new state
                $anySelected = !empty($detallesSeleccionados);
                foreach ($detalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesSeleccionados)) {
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                    } elseif (!in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                        // Update unselected details that were not sent to correction to match the new state
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                    }
                }
    
                // Update liquidation state based on the state of details
                $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
                $allInCorrection = true;
                foreach ($allDetalles as $detalle) {
                    if ($detalle['estado'] !== 'EN_CORRECCION') {
                        $allInCorrection = false;
                        break;
                    }
                }
    
                if ($allInCorrection && !$anySelected) {
                    $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
                } elseif ($anySelected) {
                    $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                } else {
                    $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                }
    
                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode(['message' => $message, 'num_corrections' => count($detailsToCorrect)]);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error al registrar revisión: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al registrar la revisión: ' . $e->getMessage()]);
            }
            exit;
        }
    
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
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_facturas')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para gestionar facturas']);
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
    
        // Handle AJAX request for fetching accounting accounts (GET request)
        if (isset($_GET['subaction']) && $_GET['subaction'] === 'getCuentasContables') {
            $this->getCuentasContables($_GET['id_centro_costo']);
            exit;
        }
    
        // Existing POST handling logic
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
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
    
                    // Validate id_cuenta_contable
                    if (empty($id_cuenta_contable)) {
                        throw new Exception('La Cuenta Contable es obligatoria.');
                    }
    
                    $cuentaContableModel = new CuentaContable();
                    $cuentaContable = $cuentaContableModel->getCuentaContableById($id_cuenta_contable);
                    if (!$cuentaContable) {
                        throw new Exception('La Cuenta Contable seleccionada no es válida.');
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
                        $tipo_combustible = 'Gasolina';
                    } elseif ($t_gasto !== 'Combustible') {
                        $tipo_combustible = null;
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
                            'monto_total' => $monto_total,
                            'cuenta_contable_nombre' => $cuentaContable['nombre']
                        ];
                    } else {
                        $errorInfo = $this->pdo->errorInfo();
                        throw new Exception('Error al crear la factura. Detalle: ' . ($errorInfo[2] ?? 'Desconocido'));
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
    
                    // Validate id_cuenta_contable
                    if (empty($id_cuenta_contable)) {
                        throw new Exception('La Cuenta Contable es obligatoria.');
                    }
    
                    $cuentaContableModel = new CuentaContable();
                    $cuentaContable = $cuentaContableModel->getCuentaContableById($id_cuenta_contable);
                    if (!$cuentaContable) {
                        throw new Exception('La Cuenta Contable seleccionada no es válida.');
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
                        $tipo_combustible = 'Gasolina';
                    } elseif ($t_gasto !== 'Combustible') {
                        $tipo_combustible = null;
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
    
                    $iva = $iva ?? 0;
                    $idp = $idp ?? 0;
                    $inguat = $inguat ?? 0;
    
                    if ($this->detalleModel->updateDetalleLiquidacion($detalle_id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $subtotal, $total_factura, $id_centro_costo, $cantidad, $serie, $rutas_json, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible)) {
                        $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE', "Factura actualizada: $no_factura");
    
                        $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                        $this->liquidacionModel->updateMontoTotal($id, $monto_total);
    
                        $response = [
                            'message' => 'Factura actualizada correctamente',
                            'detalle_id' => $detalle_id,
                            'rutas_archivos' => $rutas_archivos,
                            'monto_total' => $monto_total,
                            'cuenta_contable_nombre' => $cuentaContable['nombre']
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
    
                        $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                        $this->liquidacionModel->updateMontoTotal($id, $monto_total);
    
                        $response = [
                            'message' => 'Factura eliminada correctamente',
                            'monto_total' => $monto_total
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
    
        // Rest of the method (loading data and rendering the view)...
        $centroCostoModel = new CentroCosto();
        $centroCostoLiquidacion = $centroCostoModel->getCentroCostoById($liquidacion['id_centros_de_costos']);
        $nombreCentroCostoLiquidacion = $centroCostoLiquidacion ? $centroCostoLiquidacion['nombre'] : 'N/A';
    
        $centroCostoCajaChica = $centroCostoModel->getCentroCostoById($cajaChica['id_centro_costo']);
        $nombreCentroCostoCajaChica = $centroCostoCajaChica ? $centroCostoCajaChica['nombre'] : 'N/A';
    
        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id);
        $tiposDocumentos = $this->tipoDocumentoModel->getAllTiposDocumentos();
        $tiposGastos = $this->tipoGastoModel->getAllTiposGastos();
        $centrosCostos = $this->centroCostoModel->getAllCentrosCostos();
    
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
        $suggestedCentroCostoId = $cajaChica['id_centro_costo'] ?? (isset($centrosCostos[0]['id']) ? $centrosCostos[0]['id'] : null);
        foreach ($centrosCostos as $centro) {
            $selected = ($centro['id'] == $suggestedCentroCostoId) ? 'selected' : '';
            $select_centros_costos .= "<option value='{$centro['id']}' $selected>{$centro['nombre']}</option>";
        }
    
        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id);
        $monto_total = array_sum(array_column($detalles, 'total_factura'));
    
        $data = [
            'id' => $liquidacion['id'],
            'nombre_caja_chica' => $cajaChica['nombre'],
            'id_caja_chica' => $liquidacion['id_caja_chica'],
            'centro_costo_caja_chica_id' => $cajaChica['id_centro_costo'],
            'centro_costo_caja_chica_nombre' => $nombreCentroCostoCajaChica,
            'centro_costo_liquidacion_id' => $liquidacion['id_centros_de_costos'],
            'centro_costo_liquidacion_nombre' => $nombreCentroCostoLiquidacion,
            'fecha_inicio' => $liquidacion['fecha_inicio'],
            'fecha_fin' => $liquidacion['fecha_fin'],
            'updated_at' => $liquidacion['updated_at'],
            'suggested_centro_costo_id' => $suggestedCentroCostoId,
            'monto_total' => $monto_total,
        ];
    
        require_once '../views/liquidaciones/manage_facturas.html';
    }

    public function listCorrecciones() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en listCorrecciones');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_correcciones')) {
            error_log('Error: No tienes permiso para listar correcciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para listar correcciones']);
            exit;
        }

        $liquidaciones = $this->liquidacionModel->getLiquidacionesWithCorrections();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(array_values($liquidaciones));
        } else {
            require '../views/liquidaciones/correccion_list.php';
        }
        exit;
    }

    public function updateCorreccion($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateCorreccion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_correcciones')) {
            error_log('Error: No tienes permiso para actualizar correcciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar correcciones']);
            exit;
        }
    
        $rol = strtoupper($usuario['rol']);
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            try {
                $this->pdo->beginTransaction();
    
                $rutas_archivos = [];
                $uploadDir = '../uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
                $maxFileSize = 5 * 1024 * 1024;
    
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
    
                if ($action === 'update') {
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
                    $id_centro_costo = $_POST['id_centro_costo'] ?? null;
                    $id_cuenta_cont蛋able = $_POST['id_cuenta_contable'] ?? null;
                    $cantidad = $_POST['cantidad'] ?? null;
                    $serie = $_POST['serie'] ?? null;
    
                    if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                        throw new Exception('Los campos obligatorios deben ser válidos.');
                    }
    
                    if (empty($id_centro_costo)) {
                        throw new Exception('El Centro de Costo es obligatorio.');
                    }
    
                    if ($tipo_documento === 'FACTURA') {
                        if ($t_gasto === 'Combustible' && empty($tipo_combustible)) {
                            throw new Exception('El tipo de combustible es obligatorio para el tipo de gasto Combustible.');
                        }
                        if (in_array($t_gasto, ['Combustible', 'Gasto Operativo']) && (empty($cantidad) || $cantidad <= 0)) {
                            throw new Exception('La cantidad de galones es obligatoria y debe ser mayor a 0 para el tipo de gasto ' . $t_gasto . '.');
                        }
                    }
    
                    if ($t_gasto === 'Gasto Operativo') {
                        $tipo_combustible = 'Gasolina';
                    } elseif ($t_gasto !== 'Combustible') {
                        $tipo_combustible = null;
                    }
    
                    $detalle = $this->detalleModel->getDetalleById($detalle_id);
                    if (!$detalle) {
                        throw new Exception('Detalle no encontrado.');
                    }
    
                    if ($detalle['estado'] !== 'EN_CORRECCION') {
                        throw new Exception('El detalle no está en estado EN_CORRECCION y no puede ser actualizado desde este módulo.');
                    }
    
                    $existing_rutas = json_decode($detalle['rutas_archivos'], true) ?? [];
                    $rutas_archivos = array_merge($existing_rutas, $rutas_archivos);
                    $rutas_json = json_encode($rutas_archivos);
    
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
    
                    // Automatically calculate taxes based on subtotal (like manageFacturas)
                    $iva = $subtotal * 0.12; // 12% IVA
                    $idp = $t_gasto === 'Combustible' ? $subtotal * 0.02 : 0; // 2% IDP for Combustible
                    $inguat = 0; // Assuming INGUAT is not applicable or 0
                    $total_factura = $subtotal + $iva + $idp + $inguat;
    
                    if ($this->detalleModel->updateDetalleLiquidacion(
                        $detalle_id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto,
                        $subtotal, $total_factura, $id_centro_costo, $cantidad, $serie, $rutas_json, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible
                    )) {
                        $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE_EN_CORRECCION', "Factura actualizada en corrección: $no_factura");
    
                        // Send directly to Contabilidad after correction
                        $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                        $this->detalleModel->updateEstado($detalle_id, $nuevoEstado);
    
                        $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                        $this->liquidacionModel->updateMontoTotal($id, $monto_total);
    
                        // Check if all details are now in a state that allows the liquidation to move to Contabilidad
                        $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $allReadyForContabilidad = true;
                        foreach ($allDetalles as $detalle) {
                            if ($detalle['estado'] !== 'PENDIENTE_REVISION_CONTABILIDAD' && $detalle['estado'] !== 'EN_CORRECCION') {
                                $allReadyForContabilidad = false;
                                break;
                            }
                        }
    
                        if ($allReadyForContabilidad) {
                            $this->liquidacionModel->updateEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD');
                            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'TODAS_CORRECCIONES_COMPLETADAS', "Todas las correcciones completadas, liquidación lista para PENDIENTE_REVISION_CONTABILIDAD");
                        }
    
                        $response = [
                            'message' => 'Factura actualizada correctamente y enviada a Contabilidad',
                            'detalle_id' => $detalle_id,
                            'rutas_archivos' => $rutas_archivos,
                            'monto_total' => $monto_total
                        ];
                    } else {
                        throw new Exception('Error al actualizar la factura.');
                    }
                } elseif ($action === 'submit_corrections') {
                    $detalles = $this->detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'EN_CORRECCION');
                    if (empty($detalles)) {
                        $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $allReadyForContabilidad = true;
                        foreach ($allDetalles as $detalle) {
                            if ($detalle['estado'] !== 'PENDIENTE_REVISION_CONTABILIDAD') {
                                $allReadyForContabilidad = false;
                                break;
                            }
                        }
    
                        if ($allReadyForContabilidad) {
                            $this->liquidacionModel->updateEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD');
                            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'TODAS_CORRECCIONES_COMPLETADAS', "Todas las correcciones completadas, liquidación lista para PENDIENTE_REVISION_CONTABILIDAD");
                        } else {
                            throw new Exception('No hay detalles en estado EN_CORRECCION para enviar a Contabilidad, pero no todos los detalles están listos para revisión.');
                        }
                    } else {
                        $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                        foreach ($detalles as $detalle) {
                            $detalleId = $detalle['id'];
                            $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'CORRECCION_COMPLETADA', "Corrección completada y enviada a $nuevoEstado");
                        }
    
                        $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
                        $allReadyForContabilidad = true;
                        foreach ($allDetalles as $detalle) {
                            if ($detalle['estado'] !== 'PENDIENTE_REVISION_CONTABILIDAD') {
                                $allReadyForContabilidad = false;
                                break;
                            }
                        }
    
                        if ($allReadyForContabilidad) {
                            $this->liquidacionModel->updateEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD');
                            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'TODAS_CORRECCIONES_COMPLETADAS', "Todas las correcciones completadas, liquidación lista para $nuevoEstado");
                        }
                    }
    
                    $response = ['message' => "Correcciones enviadas correctamente a Contabilidad."];
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
    
        $detalles = $this->detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'EN_CORRECCION');
        if (empty($detalles)) {
            header('Location: index.php?controller=liquidacion&action=list&mode=correccion');
            exit;
        }
    
        foreach ($detalles as &$detalle) {
            $centroCosto = $this->centroCostoModel->getCentroCostoById($detalle['id_centro_costo']);
            $detalle['nombre_centro_costo'] = $centroCosto['nombre'] ?? 'N/A';
            // Keep actual values or null, avoid 'N/A' for fields used in logic
            $detalle['tipo_documento'] = $detalle['tipo_documento'] ?? null;
            $detalle['no_factura'] = $detalle['no_factura'] ?? null;
            $detalle['nombre_proveedor'] = $detalle['nombre_proveedor'] ?? null;
            $detalle['nit_proveedor'] = $detalle['nit_proveedor'] ?? null;
            $detalle['dpi'] = $detalle['dpi'] ?? null;
            $detalle['fecha'] = $detalle['fecha'] ?? null;
            $detalle['t_gasto'] = $detalle['t_gasto'] ?? null;
            $detalle['tipo_combustible'] = $detalle['tipo_combustible'] ?? null;
            $detalle['cantidad'] = $detalle['cantidad'] ?? null;
            $detalle['serie'] = $detalle['serie'] ?? null;
            $detalle['subtotal'] = $detalle['p_unitario'] ?? 0; // Use p_unitario for subtotal
            $detalle['total_factura'] = $detalle['total_factura'] ?? 0;
            $detalle['iva'] = $detalle['iva'] ?? 0;
            $detalle['idp'] = $detalle['idp'] ?? 0;
            $detalle['inguat'] = $detalle['inguat'] ?? 0;
            $detalle['correccion_comentario'] = $detalle['correccion_comentario'] ?? 'Sin comentario';
        }
        unset($detalle);
    
        $cajaChica = $this->cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
        if (!$cajaChica) {
            error_log("Caja chica no encontrada para ID: " . $liquidacion['id_caja_chica']);
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Caja chica no encontrada']);
            exit;
        }
    
        $tipoDocumentoModel = new TipoDocumento();
        $tiposDocumentos = $tipoDocumentoModel->getAllTiposDocumentos();
        $select_tipos_documentos = '';
        foreach ($tiposDocumentos as $tipo) {
            $select_tipos_documentos .= "<option value='{$tipo['nombre']}'>{$tipo['nombre']}</option>";
        }
    
        $tipoGastoModel = new TipoGasto();
        $tiposGastos = $tipoGastoModel->getAllTiposGastos();
        $select_tipos_gastos = '';
        foreach ($tiposGastos as $tipo) {
            $select_tipos_gastos .= "<option value='{$tipo['nombre']}'>{$tipo['nombre']}</option>";
        }
    
        $centroCostoModel = new CentroCosto();
        $centrosCostos = $centroCostoModel->getAllCentrosCostos();
        $select_centros_costos = '';
        foreach ($centrosCostos as $centro) {
            $select_centros_costos .= "<option value='{$centro['id']}'>{$centro['nombre']}</option>";
        }
    
        $data = $liquidacion;
        $data['nombre_caja_chica'] = $cajaChica['nombre'];
        $data['suggested_centro_costo_id'] = $cajaChica['id_centro_costo'] ?? null;
    
        require '../views/liquidaciones/correccion.html';
        exit;
    }

    public function submitCorreccion($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_correcciones')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para enviar correcciones']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $detalles = $this->detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'EN_CORRECCION');
            $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
            foreach ($detalles as $detalle) {
                $this->detalleModel->updateEstado($detalle['id'], $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'CORRECCION_ENVIADA', "Detalle corregido y enviado a $nuevoEstado");
            }

            $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
            $allCorrected = true;
            foreach ($allDetalles as $detalle) {
                if ($detalle['estado'] === 'EN_CORRECCION') {
                    $allCorrected = false;
                    break;
                }
            }

            if ($allCorrected) {
                $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'LIQUIDACION_ENVIADA', "Liquidación enviada a $nuevoEstado tras corrección");
            }

            $this->pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Correcciones enviadas correctamente a Contabilidad']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error al enviar correcciones: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al enviar correcciones: ' . $e->getMessage()]);
        }
        exit;
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

    public function getLiquidacionDetails($id) {
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
    
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            error_log("Liquidación no encontrada para ID: $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
    
        $liquidacion['detalles'] = $this->detalleModel->getDetallesByLiquidacionId($id);
    
        header('Content-Type: application/json');
        echo json_encode($liquidacion);
        exit;
    }

    public function getCuentasContables($id_centro_costo) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Sesión no válida. Por favor, inicia sesión.']);
            exit;
        }
    
        try {
            // Validate centro de costo
            $centroCostoModel = new CentroCosto();
            $centro = $centroCostoModel->getCentroCostoById($id_centro_costo);
            if (!$centro) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['error' => 'Centro de costo no encontrado']);
                exit;
            }
    
            // Fetch accounts from HANA
            $sociedad = $_SESSION['sociedad'] ?? 'GT_AGROCENTRO_2016';
            $cuenta = $centro['tipo'] ?? 5;
            $cuentas = $this->ctrObtenerCuentas($sociedad, $cuenta);
    
            $cuentas_array = [];
            $cuentaContableModel = new CuentaContable();
            $pdo = Database::getInstance()->getPdo();
    
            if ($cuentas && $cuentas !== 'sin_datos') {
                $cuentas_list = explode('|', trim($cuentas, '|'));
                foreach ($cuentas_list as $cuenta_item) {
                    if (!empty($cuenta_item)) {
                        list($code, $name) = explode('-', $cuenta_item, 2);
                        $name = utf8_decode($name);
    
                        // Check if the account exists in cuentas_contables by codigo_cuenta
                        $stmt = $pdo->prepare("SELECT id, nombre FROM cuentas_contables WHERE codigo_cuenta = ?");
                        $stmt->execute([$code]);
                        $existingCuenta = $stmt->fetch(PDO::FETCH_ASSOC);
    
                        if (!$existingCuenta) {
                            // Insert the account into cuentas_contables
                            $stmt = $pdo->prepare("
                                INSERT INTO cuentas_contables (nombre, descripcion, estado, id_centro_costo, codigo_cuenta)
                                VALUES (?, ?, ?, ?, ?)
                            ");
                            $descripcion = '';
                            $estado = 'ACTIVO';
                            $stmt->execute([$name, $descripcion, $estado, $id_centro_costo, $code]);
    
                            $id = $pdo->lastInsertId();
                        } else {
                            $id = $existingCuenta['id'];
                            $name = $existingCuenta['nombre'];
                        }
    
                        $cuentas_array[] = ['id' => $id, 'nombre' => $name];
                    }
                }
            }
    
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($cuentas_array);
        } catch (Exception $e) {
            error_log("Error in getCuentasContables: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener cuentas contables: ' . $e->getMessage()]);
        }
        exit;
    }
}