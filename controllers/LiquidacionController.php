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

    // Validate role to prevent JavaScript syntax issues
    if (!isset($usuario['rol']) || !is_string($usuario['rol']) || empty(trim($usuario['rol']))) {
        error_log('Error: Rol de usuario inválido o vacío para ID: ' . $_SESSION['user_id']);
        $usuario['rol'] = 'INVALID_ROLE'; // Fallback to prevent JS errors
    } else {
        error_log('Rol de usuario: ' . $usuario['rol']);
    }

    $rol = strtoupper($usuario['rol']);
    $id_rol = $usuario['id_rol'] ?? null;
    $urlParams = $_GET['mode'] ?? '';
    $isRevisarMode = $urlParams === 'revisar';

    // Fetch role details to check name and description
    $isSupervisorRole = false;
    $isContabilidadRole = false;
    if ($id_rol) {
        $rolModel = new Role();
        $roleData = $rolModel->getRolById($id_rol);
        if ($roleData) {
            $roleName = strtoupper($roleData['nombre'] ?? '');
            $roleDescription = strtoupper($roleData['descripcion'] ?? '');
            $isSupervisorRole = strpos($roleName, 'SUPERVISOR') !== false || strpos($roleDescription, 'SUPERVISOR') !== false;
            $isContabilidadRole = $roleName === 'CONTABILIDAD' ||
                                 $id_rol == 4 ||
                                 strpos($roleName, 'CONTADOR') !== false ||
                                 strpos($roleName, 'CONTABILIDAD') !== false ||
                                 strpos($roleDescription, 'CONTADOR') !== false ||
                                 strpos($roleDescription, 'CONTABILIDAD') !== false;
        }
        error_log("Usuario ID: {$_SESSION['user_id']}, id_rol: {$id_rol}, es rol supervisor: " . ($isSupervisorRole ? 'SÍ' : 'NO') . ", es rol contabilidad: " . ($isContabilidadRole ? 'SÍ' : 'NO'));
    }

    // Fetch liquidations based on role
    $liquidaciones = [];
    $supervisorLiquidaciones = [];
    $contabilidadLiquidaciones = [];

    if ($isSupervisorRole) {
        // Fetch liquidations for supervisor
        if ($urlParams === 'autorizar') {
            $supervisorLiquidaciones = $this->liquidacionModel->getAllLiquidaciones(null, $_SESSION['user_id'], 'PENDIENTE_AUTORIZACION');
            error_log('Liquidaciones obtenidas para rol supervisor (autorizar, ID: ' . $_SESSION['user_id'] . '): ' . count($supervisorLiquidaciones) . ' registros');
        } else {
            $supervisorLiquidaciones = $this->liquidacionModel->getAllLiquidaciones(null, $_SESSION['user_id']);
            error_log('Liquidaciones obtenidas para rol supervisor (ID: ' . $_SESSION['user_id'] . '): ' . count($supervisorLiquidaciones) . ' registros');
        }

        // Filter by id_supervisor
        $supervisorLiquidaciones = array_filter($supervisorLiquidaciones, function ($liquidacion) use ($usuario) {
            return !isset($liquidacion['id_supervisor']) || $liquidacion['id_supervisor'] == $usuario['id'];
        });
        error_log('Liquidaciones filtradas por id_supervisor (ID: ' . $_SESSION['user_id'] . '): ' . count($supervisorLiquidaciones) . ' registros');
    }

    if ($isContabilidadRole) {
        // Fetch all liquidations for contabilidad, without id_contador filter
        $contabilidadLiquidaciones = $this->liquidacionModel->getAllLiquidaciones(null, null, null);
        error_log('Liquidaciones obtenidas para CONTABILIDAD (sin filtro id_contador, ID: ' . $_SESSION['user_id'] . '): ' . count($contabilidadLiquidaciones) . ' registros');
        foreach ($contabilidadLiquidaciones as $liquidacion) {
            error_log('Liquidacion ID: ' . $liquidacion['id'] . ', id_contador: ' . ($liquidacion['id_contador'] ?? 'N/A') . ', Estado: ' . ($liquidacion['estado'] ?? 'N/A'));
        }

        // Apply state filter
        $contabilidadLiquidaciones = array_filter($contabilidadLiquidaciones, function ($liquidacion) {
            return in_array($liquidacion['estado'], [
                'PENDIENTE_AUTORIZACION',
                'PENDIENTE_REVISION_CONTABILIDAD',
                'EN_PROCESO',
                'FINALIZADO',
                'RECHAZADO_POR_CONTABILIDAD'
            ]);
        });
        error_log('Liquidaciones filtradas por estado para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($contabilidadLiquidaciones) . ' registros');
    }

    // Combine liquidations for hybrid roles
    $liquidaciones = array_merge($supervisorLiquidaciones, $contabilidadLiquidaciones);
    // Remove duplicates by ID
    $liquidaciones = array_values(array_reduce($liquidaciones, function ($carry, $liquidacion) {
        $carry[$liquidacion['id']] = $liquidacion;
        return $carry;
    }, []));
    error_log('Liquidaciones combinadas (tras eliminar duplicados): ' . count($liquidaciones) . ' registros');

    // Additional filtering for CONTABILIDAD in revisar mode
    if ($isRevisarMode && $isContabilidadRole) {
        $liquidaciones = array_filter($liquidaciones, function ($liquidacion) {
            return in_array($liquidacion['estado'], [
                'PENDIENTE_REVISION_CONTABILIDAD',
                'FINALIZADO',
                'RECHAZADO_POR_CONTABILIDAD',
                'EN_PROCESO'
            ]);
        });
        error_log('Liquidaciones filtradas para CONTABILIDAD en modo revisar: ' . count($liquidaciones) . ' registros');
    }

    // For non-supervisor/non-contabilidad roles, fetch liquidations by id_usuario
    if (!$isSupervisorRole && !$isContabilidadRole) {
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
    if ($isSupervisorRole && $urlParams === 'autorizar') {
        $correctedDetalles = $this->detalleModel->getAllCorrectedDetallesForSupervisors($_SESSION['user_id']);
        error_log('Detalles corregidos obtenidos para supervisor ID: ' . $_SESSION['user_id'] . ': ' . count($correctedDetalles) . ' registros');
    } elseif ($isContabilidadRole && $urlParams === 'corrections') {
        $correctedDetalles = $this->detalleModel->getCorrectedDetallesForContador($_SESSION['user_id']);
        error_log('Detalles corregidos obtenidos para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($correctedDetalles) . ' registros');
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=UTF-8');
        $response = [
            'liquidaciones' => array_values($liquidaciones),
            'corrected_detalles' => array_values($correctedDetalles),
            'isContabilidadLike' => $isContabilidadRole
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        error_log('Rendering liquidaciones view');
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

    public function deleteLiquidacion($id)
    {
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

    // Allow autorizar_liquidaciones with any SUPERVISOR role or manage_correcciones
    $isSupervisorRole = strpos(strtoupper($usuario['rol'] ?? ''), 'SUPERVISOR') !== false;
    if (
        !($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && $isSupervisorRole) &&
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

        // Delete the detail
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

    $isSupervisorRole = strpos(strtoupper($usuario['rol'] ?? ''), 'SUPERVISOR') !== false;
    if (
        !$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') &&
        !($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && $isSupervisorRole)
    ) {
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

    $liquidacion = $this->liquidacionModel->getLiquidacionById($liquidacionId);
    if (!$liquidacion || $liquidacion['estado'] !== 'EN_PROCESO') {
        error_log("Liquidación no encontrada o no en EN_PROCESO para ID: $liquidacionId");
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Liquidación no encontrada o no está en estado EN_PROCESO']);
        exit;
    }

    // Allow supervisors to act on behalf of the detail's creator
    if (!$isSupervisorRole || !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
        if ($detalle['id_usuario'] != $userId) {
            error_log("El user_id proporcionado ($userId) no coincide con id_usuario del detalle ($detalle[id_usuario])");
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'El usuario no coincide con el propietario del detalle']);
            exit;
        }
        if ($liquidacion['id_usuario'] != $userId) {
            error_log("La liquidación ID $liquidacionId no pertenece al usuario ID $userId");
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'La liquidación no pertenece al usuario especificado']);
            exit;
        }
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

    $isSupervisorRole = strpos(strtoupper($usuario['rol'] ?? ''), 'SUPERVISOR') !== false;
    if (
        !$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') &&
        !($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && $isSupervisorRole)
    ) {
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

    // Allow supervisors to act on behalf of the detail's creator
    if (!$isSupervisorRole || !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
        if ($detalle['id_usuario'] != $userId) {
            error_log("El user_id proporcionado ($userId) no coincide con id_usuario del detalle ($detalle[id_usuario])");
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'El usuario no coincide con el propietario del detalle']);
            exit;
        }
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

    public function getEnProcesoLiquidaciones(){
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

    // Allow users with either create_liquidaciones or autorizar_liquidaciones permission
    if (
        !$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') &&
        !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')
    ) {
        error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para acceder a liquidaciones en proceso. Rol: " . $usuario['rol']);
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para acceder a liquidaciones en proceso']);
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
        error_log('getDetalleInfo: No hay session user_id');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuario) {
        error_log("getDetalleInfo: Usuario no encontrado para ID: {$_SESSION['user_id']}");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Allow users with manage_correcciones, autorizar_liquidaciones, or revisar_liquidaciones
    if (
        !$usuarioModel->tienePermiso($usuario, 'manage_correcciones') &&
        !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') &&
        !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')
    ) {
        error_log("getDetalleInfo: Usuario ID {$_SESSION['user_id']} no tiene permisos suficientes. Rol: {$usuario['rol']}");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para ver detalles'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $detalleModel = new DetalleLiquidacion();
    $detalle = $detalleModel->getDetalleById($id);
    if (!$detalle) {
        error_log("getDetalleInfo: Detalle no encontrado para ID: $id");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(404);
        echo json_encode(['error' => 'Detalle no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    error_log("getDetalleInfo: Detalle ID $id obtenido por usuario ID {$_SESSION['user_id']} ({$usuario['rol']})");
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'correccion_comentario' => $detalle['correccion_comentario'] ?? '',
        'id_usuario' => $detalle['id_usuario'] ?? null,
        'estado' => $detalle['estado'] ?? 'N/A'
    ], JSON_UNESCAPED_UNICODE);
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

    public function autorizarDetalle($id){
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

    $isSupervisorRole = false;
    if (isset($usuario['id_rol'])) {
        $rolModel = new Role();
        $roleData = $rolModel->getRolById($usuario['id_rol']);
        if ($roleData) {
            $roleName = strtoupper($roleData['nombre'] ?? '');
            $roleDescription = strtoupper($roleData['descripcion'] ?? '');
            $isSupervisorRole = strpos($roleName, 'SUPERVISOR') !== false || strpos($roleDescription, 'SUPERVISOR') !== false;
        }
    }

    if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') || !$isSupervisorRole) {
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

    $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
    if (!$liquidacion) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Liquidación no encontrada']);
        exit;
    }

    // Block authorization if liquidation is FINALIZADO
    if ($liquidacion['estado'] === 'FINALIZADO') {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'No se puede autorizar porque la liquidación está finalizada']);
        exit;
    }

    // Allow EN_CORRECCION state
    if (!in_array($detalle['estado'], ['PENDIENTE_AUTORIZACION', 'EN_CORRECCION'])) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'El detalle no está en estado PENDIENTE_AUTORIZACION o EN_CORRECCION']);
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
                // Fetch the user to determine their role
                $targetUser = $usuarioModel->getUsuarioById($idUsuario);
                if (!$targetUser) {
                    throw new Exception('Usuario destino no encontrado');
                }
                $rolName = strtoupper($targetUser['rol']);
                $rolDescription = strtoupper($targetUser['descripcion'] ?? '');
                $isSupervisor = strpos($rolName, 'SUPERVISOR') !== false || strpos($rolDescription, 'SUPERVISOR') !== false;
                $rol = $isSupervisor ? 'SUPERVISOR' : null;
                $supervisorId = $isSupervisor ? $idUsuario : null;
                $detalleModel->updateEstadoWithComment($detalleId, $nuevoEstado, $rol, $motivo, $supervisorId);
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
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuario) {
        error_log("Usuario no encontrado para ID: " . $_SESSION['user_id']);
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')) {
        error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para autorizar liquidaciones. Rol: " . ($usuario['rol'] ?? 'N/A'));
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para autorizar liquidaciones'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
    if (!$liquidacion) {
        error_log("Liquidación no encontrada para ID: $id");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(404);
        echo json_encode(['error' => 'Liquidación no encontrada'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate and sanitize role
    $rol = strtoupper($usuario['rol'] ?? 'INVALID_ROLE');
    if (!is_string($usuario['rol']) || empty(trim($usuario['rol']))) {
        error_log("Rol inválido para usuario ID: {$_SESSION['user_id']}");
        $rol = 'INVALID_ROLE';
    }

    // Determine if user has a Contabilidad-like or Supervisor-like role
    $isContabilidadRole = false;
    $isSupervisorRole = false;
    $id_rol = $usuario['id_rol'] ?? null;
    if ($id_rol) {
        $rolModel = new Role();
        $roleData = $rolModel->getRolById($id_rol);
        if ($roleData) {
            $roleName = strtoupper($roleData['nombre'] ?? '');
            $roleDescription = strtoupper($roleData['descripcion'] ?? '');
            $isContabilidadRole = $roleName === 'CONTABILIDAD' ||
                                 $id_rol == 4 ||
                                 strpos($roleName, 'CONTADOR') !== false ||
                                 strpos($roleName, 'CONTABILIDAD') !== false ||
                                 strpos($roleDescription, 'CONTADOR') !== false ||
                                 strpos($roleDescription, 'CONTABILIDAD') !== false;
            $isSupervisorRole = strpos($roleName, 'SUPERVISOR') !== false ||
                                strpos($roleDescription, 'SUPERVISOR') !== false;
        }
    }
    error_log("Usuario ID: {$_SESSION['user_id']}, Rol: $rol, id_rol: {$id_rol}, es contabilidad: " . ($isContabilidadRole ? 'SÍ' : 'NO') . ", es supervisor: " . ($isSupervisorRole ? 'SÍ' : 'NO'));

    $expectedEstado = $isSupervisorRole ? 'PENDIENTE_AUTORIZACION' : 'PENDIENTE_REVISION_CONTABILIDAD';

    $isFromCorrection = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_correccion';
    if (!$isFromCorrection && $liquidacion['estado'] !== $expectedEstado) {
        error_log("Estado de la liquidación no válido. Esperado: $expectedEstado, Actual: " . $liquidacion['estado']);
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(400);
        echo json_encode(['error' => "Solo se pueden autorizar liquidaciones en estado $expectedEstado"], JSON_UNESCAPED_UNICODE);
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

            error_log("send_to_correction: detalleIds=" . json_encode($detalleIds) . ", user_id={$_SESSION['user_id']}, liquidation_id=$id, rol=$rol");

            if (empty($detalleIds)) {
                error_log("No se proporcionaron detalleIds");
                header('Content-Type: application/json; charset=UTF-8');
                http_response_code(400);
                echo json_encode(['error' => 'No se proporcionaron IDs de detalle'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            try {
                $this->pdo->beginTransaction();
                $numCorrections = 0;
                $skipReasons = [];

                foreach ($detalleIds as $detalleId) {
                    $detalleId = intval($detalleId);
                    error_log("Procesando detalleId=$detalleId");
                    $detalle = $detalleModel->getDetalleById($detalleId);
                    if (!$detalle) {
                        error_log("Detalle ID $detalleId no encontrado");
                        $skipReasons[$detalleId] = "Detalle no encontrado";
                        continue;
                    }
                    $detalleLiquidacionId = intval($detalle['id_liquidacion'] ?? 0);
                    if ($detalleLiquidacionId !== $id) {
                        error_log("Detalle ID $detalleId pertenece a otra liquidación: $detalleLiquidacionId != $id");
                        $skipReasons[$detalleId] = "Pertenece a otra liquidación";
                        continue;
                    }
                    if ($detalle['estado'] === 'EN_CORRECCION') {
                        error_log("Detalle ID $detalleId ya está en EN_CORRECCION");
                        $skipReasons[$detalleId] = "Ya en corrección";
                        continue;
                    }
                    $comment = $correccionComentarios[$detalleId] ?? '';
                    if (empty($comment)) {
                        error_log("Comentario vacío para detalle ID $detalleId");
                        throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                    }
                    error_log("Actualizando detalle ID $detalleId a EN_CORRECCION con comentario='$comment'");
                    $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, $isSupervisorRole ? $_SESSION['user_id'] : null);
                    $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por " . ($isSupervisorRole ? "supervisor ID {$_SESSION['user_id']}" : "contador"));
                    $numCorrections++;
                }

                if ($numCorrections === 0 && !empty($skipReasons)) {
                    error_log("No se procesaron detalles. Razones: " . json_encode($skipReasons));
                    throw new Exception("No se procesaron detalles: " . json_encode($skipReasons));
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
                    error_log("Todos los detalles en EN_CORRECCION, actualizando liquidación ID $id a EN_PROCESO");
                    $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
                }

                $this->pdo->commit();
                error_log("Corrección completada: $numCorrections detalle(s) enviados a corrección");
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['message' => "$numCorrections detalle(s) enviado(s) a corrección correctamente", 'num_corrections' => $numCorrections], JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error al enviar detalle a corrección: ' . $e->getMessage());
                header('Content-Type: application/json; charset=UTF-8');
                http_response_code(400);
                echo json_encode(['error' => 'Error al enviar detalle a corrección: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
            }
            exit;
        }

        $accion = $_POST['accion'] ?? '';
        $motivo = $_POST['motivo'] ?? '';
        $detallesSeleccionados = $_POST['detalles'] ?? [];
        $detallesNoSeleccionados = json_decode($_POST['unselected_detalles'] ?? '[]', true);
        $correccionComentarios = json_decode($_POST['correccion_comentarios'] ?? '{}', true);

        error_log("Rol del usuario: $rol");
        error_log("Acción recibida en autorizar: $accion");
        error_log("Estado actual de la liquidación: " . $liquidacion['estado']);
        error_log("Detalles seleccionados: " . json_encode($detallesSeleccionados));
        error_log("Detalles no seleccionados: " . json_encode($detallesNoSeleccionados));

        $allowedAcciones = ['APROBADO', 'RECHAZADO', 'DESCARTADO'];
        if (!in_array($accion, $allowedAcciones)) {
            error_log("Acción no válida: $accion. Acciones permitidas: " . implode(', ', $allowedAcciones));
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $nuevoEstado = '';
            $auditoriaAccion = '';
            $message = '';

            if ($accion === 'APROBADO') {
                if ($isSupervisorRole) {
                    $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                    $auditoriaAccion = 'AUTORIZADO_POR_SUPERVISOR';
                    $message = 'Liquidación autorizada por supervisor';
                } elseif ($isContabilidadRole) {
                    $nuevoEstado = 'FINALIZADO';
                    $auditoriaAccion = 'AUTORIZADO_POR_CONTABILIDAD';
                    $message = 'Liquidación finalizada por contabilidad';
                }
            } elseif ($accion === 'RECHAZADO') {
                if ($isSupervisorRole) {
                    $nuevoEstado = 'RECHAZADO_AUTORIZACION';
                    $auditoriaAccion = 'RECHAZADO_POR_SUPERVISOR';
                    $message = 'Liquidación rechazada por supervisor';
                } elseif ($isContabilidadRole) {
                    $nuevoEstado = 'RECHAZADO_POR_CONTABILIDAD';
                    $auditoriaAccion = 'RECHAZADO_POR_CONTABILIDAD';
                    $message = 'Liquidación rechazada por contabilidad';
                }
            } elseif ($accion === 'DESCARTADO') {
                $nuevoEstado = 'DESCARTADO';
                $auditoriaAccion = 'DESCARTADO';
                $message = 'Liquidación descartada';
            }

            if (empty($nuevoEstado)) {
                throw new Exception("Estado no asignado para acción: $accion y rol: $rol");
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
                    $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, $isSupervisorRole ? $_SESSION['user_id'] : null);
                    $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por " . ($isSupervisorRole ? "supervisor ID {$_SESSION['user_id']}" : "contador"));
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
                if ($detalle['estado'] !== 'EN_CORRECTION') {
                    $allInCorrection = false;
                    $hasNonCorrectionDetails = true;
                }
            }

            if ($allInCorrection && !$anySelected) {
                $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
            } elseif ($isSupervisorRole && $accion === 'APROBADO' && $hasApprovedDetails) {
                $this->liquidacionModel->updateEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD', null, null);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            } elseif ($isContabilidadRole && $accion === 'APROBADO' && $hasNonCorrectionDetails) {
                $this->liquidacionModel->updateEstado($id, 'FINALIZADO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            } else {
                $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            }

            $isExported = $this->liquidacionModel->isExported($id);
            if ($isExported && $isContabilidadRole && $accion === 'APROBADO') {
                $message .= "\nLa liquidación ya fue exportada. Se ha reenviado a corrección.";
                $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'REENVIADO_A_CORRECCION', 'Liquidación reenviada a corrección por exportación previa');
            }

            $this->pdo->commit();
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['message' => $message, 'num_corrections' => count($detailsToCorrect)], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error al registrar autorización: ' . $e->getMessage());
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode(['error' => 'Error al registrar la autorización: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    $data = [
        'id' => htmlspecialchars($liquidacion['id'], ENT_QUOTES, 'UTF-8'),
        'id_caja_chica' => htmlspecialchars($liquidacion['id_caja_chica'], ENT_QUOTES, 'UTF-8'),
        'nombre_caja_chica' => htmlspecialchars($nombre_caja_chica, ENT_QUOTES, 'UTF-8'),
        'centro_costo_caja_chica_id' => htmlspecialchars($cajaChica['id_centro_costo'] ?? '', ENT_QUOTES, 'UTF-8'),
        'centro_costo_caja_chica_nombre' => htmlspecialchars($centro_costo_caja_chica_nombre, ENT_QUOTES, 'UTF-8'),
        'fecha_creacion' => htmlspecialchars($liquidacion['fecha_creacion'], ENT_QUOTES, 'UTF-8'),
        'monto_total' => $monto_total,
        'estado' => htmlspecialchars($liquidacion['estado'], ENT_QUOTES, 'UTF-8'),
        'id_contador' => htmlspecialchars($liquidacion['id_contador'] ?? '', ENT_QUOTES, 'UTF-8'),
    ];

    // Commented out contadores fetch as accountant selection is removed
    /*
    $contadores = [];
    if ($isSupervisorRole) {
        $contadores = $usuarioModel->getUsuariosByRol('CONTABILIDAD');
        foreach ($contadores as &$contador) {
            $contador['id'] = htmlspecialchars($contador['id'], ENT_QUOTES, 'UTF-8');
            $contador['nombre'] = htmlspecialchars($contador['nombre'], ENT_QUOTES, 'UTF-8');
            $contador['email'] = htmlspecialchars($contador['email'], ENT_QUOTES, 'UTF-8');
        }
        unset($contador);
    }
    */

    // Sanitize detalles data
    foreach ($detalles as &$detalle) {
        $detalle['id'] = htmlspecialchars($detalle['id'], ENT_QUOTES, 'UTF-8');
        $detalle['estado'] = htmlspecialchars($detalle['estado'], ENT_QUOTES, 'UTF-8');
        $detalle['id_contador'] = htmlspecialchars($detalle['id_contador'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cuenta_contable_nombre'] = htmlspecialchars($detalle['cuenta_contable_nombre'], ENT_QUOTES, 'UTF-8');
    }
    unset($detalle);

    $mode = 'autorizar';
    $usuario_data = [
        'rol' => htmlspecialchars($rol, ENT_QUOTES, 'UTF-8'),
        'id' => htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8')
    ];
    require '../views/liquidaciones/autorizar_individual.html';
    exit;
}

public function revisar($id)
{
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuario) {
        error_log("Usuario no encontrado para ID: " . $_SESSION['user_id']);
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Check permission
    $hasPermission = $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones');
    if (!$hasPermission) {
        error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para revisar liquidaciones. Rol: " . ($usuario['rol'] ?? 'N/A'));
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para revisar liquidaciones'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validate and sanitize role
    $rol = strtoupper($usuario['rol'] ?? 'INVALID_ROLE');
    if (!is_string($usuario['rol']) || empty(trim($usuario['rol']))) {
        error_log("Rol inválido para usuario ID: {$_SESSION['user_id']}");
        $rol = 'INVALID_ROLE';
    }

    // Check for Contabilidad-like role
    $isContabilidadRole = false;
    $id_rol = $usuario['id_rol'] ?? null;
    if ($id_rol) {
        $rolModel = new Role();
        $roleData = $rolModel->getRolById($id_rol);
        if ($roleData) {
            $roleName = strtoupper($roleData['nombre'] ?? '');
            $roleDescription = strtoupper($roleData['descripcion'] ?? '');
            error_log("Role data for id_rol {$id_rol}: nombre={$roleName}, descripcion={$roleDescription}");
            $isContabilidadRole = $roleName === 'CONTABILIDAD' ||
                                 $id_rol == 4 ||
                                 strpos($roleName, 'CONTADOR') !== false ||
                                 strpos($roleName, 'CONTABILIDAD') !== false ||
                                 strpos($roleDescription, 'CONTADOR') !== false ||
                                 strpos($roleDescription, 'CONTABILIDAD') !== false;
        } else {
            error_log("No se encontró role data para id_rol: {$id_rol}");
        }
    } else {
        error_log("id_rol no definido para usuario ID: {$_SESSION['user_id']}");
    }
    error_log("Usuario ID: {$_SESSION['user_id']}, Rol: {$rol}, id_rol: {$id_rol}, es contabilidad: " . ($isContabilidadRole ? 'SÍ' : 'NO'));

    // Allow access if user has permission or Contabilidad role
    if (!$hasPermission && !$isContabilidadRole) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'Acceso denegado: se requiere permiso o rol de contabilidad'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
    if (!$liquidacion) {
        error_log("Liquidación no encontrada para ID: $id");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(404);
        echo json_encode(['error' => 'Liquidación no encontrada'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $isFromCorrection = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_correccion';
    if (!$isFromCorrection && $liquidacion['estado'] !== 'PENDIENTE_REVISION_CONTABILIDAD') {
        error_log("Estado de la liquidación no válido. Esperado: PENDIENTE_REVISION_CONTABILIDAD, Actual: " . $liquidacion['estado']);
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(400);
        echo json_encode(['error' => 'Solo se pueden revisar liquidaciones en estado PENDIENTE_REVISION_CONTABILIDAD'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $detalleModel = new DetalleLiquidacion();
    $allDetalles = $detalleModel->getDetallesByLiquidacionId($id);
    $detalles = $detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD');

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
            $idContador = isset($_POST['id_contador']) ? intval($_POST['id_contador']) : ($isContabilidadRole ? $_SESSION['user_id'] : null);
        
            error_log("send_to_correction: detalleIds=" . json_encode($detalleIds) . ", idContador=$idContador, user_id={$_SESSION['user_id']}, liquidation_id=$id");
        
            if (empty($detalleIds)) {
                error_log("No se proporcionaron detalleIds");
                header('Content-Type: application/json; charset=UTF-8');
                http_response_code(400);
                echo json_encode(['error' => 'No se proporcionaron IDs de detalle'], JSON_UNESCAPED_UNICODE);
                exit;
            }
        
            if (!$idContador && $isContabilidadRole) {
                error_log("idContador no establecido para Contabilidad role, usando user_id={$_SESSION['user_id']}");
                $idContador = $_SESSION['user_id'];
            }
        
            try {
                $this->pdo->beginTransaction();
                $numCorrections = 0;
                $skipReasons = [];
        
                foreach ($detalleIds as $detalleId) {
                    $detalleId = intval($detalleId);
                    error_log("Procesando detalleId=$detalleId, tipo=" . gettype($detalleId));
                    $detalle = $detalleModel->getDetalleById($detalleId);
                    if (!$detalle) {
                        error_log("Detalle ID $detalleId no encontrado");
                        $skipReasons[$detalleId] = "Detalle no encontrado";
                        continue;
                    }
                    $detalleLiquidacionId = intval($detalle['id_liquidacion'] ?? 0);
                    $id = intval($id);
                    error_log("Validando: detalle_liquidacion_id=$detalleLiquidacionId, liquidation_id=$id");
        
                    // Fallback: Correct id_liquidacion if 0
                    if ($detalleLiquidacionId === 0) {
                        error_log("id_liquidacion=0 para detalle ID $detalleId, intentando corregir a $id");
                        $detalleModel->updateLiquidacionId($detalleId, $id);
                        $detalle = $detalleModel->getDetalleById($detalleId); // Reload
                        $detalleLiquidacionId = intval($detalle['id_liquidacion'] ?? 0);
                        if ($detalleLiquidacionId !== $id) {
                            error_log("Fallo al corregir id_liquidacion para detalle ID $detalleId, sigue siendo $detalleLiquidacionId");
                            $skipReasons[$detalleId] = "Pertenece a otra liquidación (detalle_liquidacion_id=$detalleLiquidacionId, esperado=$id)";
                            continue;
                        }
                        error_log("id_liquidacion corregido a $id para detalle ID $detalleId");
                    }
        
                    if ($detalleLiquidacionId !== $id) {
                        error_log("Detalle ID $detalleId pertenece a otra liquidación: $detalleLiquidacionId != $id");
                        $skipReasons[$detalleId] = "Pertenece a otra liquidación (detalle_liquidacion_id=$detalleLiquidacionId, esperado=$id)";
                        continue;
                    }
                    if ($detalle['estado'] === 'EN_CORRECCION') {
                        error_log("Detalle ID $detalleId ya está en EN_CORRECCION");
                        $skipReasons[$detalleId] = "Ya en corrección";
                        continue;
                    }
                    $comment = $correccionComentarios[$detalleId] ?? '';
                    if (empty($comment)) {
                        error_log("Comentario vacío para detalle ID $detalleId");
                        throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                    }
                    error_log("Actualizando detalle ID $detalleId a EN_CORRECCION con comentario='$comment', id_contador=$idContador");
                    $detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, null, $idContador);
                    $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por contador ID " . ($idContador ?? 'N/A'));
                    $numCorrections++;
                }
        
                if ($numCorrections === 0 && !empty($skipReasons)) {
                    error_log("No se procesaron detalles. Razones: " . json_encode($skipReasons));
                    throw new Exception("No se procesaron detalles: " . json_encode($skipReasons));
                }
        
                $updatedDetalles = $detalleModel->getDetallesByLiquidacionId($id);
                $allInCorrection = true;
                foreach ($updatedDetalles as $detalle) {
                    if ($detalle['estado'] !== 'EN_CORRECCION') {
                        $allInCorrection = false;
                        break;
                    }
                }
        
                if ($allInCorrection) {
                    error_log("Todos los detalles en EN_CORRECCION, actualizando liquidación ID $id a EN_PROCESO");
                    $this->liquidacionModel->updateEstado($id, 'EN_PROCESO');
                    $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', 'Liquidación enviada a corrección');
                }
        
                $this->pdo->commit();
                error_log("Corrección completada: $numCorrections detalle(s) enviados a corrección");
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['message' => "$numCorrections detalle(s) enviado(s) a corrección correctamente", 'num_corrections' => $numCorrections], JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log('Error al enviar detalle a corrección: ' . $e->getMessage());
                header('Content-Type: application/json; charset=UTF-8');
                http_response_code(400);
                echo json_encode(['error' => 'Error al enviar detalle a corrección: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
            }
            exit;
        }

        $accion = $_POST['accion'] ?? '';
        $motivo = $_POST['motivo'] ?? '';
        $detallesSeleccionados = $_POST['detalles'] ?? [];
        $detallesNoSeleccionados = json_decode($_POST['unselected_detalles'] ?? '[]', true);
        $correccionComentarios = json_decode($_POST['correccion_comentarios'] ?? '{}', true);

        $allowedAcciones = ['APROBADO', 'RECHAZADO', 'DESCARTADO'];
        if (!in_array($accion, $allowedAcciones)) {
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            $nuevoEstado = '';
            $auditoriaAccion = '';
            $message = '';

            if ($accion === 'APROBADO') {
                $nuevoEstado = $isContabilidadRole ? 'PENDIENTE_REVISION_CONTABILIDAD' : 'FINALIZADO'; // Keep PENDIENTE_REVISION_CONTABILIDAD for Contabilidad
                $auditoriaAccion = $isContabilidadRole ? 'APROBADO_POR_CONTABILIDAD' : 'AUTORIZADO_POR_CONTABILIDAD';
                $message = $isContabilidadRole ? 'Aprobado por contabilidad, listo para exportar a SAP.' : 'Se autorizaron por contabilidad.';
            } elseif ($accion === 'RECHAZADO') {
                $nuevoEstado = 'RECHAZADO_POR_CONTABILIDAD';
                $auditoriaAccion = 'RECHAZADO_POR_CONTABILIDAD';
                $message = 'Rechazado por contabilidad.';
            } elseif ($accion === 'DESCARTADO') {
                $nuevoEstado = 'DESCARTADO';
                $auditoriaAccion = 'DESCARTADO_POR_CONTABILIDAD';
                $message = 'Descartado por contabilidad.';
            }

            $detailsToCorrect = [];
            foreach ($allDetalles as $detalle) {
                $detalleId = $detalle['id'];
                if (in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD') {
                    $detailsToCorrect[] = $detalleId;
                    $comment = $correccionComentarios[$detalleId] ?? '';
                    if (empty($comment)) {
                        throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                    }
                    $idContador = $isContabilidadRole ? $_SESSION['user_id'] : ($liquidacion['id_contador'] ?? null);
                    $detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, null, $idContador);
                    $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment");
                }
            }

            $anySelected = !empty($detallesSeleccionados);
            $nonCorrectedDetailsProcessed = false;
            if ($isFromCorrection && $anySelected) {
                foreach ($allDetalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if (in_array($detalleId, $detallesSeleccionados) && $detalle['estado'] === 'EN_CORRECCION') {
                        $detalleModel->updateEstado($detalleId, $nuevoEstado);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                        $nonCorrectedDetailsProcessed = true;
                    }
                }
            } else {
                foreach ($allDetalles as $detalle) {
                    $detalleId = $detalle['id'];
                    if ($detalle['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD') {
                        if (in_array($detalleId, $detallesSeleccionados)) {
                            $detalleModel->updateEstado($detalleId, $nuevoEstado);
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                            $nonCorrectedDetailsProcessed = true;
                        } elseif (!in_array($detalleId, $detallesNoSeleccionados)) {
                            $detalleModel->updateEstado($detalleId, $nuevoEstado);
                            $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], $auditoriaAccion, $motivo);
                            $nonCorrectedDetailsProcessed = true;
                        }
                    }
                }
            }

            $updatedDetalles = $detalleModel->getDetallesByLiquidacionId($id);
            $allInCorrection = true;
            $hasNonCorrectionDetails = false;

            foreach ($updatedDetalles as $detalle) {
                if ($detalle['estado'] !== 'EN_CORRECCION') {
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
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['message' => $message, 'num_corrections' => count($detailsToCorrect)], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error al registrar revisión: ' . $e->getMessage());
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode(['error' => 'Error al registrar la revisión: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    $data = [
        'id' => htmlspecialchars($liquidacion['id'], ENT_QUOTES, 'UTF-8'),
        'id_caja_chica' => htmlspecialchars($liquidacion['id_caja_chica'], ENT_QUOTES, 'UTF-8'),
        'nombre_caja_chica' => htmlspecialchars($nombre_caja_chica, ENT_QUOTES, 'UTF-8'),
        'centro_costo_caja_chica_id' => htmlspecialchars($cajaChica['id_centro_costo'] ?? '', ENT_QUOTES, 'UTF-8'),
        'centro_costo_caja_chica_nombre' => htmlspecialchars($centro_costo_caja_chica_nombre, ENT_QUOTES, 'UTF-8'),
        'fecha_creacion' => htmlspecialchars($liquidacion['fecha_creacion'], ENT_QUOTES, 'UTF-8'),
        'monto_total' => $monto_total,
        'estado' => htmlspecialchars($liquidacion['estado'], ENT_QUOTES, 'UTF-8'),
        'id_contador' => htmlspecialchars($liquidacion['id_contador'] ?? '', ENT_QUOTES, 'UTF-8'),
    ];

    // Sanitize detalles data
    foreach ($detalles as &$detalle) {
        $detalle['id'] = htmlspecialchars($detalle['id'], ENT_QUOTES, 'UTF-8');
        $detalle['estado'] = htmlspecialchars($detalle['estado'], ENT_QUOTES, 'UTF-8');
        $detalle['id_contador'] = htmlspecialchars($detalle['id_contador'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cuenta_contable_nombre'] = htmlspecialchars($detalle['cuenta_contable_nombre'], ENT_QUOTES, 'UTF-8');
    }
    unset($detalle);

    $mode = 'revisar';
    $usuario_data = [
        'rol' => htmlspecialchars($rol, ENT_QUOTES, 'UTF-8'),
        'id' => htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8')
    ];
    require '../views/liquidaciones/autorizar_individual.html';
    exit;
}
private function login_sap($db){
        $usuario = 'manager';
        $contrasena = 'Team64110';
        $sociedad = $db;

        $curl = curl_init();

        $urlServer = 'https://192.168.1.9:50000/b1s/v1/';
        $sboObjType = 'Login';

        curl_setopt_array($curl, [
            CURLOPT_PORT => 50000,
            CURLOPT_URL => $urlServer . $sboObjType,
            CURLOPT_SSL_VERIFYHOST => false, // Insecure; use valid SSL in production
            CURLOPT_SSL_VERIFYPEER => false, // Insecure; use valid SSL in production
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_COOKIEJAR => __DIR__ . "/cookie.txt",
            CURLOPT_COOKIEFILE => __DIR__ . "/cookie.txt",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "UserName" => $usuario,
                "Password" => $contrasena,
                "CompanyDB" => $sociedad
            ], JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Cache-Control: no-cache"
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false || $curlError) {
            $errorMsg = $curlError ? "cURL Error: $curlError" : "No response received";
            error_log("SAP Login Failed: $errorMsg");
            return ['success' => false, 'error' => $errorMsg];
        }

        if ($httpCode !== 200) {
            error_log("SAP Login Failed: HTTP $httpCode - $response");
            return ['success' => false, 'error' => "HTTP $httpCode - $response"];
        }

        $sessionData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($sessionData['SessionId'])) {
            error_log("SAP Login Failed: Invalid JSON or no SessionId - $response");
            return ['success' => false, 'error' => 'Invalid JSON or no SessionId returned'];
        }

        return [
            'success' => true,
            'sessionId' => $sessionData['SessionId'],
            'routeId' => $sessionData['RouteId'] ?? '.guid',
            'response' => $sessionData
        ];
    }

    private function logout_sap()
    {
        $curl = curl_init();

        $urlServer = 'https://192.168.1.9:50000/b1s/v1/';
        $sboObjType = 'Logout';

        curl_setopt_array($curl, [
            CURLOPT_PORT => 50000,
            CURLOPT_URL => $urlServer . $sboObjType,
            CURLOPT_SSL_VERIFYHOST => false, // Insecure; use valid SSL in production
            CURLOPT_SSL_VERIFYPEER => false, // Insecure; use valid SSL in production
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_COOKIEJAR => __DIR__ . "/cookie.txt",
            CURLOPT_COOKIEFILE => __DIR__ . "/cookie.txt",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Cache-Control: no-cache"
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false || $curlError) {
            $errorMsg = $curlError ? "cURL Error: $curlError" : "No response received";
            error_log("SAP Logout Failed: $errorMsg");
            return ['success' => false, 'error' => $errorMsg];
        }

        if ($httpCode !== 204) {
            error_log("SAP Logout Failed: HTTP $httpCode - $response");
            return ['success' => false, 'error' => "HTTP $httpCode - $response"];
        }

        $cookieFile = __DIR__ . '/cookie.txt';
        if (file_exists($cookieFile) && is_writable($cookieFile)) {
            unlink($cookieFile); // Clean up cookie file
        } elseif (file_exists($cookieFile)) {
            error_log("Warning: Could not delete cookie file; not writable");
        }

        return ['success' => true];
    }

    public function exportar($id)
{
    ob_start();
    error_log("Iniciando exportar para id: $id");

    if (!isset($_SESSION['user_id'])) {
        error_log('Error: No hay session user_id en exportar');
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        // Validate user and permissions
        error_log("Cargando modelo Usuario");
        if (!class_exists('Usuario')) {
            throw new Exception('Clase Usuario no encontrada');
        }
        $usuarioModel = new Usuario();
        error_log("Obteniendo usuario por ID: {$_SESSION['user_id']}");
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuario || !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
            error_log('Error: No tienes permiso para exportar liquidaciones');
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para exportar liquidaciones'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Load liquidacion
        error_log("Cargando modelo Liquidacion");
        if (!class_exists('Liquidacion')) {
            throw new Exception('Clase Liquidacion no encontrada');
        }
        $liquidacionModel = new Liquidacion();
        error_log("Obteniendo liquidación por ID: $id");
        $liquidacion = $liquidacionModel->getLiquidacionById($id);
        if (!$liquidacion) {
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(404);
            echo json_encode(['error' => 'Liquidación no encontrada'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Check liquidacion state
        $invalidStates = ['EN_CORRECCION', 'PENDIENTE_AUTORIZACION', 'EN_PROCESO'];
        if (in_array($liquidacion['estado'], $invalidStates)) {
            $stateMessage = "Solo se pueden exportar liquidaciones en estado PENDIENTE_REVISION_CONTABILIDAD o FINALIZADO, no en {$liquidacion['estado']}";
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(['error' => $stateMessage], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Fetch detalle_liquidacion records
        error_log("Cargando modelo DetalleLiquidacion");
        if (!class_exists('DetalleLiquidacion')) {
            throw new Exception('Clase DetalleLiquidacion no encontrada');
        }
        $detalleLiquidacionModel = new DetalleLiquidacion();
        error_log("Obteniendo detalles por liquidación ID: $id");
        $detalleLiquidaciones = $detalleLiquidacionModel->getDetallesByLiquidacionId($id);
        if (empty($detalleLiquidaciones)) {
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(404);
            echo json_encode(['error' => 'No se encontraron detalles de liquidación'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Filter detalle_liquidaciones to only include PENDIENTE_REVISION_CONTABILIDAD state
        $pendingDetalles = array_filter($detalleLiquidaciones, function ($dl) {
            return $dl['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD';
        });

        if (empty($pendingDetalles)) {
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(['error' => 'No se encontraron detalles en estado PENDIENTE_REVISION_CONTABILIDAD para exportar'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Start transaction
        $this->pdo->beginTransaction();

        // SAP Login
        error_log("Intentando login en SAP");
        $loginResult = $this->login_sap('T_GT_AGROCENTRO_2016');
        if (!$loginResult['success']) {
            error_log("Login SAP Failed: {$loginResult['error']}");
            $this->pdo->rollBack();
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(['error' => 'No es posible exportar por problemas en SAP, intente más tarde'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $cookie = "B1SESSION={$loginResult['sessionId']}; ROUTEID={$loginResult['routeId']}";

        $results = [];
        $jsonDir = __DIR__ . "/json";
        if (!file_exists($jsonDir)) {
            if (!mkdir($jsonDir, 0777, true)) {
                throw new Exception('No se pudo crear el directorio JSON: ' . $jsonDir);
            }
        }
        if (!is_writable($jsonDir)) {
            throw new Exception('El directorio JSON no es escribible: ' . $jsonDir);
        }

        $forceExport = isset($_GET['force']) && $_GET['force'] === 'true';
        $timestamp = date('Ymd\THis');

        // Create a separate invoice for pending detalle_liquidaciones
        $allExportsSuccessful = true;
        foreach ($pendingDetalles as $index => $dl) {
            try {
                error_log("Procesando detalle_liquidacion index: $index (estado: {$dl['estado']}, tipo_documento: {$dl['tipo_documento']})");
                $docDate = date('Y-m-d', strtotime($dl['fecha']));
                $suffix = $forceExport ? '-' . substr(md5(uniqid()), 0, 8) : '';
                $numAtCard = "DLIQ-{$id}-{$index}-{$timestamp}{$suffix}";
                $documentLines = [];
                $docTotal = 0;

                // Determine document type and generate document lines
                $tipoDocumento = strtoupper($dl['tipo_documento'] ?? 'FACTURA'); // Default to FACTURA if not set
                if ($tipoDocumento === 'FACTURA') {
                    // Handle FACTURA as before
                    if (floatval($dl['iva']) > 0) {
                        $documentLines[] = [
                            "LineType" => 0,
                            "ItemDescription" => $dl['t_gasto'],
                            "Price" => floatval($dl['iva']),
                            "TaxCode" => "IVA",
                            "CostingCode" => "D08",
                            "AccountCode" => "641101001"
                        ];
                        $docTotal += floatval($dl['iva']);
                    }
                    if (floatval($dl['idp']) > 0) {
                        $documentLines[] = [
                            "LineType" => count($documentLines),
                            "ItemDescription" => "IDP",
                            "Price" => floatval($dl['idp']),
                            "TaxCode" => "EXE",
                            "CostingCode" => "D08",
                            "AccountCode" => "641101001"
                        ];
                        $docTotal += floatval($dl['idp']);
                    } elseif (floatval($dl['inguat']) > 0) {
                        $documentLines[] = [
                            "LineType" => count($documentLines),
                            "ItemDescription" => "INGUAT",
                            "Price" => floatval($dl['inguat']),
                            "TaxCode" => "EXE",
                            "CostingCode" => "D08",
                            "AccountCode" => "641101001"
                        ];
                        $docTotal += floatval($dl['inguat']);
                    }
                } else {
                    // Handle COMPROBANTE or RECIBO
                    if (floatval($dl['monto_total']) > 0) {
                        $documentLines[] = [
                            "LineType" => 0,
                            "ItemDescription" => $dl['t_gasto'] . " ({$tipoDocumento})",
                            "Price" => floatval($dl['monto_total']),
                            "TaxCode" => "EXE", // Assuming no tax for comprobantes/recibos, adjust if needed
                            "CostingCode" => "D08",
                            "AccountCode" => "641101002" // Different account for non-factura, adjust as needed
                        ];
                        $docTotal += floatval($dl['monto_total']);
                    }
                }

                // Validate document lines
                if (empty($documentLines)) {
                    error_log("No document lines for detalle_liquidacion index $index: tipo_documento={$tipoDocumento}");
                    $detalleLiquidacionModel->updateEstado($dl['id'], 'EN_CORRECCION');
                    $this->auditoriaModel->createAuditoria($id, $dl['id'], $_SESSION['user_id'], 'DETALLE_INVALIDO', "Detalle inválido: sin valores financieros válidos para {$tipoDocumento}");
                    $results[] = [
                        'index' => $index,
                        'success' => false,
                        'error' => "No se generaron líneas para el documento: sin valores financieros válidos para {$tipoDocumento}"
                    ];
                    continue; // Skip to next detail
                }

                // Use comentarios field, with fallback to "Sin comentarios" if empty or null, and trim to 254 chars
                $comments = !empty(trim($dl['comentarios'])) ? substr(trim($dl['comentarios']), 0, 254) : 'Sin comentarios';

                $purchaseInvoice = [
                    "DocType" => "dDocument_Service",
                    "CardCode" => "CCHD0012",
                    "DocDate" => $docDate,
                    "Comments" => $comments,
                    "U_NIT" => "321052",
                    "Series" => 82,
                    "DocTotal" => $docTotal,
                    "Reference1" => "{$id}-{$index}",
                    "NumAtCard" => $numAtCard,
                    "DocCurrency" => "QTZ",
                    "DocRate" => 1,
                    "DocumentLines" => $documentLines
                ];

                // Generate JSON file
                $jsonFilePath = "$jsonDir/export_liquidacion_{$id}_{$index}.json";
                $jsonContent = json_encode($purchaseInvoice, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if (file_put_contents($jsonFilePath, "\xEF\xBB\xBF" . $jsonContent) === false) {
                    throw new Exception("No se pudo escribir el archivo JSON: $jsonFilePath");
                }
                error_log("JSON file generated at: $jsonFilePath");

                // Send to SAP
                $sapUrl = "https://192.168.1.9:50000/b1s/v1/PurchaseInvoices";
                $ch = curl_init($sapUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Cookie: ' . $cookie
                    ],
                    CURLOPT_POSTFIELDS => $jsonContent,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($response === false || $curlError) {
                    error_log("SAP Error for index $index: $curlError");
                    $results[] = [
                        'index' => $index,
                        'success' => false,
                        'error' => "No es posible exportar por problemas en SAP, intente más tarde",
                        'filePath' => $jsonFilePath
                    ];
                    $allExportsSuccessful = false;
                    continue;
                }

                $sapResponse = json_decode($response, true);
                if ($httpCode >= 400 || json_last_error() !== JSON_ERROR_NONE) {
                    error_log("SAP Error for index $index: HTTP $httpCode - $response");
                    $errorMsg = "No es posible exportar por problemas en SAP, intente más tarde";
                    if ($httpCode === 400 && strpos($response, 'El número de referencia YA EXISTE') !== false && !$forceExport) {
                        $results[] = [
                            'index' => $index,
                            'success' => false,
                            'error' => 'Esta liquidación ya ha sido exportada',
                            'filePath' => $jsonFilePath
                        ];
                    } else {
                        $results[] = [
                            'index' => $index,
                            'success' => false,
                            'error' => $errorMsg,
                            'sap_response' => $sapResponse,
                            'filePath' => $jsonFilePath
                        ];
                    }
                    $allExportsSuccessful = false;
                    continue;
                }

                // Update detail state to FINALIZADO on success
                $detalleLiquidacionModel->updateEstado($dl['id'], 'FINALIZADO');
                $this->auditoriaModel->createAuditoria($id, $dl['id'], $_SESSION['user_id'], 'EXPORTADO_A_SAP', 'Detalle exportado a SAP');

                $results[] = [
                    'index' => $index,
                    'success' => true,
                    'message' => 'Factura enviada a SAP exitosamente',
                    'filePath' => $jsonFilePath,
                    'sap_response' => $sapResponse
                ];
            } catch (Exception $e) {
                error_log("Error processing detalle_liquidacion index $index: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
                $results[] = [
                    'index' => $index,
                    'success' => false,
                    'error' => 'No es posible exportar por problemas en SAP, intente más tarde',
                    'filePath' => $jsonFilePath ?? null
                ];
                $allExportsSuccessful = false;
            }
        }

        // SAP Logout
        error_log("Intentando logout de SAP");
        $logoutResult = $this->logout_sap();
        if (!$logoutResult['success']) {
            error_log("SAP Logout Failed: {$logoutResult['error']}");
        }

        // Update liquidacion state if at least one export was successful
        $successCount = count(array_filter($results, fn($r) => $r['success']));
        if ($successCount > 0) {
            $liquidacionModel->updateEstado($id, 'FINALIZADO');
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Exportación completada: $successCount de " . count($pendingDetalles) . " documentos");
            $this->pdo->commit();
        } else {
            $this->pdo->rollBack();
            error_log("Exportación fallida para ID $id: ningún detalle exportado");
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(['error' => 'No se exportaron documentos válidos: revise los detalles'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Prepare response
        $response = [
            'success' => $successCount > 0,
            'message' => "$successCount de " . count($pendingDetalles) . " documentos procesados exitosamente",
            'results' => $results
        ];

        // Handle duplicate reference
        if (!$forceExport && array_filter($results, fn($r) => $r['error'] === 'Esta liquidación ya ha sido exportada')) {
            $this->pdo->rollBack();
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(['error' => 'Esta liquidación ya ha sido exportada'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;

    } catch (Exception $e) {
        error_log('Error exporting: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        if (isset($loginResult) && $loginResult['success']) {
            $this->logout_sap();
        }
        $this->pdo->rollBack();
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['error' => 'No es posible exportar por problemas en SAP, intente más tarde'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
    // Función auxiliar para mapear Tipo_Gasto a AccountCode
    
    private function getAccountCode($tipoGasto, $sociedad)
    {
        $cuentaMap = [
            'Combustible' => '630110002', // Validado desde factura existente
            'Alimentación' => '630110002',
            'Hospedaje' => '630110002',
            'Transporte' => '630110002',
            'Otros' => '630110002'
        ];
        return $cuentaMap[$tipoGasto] ?? '630110002'; // Cuenta por defecto
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

            if ($action === 'create') {
                $id_liquidacion = $_POST['id_liquidacion'] ?? null;
                if (empty($id_liquidacion)) {
                    error_log('Error: id_liquidacion missing in create action. POST data: ' . print_r($_POST, true));
                    throw new Exception('El ID de liquidación es obligatorio.');
                }

                if ($id_liquidacion != $id) {
                    error_log("Error: id_liquidacion ($id_liquidacion) does not match URL id ($id)");
                    throw new Exception('El ID de liquidación no coincide con la liquidación actual.');
                }

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
                $comentarios = $_POST['comentarios'] ?? null;
                $estado = 'EN_PROCESO';

                if ($tipo_documento === 'RECIBO') {
                    $nit_proveedor = null;
                } else {
                    $dpi = null;
                }

                if (empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                    error_log('Error: Missing or invalid required fields in create action. POST data: ' . print_r($_POST, true));
                    throw new Exception('Los campos obligatorios (tipo_documento, no_factura, nombre_proveedor, fecha, t_gasto, subtotal, total_factura) deben ser válidos.');
                }

                if (empty($id_centro_costo)) {
                    throw new Exception('El Centro de Costo es obligatorio.');
                }

                if (empty($id_cuenta_contable)) {
                    throw new Exception('La Cuenta Contable es obligatoria.');
                }

                // Validate id_cuenta_contable against id_centro_costo
                $stmt = $this->pdo->prepare("SELECT id, nombre FROM cuentas_contables WHERE id = :id_cuenta_contable AND id_centro_costo = :id_centro_costo AND estado = 'ACTIVO'");
                $stmt->execute([
                    ':id_cuenta_contable' => $id_cuenta_contable,
                    ':id_centro_costo' => $id_centro_costo,
                ]);
                $cuentaContable = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$cuentaContable) {
                    throw new Exception('La Cuenta Contable seleccionada no es válida para el Centro de Costo seleccionado o está inactiva.');
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
                $stmt->execute([$id_liquidacion, $no_factura]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("El número de factura '$no_factura' ya existe para esta liquidación.");
                }

                $iva = $iva ?? 0;
                $idp = $idp ?? 0;
                $inguat = $inguat ?? 0;

                $id_usuario = $liquidacion['id_usuario'];

                $detalleModel = new DetalleLiquidacion();
                $rutas_json = json_encode($rutas_archivos);
                if ($detalleModel->createDetalleLiquidacion($id_liquidacion, $tipo_documento, $no_factura, $nombre_proveedor, $nit_proveedor, $dpi, $fecha, $t_gasto, $subtotal, $total_factura, $estado, $id_centro_costo, $cantidad, $serie, $rutas_json, $iva, $idp, $inguat, $id_cuenta_contable, $tipo_combustible, $id_usuario, $comentarios)) {
                    $lastInsertId = $this->pdo->lastInsertId();
                    $this->auditoriaModel->createAuditoria($id_liquidacion, $lastInsertId, $_SESSION['user_id'], 'CREAR_DETALLE', "Factura creada: $no_factura para usuario ID $id_usuario");

                    $detallesActualizados = $this->detalleModel->getDetallesByLiquidacionId($id_liquidacion);
                    $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                    $this->liquidacionModel->updateMontoTotal($id_liquidacion, $monto_total);

                    $response = [
                        'message' => 'Factura creada correctamente',
                        'detalle_id' => $lastInsertId,
                        'rutas_archivos' => $rutas_archivos,
                        'monto_total' => $monto_total,
                        'cuenta_contable_nombre' => $cuentaContable['nombre']
                    ];
                } else {
                    $errorInfo = $this->pdo->errorInfo();
                    error_log('Error creating detalle_liquidacion. PDO Error: ' . print_r($errorInfo, true) . '. POST data: ' . print_r($_POST, true));
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
                $comentarios = $_POST['comentarios'] ?? null;

                if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                    throw new Exception('Los campos obligatorios deben ser válidos.');
                }

                if (empty($id_centro_costo)) {
                    throw new Exception('El Centro de Costo es obligatorio.');
                }

                if (empty($id_cuenta_contable)) {
                    throw new Exception('La Cuenta Contable es obligatoria.');
                }

                // Validate id_cuenta_contable against id_centro_costo
                $stmt = $this->pdo->prepare("SELECT id, nombre FROM cuentas_contables WHERE id = :id_cuenta_contable AND id_centro_costo = :id_centro_costo AND estado = 'ACTIVO'");
                $stmt->execute([
                    ':id_cuenta_contable' => $id_cuenta_contable,
                    ':id_centro_costo' => $id_centro_costo,
                ]);
                $cuentaContable = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$cuentaContable) {
                    throw new Exception('La Cuenta Contable seleccionada no es válida para el Centro de Costo seleccionado o está inactiva.');
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

                $existingRutas = json_decode($detalle['rutas_archivos'], true) ?? [];
                $rutas_archivo = array_merge($existingRutas, $rutas_archivos);
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

                // Perform the update
                $stmt = $this->pdo->prepare("
                    UPDATE detalle_liquidaciones
                    SET
                        tipo_documento = :tipo_documento,
                        no_factura = :no_factura,
                        nombre_proveedor = :nombre_proveedor,
                        nit_proveedor = :nit_proveedor,
                        dpi = :dpi,
                        fecha = :fecha,
                        t_gasto = :t_gasto,
                        p_unitario = :subtotal,
                        total_factura = :total_factura,
                        id_centro_costo = :id_centro_costo,
                        cantidad = :cantidad,
                        serie = :serie,
                        rutas_archivos = :rutas_json,
                        iva = :iva,
                        idp = :idp,
                        inguat = :inguat,
                        id_cuenta_contable = :id_cuenta_contable,
                        tipo_combustible = :tipo_combustible,
                        comentarios = :comentarios,
                        updated_at = NOW()
                    WHERE id = :detalle_id
                ");
                $stmt->execute([
                    ':tipo_documento' => $tipo_documento,
                    ':no_factura' => $no_factura,
                    ':nombre_proveedor' => $nombre_proveedor,
                    ':nit_proveedor' => $nit_proveedor,
                    ':dpi' => $dpi,
                    ':fecha' => $fecha,
                    ':t_gasto' => $t_gasto,
                    ':subtotal' => $subtotal,
                    ':total_factura' => $total_factura,
                    ':id_centro_costo' => $id_centro_costo,
                    ':cantidad' => $cantidad,
                    ':serie' => $serie,
                    ':rutas_json' => $rutas_json,
                    ':iva' => $iva,
                    ':idp' => $idp,
                    ':inguat' => $inguat,
                    ':id_cuenta_contable' => $id_cuenta_contable,
                    ':tipo_combustible' => $tipo_combustible,
                    ':comentarios' => $comentarios,
                    ':detalle_id' => $detalle_id,
                ]);

                $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE', "Facturaura actualizada: $no_factura");

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
            } elseif ($action === 'delete') {
                // Delete action remains unchanged
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
            error_log('Error in manageFacturas: ' . $e->getMessage() . '. POST data: ' . print_r($_POST, true));
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

                // Handle file uploads (matching manageFacturas style)
                $rutas_archivos = [];
                $uploadDir = '../Uploads/'; // Uppercase to match manageFacturas
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
                $maxFileSize = 5 * 1024 * 1024;

                $newFilesUploaded = false;
                if (isset($_FILES['archivos']) && !empty($_FILES['archivos']['name'][0])) {
                    $newFilesUploaded = true;
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
                            // Use backslashes and uppercase Uploads to match the desired format
                            $rutas_archivos[] = 'Uploads\\' . basename($filePath);
                        } elseif ($_FILES['archivos']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                            throw new Exception('Error al subir el archivo: ' . $name);
                        }
                    }
                }

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
                    $existing_rutas = $_POST['existing_rutas'] ?? '[]'; // New: Receive existing file paths from frontend
                
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
                
                    // Parse existing rutas from frontend
                    $existingRutasFromFrontend = json_decode($existing_rutas, true) ?? [];
                    // Get existing rutas from database
                    $existingRutasFromDB = json_decode($detalle['rutas_archivos'], true) ?? [];
                    // Merge with new uploads, ensuring no duplicates
                    if ($newFilesUploaded) {
                        $rutas_archivos = array_unique(array_merge($existingRutasFromFrontend, $rutas_archivos));
                    } else {
                        $rutas_archivos = array_unique($existingRutasFromFrontend);
                    }
                    // Remove any paths not in frontend or new uploads but present in DB
                    $rutas_archivos = array_filter($rutas_archivos, function ($ruta) use ($existingRutasFromDB, $rutas_archivos) {
                        return in_array($ruta, $rutas_archivos) || !in_array($ruta, $existingRutasFromDB);
                    });
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
                
                    // Update detalle with corrected parameter order
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
                            $iva,
                            $idp,
                            $inguat,
                            $id_cuenta_contable,
                            $cantidad,
                            $serie,
                            $rutas_json,
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
                // Clean up uploaded files on error (matching manageFacturas)
                if (!empty($rutas_archivos)) {
                    foreach ($rutas_archivos as $ruta) {
                        $filePath = '../' . str_replace('\\', '/', $ruta); // Normalize path for deletion
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
                error_log('Error en updateCorreccion: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        }

        // Load correction view (unchanged)
        $detalles = $this->detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'EN_CORRECCION');
        if (empty($detalles)) {
            header('Location: index.php?controller=liquidacion&action=list&mode=correccion');
            exit;
        }

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

    public function submitCorreccion($id){
    if (!isset($_SESSION['user_id'])) {
        error_log('submitCorreccion: No hay session user_id');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuario) {
        error_log("submitCorreccion: Usuario no encontrado para ID: {$_SESSION['user_id']}");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!$usuarioModel->tienePermiso($usuario, 'manage_correcciones')) {
        error_log("submitCorreccion: Usuario ID {$_SESSION['user_id']} no tiene permiso manage_correcciones. Rol: {$usuario['rol']}");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para enviar correcciones'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log('submitCorreccion: Método no permitido');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
    if (!$liquidacion) {
        error_log("submitCorreccion: Liquidación no encontrada para ID: $id");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(404);
        echo json_encode(['error' => 'Liquidación no encontrada'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $submitted_role = $input['originating_role'] ?? null;
    error_log("submitCorreccion: Liquidación ID $id, usuario ID {$_SESSION['user_id']} ({$usuario['rol']}), submitted_role: " . ($submitted_role ?? 'N/A'));

    // Normalize submitted role
    if ($submitted_role === 'CONTABILIDAD') {
        $normalized_role = 'CONTABILIDAD';
    } elseif (stripos($submitted_role, 'SUPERVISOR') !== false) {
        $normalized_role = 'SUPERVISOR';
    } else {
        error_log("submitCorreccion: Rol de origen no válido: " . ($submitted_role ?? 'N/A'));
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(400);
        echo json_encode(['error' => 'Rol de origen no válido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $this->pdo->beginTransaction();

        $detalles = $this->detalleModel->getDetallesByLiquidacionIdAndEstado($id, 'EN_CORRECCION');
        if (empty($detalles)) {
            error_log("submitCorreccion: No hay detalles EN_CORRECCION para liquidación ID $id");
            throw new Exception('No hay detalles en estado EN_CORRECCION para procesar.');
        }

        $updatedCount = 0;
        foreach ($detalles as $detalle) {
            $original_role = $detalle['original_role'] ?? 'CONTABILIDAD';
            $normalized_original_role = stripos($original_role, 'SUPERVISOR') !== false ? 'SUPERVISOR' : $original_role;

            if ($normalized_original_role !== $normalized_role) {
                error_log("submitCorreccion: Saltando detalle ID {$detalle['id']} (original_role: $original_role, normalized: $normalized_original_role) no coincide con $normalized_role");
                continue;
            }

            $nuevoEstado = $normalized_original_role === 'SUPERVISOR' ? 'PENDIENTE_AUTORIZACION' : 'PENDIENTE_REVISION_CONTABILIDAD';
            error_log("submitCorreccion: Actualizando detalle ID {$detalle['id']} a $nuevoEstado (original_role: $original_role)");
            $this->detalleModel->updateEstado($detalle['id'], $nuevoEstado);
            if ($normalized_original_role === 'SUPERVISOR') {
                $this->auditoriaModel->createAuditoria(
                    $id,
                    $detalle['id'],
                    $_SESSION['user_id'],
                    'CORRECTION_ENVIADA',
                    "Detalle corregido y enviado a $nuevoEstado para supervisor ID {$detalle['id_supervisor_correccion']}"
                );
            } else {
                $this->auditoriaModel->createAuditoria(
                    $id,
                    $detalle['id'],
                    $_SESSION['user_id'],
                    'CORRECTION_ENVIADA',
                    "Detalle corregido y enviado a $nuevoEstado para contador ID {$detalle['id_contador_correccion']}"
                );
            }
            $updatedCount++;
        }

        if ($updatedCount === 0) {
            error_log("submitCorreccion: No se actualizaron detalles para rol $normalized_role en liquidación ID $id");
            throw new Exception('No se procesaron detalles para el rol especificado.');
        }

        $this->auditoriaModel->createAuditoria(
            $id,
            null,
            $_SESSION['user_id'],
            'CORRECCIONES_ENVIADAS',
            "Correcciones enviadas a $normalized_role, liquidación sin cambio de estado"
        );

        $this->pdo->commit();
        error_log("submitCorreccion: $updatedCount detalle(s) actualizados para liquidación ID $id a rol $normalized_role");
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['message' => "Correcciones enviadas correctamente a $normalized_role"], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $this->pdo->rollBack();
        error_log('submitCorreccion: Error al enviar correcciones para liquidación ID $id: ' . $e->getMessage());
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(500);
        echo json_encode(['error' => 'Error al enviar correcciones: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
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
    
        // if ($liquidacion['estado'] !== 'EN_PROCESO') {
        //     header('Content-Type: application/json');
        //     http_response_code(400);
        //     echo json_encode(['error' => 'Solo se pueden finalizar liquidaciones en estado EN_PROCESO']);
        //     exit;
        // }
    
        // Check if the liquidation has any details (facturas)
        $detalleModel = new DetalleLiquidacion();
        $detalles = $detalleModel->getDetallesByLiquidacionId($id);
        if (empty($detalles)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'No se puede finalizar una liquidación vacía.']);
            exit;
        }
    
        // Get the supervisor ID from the associated caja_chica
        $stmt = $this->pdo->prepare("
            SELECT id_supervisor 
            FROM cajas_chicas 
            WHERE id = ?
        ");
        $stmt->execute([$liquidacion['id_caja_chica']]);
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$caja || !$caja['id_supervisor']) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'No se encontró un supervisor asignado en la caja chica asociada.']);
            exit;
        }
        $supervisorId = $caja['id_supervisor'];
    
        // Validate that the supervisor exists
        $supervisor = $usuarioModel->getUsuarioById($supervisorId);
        if (!$supervisor) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El supervisor asignado no existe.']);
            exit;
        }
    
        // Check if the user is a valid supervisor
        $supervisores = array_merge(
            $usuarioModel->getUsuariosBySupervisorRole(),
            $usuarioModel->getUsuariosByContadorRole()
        );
        $isSupervisor = array_reduce($supervisores, function($carry, $user) use ($supervisorId) {
            return $carry || $user['id'] == $supervisorId;
        }, false);
    
        if (!$isSupervisor || !$usuarioModel->tienePermiso($supervisor, 'autorizar_liquidaciones')) {
            error_log("Supervisor ID $supervisorId no válido o sin permiso 'autorizar_liquidaciones'. Rol: " . $supervisor['rol']);
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El supervisor asignado no es válido o no tiene permisos.']);
            exit;
        }
    
        try {
            // Update the liquidation state and assign it to the supervisor
            $liquidacionModel->updateEstado($id, 'PENDIENTE_AUTORIZACION', $supervisorId);
    
            // Log the action in the audit trail, including the supervisor's details
            $auditMessage = sprintf(
                'Liquidación finalizada por encargado y asignada al supervisor %s (%s)',
                $supervisor['nombre'],
                $supervisor['email']
            );
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'FINALIZADO', $auditMessage);
    
            header('Content-Type: application/json');
            echo json_encode([
                'message' => 'Liquidación finalizada y asignada al supervisor correctamente',
                'supervisor' => [
                    'id' => $supervisorId,
                    'nombre' => $supervisor['nombre'],
                    'email' => $supervisor['email']
                ]
            ]);
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

        // Initialize cuenta contable model
        $cuentaContableModel = new CuentaContable();
        $pdo = Database::getInstance()->getPdo();
        $cuentas_array = [];

        // Fetch accounts from local database first
        $local_cuentas = $cuentaContableModel->getCuentasByCentroCosto($id_centro_costo, 'ACTIVO');
        foreach ($local_cuentas as $cuenta) {
            $cuentas_array[] = ['id' => $cuenta['id'], 'nombre' => $cuenta['nombre']];
        }

        // Fetch accounts from HANA
        $sociedad = $_SESSION['sociedad'] ?? 'GT_AGROCENTRO_2016';
        $cuenta = $centro['tipo'] ?? 5;
        $hana_cuentas = $this->ctrObtenerCuentas($sociedad, $cuenta);

        if ($hana_cuentas && $hana_cuentas !== 'sin_datos') {
            $cuentas_list = explode('|', trim($hana_cuentas, '|'));
            foreach ($cuentas_list as $cuenta_item) {
                if (!empty($cuenta_item)) {
                    list($code, $name) = explode('-', $cuenta_item, 2);
                    $name = mb_convert_encoding($name, 'UTF-8', mb_detect_encoding($name));

                    // Check if the account exists with the same codigo_cuenta for any id_centro_costo
                    $stmt = $pdo->prepare("
                        SELECT id, nombre, id_centro_costo 
                        FROM cuentas_contables 
                        WHERE codigo_cuenta = ?
                    ");
                    $stmt->execute([$code]);
                    $existingCuenta = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$existingCuenta) {
                        // Insert new account
                        $stmt = $pdo->prepare("
                            INSERT INTO cuentas_contables (nombre, descripcion, estado, id_centro_costo, codigo_cuenta)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $descripcion = '';
                        $estado = 'ACTIVO';
                        $stmt->execute([$name, $descripcion, $estado, $id_centro_costo, $code]);
                        $id = $pdo->lastInsertId();
                        $cuentas_array[] = ['id' => $id, 'nombre' => $name];
                        error_log("Inserted new account: codigo_cuenta=$code, nombre=$name, id_centro_costo=$id_centro_costo");
                    } elseif ($existingCuenta['id_centro_costo'] == $id_centro_costo && $existingCuenta['nombre'] == $name) {
                        // Account exists for this id_centro_costo and matches name, include it
                        if (!in_array(['id' => $existingCuenta['id'], 'nombre' => $existingCuenta['nombre']], $cuentas_array)) {
                            $cuentas_array[] = ['id' => $existingCuenta['id'], 'nombre' => $existingCuenta['nombre']];
                        }
                    } else {
                        // Account exists for a different id_centro_costo, log and skip
                        error_log("Skipping duplicate account: codigo_cuenta=$code exists for id_centro_costo={$existingCuenta['id_centro_costo']}, requested id_centro_costo=$id_centro_costo");
                        continue;
                    }
                }
            }
        }

        // Remove duplicates
        $cuentas_array = array_values(array_unique($cuentas_array, SORT_REGULAR));

        // Log the response
        error_log("getCuentasContables: id_centro_costo=$id_centro_costo, cuentas=" . json_encode($cuentas_array));

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($cuentas_array);
    } catch (Exception $e) {
        error_log("Error in getCuentasContables: id_centro_costo=$id_centro_costo, error=" . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener cuentas contables: ' . $e->getMessage()]);
    }
    exit;
}

    public function assignContador($id)
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