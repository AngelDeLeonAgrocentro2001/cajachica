<?php
require_once '../models/DetalleLiquidacion.php';
require_once '../models/Liquidacion.php';
require_once '../models/TipoGasto.php';
require_once '../models/Auditoria.php';
require_once '../models/Usuario.php';
require_once '../config/database.php';

class DetalleLiquidacionController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listDetallesLiquidacion() {
        error_log('Iniciando listDetallesLiquidacion');
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $detalle = new DetalleLiquidacion();
        $detalles = $detalle->getAllDetallesLiquidacion();
        error_log('Detalles obtenidos: ' . print_r($detalles, true));
    
        // Normalizar los nombres de los campos
        $detallesNormalizados = array_map(function($detalle) {
            return [
                'id' => $detalle['id'],
                'id_liquidacion' => $detalle['id_liquidacion'],
                'liquidacion' => $detalle['liquidacion'] ?? $detalle['nombre_caja_chica'] ?? 'N/A',
                'no_factura' => $detalle['no_factura'],
                'nombre_proveedor' => $detalle['nombre_proveedor'],
                'fecha' => $detalle['fecha'],
                'bien_servicio' => $detalle['bien_servicio'],
                't_gasto' => $detalle['t_gasto'],
                'p_unitario' => $detalle['p_unitario'],
                'total_factura' => $detalle['total_factura'],
                'estado' => $detalle['estado'],
                'rutas_archivos' => $detalle['rutas_archivos'],
                'comentarios' => $detalle['comentarios'] ?? 'N/A'
            ];
        }, $detalles);
    
        // Filtrar detalles en modo revisar para CONTABILIDAD
        $urlParams = $_GET['mode'] ?? '';
        $isRevisarMode = $urlParams === 'revisar';
        if ($isRevisarMode && $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            $detallesNormalizados = array_filter($detallesNormalizados, function($detalle) {
                return $detalle['estado'] !== 'DESCARTADO';
            });
        } elseif (!$usuarioModel->tienePermiso($usuario, 'create_detalles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para ver esta lista']);
            exit;
        }
    
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(array_values($detallesNormalizados));
        } else {
            if ($isRevisarMode && $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
                require '../views/detalle_liquidaciones/revisar.html';
            } else {
                require '../views/detalle_liquidaciones/list.html';
            }
        }
        exit;
    }

    public function createDetalleLiquidacion() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en createDetalleLiquidacion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_detalles')) {
            error_log('Error: No tienes permiso para crear detalles de liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear detalles de liquidaciones']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                error_log('POST data: ' . print_r($_POST, true));
                error_log('FILES data: ' . print_r($_FILES, true));

                $id_liquidacion = $_POST['id_liquidacion'] ?? '';
                $tipo_documento = $_POST['tipo_documento'] ?? 'FACTURA';
                $no_factura = $_POST['no_factura'] ?? '';
                $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                $dpi = $_POST['dpi'] ?? null;
                $fecha = $_POST['fecha'] ?? '';
                $bien_servicio = $_POST['bien_servicio'] ?? '';
                $t_gasto = $_POST['t_gasto'] ?? '';
                $p_unitario = $_POST['subtotal'] ?? 0; // Use subtotal as p_unitario
                $total_factura = $_POST['total_factura'] ?? 0;
                $estado = $_POST['estado'] ?? 'EN_PROCESO';
                $id_centro_costo = $_POST['id_centro_costo'] ?? null;
                $cantidad = $_POST['cantidad'] ?? null;
                $serie = $_POST['serie'] ?? null;
                $iva = $_POST['iva'] ?? 0;
                $idp = $_POST['idp'] ?? 0;
                $inguat = $_POST['inguat'] ?? 0;
                $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                $comentarios = $_POST['comentarios'] ?? null;

                // Transformar el estado según el rol del usuario
                $rol = strtoupper($usuario['rol']);
                if ($estado === 'APROBADO') {
                    if ($rol === 'CONTABILIDAD') {
                        $estado = 'PENDIENTE_REVISION_CONTABILIDAD';
                    } else {
                        $estado = 'PENDIENTE_AUTORIZACION';
                    }
                } elseif ($estado === 'RECHAZADO') {
                    $estado = 'DESCARTADO';
                } else {
                    $estado = 'EN_PROCESO';
                }

                // Validar que el estado transformado sea un valor permitido en el ENUM
                $allowedEstados = [
                    'EN_PROCESO',
                    'PENDIENTE_AUTORIZACION',
                    'PENDIENTE_REVISION_CONTABILIDAD',
                    'FINALIZADO',
                    'DESCARTADO'
                ];
                if (!in_array($estado, $allowedEstados)) {
                    throw new Exception("Estado no permitido: {$estado}. Contacta al administrador del sistema.");
                }

                error_log("Estado transformado: " . $estado);

                // Validar datos antes de crear
                if (empty($id_liquidacion) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($bien_servicio) || empty($t_gasto) || !is_numeric($p_unitario) || !is_numeric($total_factura)) {
                    throw new Exception('Todos los campos obligatorios deben ser válidos. Verifique id_liquidacion, no_factura, nombre_proveedor, fecha, bien_servicio, t_gasto, subtotal y total_factura.');
                }

                // Verificar que id_liquidacion exista y obtener id_usuario
                $liquidacionModel = new Liquidacion();
                $liquidacion = $liquidacionModel->getLiquidacionById($id_liquidacion);
                if (!$liquidacion) {
                    throw new Exception('El ID de liquidación ' . $id_liquidacion . ' no existe.');
                }
                $id_usuario = $liquidacion['id_usuario'];

                // Validación de fecha del detalle
                $fechaDetalle = new DateTime($fecha);
                $fechaCreacionLiquidacion = new DateTime($liquidacion['fecha_creacion']);
                if ($fechaDetalle > $fechaCreacionLiquidacion) {
                    throw new Exception('La fecha del detalle no puede ser mayor que la fecha de creación de la liquidación (' . $liquidacion['fecha_creacion'] . ').');
                }

                // Manejar la subida de múltiples archivos
                $rutas_archivos = [];
                $uploadDir = '../Uploads/';
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
                                $rutas_archivos[] = 'Uploads/' . basename($filePath);
                            } else {
                                throw new Exception('Error al subir el archivo: ' . $name);
                            }
                        } elseif ($_FILES['archivos']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                            throw new Exception('Error al subir el archivo: ' . $name);
                        }
                    }
                }
                $rutas_json = json_encode($rutas_archivos);

                $detalle = new DetalleLiquidacion();
                if ($detalle->createDetalleLiquidacion(
                    $id_liquidacion,
                    $tipo_documento,
                    $no_factura,
                    $nombre_proveedor,
                    $nit_proveedor,
                    $dpi,
                    $fecha,
                    $t_gasto,
                    $p_unitario,
                    $total_factura,
                    $estado,
                    $id_centro_costo,
                    $cantidad,
                    $serie,
                    $rutas_json,
                    $iva,
                    $idp,
                    $inguat,
                    $id_cuenta_contable,
                    $tipo_combustible,
                    $id_usuario,
                    $comentarios
                )) {
                    $lastInsertId = $this->pdo->lastInsertId();
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($id_liquidacion, $lastInsertId, $_SESSION['user_id'], 'CREADO', 'Detalle de liquidación creado por encargado para usuario ID ' . $id_usuario);
                    header('Content-Type: application/json');
                    http_response_code(201);
                    echo json_encode(['message' => 'Detalle de liquidación creado']);
                } else {
                    throw new Exception('Error al crear detalle de liquidación en la base de datos.');
                }
            } catch (Exception $e) {
                error_log('Error en createDetalleLiquidacion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }

        // Form rendering logic remains unchanged
        $liquidacion = new Liquidacion();
        $liquidaciones = $liquidacion->getAllLiquidaciones();
        $selectLiquidaciones = '';
        foreach ($liquidaciones as $l) {
            $nombreCajaChica = isset($l['nombre_caja_chica']) ? $l['nombre_caja_chica'] : 'N/A';
            $selectLiquidaciones .= "<option value='{$l['id']}'>{$nombreCajaChica} - {$l['fecha_creacion']}</option>";
        }

        $tipoGasto = new TipoGasto();
        $tiposGastos = $tipoGasto->getAllTiposGastos();
        $selectTiposGastos = '';
        foreach ($tiposGastos as $tg) {
            $selectTiposGastos .= "<option value='{$tg['name']}'>{$tg['name']}</option>";
        }

        ob_start();
        require '../views/detalle_liquidaciones/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_liquidaciones}}', $selectLiquidaciones, $html);
        $html = str_replace('{{select_tipos_gastos}}', $selectTiposGastos, $html);
        echo $html;
        exit;
    }
    
    public function updateDetalleLiquidacion($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateDetalleLiquidacion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_detalles')) {
            error_log('Error: No tienes permiso para actualizar detalles de liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar detalles de liquidaciones']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_liquidacion = $_POST['id_liquidacion'] ?? '';
                $tipo_documento = $_POST['tipo_documento'] ?? 'FACTURA';
                $no_factura = $_POST['no_factura'] ?? '';
                $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                $dpi = $_POST['dpi'] ?? null;
                $fecha = $_POST['fecha'] ?? '';
                $bien_servicio = $_POST['bien_servicio'] ?? '';
                $t_gasto = $_POST['t_gasto'] ?? '';
                $p_unitario = $_POST['subtotal'] ?? 0; // Use subtotal as p_unitario
                $total_factura = $_POST['total_factura'] ?? 0;
                $id_centro_costo = $_POST['id_centro_costo'] ?? null;
                $cantidad = $_POST['cantidad'] ?? null;
                $serie = $_POST['serie'] ?? null;
                $iva = $_POST['iva'] ?? 0;
                $idp = $_POST['idp'] ?? 0;
                $inguat = $_POST['inguat'] ?? 0;
                $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                $comentarios = $_POST['comentarios'] ?? null;
                $estado = $_POST['estado'] ?? 'EN_PROCESO';
    
                // Transformar el estado según el rol del usuario
                $rol = strtoupper($usuario['rol']);
                if ($estado === 'APROBADO') {
                    if ($rol === 'CONTABILIDAD') {
                        $estado = 'PENDIENTE_REVISION_CONTABILIDAD';
                    } else {
                        $estado = 'PENDIENTE_AUTORIZACION';
                    }
                } elseif ($estado === 'RECHAZADO') {
                    $estado = 'DESCARTADO';
                }
    
                // Validar que el estado transformado sea un valor permitido en el ENUM
                $allowedEstados = [
                    'EN_PROCESO',
                    'PENDIENTE_AUTORIZACION',
                    'PENDIENTE_REVISION_CONTABILIDAD',
                    'FINALIZADO',
                    'DESCARTADO',
                    'ENVIADO_A_CORRECCION',
                    'EN_CORRECCION'
                ];
                if (!in_array($estado, $allowedEstados)) {
                    throw new Exception("Estado no permitido: {$estado}. Contacta al administrador del sistema.");
                }
    
                error_log("Estado transformado: " . $estado);
    
                // Validar datos antes de actualizar
                if (empty($id_liquidacion) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($bien_servicio) || empty($t_gasto) || !is_numeric($p_unitario) || !is_numeric($total_factura)) {
                    throw new Exception('Todos los campos obligatorios deben ser válidos. Verifique id_liquidacion, no_factura, nombre_proveedor, fecha, bien_servicio, t_gasto, subtotal y total_factura.');
                }
    
                $liquidacionModel = new Liquidacion();
                if (!$liquidacionModel->getLiquidacionById($id_liquidacion)) {
                    throw new Exception('El ID de liquidación ' . $id_liquidacion . ' no existe.');
                }
    
                $rutas_archivos = [];
                $uploadDir = '../Uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
    
                $detalle = new DetalleLiquidacion();
                $existingData = $detalle->getDetalleLiquidacionById($id);
                if (!$existingData) {
                    throw new Exception('El detalle con ID ' . $id . ' no existe.');
                }
    
                $existingRutas = [];
                if (isset($existingData['rutas_archivos'])) {
                    if (is_array($existingData['rutas_archivos'])) {
                        $existingRutas = $existingData['rutas_archivos'];
                    } else {
                        $decodedRutas = json_decode($existingData['rutas_archivos'], true);
                        $existingRutas = is_array($decodedRutas) ? $decodedRutas : [];
                    }
                }
    
                if (isset($_FILES['archivos']) && !empty($_FILES['archivos']['name'][0])) {
                    $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
                    $maxFileSize = 5 * 1024 * 1024;
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
                                $rutas_archivos[] = 'Uploads/' . basename($filePath);
                            } else {
                                throw new Exception('Error al subir uno o más archivos.');
                            }
                        }
                    }
                    $rutas_archivos = array_merge($existingRutas, $rutas_archivos);
                } else {
                    $rutas_archivos = $existingRutas;
                }
    
                $rutas_json = json_encode($rutas_archivos);
    
                // Iniciar una transacción para asegurar integridad
                $this->pdo->beginTransaction();
    
                if ($detalle->updateDetalleLiquidacion(
                    $id,
                    $tipo_documento,
                    $no_factura,
                    $nombre_proveedor,
                    $nit_proveedor,
                    $dpi,
                    $fecha,
                    $t_gasto,
                    $p_unitario,
                    $total_factura,
                    $id_centro_costo,
                    $iva,
                    $idp,
                    $inguat,
                    $id_cuenta_contable,
                    $cantidad,
                    $serie,
                    $rutas_json,
                    $tipo_combustible,
                    $comentarios
                )) {
                    $auditoria = new Auditoria();
                    $auditoria->createAuditoria($id_liquidacion, $id, $_SESSION['user_id'], 'ACTUALIZADO', 'Detalle de liquidación actualizado');
    
                    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM detalle_liquidaciones WHERE id_liquidacion = ? AND estado = 'DESCARTADO'");
                    $stmt->execute([$id_liquidacion]);
                    $descartados = $stmt->fetchColumn();
    
                    if ($descartados == 0) {
                        $liquidacionModel = new Liquidacion();
                        if (!$liquidacionModel->updateEstado($id_liquidacion, 'PENDIENTE_AUTORIZACION')) {
                            throw new Exception('Error al actualizar el estado de la liquidación a PENDIENTE_AUTORIZACION');
                        }
                        $auditoria->createAuditoria($id_liquidacion, null, $_SESSION['user_id'], 'PENDIENTE_AUTORIZACION', 'Liquidación restaurada a PENDIENTE_AUTORIZACION tras corrección de detalles');
                    }
    
                    $this->pdo->commit();
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Detalle de liquidación actualizado']);
                } else {
                    throw new Exception('Error al actualizar detalle de liquidación en la base de datos.');
                }
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error en updateDetalleLiquidacion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
            exit;
        }
    
        $detalle = new DetalleLiquidacion();
        $data = $detalle->getDetalleLiquidacionById($id);
        if (!$data) {
            echo "<h2>Error: Detalle no encontrado</h2>";
            echo "<p>No se pudo cargar el detalle con ID " . htmlspecialchars($id) . ".</p>";
            echo '<a href="index.php?controller=detalleliquidacion&action=list">Volver a Lista</a>';
            exit;
        }
    
        $liquidacion = new Liquidacion();
        $liquidaciones = $liquidacion->getAllLiquidaciones();
        $selectLiquidaciones = '';
        foreach ($liquidaciones as $l) {
            $selected = $data['id_liquidacion'] == $l['id'] ? 'selected' : '';
            $nombreCajaChica = isset($l['nombre_caja_chica']) ? $l['nombre_caja_chica'] : 'N/A';
            $selectLiquidaciones .= "<option value='{$l['id']}' {$selected}>{$nombreCajaChica} - {$l['fecha_creacion']}</option>";
        }
    
        $tipoGasto = new TipoGasto();
        $tiposGastos = $tipoGasto->getAllTiposGastos();
        $selectTiposGastos = '';
        foreach ($tiposGastos as $tg) {
            $selected = $data['t_gasto'] == $tg['name'] ? 'selected' : '';
            $selectTiposGastos .= "<option value='{$tg['name']}' {$selected}>{$tg['name']}</option>";
        }
    
        ob_start();
        require '../views/detalle_liquidaciones/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_liquidaciones}}', $selectLiquidaciones, $html);
        $html = str_replace('{{select_tipos_gastos}}', $selectTiposGastos, $html);
        echo $html;
        exit;
    }
    
    public function deleteDetalleLiquidacion($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_detalles')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar detalles de liquidaciones']);
            exit;
        }
    
        $detalle = new DetalleLiquidacion();
        $detalleData = $detalle->getDetalleLiquidacionById($id);
        if (!$detalleData) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }
    
        // Eliminar registros dependientes en auditoria
        $auditoria = new Auditoria();
        $stmt = $this->pdo->prepare("DELETE FROM auditoria WHERE id_detalle_liquidacion = ?");
        if (!$stmt->execute([$id])) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar registros de auditoría relacionados']);
            exit;
        }
    
        if ($detalle->deleteDetalleLiquidacion($id)) {
            $auditoria->createAuditoria($detalleData['id_liquidacion'], $id, $_SESSION['user_id'], 'ELIMINADO', 'Detalle de liquidación eliminado');
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Detalle de liquidación eliminado']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar detalle de liquidación']);
        }
        exit;
    }
    
    public function revisarDetalle($id = null) {
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
            echo json_encode(['error' => 'No tienes permiso para revisar detalles']);
            exit;
        }
    
        $detalleModel = new DetalleLiquidacion();
    
        if ($id === null) {
            $detalles = $detalleModel->getAllDetallesLiquidacion();
            $detalles = array_filter($detalles, function($detalle) use ($usuario, $usuarioModel) {
                $canReview = in_array($detalle['estado'], ['PENDIENTE_AUTORIZACION', 'PENDIENTE_REVISION_CONTABILIDAD']);
                if ($usuario['rol'] === 'CONTABILIDAD') {
                    $liquidacionModel = new Liquidacion();
                    $liquidacion = $liquidacionModel->getLiquidacionById($detalle['id_liquidacion']);
                    return $canReview && $liquidacion && !in_array($liquidacion['estado'], ['DESCARTADO', 'FINALIZADO']);
                }
                return $canReview;
            });
    
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(array_values($detalles));
            } else {
                require '../views/detalle_liquidaciones/revisar.html';
            }
            exit;
        }
    
        $data = $detalleModel->getDetalleLiquidacionById($id);
        if (!$data) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }
    
        if ($usuario['rol'] === 'CONTABILIDAD' && !in_array($data['estado'], ['PENDIENTE_AUTORIZACION', 'PENDIENTE_REVISION_CONTABILIDAD'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Este detalle no puede ser revisado porque no está en estado PENDIENTE_AUTORIZACION o PENDIENTE_REVISION_CONTABILIDAD']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accionBase = $_POST['accion'] ?? 'EN_REVISIÓN';
            $motivo = $_POST['motivo'] ?? 'Enviado a revisión contable';
    
            // Determinar la acción según el rol del usuario
            $rol = strtoupper($usuario['rol']);
            $validAcciones = ['AUTORIZADO', 'RECHAZADO', 'DESCARTADO'];
            if (!in_array($accionBase, $validAcciones)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
            // Construir el estado con el rol del usuario
            $accion = $accionBase === 'AUTORIZADO' ? "FINALIZADO" : ($accionBase === 'RECHAZADO' ? "RECHAZADO_POR_{$rol}" : 'DESCARTADO');
    
            try {
                $this->pdo->beginTransaction();
                $detalleModel->updateEstado($id, $accion);
                $auditoria = new Auditoria();
                $auditoria->createAuditoria($data['id_liquidacion'], $id, $_SESSION['user_id'], $accion, $motivo);
    
                $liquidacionModel = new Liquidacion();
                $detalles = $detalleModel->getDetallesByLiquidacionId($data['id_liquidacion']);
                $allAutorizado = true;
                $anyDescartado = false;
    
                foreach ($detalles as $d) {
                    if ($d['estado'] !== 'FINALIZADO') {
                        $allAutorizado = false;
                    }
                    if ($d['estado'] === 'DESCARTADO') {
                        $anyDescartado = true;
                    }
                    if (in_array($d['estado'], ['RECHAZADO_POR_SUPERVISOR', 'RECHAZADO_POR_CONTABILIDAD'])) {
                        $allAutorizado = false;
                        $anyDescartado = true;
                    }
                }
    
                if ($anyDescartado) {
                    $liquidacionModel->updateEstado($data['id_liquidacion'], 'ENVIADO_A_CORRECCION');
                    $auditoria->createAuditoria($data['id_liquidacion'], null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación marcada para corrección');
                } elseif ($allAutorizado) {
                    $liquidacionModel->updateEstado($data['id_liquidacion'], 'FINALIZADO');
                    $auditoria->createAuditoria($data['id_liquidacion'], null, $_SESSION['user_id'], 'FINALIZADO', 'Liquidación finalizada por ' . $rol);
                }
    
                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Revisión registrada correctamente']);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error al registrar revisión: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Error al registrar la revisión: ' . $e->getMessage()]);
            }
            exit;
        }
    
        ob_start();
        require '../views/detalle_liquidaciones/revisar_individual.html';
        $html = ob_get_clean();
        $html = str_replace('{{id}}', htmlspecialchars($id), $html);
        $html = str_replace('{{no_factura}}', htmlspecialchars($data['no_factura']), $html);
        $html = str_replace('{{nombre_proveedor}}', htmlspecialchars($data['nombre_proveedor']), $html);
        $html = str_replace('{{total_factura}}', htmlspecialchars($data['total_factura']), $html);
        $html = str_replace('{{estado}}', htmlspecialchars($data['estado']), $html);
        $html = str_replace('{{comentarios}}', htmlspecialchars($data['comentarios'] ?? ''), $html);
        echo $html;
        exit;
    }
    
    private function generateSapCsv($liquidacionId) {
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($liquidacionId);
    
        $csvData = "ID,Factura,Proveedor,Fecha,Total,Estado,Comentarios\n";
        foreach ($detalles as $detalle) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $detalle['id'],
                $detalle['no_factura'],
                $detalle['nombre_proveedor'],
                $detalle['fecha'],
                $detalle['total_factura'],
                $detalle['estado'],
                $detalle['comentarios'] ?? ''
            );
        }
    
        $filename = "sap_export_liquidacion_{$liquidacionId}_" . date('YmdHis') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    
        echo $csvData;
        exit;
    }
}
?>