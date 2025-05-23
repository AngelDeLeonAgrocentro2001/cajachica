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

class LiquidacionController
{
    private $pdo;
    private $liquidacionModel;
    private $detalleModel;
    private $cajaChicaModel;
    private $tipoDocumentoModel;
    private $tipoGastoModel;
    private $centroCostoModel;
    private $cuentaContableModel;
    private $auditoriaModel;

    public function __construct()
    {
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

    public function CONEXION_HANA($db_name)
    {
        $driver = "HDBODBC";
        $servername = "192.168.1.9:30015";
        $username = "SAPDBA";
        $password = "B1Adminh";
        $conn = odbc_connect("Driver=$driver;ServerNode=$servername;Database=$db_name;", $username, $password, SQL_CUR_USE_ODBC);
        if (!$conn) {
            error_log("Error al conectar a HANA: " . odbc_errormsg());
            throw new Exception("Error al conectar a la base de datos HANA.");
        }
        // Set UTF-8 encoding for ODBC connection
        odbc_exec($conn, "SET CHARACTER SET UTF8");
        return $conn;
    }

    public function ctrObtenerCuentas($Sociedad, $cuenta)
    {
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

    public function mdlObtenerCuentas($query)
    {
        try {
            $conexion = $this->CONEXION_HANA('GT_AGROCENTRO_2016');
            error_log("Ejecutando consulta HANA: " . $query);
            $prov = odbc_exec($conexion, $query);
            $json = "";
            if ($prov) {
                while ($proveedor = odbc_fetch_object($prov)) {
                    // Registro del nombre de cuenta sin procesar para depurar problemas de codificación
                    error_log("Cuenta obtenida (sin procesar): " . $proveedor->AcctCode . '-' . $proveedor->AcctName);
                    // Detectar codificación del nombre de cuenta y convertir a UTF-8 si es necesario
                    $acctName = $proveedor->AcctName;
                    if (!mb_check_encoding($acctName, 'UTF-8')) {
                        // Si no es UTF-8, asumir que es ISO-8859-1 (común en HANA) y convertir
                        $acctName = mb_convert_encoding($acctName, 'UTF-8', 'ISO-8859-1');
                        error_log("Nombre de cuenta convertido a UTF-8: " . $proveedor->AcctCode . '-' . $acctName);
                    }
                    $json .= "|" . $proveedor->AcctCode . '-' . $acctName;
                    error_log("Cuenta procesada: " . $proveedor->AcctCode . '-' . $acctName);
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

    public function listLiquidaciones()
    {
        error_log('Iniciando listLiquidaciones');
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id');
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario) {
            error_log('Usuario no encontrado para ID: ' . $_SESSION['user_id']);
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(401);
            echo json_encode(['error' => 'Usuario no encontrado.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $rol = strtoupper($usuario['rol']);
        $urlParams = $_GET['mode'] ?? '';
        $isRevisarMode = $urlParams === 'revisar';

        // Fetch liquidations based on role and user
        if ($rol === 'SUPERVISOR') {
            // For SUPERVISOR, filter by id_supervisor and optionally by state in autorizar mode
            if ($urlParams === 'autorizar') {
                $liquidaciones = $this->liquidacionModel->getAllLiquidaciones(null, $_SESSION['user_id'], 'PENDIENTE_AUTORIZACION');
                error_log('Liquidaciones obtenidas para SUPERVISOR (ID: ' . $_SESSION['user_id'] . '): ' . count($liquidaciones) . ' registros');
            } else {
                // In other modes (like list without mode), still filter by id_supervisor
                $liquidaciones = $this->liquidacionModel->getAllLiquidaciones(null, $_SESSION['user_id']);
                error_log('Liquidaciones obtenidas para SUPERVISOR (ID: ' . $_SESSION['user_id'] . '): ' . count($liquidaciones) . ' registros');
            }

            // Additional filter to ensure only liquidations with matching id_supervisor are shown
            $liquidaciones = array_filter($liquidaciones, function ($liquidacion) use ($usuario) {
                return !isset($liquidacion['id_supervisor']) || $liquidacion['id_supervisor'] == $usuario['id'];
            });
            error_log('Liquidaciones filtradas por id_supervisor para SUPERVISOR (ID: ' . $_SESSION['user_id'] . '): ' . count($liquidaciones) . ' registros');
        } elseif ($rol === 'CONTABILIDAD') {
            // For CONTABILIDAD, filter by id_contador or NULL to allow assignment
            $liquidaciones = $this->liquidacionModel->getAllLiquidaciones(null, null, null, $_SESSION['user_id']);
            error_log('Liquidaciones obtenidas para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($liquidaciones) . ' registros');
            foreach ($liquidaciones as $liquidacion) {
                error_log('Liquidacion ID: ' . $liquidacion['id'] . ', id_contador: ' . ($liquidacion['id_contador'] ?? 'N/A') . ', Estado: ' . ($liquidacion['estado'] ?? 'N/A'));
            }

            // Allow CONTABILIDAD to see liquidations where id_contador matches or is NULL
            $liquidaciones = array_filter($liquidaciones, function ($liquidacion) use ($usuario) {
                return !isset($liquidacion['id_contador']) || $liquidacion['id_contador'] == $usuario['id'];
            });
            error_log('Liquidaciones filtradas por id_contador para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($liquidaciones) . ' registros');

            // Broaden state filter to match SUPERVISOR behavior
            $liquidaciones = array_filter($liquidaciones, function ($liquidacion) {
                return in_array($liquidacion['estado'], [
                    'PENDIENTE_AUTORIZACION',
                    'PENDIENTE_REVISION_CONTABILIDAD',
                    'EN_PROCESO',
                    'FINALIZADO',
                    'RECHAZADO_POR_CONTABILIDAD'
                ]);
            });
            error_log('Liquidaciones filtradas por estado para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($liquidaciones) . ' registros');
        } else {
            // For other roles, fetch all liquidations and filter by id_usuario
            $liquidaciones = $this->liquidacionModel->getAllLiquidaciones();
            error_log('Liquidaciones obtenidas: ' . count($liquidaciones) . ' registros para el usuario ID ' . $_SESSION['user_id']);

            $liquidaciones = array_filter($liquidaciones, function ($liquidacion) use ($usuario) {
                return $liquidacion['id_usuario'] == $usuario['id'];
            });
            error_log('Liquidaciones filtradas para usuario no SUPERVISOR/CONTABILIDAD: ' . count($liquidaciones) . ' registros');
        }

        foreach ($liquidaciones as &$liquidacion) {
            $liquidacion['detalles'] = $this->detalleModel->getDetallesByLiquidacionId($liquidacion['id']);
        }
        unset($liquidacion);

        // Fetch corrected details based on role
        $correctedDetalles = [];
        if ($rol === 'SUPERVISOR' && $urlParams === 'autorizar') {
            $correctedDetalles = $this->detalleModel->getCorrectedDetallesForSupervisor($_SESSION['user_id']);
            // Filter corrected details to ensure they belong to liquidations with matching id_supervisor
            $correctedDetalles = array_filter($correctedDetalles, function ($detalle) use ($usuario, $liquidaciones) {
                $liquidacion = array_filter($liquidaciones, function ($liq) use ($detalle) {
                    return $liq['id'] == $detalle['liquidacion_id'];
                });
                $liquidacion = reset($liquidacion);
                return $liquidacion && (!isset($liquidacion['id_supervisor']) || $liquidacion['id_supervisor'] == $usuario['id']);
            });
            error_log('Detalles corregidos obtenidos para SUPERVISOR (ID: ' . $_SESSION['user_id'] . '): ' . count($correctedDetalles) . ' registros');
        } elseif ($rol === 'CONTABILIDAD' && $urlParams === 'autorizar') {
            $correctedDetalles = $this->detalleModel->getCorrectedDetallesForContador($_SESSION['user_id']);
            // Filter corrected details to ensure they belong to liquidations with matching id_contador
            $correctedDetalles = array_filter($correctedDetalles, function ($detalle) use ($usuario, $liquidaciones) {
                $liquidacion = array_filter($liquidaciones, function ($liq) use ($detalle) {
                    return $liq['id'] == $detalle['liquidacion_id'];
                });
                $liquidacion = reset($liquidacion);
                return $liquidacion && (!isset($liquidacion['id_contador']) || $liquidacion['id_contador'] == $usuario['id']);
            });
            error_log('Detalles corregidos obtenidos para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($correctedDetalles) . ' registros');
        }

        // Additional filtering for CONTABILIDAD in revisar mode
        if ($isRevisarMode && $rol === 'CONTABILIDAD') {
            $liquidaciones = array_filter($liquidaciones, function ($liquidacion) {
                return in_array($liquidacion['estado'], [
                    'PENDIENTE_REVISION_CONTABILIDAD',
                    'FINALIZADO',
                    'RECHAZADO_POR_CONTABILIDAD',
                    'EN_PROCESO'
                ]);
            });
            error_log('Liquidaciones filtradas para CONTABILIDAD: ' . count($liquidaciones) . ' registros');
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json; charset=UTF-8');
            $response = [
                'liquidaciones' => array_values($liquidaciones),
                'corrected_detalles' => $correctedDetalles
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } else {
            require '../views/liquidaciones/list.html';
        }
        exit;
    }

    public function createLiquidacion()
    {
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
                $id_usuario = $_SESSION['user_id']; // Asignar el ID del usuario actual

                if (empty($id_caja_chica) || empty($fecha_creacion)) {
                    throw new Exception('Campos obligatorios (id_caja_chica, fecha_creacion) son requeridos y deben ser válidos.');
                }

                if ($this->liquidacionModel->createLiquidacion($id_caja_chica, $fecha_creacion, $fecha_inicio, $fecha_fin, $monto_total, $estado, $id_usuario)) {
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

    public function updateLiquidacion($id)
{
    if (!isset($_SESSION['user_id'])) {
        error_log('Error: No hay session user_id en updateLiquidacion');
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (
        !$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') &&
        !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones') &&
        !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')
    ) {
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

            // Obtener la liquidación actual para verificar el creador
            $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
            if (!$liquidacion) {
                throw new Exception('Liquidación no encontrada');
            }
            $monto_total = $liquidacion['monto_total']; // Mantener el monto_total actual

            // Solo el creador o usuarios con permisos de autorización/revisión pueden actualizar
            if ($usuario['id'] != $liquidacion['id_usuario'] && !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
                throw new Exception('No tienes permiso para editar esta liquidación');
            }

            // Solo usuarios con permisos de autorización pueden cambiar el estado
            if ($usuario['id'] != $liquidacion['id_usuario'] && !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
                $estado = $liquidacion['estado']; // Mantener el estado actual
            } else {
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

            if (empty($id_caja_chica) || empty($fecha_creacion)) {
                throw new Exception('Campos obligatorios (id_caja_chica, fecha_creacion) son requeridos.');
            }

            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $fechaInicioDate = new DateTime($fecha_inicio);
                $fechaFinDate = new DateTime($fecha_fin);
                if ($fechaInicioDate > $fechaFinDate) {
                    throw new Exception('La fecha de inicio no puede ser mayor que la fecha de fin.');
                }
            }

            $fechaActual = new DateTime();
            if (!empty($fecha_creacion) && new DateTime($fecha_creacion) > $fechaActual) {
                throw new Exception('La fecha de creación no puede ser posterior a la fecha actual.');
            }
            if (!empty($fecha_inicio) && new DateTime($fecha_inicio) > $fechaActual) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha actual.');
            }
            // Removed the restriction on fecha_fin
            // if (!empty($fecha_fin) && new DateTime($fecha_fin) > $fechaActual) {
            //     throw new Exception('La fecha de fin no puede ser posterior a la fecha actual.');
            // }

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
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);

    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if ($usuario === false || !$usuarioModel->tienePermiso($usuario, 'delete_liquidaciones')) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para eliminar liquidaciones']);
        exit;
    }

    $liquidacionModel = new Liquidacion();
    $targetLiquidacion = $liquidacionModel->getLiquidacionById($id);
    if ($targetLiquidacion === false) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Liquidación no encontrada']);
        exit;
    }

    $pdo = Database::getInstance()->getPdo();
    try {
        $pdo->beginTransaction();

        // Delete related auditoria records first
        $stmt = $pdo->prepare("DELETE FROM auditoria WHERE id_liquidacion = ?");
        $stmt->execute([$id]);
        error_log("Registros de auditoría eliminados para liquidación ID $id");

        // Delete the liquidation (corrected method name)
        if ($liquidacionModel->deleteLiquidation($id)) {
            $pdo->commit();
            $this->auditoriaModel->createAuditoria(null, null, $_SESSION['user_id'], 'ELIMINAR_LIQUIDACION', "Liquidación eliminada: ID $id");
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Liquidación eliminada']);
        } else {
            $pdo->rollBack();
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar liquidación']);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al eliminar liquidación $id: " . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar liquidación: ' . $e->getMessage()]);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error inesperado al eliminar liquidación $id: " . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error inesperado al eliminar liquidación: ' . $e->getMessage()]);
    }
    exit;
}

    public function deleteDetail($detalleId)
    {
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

        // Allow either autorizar_liquidaciones with SUPERVISOR role OR manage_correcciones permission
        if (
            !($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && strtoupper($usuario['rol']) === 'SUPERVISOR') &&
            !$usuarioModel->tienePermiso($usuario, 'manage_correcciones')
        ) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para eliminar detalles. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar detalles']);
            exit;
        }

        $detalleModel = new DetalleLiquidacion();
        $detalle = $detalleModel->getDetalleById($detalleId);
        if (!$detalle) {
            error_log("Detalle no encontrado para ID: $detalleId");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }

        if (empty($detalle['correccion_comentario'])) {
            error_log("El detalle ID $detalleId no tiene comentario de corrección para eliminar");
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El detalle no tiene comentario de corrección para eliminar']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            // Delete the detail (no state check needed)
            if (!$detalleModel->deleteDetalleLiquidacion($detalleId)) {
                throw new Exception('Error al eliminar el detalle');
            }

            // Create audit entry
            $this->auditoriaModel->createAuditoria($detalle['id_liquidacion'], $detalleId, $_SESSION['user_id'], 'DETALLE_ELIMINADO', 'Detalle con comentario de corrección eliminado');

            $this->pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Detalle eliminado correctamente']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar detalle ID $detalleId: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar el detalle: ' . $e->getMessage()]);
        }
        exit;
    }

    public function addDetailToLiquidacion()
    {
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

        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para modificar liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para modificar liquidaciones']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $detalleId = $input['detalle_id'] ?? null;
        $liquidacionId = $input['liquidacion_id'] ?? null;
        $userId = $input['user_id'] ?? null;

        if (!$detalleId || !$liquidacionId || !$userId) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros requeridos']);
            exit;
        }

        $detalleModel = new DetalleLiquidacion();
        $detalle = $detalleModel->getDetalleById($detalleId);
        if (!$detalle) {
            error_log("Detalle no encontrado para ID: $detalleId");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }

        // Verify that user_id matches the detail's id_usuario
        if ($detalle['id_usuario'] != $userId) {
            error_log("El user_id proporcionado ($userId) no coincide con id_usuario del detalle ($detalle[id_usuario])");
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'El usuario no coincide con el propietario del detalle']);
            exit;
        }

        $liquidacion = $this->liquidacionModel->getLiquidacionById($liquidacionId);
        if (!$liquidacion || $liquidacion['estado'] !== 'EN_PROCESO') {
            error_log("Liquidación no encontrada o no en EN_PROCESO para ID: $liquidacionId");
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Liquidación no encontrada o no está en estado EN_PROCESO']);
            exit;
        }

        // Verify that the liquidation belongs to user_id
        if ($liquidacion['id_usuario'] != $userId) {
            error_log("La liquidación ID $liquidacionId no pertenece al usuario ID $userId");
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'La liquidación no pertenece al usuario especificado']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();
            error_log("Adding detail $detalleId to liquidation $liquidacionId");

            // Update the detail: set new liquidation ID, state to EN_PROCESO, clear correccion_comentario
            $detalleModel->updateLiquidacionId($detalleId, $liquidacionId);
            $detalleModel->updateEstadoWithComment($detalleId, 'EN_PROCESO', null, null);
            error_log("Updated detalle $detalleId with liquidacion_id: $liquidacionId, state: EN_PROCESO, cleared correccion_comentario");

            // Update the liquidation's monto_total
            $currentMonto = $liquidacion['monto_total'] ?? 0;
            $detalleMonto = $detalle['total_factura'] ?? 0;
            $newMonto = $currentMonto + $detalleMonto;
            error_log("Updating monto_total from $currentMonto to $newMonto");
            $this->liquidacionModel->updateMontoTotal($liquidacionId, $newMonto);

            $this->auditoriaModel->createAuditoria($liquidacionId, $detalleId, $_SESSION['user_id'], 'DETALLE_ASIGNADO', 'Detalle asignado a liquidación existente');

            $this->pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Detalle agregado a la liquidación']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error al agregar detalle a liquidación: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al agregar detalle a liquidación: ' . $e->getMessage()]);
        }
        exit;
    }

    public function createWithDetail()
    {
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

        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para crear liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear liquidaciones']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $detalleId = $input['detalle_id'] ?? null;
        $userId = $input['user_id'] ?? null;

        if (!$detalleId || !$userId) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Falta el ID del detalle o del usuario']);
            exit;
        }

        $detalleModel = new DetalleLiquidacion();
        $detalle = $detalleModel->getDetalleById($detalleId);
        if (!$detalle) {
            error_log("Detalle no encontrado para ID: $detalleId");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }

        // Verify that user_id matches the detail's id_usuario
        if ($detalle['id_usuario'] != $userId) {
            error_log("El user_id proporcionado ($userId) no coincide con id_usuario del detalle ($detalle[id_usuario])");
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'El usuario no coincide con el propietario del detalle']);
            exit;
        }

        // Fetch the id_caja_chica from the associated liquidation
        $liquidacion = $this->liquidacionModel->getLiquidacionById($detalle['id_liquidacion']);
        if (!$liquidacion) {
            error_log("Liquidación asociada no encontrada para detalle ID: $detalleId");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación asociada no encontrada']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();
            error_log("Creating new liquidation with detalle_id: $detalleId for user_id: $userId");

            // Create a new liquidation
            $newLiquidacion = [
                'id_caja_chica' => $liquidacion['id_caja_chica'],
                'id_usuario' => $userId,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'fecha_inicio' => date('Y-m-d'),
                'fecha_fin' => date('Y-m-d'),
                'monto_total' => $detalle['total_factura'] ?? 0,
                'estado' => 'EN_PROCESO'
            ];
            error_log("New liquidation data: " . print_r($newLiquidacion, true));

            // Insert the liquidation and get the ID
            $stmt = $this->pdo->prepare("
                INSERT INTO liquidaciones (id_caja_chica, id_usuario, fecha_creacion, fecha_inicio, fecha_fin, monto_total, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $newLiquidacion['id_caja_chica'],
                $newLiquidacion['id_usuario'],
                $newLiquidacion['fecha_creacion'],
                $newLiquidacion['fecha_inicio'],
                $newLiquidacion['fecha_fin'],
                $newLiquidacion['monto_total'],
                $newLiquidacion['estado']
            ]);
            $newLiquidacionId = $this->pdo->lastInsertId();
            error_log("New liquidation ID: $newLiquidacionId");

            // Update the detail: set new liquidation ID, state to EN_PROCESO, clear correccion_comentario
            $detalleModel->updateLiquidacionId($detalleId, $newLiquidacionId);
            $detalleModel->updateEstadoWithComment($detalleId, 'EN_PROCESO', null, null);
            error_log("Updated detalle $detalleId with liquidacion_id: $newLiquidacionId, state: EN_PROCESO, cleared correccion_comentario");

            // Create audit entry
            $this->auditoriaModel->createAuditoria($newLiquidacionId, $detalleId, $_SESSION['user_id'], 'DETALLE_ASIGNADO', 'Detalle asignado a nueva liquidación');

            $this->pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Nueva liquidación creada con el detalle']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error al crear nueva liquidación: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear nueva liquidación: ' . $e->getMessage()]);
        }
        exit;
    }

    public function getEnProcesoLiquidaciones()
    {
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

        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para crear liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear liquidaciones']);
            exit;
        }

        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Falta user_id']);
            exit;
        }

        $liquidaciones = $this->liquidacionModel->getLiquidacionesByUsuario($userId);
        $enProcesoLiquidaciones = array_filter($liquidaciones, function ($liquidacion) {
            return $liquidacion['estado'] === 'EN_PROCESO';
        });

        header('Content-Type: application/json');
        echo json_encode(['liquidaciones' => array_values($enProcesoLiquidaciones)]);
        exit;
    }

    public function getDetalleInfo($id)
    {
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

        if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para ver detalles. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para ver detalles']);
            exit;
        }

        $detalleModel = new DetalleLiquidacion();
        $detalle = $detalleModel->getDetalleById($id);
        if (!$detalle) {
            error_log("Detalle no encontrado para ID: $id");
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'correccion_comentario' => $detalle['correccion_comentario'] ?? '',
            'id_usuario' => $detalle['id_usuario'] ?? null
        ]);
        exit;
    }

    public function getEstado($id)
    {
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

        // Check if user has permission to view liquidations
        if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para ver el estado de liquidaciones. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para ver el estado de liquidaciones']);
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

        header('Content-Type: application/json');
        echo json_encode(['estado' => $liquidacion['estado']]);
        exit;
    }

    public function autorizarDetalle($id)
    {
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

        if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') || strtoupper($usuario['rol']) !== 'SUPERVISOR') {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para autorizar detalles. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para autorizar detalles']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $detalleId = $_POST['detalle_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $motivo = $_POST['motivo'] ?? '';
        $idUsuario = $_POST['id_usuario'] ?? null; // For send_to_correction

        if (!$detalleId || !$action) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros requeridos']);
            exit;
        }

        $detalleModel = new DetalleLiquidacion();
        $detalle = $detalleModel->getDetalleById($detalleId);
        if (!$detalle) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Detalle no encontrado']);
            exit;
        }

        // Allow EN_CORRECCION state
        if (!in_array($detalle['estado'], ['PENDIENTE_AUTORIZACION', 'EN_CORRECCION'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El detalle no está en estado PENDIENTE_AUTORIZACION o EN_CORRECCION']);
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

            $nuevoEstado = '';
            $auditoriaAccion = '';
            $message = '';

            switch (strtolower($action)) {
                case 'autorizar':
                    $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                    $auditoriaAccion = 'AUTORIZADO_POR_SUPERVISOR';
                    $message = 'Detalle autorizado por supervisor';
                    break;
                case 'rechazar':
                    $nuevoEstado = 'RECHAZADO_AUTORIZACION';
                    $auditoriaAccion = 'RECHAZADO_POR_SUPERVISOR';
                    $message = 'Detalle rechazado por supervisor';
                    break;
                case 'descartar':
                    $nuevoEstado = 'DESCARTADO';
                    $auditoriaAccion = 'DESCARTADO';
                    $message = 'Detalle descartado';
                    break;
                case 'send_to_correction':
                    if (!$idUsuario) {
                        throw new Exception('Falta id_usuario para enviar a corrección');
                    }
                    if (!$motivo) {
                        throw new Exception('Falta motivo para enviar a corrección');
                    }
                    $nuevoEstado = 'EN_CORRECCION';
                    $auditoriaAccion = 'ENVIADO_A_CORRECCION';
                    $message = 'Detalle enviado a corrección';
                    $detalleModel->updateEstadoWithComment($detalleId, $nuevoEstado, null, $motivo);
                    break;
                default:
                    throw new Exception('Acción no válida');
            }

            if ($action !== 'send_to_correction') {
                $detalleModel->updateEstado($detalleId, $nuevoEstado);
            }

            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);

            // Check the state of all details in the liquidation
            $allDetalles = $detalleModel->getDetallesByLiquidacionId($id);
            $allInSameState = true;
            $hasNonCorrectionDetails = false;
            $targetState = $nuevoEstado;

            foreach ($allDetalles as $d) {
                if ($d['estado'] !== $targetState && $d['estado'] !== 'EN_CORRECCION') {
                    $allInSameState = false;
                }
                if ($d['estado'] !== 'EN_CORRECCION') {
                    $hasNonCorrectionDetails = true;
                }
            }

            // Update liquidation state only if all non-corrected details are in the same state
            if ($allInSameState && $hasNonCorrectionDetails && $action !== 'send_to_correction') {
                $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            }

            $this->pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['message' => $message]);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error al procesar el detalle: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al procesar el detalle: ' . $e->getMessage()]);
        }
        exit;
    }

    public function autorizar($id)
{
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

    $isFromCorrection = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_correccion';
    if (!$isFromCorrection && $liquidacion['estado'] !== $expectedEstado) {
        error_log("Estado de la liquidación no válido. Esperado: $expectedEstado, Actual: " . $liquidacion['estado']);
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => "Solo se pueden autorizar liquidaciones en estado $expectedEstado"]);
        exit;
    }

    $detalleModel = new DetalleLiquidacion();
    $detalles = $detalleModel->getDetallesByLiquidacionId($id);

    if (empty($detalles)) {
        error_log("No se encontraron detalles para la liquidación ID: $id");
    } else {
        $states = array_column($detalles, 'estado');
        error_log("Estados de los detalles para la liquidación ID $id: " . json_encode($states));
    }

    $cuentaContableModel = new CuentaContable();
    foreach ($detalles as &$detalle) {
        $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
        $detalle['cuenta_contable_nombre'] = $cuentaContable['nombre'] ?? 'N/A';
    }
    unset($detalle);

    $cajaChicaModel = new CajaChica();
    $cajaChica = $cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
    $nombre_caja_chica = $cajaChica['nombre'] ?? 'N/A';

    $centroCostoModel = new CentroCosto();
    $centroCostoCajaChica = $centroCostoModel->getCentroCostoById($cajaChica['id_centro_costo'] ?? null);
    $centro_costo_caja_chica_nombre = $centroCostoCajaChica['nombre'] ?? 'N/A';

    $monto_total = array_sum(array_column($detalles, 'total_factura'));

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
                $contadorId = isset($_POST['id_contador']) ? intval($_POST['id_contador']) : null;
                foreach ($detalleIds as $detalleId) {
                    $comment = $correccionComentarios[$detalleId] ?? '';
                    if (empty($comment)) {
                        throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                    }
                    if ($rol === 'SUPERVISOR') {
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, $_SESSION['user_id']);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por supervisor ID {$_SESSION['user_id']}");
                    } elseif ($rol === 'CONTABILIDAD') {
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, null, $contadorId);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por contador ID " . ($contadorId ?? 'N/A'));
                    }
                    $numCorrections++;
                }

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
        $idContador = isset($_POST['id_contador']) ? intval($_POST['id_contador']) : null;
        $detallesSeleccionados = $_POST['detalles'] ?? [];
        $detallesNoSeleccionados = json_decode($_POST['unselected_detalles'] ?? '[]', true);
        $correccionComentarios = json_decode($_POST['correccion_comentarios'] ?? '{}', true);

        error_log("Rol del usuario: $rol");
        error_log("Acción recibida en autorizar: " . $accion);
        error_log("Estado actual de la liquidación: " . $liquidacion['estado']);
        error_log("Detalles seleccionados: " . json_encode($detallesSeleccionados));
        error_log("Detalles no seleccionados: " . json_encode($detallesNoSeleccionados));
        error_log("ID Contador seleccionado: " . ($idContador ?? 'N/A'));

        $allowedAcciones = ['APROBADO', 'RECHAZADO', 'DESCARTADO'];
        if (!in_array($accion, $allowedAcciones)) {
            error_log("Acción no válida: " . $accion . ". Acciones permitidas: " . implode(', ', $allowedAcciones));
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
            exit;
        }

        if ($rol === 'SUPERVISOR' && $accion === 'APROBADO' && empty($idContador)) {
            error_log("ID de contador requerido para autorización por SUPERVISOR");
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Debes seleccionar un contador para autorizar la liquidación']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $nuevoEstado = '';
            $auditoriaAccion = '';
            $message = '';

            if ($accion === 'APROBADO') {
                if ($rol === 'SUPERVISOR') {
                    $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                    $auditoriaAccion = 'AUTORIZADO_POR_SUPERVISOR';
                    $message = 'Liquidación autorizada por supervisor y asignada a contador';
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

            $detailsToCorrect = [];
            foreach ($detalles as $detalle) {
                $detalleId = $detalle['id'];
                if (in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                    $detailsToCorrect[] = $detalleId;
                    $comment = $correccionComentarios[$detalleId] ?? '';
                    if (empty($comment)) {
                        throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                    }
                    if ($rol === 'SUPERVISOR') {
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, $_SESSION['user_id']);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por supervisor ID {$_SESSION['user_id']}");
                    } elseif ($rol === 'CONTABILIDAD') {
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, null, $idContador ?? $liquidacion['id_contador']);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por contador ID " . ($idContador ?? $liquidacion['id_contador']));
                    }
                }
            }

            if ($isFromCorrection && !empty($detallesSeleccionados)) {
                foreach ($detalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesSeleccionados) && $detalle['estado'] === 'EN_CORRECCION') {
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                    }
                }
            } else {
                $anySelected = !empty($detallesSeleccionados);
                foreach ($detalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                    } elseif (!in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                    }
                }
            }

            $allDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
            $allInCorrection = true;
            $hasApprovedDetails = false;
            $hasNonCorrectionDetails = false;

            foreach ($allDetalles as $detalle) {
                if ($detalle['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD') {
                    $hasApprovedDetails = true;
                }
                if ($detalle['estado'] !== 'EN_CORRECCION') {
                    $allInCorrection = false;
                    $hasNonCorrectionDetails = true;
                }
            }

            if ($allInCorrection && !$anySelected) {
                $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
            } elseif ($rol === 'SUPERVISOR' && $accion === 'APROBADO' && $hasApprovedDetails) {
                $this->liquidacionModel->updateEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD', null, $idContador);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo . " - Asignada a contador ID $idContador");
            } elseif ($rol === 'CONTABILIDAD' && $accion === 'APROBADO' && $hasNonCorrectionDetails) {
                $this->liquidacionModel->updateEstado($id, 'FINALIZADO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            } else {
                $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            }

            $isExported = $this->liquidacionModel->isExported($id);
            if ($isExported && $rol === 'CONTABILIDAD' && $accion === 'APROBADO') {
                $message .= "\nLa liquidación ya fue exportada. Se ha reenviado a corrección.";
                $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'REENVIADO_A_CORRECCION', 'Liquidación reenviada a corrección por exportación previa');
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

    $data = [
        'id' => $liquidacion['id'],
        'id_caja_chica' => $liquidacion['id_caja_chica'],
        'nombre_caja_chica' => $nombre_caja_chica,
        'centro_costo_caja_chica_id' => $cajaChica['id_centro_costo'] ?? null,
        'centro_costo_caja_chica_nombre' => $centro_costo_caja_chica_nombre,
        'fecha_creacion' => $liquidacion['fecha_creacion'],
        'monto_total' => $monto_total,
        'estado' => $liquidacion['estado'],
    ];

    $contadores = [];
    if ($rol === 'SUPERVISOR') {
        $contadores = $usuarioModel->getUsuariosByRol('CONTABILIDAD');
    }

    $mode = 'autorizar';
    $usuario_data = ['rol' => $rol];
    require '../views/liquidaciones/autorizar_individual.html';
    exit;
}

public function revisar($id)
{
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

    // Permitir procesamiento si viene de updateCorreccion y hay facturas corregidas
    $isFromCorrection = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_correccion';
    if (!$isFromCorrection && $liquidacion['estado'] !== 'PENDIENTE_REVISION_CONTABILIDAD') {
        error_log("Estado de la liquidación no válido. Esperado: PENDIENTE_REVISION_CONTABILIDAD, Actual: " . $liquidacion['estado']);
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Solo se pueden revisar liquidaciones en estado PENDIENTE_REVISION_CONTABILIDAD']);
        exit;
    }

    $detalleModel = new DetalleLiquidacion();
    // Fetch all details for POST logic (approval/rejection/correction)
    $allDetalles = $detalleModel->getDetallesByLiquidacionId($id);

    // For the view, only fetch details that are in PENDIENTE_REVISION_CONTABILIDAD (authorized by SUPERVISOR)
    $detalles = $detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD');

    // Enrich detalles with cuenta_contable_nombre
    $cuentaContableModel = new CuentaContable();
    foreach ($detalles as &$detalle) {
        $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
        $detalle['cuenta_contable_nombre'] = $cuentaContable['nombre'] ?? 'N/A';
    }
    unset($detalle);

    $cajaChicaModel = new CajaChica();
    $cajaChica = $cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
    $nombre_caja_chica = $cajaChica['nombre'] ?? 'N/A';

    $centroCostoModel = new CentroCosto();
    $centroCostoCajaChica = $centroCostoModel->getCentroCostoById($cajaChica['id_centro_costo'] ?? null);
    $centro_costo_caja_chica_nombre = $centroCostoCajaChica['nombre'] ?? 'N/A';

    $monto_total = array_sum(array_column($detalles, 'total_factura'));

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
            foreach ($allDetalles as $detalle) {
                $detalleId = $detalle['id'];
                if (in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD') {
                    $detailsToCorrect[] = $detalleId;
                    $comment = $correccionComentarios[$detalleId] ?? '';
                    if (empty($comment)) {
                        throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                    }
                    $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment);
                    $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment");
                }
            }

            // Process selected details from updateCorreccion
            $anySelected = !empty($detallesSeleccionados);
            $nonCorrectedDetailsProcessed = false;
            if ($isFromCorrection && $anySelected) {
                foreach ($allDetalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesSeleccionados) && $detalle['estado'] === 'EN_CORRECCION') {
                        $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                        $nonCorrectedDetailsProcessed = true;
                    }
                }
            } else {
                // Update only selected details that are in PENDIENTE_REVISION_CONTABILIDAD
                foreach ($allDetalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if ($detalle['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD') {
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                            $nonCorrectedDetailsProcessed = true;
                        } elseif (!in_array($detalleId, $detallesNoSeleccionados)) {
                            // Update unselected details that were not sent to correction
                            $this->detalleModel->updateEstado($detalleId, $nuevoEstado);
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                            $nonCorrectedDetailsProcessed = true;
                        }
                    }
                }
            }

            // Update liquidation state based on the state of details
            $updatedDetalles = $this->detalleModel->getDetallesByLiquidacionId($id);
            $allInCorrection = true;
            $hasNonCorrectionDetails = false;

            foreach ($updatedDetalles as $detalle) {
                if ($detalle['estado'] !== 'EN_CORRECTION') {
                    $allInCorrection = false;
                    $hasNonCorrectionDetails = true;
                }
            }

            if ($allInCorrection && !$anySelected) {
                $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
            } elseif ($anySelected && $nonCorrectedDetailsProcessed) {
                $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            } elseif ($hasNonCorrectionDetails) {
                $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            } else {
                $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
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

    $data = [
        'id' => $liquidacion['id'],
        'id_caja_chica' => $liquidacion['id_caja_chica'],
        'nombre_caja_chica' => $nombre_caja_chica,
        'centro_costo_caja_chica_id' => $cajaChica['id_centro_costo'] ?? null,
        'centro_costo_caja_chica_nombre' => $centro_costo_caja_chica_nombre,
        'fecha_creacion' => $liquidacion['fecha_creacion'],
        'monto_total' => $monto_total,
        'estado' => $liquidacion['estado'],
    ];

    $mode = 'revisar';
    require '../views/liquidaciones/autorizar_individual.html';
    exit;
}

    public function exportar($id)
{
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

    // Filter out details in EN_CORRECTION
    $detallesToExport = array_filter($detalles, function ($detalle) {
        return $detalle['estado'] !== 'EN_CORRECTION';
    });

    if (empty($detallesToExport)) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'No hay detalles para exportar que no estén en corrección']);
        exit;
    }

    // Export to SAP
    try {
        $conn = $this->CONEXION_HANA('GT_AGROCENTRO_2016');
        $sociedad = 'GT_AGROCENTRO_2016'; // Adjust based on your SAP company code

        foreach ($detallesToExport as $detalle) {
            // Map data to SAP-compatible format
            $liquidacionData = [
                'Liquidacion_ID' => $liquidacion['id'],
                'Caja_Chica' => $liquidacion['nombre_caja_chica'],
                'Fecha_Creacion' => $liquidacion['fecha_creacion'],
                'Monto_Total' => floatval($liquidacion['monto_total']),
                'Estado_Liquidacion' => $liquidacion['estado'],
                'Exportado' => $liquidacion['exportado'],
                'Detalle_ID' => $detalle['id'],
                'Numero_Factura' => $detalle['no_factura'],
                'Proveedor' => $detalle['nombre_proveedor'],
                'Fecha_Detalle' => $detalle['fecha'],
                'Bien_Servicio' => $detalle['bien_servicio'],
                'Tipo_Gasto' => $detalle['t_gasto'],
                'Precio_Unitario' => floatval($detalle['p_unitario']),
                'Total_Factura' => floatval($detalle['total_factura']),
                'Estado_Detalle' => $detalle['estado']
            ];

            // Determine the appropriate account based on Tipo_Gasto
            $cuentaMap = [
                'Combustible' => 1,
                'Alimentación' => 2,
                'Hospedaje' => 3,
                'Transporte' => 4,
                'Otros' => 5 // Default case, adjust as needed
            ];
            $cuentaKey = isset($cuentaMap[$detalle['t_gasto']]) ? $cuentaMap[$detalle['t_gasto']] : 5;
            $cuentas = $this->ctrObtenerCuentas($sociedad, $cuentaKey);
            $cuentaData = explode('|', trim($cuentas, '|'));
            $accountCode = $cuentaData[0] ?? 'DEFAULT_ACCOUNT'; // Fallback account if no match

            // Prepare SQL for SAP insertion (example staging table)
            $sql = "INSERT INTO STAGING_LIQUIDACIONES (
                Liquidacion_ID, Caja_Chica, Fecha_Creacion, Monto_Total, Estado_Liquidacion, Exportado,
                Detalle_ID, Numero_Factura, Proveedor, Fecha_Detalle, Bien_Servicio, Tipo_Gasto,
                Precio_Unitario, Total_Factura, Estado_Detalle, Cuenta_Contable
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $liquidacionData['Liquidacion_ID'],
                $liquidacionData['Caja_Chica'],
                $liquidacionData['Fecha_Creacion'],
                $liquidacionData['Monto_Total'],
                $liquidacionData['Estado_Liquidacion'],
                $liquidacionData['Exportado'],
                $liquidacionData['Detalle_ID'],
                $liquidacionData['Numero_Factura'],
                $liquidacionData['Proveedor'],
                $liquidacionData['Fecha_Detalle'],
                $liquidacionData['Bien_Servicio'],
                $liquidacionData['Tipo_Gasto'],
                $liquidacionData['Precio_Unitario'],
                $liquidacionData['Total_Factura'],
                $liquidacionData['Estado_Detalle'],
                $accountCode
            ];

            $stmt = odbc_prepare($conn, $sql);
            if (!$stmt) {
                error_log("Error al preparar la consulta SQL: " . odbc_errormsg($conn));
                throw new Exception("Error al preparar la consulta para SAP.");
            }

            $success = odbc_execute($stmt, $params);
            if (!$success) {
                error_log("Error al ejecutar la consulta SQL: " . odbc_errormsg($conn));
                throw new Exception("Error al insertar datos en SAP.");
            }
        }

        odbc_close($conn);
    } catch (Exception $e) {
        error_log("Error al exportar a SAP: " . $e->getMessage());
        // Continue with CSV export even if SAP fails, log the error
    }

    // Generate CSV file as backup
    $filename = "liquidacion_{$id}_" . date('Ymd_His') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputcsv($output, [
        'Liquidacion_ID',
        'Caja_Chica',
        'Fecha_Creacion',
        'Monto_Total',
        'Estado_Liquidacion',
        'Exportado',
        'Detalle_ID',
        'Numero_Factura',
        'Proveedor',
        'Fecha_Detalle',
        'Bien_Servicio',
        'Tipo_Gasto',
        'Precio_Unitario',
        'Total_Factura',
        'Estado_Detalle'
    ]);

    foreach ($detallesToExport as $detalle) {
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

    // Mark as exported and log audit
    if ($forceExport) {
        $liquidacionModel->markAsExported($id);
        $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO', 'Liquidación reexportada a SAP como ' . $filename);
    } else {
        $liquidacionModel->markAsExported($id);
        $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO', 'Liquidación exportada a SAP como ' . $filename);
    }

    exit;
}

    public function manageFacturas($id)
    {
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

        if (isset($_GET['subaction']) && $_GET['subaction'] === 'getCuentasContables') {
            $this->getCuentasContables($_GET['id_centro_costo']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $action = $_POST['action'] ?? '';
            try {
                $this->pdo->beginTransaction();

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
                    $id_centro_costo = $_POST['id_centro_costo'] ?? null;
                    $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                    $cantidad = $_POST['cantidad'] ?? null;
                    $serie = $_POST['serie'] ?? null;
                    $estado = 'EN_PROCESO';

                    if ($tipo_documento === 'RECIBO') {
                        $nit_proveedor = null;
                    } else {
                        $dpi = null;
                    }

                    if (empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                        throw new Exception('Los campos obligatorios (tipo_documento, no_factura, nombre_proveedor, fecha, t_gasto, subtotal, total_factura) deben ser válidos.');
                    }

                    if (empty($id_centro_costo)) {
                        throw new Exception('El Centro de Costo es obligatorio.');
                    }

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

                    $iva = $iva ?? 0;
                    $idp = $idp ?? 0;
                    $inguat = $inguat ?? 0;

                    $id_usuario = $liquidacion['id_usuario']; // Fetch id_usuario from liquidacion

                    $detalleModel = new DetalleLiquidacion();
                    if ($detalleModel->createDetalleLiquidacion($id, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $subtotal, $total_factura, $estado, $id_centro_costo, $cantidad, $serie, $rutas_json, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible, $id_usuario)) {
                        $lastInsertId = $this->pdo->lastInsertId();
                        $this->auditoriaModel->createAuditoria($id, $lastInsertId, $_SESSION['user_id'], 'CREAR_DETALLE', "Factura creada: $no_factura para usuario ID $id_usuario");

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
                    // Update action remains unchanged
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
                    $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                    $cantidad = $_POST['cantidad'] ?? null;
                    $serie = $_POST['serie'] ?? null;

                    if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                        throw new Exception('Los campos obligatorios deben ser válidos.');
                    }

                    if (empty($id_centro_costo)) {
                        throw new Exception('El Centro de Costo es obligatorio.');
                    }

                    if (empty($id_cuenta_contable)) {
                        throw new Exception('La Cuenta Contable es obligatoria.');
                    }

                    $cuentaContableModel = new CuentaContable();
                    $cuentaContable = $cuentaContableModel->getCuentaContableById($id_cuenta_contable);
                    if (!$cuentaContable) {
                        throw new Exception('La Cuenta Contable seleccionada no es válida.');
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

        // View rendering logic remains unchanged
        $centroCostoModel = new CentroCosto();
        $centroCostoLiquidacion = $centroCostoModel->getCentroCostoById($liquidacion['id_centros_de_costos']);
        $nombreCentroCostoLiquidacion = $centroCostoLiquidacion ? $centroCostoLiquidacion['nombre'] : 'N/A';

        $centroCostoCajaChica = $centroCostoModel->getCentroCostoById($cajaChica['id_centro_costo']);
        $nombreCentroCostoCajaChica = $centroCostoCajaChica ? $centroCostoCajaChica['nombre'] : 'N/A';

        $detalles = $this->detalleModel->getDetallesByLiquidacionId($id);
        $tiposDocumentos = $this->tipoDocumentoModel->getAllTiposDocumentos();
        $tiposGastos = $this->tipoGastoModel->getAllTiposGastos();
        $centrosCostos = $this->centroCostoModel->getAllCentrosCostos();

        $cuentaContableModel = new CuentaContable();
        foreach ($detalles as &$detalle) {
            if (isset($detalle['id_cuenta_contable'])) {
                $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
                $detalle['cuenta_contable_nombre'] = $cuentaContable ? $cuentaContable['nombre'] : 'N/A';
            } else {
                $detalle['cuenta_contable_nombre'] = 'N/A';
            }
        }
        unset($detalle);

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

    public function listCorrecciones()
{
    if (!isset($_SESSION['user_id'])) {
        error_log('Error: No hay sesión user_id en listCorrecciones para el usuario');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuarioModel->tienePermiso($usuario, 'manage_correcciones')) {
        error_log('Error: El usuario ID ' . $_SESSION['user_id'] . ' no tiene permiso para listar correcciones');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para listar correcciones.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $liquidaciones = $this->liquidacionModel->getLiquidacionesWithCorrections();
    error_log('Liquidaciones en corrección obtenidas: ' . count($liquidaciones) . ' registros para el usuario ID ' . $_SESSION['user_id']);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array_values($liquidaciones), JSON_UNESCAPED_UNICODE);
    } else {
        require '../views/liquidaciones/correccion_list.php';
    }
    exit;
}

    public function updateCorreccion($id)
    {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateCorreccion');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario || !$usuarioModel->tienePermiso($usuario, 'manage_correcciones')) {
            error_log('Error: No tienes permiso para actualizar correcciones');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar correcciones']);
            exit;
        }

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

                // Handle file uploads
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
                            if (!move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $filePath)) {
                                throw new Exception('Error al subir el archivo: ' . $name);
                            }
                            $rutas_archivos[] = 'uploads/' . basename($filePath);
                        } elseif ($_FILES['archivos']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                            throw new Exception('Error al subir el archivo: ' . $name);
                        }
                    }
                }
                $rutas_json = json_encode($rutas_archivos);

                if ($action === 'update') {
                    // Extract and validate form data
                    $detalle_id = $_POST['detalle_id'] ?? '';
                    $tipo_documento = $_POST['tipo_documento'] ?? '';
                    $no_factura = $_POST['no_factura'] ?? '';
                    $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                    $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                    $dpi = $_POST['dpi'] ?? null;
                    $fecha = $_POST['fecha'] ?? '';
                    $t_gasto = $_POST['t_gasto'] ?? '';
                    $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                    $subtotal = floatval($_POST['subtotal'] ?? 0);
                    $total_factura = floatval($_POST['total_factura'] ?? 0);
                    $iva = isset($_POST['iva']) && $_POST['iva'] !== '' ? floatval($_POST['iva']) : 0;
                    $idp = isset($_POST['idp']) && $_POST['idp'] !== '' ? floatval($_POST['idp']) : 0;
                    $inguat = isset($_POST['inguat']) && $_POST['inguat'] !== '' ? floatval($_POST['inguat']) : 0;
                    $id_centro_costo = $_POST['id_centro_costo'] ?? null;
                    $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                    $cantidad = $_POST['cantidad'] ?? null;
                    $serie = $_POST['serie'] ?? null;
                    $correccion_comentario = $_POST['correccion_comentario'] ?? '';

                    // Validate required fields
                    if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                        throw new Exception('Los campos obligatorios deben ser válidos.');
                    }

                    if (empty($id_centro_costo) || empty($id_cuenta_contable)) {
                        throw new Exception('El Centro de Costo y la Cuenta Contable son obligatorios.');
                    }

                    $cuentaContableModel = new CuentaContable();
                    $cuentaContable = $cuentaContableModel->getCuentaContableById($id_cuenta_contable);
                    if (!$cuentaContable) {
                        throw new Exception('La Cuenta Contable seleccionada no es válida.');
                    }

                    // Document-specific validations
                    if ($tipo_documento === 'COMPROBANTE' && (empty($cantidad) || empty($serie))) {
                        throw new Exception('Cantidad y Serie son obligatorios para el tipo de documento Comprobante.');
                    }
                    if ($tipo_documento === 'RECIBO' && empty($dpi)) {
                        throw new Exception('DPI es obligatorio para el tipo de documento Recibo.');
                    }
                    if (in_array($tipo_documento, ['FACTURA', 'COMPROBANTE']) && empty($nit_proveedor)) {
                        throw new Exception('NIT es obligatorio para el tipo de documento Factura o Comprobante.');
                    }
                    if ($tipo_documento === 'FACTURA') {
                        if ($t_gasto === 'Combustible' && empty($tipo_combustible)) {
                            throw new Exception('El tipo de combustible es obligatorio para el tipo de gasto Combustible.');
                        }
                        if (in_array($t_gasto, ['Combustible', 'Gasto Operativo']) && (empty($cantidad) || $cantidad <= 0)) {
                            throw new Exception('La cantidad de galones es obligatoria y debe ser mayor a 0 para el tipo de gasto ' . $t_gasto . '.');
                        }
                    }

                    // Adjust tipo_combustible based on tipo_gasto
                    if ($t_gasto === 'Gasto Operativo') {
                        $tipo_combustible = 'Gasolina';
                    } elseif ($t_gasto !== 'Combustible') {
                        $tipo_combustible = null;
                    }

                    $detalle = $this->detalleModel->getDetalleById($detalle_id);
                    if (!$detalle || $detalle['estado'] !== 'EN_CORRECCION') {
                        throw new Exception('El detalle no está en estado EN_CORRECCION o no existe.');
                    }

                    // Merge existing and new file paths
                    $existing_rutas = json_decode($detalle['rutas_archivos'], true) ?? [];
                    $rutas_archivos = array_merge($existing_rutas, $rutas_archivos);
                    $rutas_json = json_encode($rutas_archivos);

                    // Adjust nit_proveedor and dpi based on tipo_documento
                    if ($tipo_documento === 'RECIBO') {
                        $nit_proveedor = null;
                    } else {
                        $dpi = null;
                    }

                    // Validate date range
                    $fechaFactura = new DateTime($fecha);
                    $fechaInicio = new DateTime($liquidacion['fecha_inicio']);
                    $fechaFin = new DateTime($liquidacion['fecha_fin']);
                    if ($fechaFactura < $fechaInicio || $fechaFactura > $fechaFin) {
                        throw new Exception("La fecha de la factura debe estar entre {$liquidacion['fecha_inicio']} y {$liquidacion['fecha_fin']}.");
                    }

                    // Update detalle
                    if (
                        !$this->detalleModel->updateDetalleLiquidacion(
                            $detalle_id,
                            $tipo_documento,
                            $no_factura,
                            $nombre_proveedor,
                            $nit_proveedor,
                            $dpi,
                            $fecha,
                            $t_gasto,
                            $subtotal,
                            $total_factura,
                            $id_centro_costo,
                            $cantidad,
                            $serie,
                            $rutas_json,
                            $iva,
                            $idp,
                            $inguat,
                            $id_cuenta_contable,
                            $tipo_combustible
                        )
                    ) {
                        throw new Exception('Error al actualizar la factura.');
                    }

                    $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE_EN_CORRECCION', "Factura actualizada en corrección: $no_factura");

                    // Update total amount for liquidation
                    $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
                    $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                    $this->liquidacionModel->updateMontoTotal($id, $monto_total);

                    $response = [
                        'message' => 'Factura actualizada correctamente.',
                        'detalle_id' => $detalle_id,
                        'rutas_archivos' => $rutas_archivos,
                        'monto_total' => $monto_total,
                        'cuenta_contable_nombre' => $cuentaContable['nombre'] ?? 'N/A'
                    ];

                } else {
                    throw new Exception('Acción no válida.');
                }

                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error en updateCorreccion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        }

        // Load correction view
        $detalles = $this->detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'EN_CORRECCION');
        if (empty($detalles)) {
            header('Location: index.php?controller=liquidacion&action=list&mode=correccion');
            exit;
        }

        // Collect all unique originating roles
        $originating_roles = [];
        foreach ($detalles as $detalle) {
            $role = $detalle['original_role'] ?? 'CONTABILIDAD';
            if (!in_array($role, $originating_roles)) {
                $originating_roles[] = $role;
            }
        }

        $centroCostoModel = new CentroCosto();
        $cuentaContableModel = new CuentaContable();
        foreach ($detalles as &$detalle) {
            $centroCosto = $centroCostoModel->getCentroCostoById($detalle['id_centro_costo']);
            $detalle['nombre_centro_costo'] = $centroCosto['nombre'] ?? 'N/A';

            $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
            $detalle['cuenta_contable_nombre'] = $cuentaContable['nombre'] ?? 'N/A';

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
            $detalle['subtotal'] = $detalle['p_unitario'] ?? 0;
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
            $select_tipos_documentos .= "<option value='{$tipo['name']}'>{$tipo['name']}</option>";
        }

        $tipoGastoModel = new TipoGasto();
        $tiposGastos = $tipoGastoModel->getAllTiposGastos();
        $select_tipos_gastos = '';
        foreach ($tiposGastos as $tipo) {
            $select_tipos_gastos .= "<option value='{$tipo['name']}'>{$tipo['name']}</option>";
        }

        $centroCostoModel = new CentroCosto();
        $centrosCostos = $centroCostoModel->getAllCentrosCostos();
        $select_centros_costos = '';
        foreach ($centrosCostos as $centro) {
            $select_centros_costos .= "<option value='{$centro['id']}'>{$centro['nombre']}</option>";
        }

        $data = $liquidacion;
        $data['nombre_caja_chica'] = $cajaChica['name'];
        $data['suggested_centro_costo_id'] = $cajaChica['id_centro_costo'] ?? null;
        $data['originating_roles'] = $originating_roles;

        require '../views/liquidaciones/correccion.html';
        exit;
    }

    public function submitCorreccion($id)
{
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

    $input = json_decode(file_get_contents('php://input'), true);
    $submitted_role = $input['originating_role'] ?? null;

    if (!in_array($submitted_role, ['SUPERVISOR', 'CONTABILIDAD'])) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Rol de origen no válido']);
        exit;
    }

    try {
        $this->pdo->beginTransaction();

        $detalles = $this->detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'EN_CORRECCION');
        if (empty($detalles)) {
            throw new Exception('No hay detalles en estado EN_CORRECCION para procesar.');
        }

        foreach ($detalles as $detalle) {
            $original_role = $detalle['original_role'] ?? 'CONTABILIDAD';
            if ($original_role !== $submitted_role) {
                continue;
            }

            $nuevoEstado = $original_role === 'SUPERVISOR' ? 'PENDIENTE_AUTORIZACION' : 'PENDIENTE_REVISION_CONTABILIDAD';
            $this->detalleModel->updateEstado($detalle['id'], $nuevoEstado);
            if ($original_role === 'SUPERVISOR') {
                $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'CORRECTION_ENVIADA', "Detalle corregido y enviado a $nuevoEstado para supervisor ID {$detalle['id_supervisor_correccion']}");
            } else {
                $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'CORRECTION_ENVIADA', "Detalle corregido y enviado a $nuevoEstado para contador ID {$detalle['id_contador_correccion']}");
            }
        }

        $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'CORRECCIONES_ENVIADAS', "Correcciones enviadas a $submitted_role, liquidación sin cambio de estado");

        $this->pdo->commit();
        header('Content-Type: application/json');
        echo json_encode(['message' => "Correcciones enviadas correctamente a $submitted_role"]);
    } catch (Exception $e) {
        $this->pdo->rollBack();
        error_log('Error al enviar correcciones: ' . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al enviar correcciones: ' . $e->getMessage()]);
    }
    exit;
}

    public function getLiquidacionState($id)
    {
        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['estado' => $liquidacion['estado']]);
        exit;
    }

    public function deleteFacturaCorreccion($id, $detalle_id)
    {
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
            echo json_encode(['error' => 'No tienes permiso para eliminar facturas']);
            exit;
        }

        $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada']);
            exit;
        }

        $detalle = $this->detalleModel->getDetalleById($detalle_id);
        if (!$detalle || $detalle['estado'] !== 'EN_CORRECCION') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El detalle no está en estado EN_CORRECCION o no existe']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            // Delete the invoice
            $this->detalleModel->deleteDetalleLiquidacion($detalle_id);
            $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'FACTURA_ELIMINADA_EN_CORRECCION', "Factura eliminada mientras estaba en corrección");

            // Update the liquidation's total amount
            $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id);
            $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
            $this->liquidacionModel->updateMontoTotal($id, $monto_total);

            $this->pdo->commit();
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Factura eliminada correctamente']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error al eliminar factura: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar factura: ' . $e->getMessage()]);
        }
        exit;
    }

    public function getCuentasByCentroCosto($id_centro_costo)
    {
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

    public function getImpuestosByTipoGasto($name)
    {
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

    public function finalizar($id)
{
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

    // Check if the liquidation has any details (facturas)
    $detalleModel = new DetalleLiquidacion();
    $detalles = $detalleModel->getDetallesByLiquidacionId($id);
    if (empty($detalles)) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'No se puede finalizar una liquidación vacía.']);
        exit;
    }

    // Get the selected supervisor ID from POST data
    $supervisorId = $_POST['supervisor_id'] ?? null;
    if (!$supervisorId) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Debe seleccionar un supervisor para asignar la liquidación.']);
        exit;
    }

    // Validate that the selected user is a supervisor
    $supervisor = $usuarioModel->getUsuarioById($supervisorId);
    if (!$supervisor || $supervisor['rol'] !== 'SUPERVISOR') {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'El usuario seleccionado no es un supervisor válido.']);
        exit;
    }

    try {
        // Update the liquidation state and assign it to the selected supervisor
        $liquidacionModel->updateEstado($id, 'PENDIENTE_AUTORIZACION', $supervisorId);

        // Log the action in the audit trail, including the supervisor's details
        $auditMessage = sprintf(
            'Liquidación finalizada por encargado y asignada al supervisor %s (%s)',
            $supervisor['nombre'],
            $supervisor['email']
        );
        $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'FINALIZADO', $auditMessage);

        header('Content-Type: application/json');
        echo json_encode(['message' => 'Liquidación finalizada y asignada al supervisor correctamente']);
    } catch (Exception $e) {
        error_log('Error al finalizar liquidación: ' . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al finalizar la liquidación: ' . $e->getMessage()]);
    }
    exit;
}

    public function ver($id)
    {
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
        if (
            !$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') &&
            !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') &&
            !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')
        ) {
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

        // Enrich detalles with cuenta_contable_nombre
        $cuentaContableModel = new CuentaContable();
        foreach ($detalles as &$detalle) {
            $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
            $detalle['cuenta_contable_nombre'] = $cuentaContable['nombre'] ?? 'N/A';
        }
        unset($detalle);

        $cajaChicaModel = new CajaChica();
        $cajaChica = $cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
        $nombre_caja_chica = $cajaChica['nombre'] ?? 'N/A';

        $centroCostoModel = new CentroCosto();
        $centroCostoCajaChica = $centroCostoModel->getCentroCostoById($cajaChica['id_centro_costo'] ?? null);
        $centro_costo_caja_chica_nombre = $centroCostoCajaChica['nombre'] ?? 'N/A';

        $monto_total = array_sum(array_column($detalles, 'total_factura'));

        $data = [
            'id' => $liquidacion['id'],
            'id_caja_chica' => $liquidacion['id_caja_chica'],
            'nombre_caja_chica' => $nombre_caja_chica,
            'centro_costo_caja_chica_id' => $cajaChica['id_centro_costo'] ?? null,
            'centro_costo_caja_chica_nombre' => $centro_costo_caja_chica_nombre,
            'fecha_creacion' => $liquidacion['fecha_creacion'],
            'monto_total' => $monto_total,
            'estado' => $liquidacion['estado'],
        ];

        $mode = 'ver'; // Modo de solo lectura
        require '../views/liquidaciones/ver_liquidacion.html';
        exit;
    }

    public function getLiquidacionDetails($id)
    {
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

    public function getCuentasContables($id_centro_costo)
    {
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

    public function assignContador($id) {
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

        if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && strtoupper($usuario['rol']) !== 'ADMIN') {
            error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para asignar contador. Rol: " . $usuario['rol']);
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para asignar un contador']);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idContador = isset($_POST['id_contador']) ? intval($_POST['id_contador']) : null;
            if (empty($idContador)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Debes seleccionar un contador']);
                exit;
            }

            $contadores = $this->usuarioModel->getUsuariosByRol('CONTABILIDAD');
            $validContadorIds = array_column($contadores, 'id');
            if (!in_array($idContador, $validContadorIds)) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'El contador seleccionado no es válido']);
                exit;
            }

            try {
                $this->pdo->beginTransaction();

                $this->liquidacionModel->updateEstado($id, $liquidacion['estado'], $idContador);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ASIGNAR_CONTADOR', "Contador ID $idContador asignado a liquidación ID $id");

                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Contador asignado correctamente']);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error al asignar contador: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al asignar el contador: ' . $e->getMessage()]);
            }
            exit;
        }

        $contadores = $this->usuarioModel->getUsuariosByRol('CONTABILIDAD');
        $data = [
            'id' => $liquidacion['id'],
            'estado' => $liquidacion['estado'],
        ];
        require '../views/liquidaciones/assign_contador.html';
        exit;
    }
}