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
        error_log('Detalles obtenidos: ' . count($detalles) . ' registros');
    
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
                'comentarios' => $detalle['comentarios'] ?? 'N/A',
                'nombre_usuario' => $detalle['nombre_usuario'] ?? 'N/A',
                'centros_costo' => $detalle['centros_costo'],
                'nombre_cuenta_contable' => $detalle['nombre_cuenta_contable'] ?? 'N/A'
            ];
        }, $detalles);
    
        $urlParams = $_GET['mode'] ?? '';
        $isRevisarMode = $urlParams === 'revisar';
        if ($isRevisarMode && $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            $detallesNormalizados = array_filter($detallesNormalizados, function($detalle) {
                return $detalle['estado'] !== 'DESCARTADO';
            });
            error_log('Detalles filtrados para modo revisar (CONTABILIDAD): ' . count($detallesNormalizados) . ' registros');
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
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('Error: Método no permitido en createDetalleLiquidacion, se esperaba POST');
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
    
        try {
            $this->pdo->beginTransaction();
            error_log('Iniciando transacción para crear detalle de liquidación');
            error_log('POST data: ' . print_r($_POST, true));
            error_log('FILES data: ' . print_r($_FILES, true));
    
            // Obtener y validar datos del formulario
            $id_liquidacion = $_POST['id_liquidacion'] ?? '';
            $tipo_documento = $_POST['tipo_documento'] ?? 'FACTURA';
            $no_factura = str_replace('-', '', $_POST['no_factura'] ?? '');
            $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
            $nit_proveedor = $_POST['nit_proveedor'] ?? null;
            $dpi = $_POST['dpi'] ?? null;
            $fecha = $_POST['fecha'] ?? '';
            $fechaDocumento = null;
            $fechaActual = new DateTime();
            $fechaFactura = new DateTime($fecha);
            $t_gasto = $_POST['t_gasto'] ?? '';
            $p_unitario = floatval($_POST['subtotal'] ?? 0);
            $total_factura = floatval($_POST['total_factura'] ?? 0);
            $estado = $_POST['estado'] ?? 'EN_PROCESO';
            $id_centro_costo = is_array($_POST['id_centro_costo']) ? $_POST['id_centro_costo'] : [$_POST['id_centro_costo']];
            $porcentajes = is_array($_POST['porcentaje']) ? $_POST['porcentaje'] : [$_POST['porcentaje'] ?? 100];
            $cantidad = $_POST['cantidad'] ?? null;
            $serie = $_POST['serie'] ?? null;
            $iva = floatval($_POST['iva'] ?? 0);
            $idp = floatval($_POST['idp'] ?? 0);
            $inguat = floatval($_POST['inguat'] ?? 0);
            $propina = floatval($_POST['propina'] ?? 0);
            $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
            $nombre_cuenta_contable = $_POST['nombre_cuenta_contable'] ?? 'N/A';
            $id_cuenta_contable_propina = $_POST['id_cuenta_contable_propina'] ?? null;
            $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp'] ?? null;
            $nombre_cuenta_contable_propina = $_POST['nombre_cuenta_contable_propina'] ?? 'N/A';
            $tipo_combustible = $_POST['tipo_combustible'] ?? null;
            $comentarios = $_POST['comentarios'] ?? null;
            $id_usuario = $_SESSION['user_id'];

            // Si la fecha de la factura NO es del mes actual, guardar en fecha_documento
        if ($fechaFactura->format('Y-m') !== $fechaActual->format('Y-m')) {
            $fechaDocumento = $fechaFactura->format('Y-m-d');
            error_log("Fecha del documento guardada en fecha_documento: $fechaDocumento (mes diferente al actual)");
        } else {
            error_log("Fecha del documento NO guardada en fecha_documento (mes igual al actual)");
        }
    
            // Validaciones de campos obligatorios
            if (empty($id_liquidacion) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || 
                empty($t_gasto) || !is_numeric($p_unitario) || !is_numeric($total_factura)) {
                throw new Exception('Todos los campos obligatorios deben ser válidos: id_liquidacion, no_factura, nombre_proveedor, fecha, t_gasto, subtotal, total_factura.');
            }
    
            // Validar cuenta contable
            if (empty($id_cuenta_contable) && $t_gasto !== 'otros...') {
                throw new Exception('La Cuenta Contable es obligatoria para el tipo de gasto "' . $t_gasto . '".');
            }
    
            // Validar cuenta contable de propina si aplica
            if ($t_gasto === 'Alimentos' && $propina > 0 && empty($id_cuenta_contable_propina)) {
                throw new Exception('La Cuenta Contable de Propina es obligatoria para el tipo de gasto "Alimentos" cuando hay propina.');
            }
            
            if ($t_gasto === 'Combustible') {
                $id_cuenta_contable = $_POST['id_cuenta_contable']; // Combustibles y lubricantes
                $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp']; // IDP
                $id_cuenta_contable_inguat = null;
            } elseif ($t_gasto === 'Hospedaje') {
                $id_cuenta_contable = $_POST['id_cuenta_contable']; // Viáticos locales
                $id_cuenta_contable_inguat = $this->determinarCuentaInguat($id_centro_costo[0]); // Cuenta fija para INGUAT
            }else {
                $id_cuenta_contable = $_POST['id_cuenta_contable'];
                $id_cuenta_contable_idp = null;
                $id_cuenta_contable_inguat = null;
            }
    
            // Validar liquidación
            $liquidacionModel = new Liquidacion();
            $liquidacion = $liquidacionModel->getLiquidacionById($id_liquidacion);
            if (!$liquidacion) {
                throw new Exception('El ID de liquidación ' . $id_liquidacion . ' no existe.');
            }
            if ($liquidacion['id_usuario'] != $id_usuario) {
                throw new Exception('No tienes permiso para agregar detalles a esta liquidación.');
            }
    
            // Validar fecha
            $fechaDetalle = new DateTime($fecha);
            $fechaCreacionLiquidacion = new DateTime($liquidacion['fecha_creacion']);
            if ($fechaDetalle > $fechaCreacionLiquidacion) {
                throw new Exception('La fecha del detalle no puede ser mayor que la fecha de creación de la liquidación (' . $liquidacion['fecha_creacion'] . ').');
            }
    
            // Validar centros de costo y porcentajes
            if (count($id_centro_costo) !== count($porcentajes)) {
                throw new Exception('Los centros de costo y porcentajes no coinciden.');
            }
            $totalPorcentaje = array_sum($porcentajes);
            if (abs($totalPorcentaje - 100) > 0.01) {
                throw new Exception('La suma de los porcentajes debe ser exactamente 100%.');
            }
    
            // Validar y recalcular IVA para Alimentos
            if ($t_gasto === 'Alimentos' && in_array($tipo_documento, ['FACTURA', 'FACTURA ELECTRONICA'])) {
                $ivaRate = 0.12; // Suponiendo IVA del 12% para Alimentos
                $subtotalSinImpuestos = $total_factura - $idp - $inguat - $propina;
                $expectedSubtotal = $subtotalSinImpuestos / (1 + $ivaRate);
                $expectedIva = $expectedSubtotal * $ivaRate;

                // Verificar si el IVA recibido coincide con el esperado (con tolerancia para redondeo)
                if (abs($iva - $expectedIva) > 0.01) {
                    error_log("IVA recibido ($iva) no coincide con el esperado ($expectedIva). Recalculando IVA.");
                    $iva = $expectedIva;
                    $p_unitario = $expectedSubtotal;
                }
            }
    
            // Manejar archivos
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
                            throw new Exception('Tipo de archivo no permitido: ' . $name);
                        }
                        if ($fileSize > $maxFileSize) {
                            throw new Exception('El archivo ' . $name . ' excede el tamaño máximo permitido de 5 MB.');
                        }
                        $fileName = basename($name);
                        $filePath = $uploadDir . uniqid() . '_' . $fileName;
                        if (!move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $filePath)) {
                            throw new Exception('Error al subir el archivo: ' . $name);
                        }
                        $rutas_archivos[] = 'Uploads/' . basename($filePath);
                    }
                }
            } else {
                throw new Exception('Debes subir al menos un archivo (PDF o imagen).');
            }
            $rutas_json = json_encode($rutas_archivos);
            error_log('Archivos subidos: ' . $rutas_json);
    
            // Validar DTE si aplica
            $detalleModel = new DetalleLiquidacion();
            if ($serie && $no_factura) {
                $stmt = $this->pdo->prepare("SELECT usado FROM dte WHERE serie = ? AND numero_dte = ?");
                $stmt->execute([$serie, $no_factura]);
                $dte = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$dte) {
                    throw new Exception('El DTE con serie=' . $serie . ' y numero_dte=' . $no_factura . ' no existe.');
                }
                if ($dte['usado'] === 'Y') {
                    throw new Exception('El DTE con serie=' . $serie . ' y numero_dte=' . $no_factura . ' ya está en uso.');
                }
                if (!$detalleModel->updateDteUsado($serie, $no_factura)) {
                    throw new Exception('No se pudo actualizar el estado usado del DTE para serie=' . $serie . ', numero_dte=' . $no_factura);
                }
                error_log("DTE actualizado a usado='Y' para serie=$serie, numero_dte=$no_factura");
            }
    
            // Determinar grupo_id
            $grupo_id = (count($id_centro_costo) == 1) ? 0 : $this->pdo->query("SELECT COALESCE(MAX(grupo_id), 0) + 1 FROM detalle_liquidaciones")->fetchColumn();
            error_log("Generado grupo_id: $grupo_id para " . count($id_centro_costo) . " centros de costo");
    
            // Crear detalles por cada centro de costo
            $detalle_ids = [];
    foreach ($id_centro_costo as $index => $centro_costo) {
        $porcentaje = floatval($porcentajes[$index]);
        $es_principal = ($index === 0) ? 1 : 0;

        // Para Alimentos con propina > 0, concatenar ambas cuentas contables
        if ($t_gasto === 'Alimentos' && $propina > 0 && $id_cuenta_contable_propina) {
            $cuenta_contable_nombre = $nombre_cuenta_contable . ' / ' . $nombre_cuenta_contable_propina;
            $cuenta_contable_id = $id_cuenta_contable . ',' . $id_cuenta_contable_propina; // Guardar ambos IDs separados por coma
        } else {
            $cuenta_contable_id = $id_cuenta_contable;
            $cuenta_contable_nombre = $nombre_cuenta_contable;
        }

        $detalle_id = $detalleModel->createDetalleLiquidacion(
            $id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, 
            $t_gasto, $p_unitario * ($porcentaje / 100), $total_factura * ($porcentaje / 100), $estado, 
            $centro_costo, $cantidad, $serie, $rutas_json, $iva * ($porcentaje / 100), 
            $idp * ($porcentaje / 100), $inguat * ($porcentaje / 100), $propina * ($porcentaje / 100), 
            $id_cuenta_contable, $tipo_combustible, $id_usuario, $comentarios, $porcentaje, 
            $nombre_cuenta_contable, $es_principal, $grupo_id,
            $id_cuenta_contable_propina, // Nuevo parámetro
            $nombre_cuenta_contable_propina, // Nuevo parámetro
             $id_cuenta_contable_idp,
             $fechaDocumento,
             $id_cuenta_contable_inguat 
        );

        if (!$detalle_id) {
            throw new Exception('Error al crear detalle de liquidación en la base de datos.');
        }

        $detalle_ids[] = $detalle_id;
        error_log("Creado detalle ID $detalle_id con grupo_id $grupo_id para centro de costo $centro_costo con porcentaje $porcentaje, cuenta contable: $cuenta_contable_nombre");
        error_log("Creado detalle ID $detalle_id con grupo_id $grupo_id, fecha_documento: " . ($fechaDocumento ?? 'NULL'));
    }
    
            $auditoria = new Auditoria();
            foreach ($detalle_ids as $detalle_id) {
                $auditoria->createAuditoria($id_liquidacion, $detalle_id, $id_usuario, 'CREADO', 'Detalle de liquidación creado por usuario ID ' . $id_usuario);
            }
    
            $this->pdo->commit();
            error_log('Transacción confirmada para grupo_id: ' . $grupo_id);
    
            header('Content-Type: application/json');
            http_response_code(201);
            echo json_encode([
                'detalle_id' => $detalle_ids[0],
                'grupo_id' => $grupo_id,
                'message' => 'Detalles de liquidación creados',
                'cuenta_contable_nombre' => ($t_gasto === 'Alimentos' && $propina > 0) ? $nombre_cuenta_contable_propina : $nombre_cuenta_contable,
                'rutas_archivos' => json_decode($rutas_json, true),
                'centros_costo' => array_map(function($cc, $p) {
                    return ['id_centro_costo' => $cc, 'porcentaje' => $p];
                }, $id_centro_costo, $porcentajes),
                'monto_total' => $this->calcularMontoTotal($id_liquidacion)
            ]);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error en createDetalleLiquidacion: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
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
        if (!$usuarioModel->tienePermiso($usuario, 'create_detalles') && !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log('Error: No tienes permiso para actualizar detalles de liquidaciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar detalles de liquidaciones']);
            exit;
        }
    
        $detalleModel = new DetalleLiquidacion();
        
        $detalle = $detalleModel->getDetalleById($id);
        if (!$detalle) {
            error_log('Error: Detalle no encontrado para ID ' . $id);
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }
    
        if ($detalle['id_usuario'] != $_SESSION['user_id'] && !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log('Error: No tienes permiso para editar este detalle (ID: ' . $id . ')');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para editar este detalle']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->pdo->beginTransaction();
    
                error_log('POST data en updateDetalleLiquidacion: ' . print_r($_POST, true));
                error_log('FILES data en updateDetalleLiquidacion: ' . print_r($_FILES, true));
    
                $tipo_documento = $_POST['tipo_documento'] ?? 'FACTURA';
                $no_factura = str_replace('-', '', $_POST['no_factura'] ?? '');
                $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                $dpi = $_POST['dpi'] ?? null;
                $fecha = $_POST['fecha'] ?? '';
                $fechaDocumento = null;
                $detalleExistente = $detalleModel->getDetalleById($id);
                $fechaActual = new DateTime();
                $fechaFactura = new DateTime($fecha);
                $t_gasto = $_POST['t_gasto'] ?? '';
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $total_factura = floatval($_POST['total_factura'] ?? 0);
                $id_centro_costo = is_array($_POST['id_centro_costo']) ? $_POST['id_centro_costo'] : [$_POST['id_centro_costo']];
                $porcentajes = is_array($_POST['porcentaje']) ? $_POST['porcentaje'] : [$_POST['porcentaje'] ?? 100];
                $cantidad = $_POST['cantidad'] ?? null;
                $serie = $_POST['serie'] ?? null;
                $iva = floatval($_POST['iva'] ?? 0);
                $idp = floatval($_POST['idp'] ?? 0);
                $inguat = floatval($_POST['inguat'] ?? 0);
                $propina = floatval($_POST['propina'] ?? 0);
                $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                $nombre_cuenta_contable = $_POST['nombre_cuenta_contable'] ?? 'N/A';
                $id_cuenta_contable_propina = $_POST['id_cuenta_contable_propina'] ?? null;
                $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp'] ?? null;
                $nombre_cuenta_contable_propina = $_POST['nombre_cuenta_contable_propina'] ?? 'N/A';
                $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                $comentarios = $_POST['comentarios'] ?? null;
                $estado = $_POST['estado'] ?? $detalle['estado'];
                $grupo_id = $detalle['grupo_id'] ?? 0;
    
                $rol = strtoupper($usuario['rol']);
                if ($estado === 'APROBADO') {
                    $estado = ($rol === 'CONTABILIDAD') ? 'PENDIENTE_REVISION_CONTABILIDAD' : 'PENDIENTE_AUTORIZACION';
                } elseif ($estado === 'RECHAZADO') {
                    $estado = 'DESCARTADO';
                }
    
                $allowedEstados = [
                    'EN_PROceso', 'PENDIENTE_AUTORIZACION', 'PENDIENTE_REVISION_CONTABILIDAD',
                    'FINALIZADO', 'DESCARTADO', 'ENVIADO_A_CORRECCION', 'EN_CORRECCION'
                ];
                if (!in_array($estado, $allowedEstados)) {
                    throw new Exception("Estado no permitido: {$estado}. Contacta al administrador del sistema.");
                }
    
                if (empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || 
                    !is_numeric($subtotal) || !is_numeric($total_factura)) {
                    throw new Exception('Todos los campos obligatorios deben ser válidos.');
                }
    
                if (count($id_centro_costo) !== count($porcentajes)) {
                    throw new Exception('Los centros de costo y porcentajes no coinciden.');
                }
                $totalPorcentaje = array_sum($porcentajes);
                if ($totalPorcentaje < 99.99 || $totalPorcentaje > 100.01) {
                    throw new Exception('La suma de los porcentajes debe ser entre 99.99% y 100.01%.');
                }

                 // Si ya existe una fecha_documento, mantenerla
                if (!empty($detalleExistente['fecha_documento'])) {
                    $fechaDocumento = $detalleExistente['fecha_documento'];
                    error_log("Manteniendo fecha_documento existente: $fechaDocumento");
                    } 
                    // Si no existe fecha_documento y la fecha de factura NO es del mes actual, guardarla
                elseif ($fechaFactura->format('Y-m') !== $fechaActual->format('Y-m')) {
                    $fechaDocumento = $fechaFactura->format('Y-m-d');
                    error_log("Nueva fecha_documento guardada: $fechaDocumento (mes diferente al actual)");
                } else {
                    error_log("No se guarda fecha_documento (mes igual al actual o ya existe)");
                }
    
                // Validar y recalcular IVA para Alimentos
                if ($t_gasto === 'Alimentos' && in_array($tipo_documento, ['FACTURA', 'FACTURA ELECTRONICA'])) {
                    $ivaRate = 0.12; // Suponiendo IVA del 12% para Alimentos
                    $subtotalSinImpuestos = $total_factura - $idp - $inguat - $propina;
                    $expectedSubtotal = $subtotalSinImpuestos / (1 + $ivaRate);
                    $expectedIva = $expectedSubtotal * $ivaRate;
    
                    // Verificar si el IVA recibido coincide con el esperado (con tolerancia para redondeo)
                    if (abs($iva - $expectedIva) > 0.01) {
                        error_log("IVA recibido ($iva) no coincide con el esperado ($expectedIva). Recalculando IVA.");
                        $iva = $expectedIva;
                        $subtotal = $expectedSubtotal;
                    }
                }
    
                // Validar cuenta contable de propina si aplica
                if ($t_gasto === 'Alimentos' && $propina > 0 && empty($id_cuenta_contable_propina)) {
                    throw new Exception('La Cuenta Contable de Propina es obligatoria para el tipo de gasto "Alimentos" cuando hay propina.');
                }

    
                // Manejar archivos
                $rutas_archivos = json_decode($detalle['rutas_archivos'] ?? '[]', true);
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
                                throw new Exception('Tipo de archivo no permitido: ' . $name);
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
                        }
                    }
                }
                $rutas_json = json_encode($rutas_archivos);
    
                // Actualizar estado DTE si cambió
                if ($serie && $no_factura && ($serie != $detalle['serie'] || $no_factura != str_replace('-', '', $detalle['no_factura']))) {
                    if ($detalle['serie'] && $detalle['no_factura']) {
                        $stmt = $this->pdo->prepare("UPDATE dte SET usado = 'X' WHERE serie = ? AND numero_dte = ?");
                        $stmt->execute([$detalle['serie'], str_replace('-', '', $detalle['no_factura'])]);
                        $detalleModel->liberarDte($detalle['serie'], $detalle['no_factura']);
                        error_log("Reset DTE usado to 'X' for serie={$detalle['serie']}, numero_dte=" . str_replace('-', '', $detalle['no_factura']));
                    }
                    $stmt = $this->pdo->prepare("SELECT usado FROM dte WHERE serie = ? AND numero_dte = ?");
                    $stmt->execute([$serie, $no_factura]);
                    $dte = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$dte) {
                        throw new Exception('El DTE con serie=' . $serie . ' y numero_dte=' . $no_factura . ' no existe.');
                    }
                    if ($dte['usado'] === 'Y') {
                        throw new Exception('El DTE con serie=' . $serie . ' y numero_dte=' . $no_factura . ' ya está en uso.');
                    }
                    if (!$detalleModel->updateDteUsado($serie, $no_factura)) {
                        throw new Exception('No se pudo actualizar el estado usado del DTE para serie=' . $serie . ', numero_dte=' . $no_factura);
                    }
                    error_log("DTE actualizado a usado='Y' para serie=$serie, numero_dte=$no_factura");
                }
    
                // Determinar grupo_id: usar 0 si es un solo centro de costo, de lo contrario generar nuevo grupo_id
                $new_grupo_id = (count($id_centro_costo) == 1) ? 0 : ($detalle['grupo_id'] ?: $this->pdo->query("SELECT COALESCE(MAX(grupo_id), 0) + 1 FROM detalle_liquidaciones")->fetchColumn());
                error_log("Grupo_id determinado para actualización: $new_grupo_id para " . count($id_centro_costo) . " centros de costo");
    
                // Si grupo_id es 0, no eliminar otros detalles, ya que cada detalle con grupo_id = 0 es independiente
                if ($detalle['grupo_id'] !== 0) {
                    // Eliminar detalles secundarios existentes del grupo (excepto el detalle principal)
                    $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE grupo_id = ? AND id != ?");
                    $stmt->execute([$detalle['grupo_id'], $id]);
                    error_log("Eliminados detalles secundarios para grupo_id {$detalle['grupo_id']}, excepto ID $id");
                }
    
                // Crear/Actualizar detalles para cada centro de costo
                $detalle_ids = [$id];
                foreach ($id_centro_costo as $index => $centro_costo) {
                    $porcentaje = floatval($porcentajes[$index]);
                    $es_principal = ($index == 0) ? 1 : 0;
    
                    // Para Alimentos con propina > 0, concatenar ambas cuentas contables
                    if ($t_gasto === 'Alimentos' && $propina > 0 && $id_cuenta_contable_propina) {
                        $cuenta_contable_nombre = $nombre_cuenta_contable . ' / ' . $nombre_cuenta_contable_propina;
                        $cuenta_contable_id = $id_cuenta_contable . ',' . $id_cuenta_contable_propina; // Guardar ambos IDs separados por coma
                    } else {
                        $cuenta_contable_id = $id_cuenta_contable;
                        $cuenta_contable_nombre = $nombre_cuenta_contable;
                    }
    
                    if ($index == 0) {
                        // Actualizar el detalle principal
                        $result = $detalleModel->updateDetalleLiquidacion(
                            $id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto,
                            $subtotal * ($porcentaje / 100), $total_factura * ($porcentaje / 100), $centro_costo, 
                            $iva * ($porcentaje / 100), $idp * ($porcentaje / 100), $inguat * ($porcentaje / 100), 
                            $propina * ($porcentaje / 100), $id_cuenta_contable, $cantidad, $serie, $rutas_json, 
                            $tipo_combustible, $comentarios, $porcentaje, $nombre_cuenta_contable, $estado, $new_grupo_id,
                            $id_cuenta_contable_propina, // Nuevo parámetro
                            $nombre_cuenta_contable_propina, // Nuevo parámetro
                            $id_cuenta_contable_idp,
                            $fechaDocumento
                        );
                        if (!$result) {
                            throw new Exception('Error al actualizar el detalle principal ID ' . $id);
                        }
                        error_log("Actualizado detalle principal ID $id con grupo_id $new_grupo_id, centro de costo $centro_costo, porcentaje $porcentaje, cuenta contable: $cuenta_contable_nombre");
                    } else {
                        // Crear nuevos detalles para otros centros de costo
                        $detalle_id = $detalleModel->createDetalleLiquidacion(
                            $detalle['id_liquidacion'], $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, 
                            $t_gasto, $subtotal * ($porcentaje / 100), $total_factura * ($porcentaje / 100), $estado, 
                            $centro_costo, $cantidad, $serie, $rutas_json, $iva * ($porcentaje / 100), 
                            $idp * ($porcentaje / 100), $inguat * ($porcentaje / 100), $propina * ($porcentaje / 100), 
                            $cuenta_contable_id, $tipo_combustible, $_SESSION['user_id'], $comentarios, $porcentaje, 
                            $cuenta_contable_nombre, $es_principal, $new_grupo_id,null, null, null,$fechaDocumento
                        );
                        if (!$detalle_id) {
                            throw new Exception('Error al crear detalle secundario para centro de costo ' . $centro_costo);
                        }
                        $detalle_ids[] = $detalle_id;
                        error_log("Creado detalle secundario ID $detalle_id con grupo_id $new_grupo_id para centro de costo $centro_costo con porcentaje $porcentaje, cuenta contable: $cuenta_contable_nombre");
                    }
                }
    
                $auditoria = new Auditoria();
                foreach ($detalle_ids as $detalle_id) {
                    $auditoria->createAuditoria($detalle['id_liquidacion'], $detalle_id, $_SESSION['user_id'], 'ACTUALIZADO', 'Detalle de liquidación actualizado por usuario ID ' . $_SESSION['user_id']);
                }
    
                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode([
                    'detalle_id' => $id,
                    'grupo_id' => $new_grupo_id,
                    'message' => 'Detalles de liquidación actualizados',
                    'cuenta_contable_nombre' => ($t_gasto === 'Alimentos' && $propina > 0) ? 
                        $nombre_cuenta_contable . ' / ' . $nombre_cuenta_contable_propina : $nombre_cuenta_contable,
                    'rutas_archivos' => json_decode($rutas_json, true),
                    'centros_costo' => array_map(function($cc, $p) {
                        return ['id_centro_costo' => $cc, 'porcentaje' => $p];
                    }, $id_centro_costo, $porcentajes)
                ]);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error en updateDetalleLiquidacion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }

   public function deleteDetalleLiquidacion($id) {
    if (!isset($_SESSION['user_id'])) {
        error_log('Error: No hay session user_id en deleteDetalleLiquidacion');
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }
    
    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuarioModel->tienePermiso($usuario, 'delete_detalles')) {
        error_log('Error: No tienes permiso para eliminar detalles de liquidaciones');
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para eliminar detalles de liquidaciones']);
        exit;
    }
    
    $detalleModel = new DetalleLiquidacion();
    $detalle = $detalleModel->getDetalleById($id);
    if (!$detalle) {
        error_log('Error: Detalle no encontrado para ID ' . $id);
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Detalle no encontrado']);
        exit;
    }
    
    if ($detalle['id_usuario'] != $_SESSION['user_id']) {
        error_log('Error: No tienes permiso para eliminar este detalle (ID: ' . $id . ')');
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para eliminar este detalle']);
        exit;
    }
    
    try {
        $this->pdo->beginTransaction();
        
        // 1. Primero liberar DTE si existe
        if (!empty($detalle['serie']) && !empty($detalle['no_factura'])) {
            $detalleModel->liberarDte($detalle['serie'], $detalle['no_factura']);
        }
        
        // 2. Si tiene grupo_id > 0, liberar DTEs de todos los detalles del grupo
        if ($detalle['grupo_id'] > 0) {
            $detallesGrupo = $detalleModel->getDetallesByGrupoId($detalle['grupo_id'], $detalle['id_liquidacion']);
            foreach ($detallesGrupo as $detalleGrupo) {
                if (!empty($detalleGrupo['serie']) && !empty($detalleGrupo['no_factura'])) {
                    $detalleModel->liberarDte($detalleGrupo['serie'], $detalleGrupo['no_factura']);
                }
            }
        }
        
        // 3. Eliminar los detalles
        if ($detalle['grupo_id'] == 0) {
            $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE id = ?");
            $stmt->execute([$id]);
            error_log("Eliminado detalle individual ID $id con grupo_id 0");
        } else {
            $stmt = $this->pdo->prepare("DELETE FROM detalle_liquidaciones WHERE grupo_id = ?");
            $stmt->execute([$detalle['grupo_id']]);
            error_log("Eliminados todos los detalles para grupo_id {$detalle['grupo_id']}");
        }
        
        $auditoria = new Auditoria();
        $auditoria->createAuditoria($detalle['id_liquidacion'], $id, $_SESSION['user_id'], 'ELIMINAR_DETALLE', "Detalle de liquidación eliminado: ID $id, grupo_id {$detalle['grupo_id']}");
        
        $this->pdo->commit();
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Grupo de detalles de liquidación eliminado']);
    } catch (Exception $e) {
        $this->pdo->rollBack();
        error_log("Error al eliminar detalle ID $id: " . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar grupo de detalles de liquidación: ' . $e->getMessage()]);
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
    
            $rol = strtoupper($usuario['rol']);
            $validAcciones = ['AUTORIZADO', 'RECHAZADO', 'DESCARTADO'];
            if (!in_array($accionBase, $validAcciones)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Acción no válida']);
                exit;
            }
    
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
        $html = str_replace('{{nombre_cuenta_contable}}', htmlspecialchars($data['nombre_cuenta_contable'] ?? 'N/A'), $html);
        echo $html;
        exit;
    }

    private function generateSapCsv($liquidacionId) {
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($liquidacionId);
    
        $csvData = "ID,Factura,Proveedor,Fecha,Total,Estado,Comentarios,CuentaContable\n";
        foreach ($detalles as $detalle) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s\n",
                $detalle['id'],
                $detalle['no_factura'],
                $detalle['nombre_proveedor'],
                $detalle['fecha'],
                $detalle['total_factura'],
                $detalle['estado'],
                $detalle['comentarios'] ?? '',
                $detalle['nombre_cuenta_contable'] ?? 'N/A'
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

    public function updateDteUsado() {
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
            echo json_encode(['error' => 'No tienes permiso para actualizar el estado del DTE']);
            exit;
        }
    
        $serie = $_POST['serie'] ?? '';
        $numero_dte = $_POST['numero_dte'] ?? '';
    
        if (empty($serie) || empty($numero_dte)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Serie o número de DTE no proporcionado']);
            exit;
        }
    
        try {
            $detalleModel = new DetalleLiquidacion();
            if ($detalleModel->updateDteUsado($serie, $numero_dte)) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Estado del DTE actualizado a Y']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'No se pudo actualizar el estado del DTE']);
            }
        } catch (Exception $e) {
            error_log("Error en updateDteUsado: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar el estado del DTE: ' . $e->getMessage()]);
        }
        exit;
    }

    private function calcularMontoTotal($id_liquidacion) {
        $stmt = $this->pdo->prepare("SELECT SUM(total_factura) as monto_total FROM detalle_liquidaciones WHERE id_liquidacion = ?");
        $stmt->execute([$id_liquidacion]);
        return $stmt->fetchColumn() ?: 0;
    }

    private function determinarCuentaInguat($id_centro_costo) {
        // Obtener el código del centro de costo
        $stmt = $this->pdo->prepare("SELECT codigo FROM centros_costos WHERE id = ?");
        $stmt->execute([$id_centro_costo]);
        $centro_costo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$centro_costo) {
            error_log("Centro de costo no encontrado para ID: $id_centro_costo, usando cuenta por defecto");
            return '641001003'; // Cuenta por defecto
        }
        
        $codigo = $centro_costo['codigo'];
        error_log("Determinando cuenta INGUAT para centro de costo: $codigo");
        
        // Verificar si el código empieza con T y tiene 3 o 4 dígitos
        if (preg_match('/^T(\d{2})$/', $codigo, $matches)) {
            // T00 (3 dígitos) -> 61
            $cuenta_inguat = '611001003';
            error_log("Centro de costo T con 3 dígitos ($codigo), cuenta INGUAT: $cuenta_inguat");
            return $cuenta_inguat;
        } elseif (preg_match('/^T(\d{3})$/', $codigo, $matches)) {
            // T000 (4 dígitos) -> 62  
            $cuenta_inguat = '621001003';
            error_log("Centro de costo T con 4 dígitos ($codigo), cuenta INGUAT: $cuenta_inguat");
            return $cuenta_inguat;
        } else {
            // Por defecto
            $cuenta_inguat = '641001003';
            error_log("Centro de costo no coincide con patrones T ($codigo), cuenta INGUAT por defecto: $cuenta_inguat");
            return $cuenta_inguat;
        }
    }
}
?>