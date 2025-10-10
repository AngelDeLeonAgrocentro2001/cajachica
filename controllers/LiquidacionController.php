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
require_once '../models/Usuario.php';
require_once '../models/DteModel.php';
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
    private $dteModel;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
        $this->usuarioModel = new Usuario();
        $this->liquidacionModel = new Liquidacion();
        $this->detalleModel = new DetalleLiquidacion();
        $this->cajaChicaModel = new CajaChica();
        $this->tipoDocumentoModel = new TipoDocumento();
        $this->tipoGastoModel = new TipoGasto();
        $this->centroCostoModel = new CentroCosto();
        $this->cuentaContableModel = new CuentaContable();
        $this->auditoriaModel = new Auditoria();
        $this->dteModel = new DteModel();
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
    error_log("ctrObtenerCuentas called with Sociedad=$Sociedad, cuenta=$cuenta");
    
    if ($Sociedad != 'EC_AGROCENTRO_2015') {
        $segmento = '';
        $segmento1 = '';
        $segmento2 = '';
        $sub = 2;

        // Map cuenta to segmento
        if ($cuenta == 1) {
            $segmento = 64;
            $segmento1 = 65;
            $sub = 2;
        } elseif ($cuenta == 2) {
            $segmento = 61;
            $segmento1 = 65;
            $sub = 2;
        } elseif ($cuenta == 3) {
            $segmento = 62;
            $segmento1 = 65;
            $sub = 2;
        } elseif ($cuenta == 4) {
            $segmento = 63;
            $segmento1 = 65;
            $sub = 2;
        } elseif ($cuenta == 5) {
            $segmento = 52;
            $sub = 2;
        } elseif ($cuenta == 6) {
            $segmento = 52;
            $sub = 2;
        } elseif ($cuenta == 7) {
            $segmento = 52;
            $sub = 2;
        } elseif ($cuenta == 8) {
            $segmento = 52;
            $sub = 2;
        } elseif ($cuenta == 9) {
            $segmento = 52;
            $sub = 2;
        } elseif ($cuenta == 10) {
            $segmento = 52;
            $sub = 2;
        } elseif ($cuenta == 'DB01') {
            // Handle DB01 specifically (adjust segment as needed)
            $segmento = 52; // Example: Use segment 52 or consult SAP HANA schema
            $sub = 2;
            error_log("Handling cuenta=DB01 with segmento=$segmento, sub=$sub");
        } else {
            // Fallback for unknown cuenta values
            $segmento = 52;
            $sub = 2;
            error_log("Unknown cuenta value '$cuenta', using fallback segmento=$segmento");
        }

        if ($segmento == 81) {
            $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND (LEFT("AcctCode", ' . $sub . ') = \'' . $segmento . '\' OR LEFT("AcctCode", ' . $sub . ') = \'' . $segmento1 . '\' OR LEFT("AcctCode", ' . $sub . ') = \'' . $segmento2 . '\') OR "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) IN (\'65\', \'72\', \'83\', \'74\')';
        } elseif ($cuenta == 7) {
            $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) IN (\'51\', \'52\', \'54\', \'65\', \'72\', \'83\', \'74\')';
        } else {
            $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", ' . $sub . ') = \'' . $segmento . '\' OR "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) IN (\'65\', \'72\', \'83\', \'74\')';
        }
    } else {
        $qry = 'SELECT "AcctCode", "AcctName" FROM ' . $Sociedad . '.OACT WHERE "Levels" = 5 AND (("ValidFor" = \'Y\' AND "FrozenFor" = \'N\') OR ("ValidFor" = \'N\' AND "FrozenFor" = \'N\')) AND LEFT("AcctCode", 2) = \'81\'';
    }

    error_log("Query constructed: $qry");
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
                error_log("Cuenta obtenida (sin procesar): " . $proveedor->AcctCode . '-' . $proveedor->AcctName);
                $acctName = $proveedor->AcctName;
                if (!mb_check_encoding($acctName, 'UTF-8')) {
                    $acctName = mb_convert_encoding($acctName, 'UTF-8', 'ISO-8859-1');
                    error_log("Nombre de cuenta convertido a UTF-8: " . $proveedor->AcctCode . '-' . $acctName);
                }
                $json .= "|" . $proveedor->AcctCode . '-' . $acctName;
                error_log("Cuenta procesada: " . $proveedor->AcctCode . '-' . $acctName);
            }
            odbc_free_result($prov);
        } else {
            $errorMsg = odbc_errormsg($conexion);
            error_log("Error al ejecutar la consulta HANA: " . $errorMsg);
            throw new Exception("Error al ejecutar la consulta en la base de datos HANA: " . $errorMsg);
        }
        odbc_close($conexion);
        if (empty($json)) {
            error_log("No se encontraron cuentas en HANA para la consulta: " . $query);
            return 'sin_datos';
        }
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
    $isAutorizarMode = $urlParams === 'autorizar';

    // Fetch role details to check name and description
    $isSupervisorRole = false;
    $isContabilidadRole = false;
    $isEncargadoRole = false;
    
    if ($id_rol) {
        $rolModel = new Role();
        $roleData = $rolModel->getRolById($id_rol);
        if ($roleData) {
            $roleName = strtoupper($roleData['nombre'] ?? '');
            $roleDescription = strtoupper($roleData['descripcion'] ?? '');
            
            $isSupervisorRole = strpos($roleName, 'SUPERVISOR') !== false || 
                               strpos($roleDescription, 'SUPERVISOR') !== false;
            $isContabilidadRole = $roleName === 'CONTABILIDAD' ||
                                $id_rol == 4 ||
                                strpos($roleName, 'CONTADOR') !== false ||
                                strpos($roleName, 'CONTABILIDAD') !== false ||
                                strpos($roleDescription, 'CONTADOR') !== false ||
                                strpos($roleDescription, 'CONTABILIDAD') !== false;
            
            // Detectar rol de encargado
            $isEncargadoRole = strpos($roleName, 'ENCARGADO') !== false || 
                              strpos($roleDescription, 'ENCARGADO') !== false ||
                              strpos($roleName, 'CAJA_CHICA') !== false ||
                              strpos($roleDescription, 'CAJA_CHICA') !== false ||
                              strpos($roleName, 'ENCARGADO_CAJA_CHICA') !== false;
        }
        error_log("Usuario ID: {$_SESSION['user_id']}, id_rol: {$id_rol}, es rol supervisor: " . ($isSupervisorRole ? 'SÍ' : 'NO') . ", es rol contabilidad: " . ($isContabilidadRole ? 'SÍ' : 'NO') . ", es rol encargado: " . ($isEncargadoRole ? 'SÍ' : 'NO'));
    }

    // Fetch liquidations based on role
    $liquidaciones = [];
    $supervisorLiquidaciones = [];
    $contabilidadLiquidaciones = [];
    $encargadoLiquidaciones = [];

    // 1. Si es supervisor O tiene rol mixto con supervisor, obtener liquidaciones para supervisar
    if ($isSupervisorRole || ($isEncargadoRole && $isAutorizarMode)) {
        if ($isAutorizarMode) {
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

    // 2. MODIFICACIÓN: Si es contabilidad O tiene rol mixto con contabilidad, obtener TODAS las liquidaciones
    if ($isContabilidadRole || ($isEncargadoRole && $isRevisarMode)) {
        // OBTENER TODAS LAS LIQUIDACIONES, NO SOLO LAS ASIGNADAS
        $contabilidadLiquidaciones = $this->liquidacionModel->getAllLiquidaciones();
        error_log('TODAS las liquidaciones obtenidas para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($contabilidadLiquidaciones) . ' registros');
        
        // Apply state filter only in revisar mode
        if ($isRevisarMode) {
            $contabilidadLiquidaciones = array_filter($contabilidadLiquidaciones, function ($liquidacion) {
                return in_array($liquidacion['estado'], [
                    'PENDIENTE_REVISION_CONTABILIDAD',
                    'FINALIZADO',
                    'RECHAZADO_POR_CONTABILIDAD',
                    'EN_PROCESO'
                ]);
            });
            error_log('Liquidaciones filtradas por estado para revisar: ' . count($contabilidadLiquidaciones) . ' registros');
        }
        
        // Remove duplicates
        $contabilidadLiquidaciones = array_values(array_reduce($contabilidadLiquidaciones, function ($carry, $liquidacion) {
            $carry[$liquidacion['id']] = $liquidacion;
            return $carry;
        }, []));
        error_log('Liquidaciones finales para CONTABILIDAD (ID: ' . $_SESSION['user_id'] . '): ' . count($contabilidadLiquidaciones) . ' registros');
    }

    // 3. Si es encargado, obtener sus propias liquidaciones (solo si no está en modo autorizar/revisar)
    if ($isEncargadoRole && !$isAutorizarMode && !$isRevisarMode) {
        $encargadoLiquidaciones = $this->liquidacionModel->getAllLiquidaciones();
        error_log('Liquidaciones obtenidas para ENCARGADO (ID: ' . $_SESSION['user_id'] . '): ' . count($encargadoLiquidaciones) . ' registros');

        $encargadoLiquidaciones = array_filter($encargadoLiquidaciones, function ($liquidacion) use ($usuario) {
            return $liquidacion['id_usuario'] == $usuario['id'];
        });
        error_log('Liquidaciones filtradas para usuario ENCARGADO: ' . count($encargadoLiquidaciones) . ' registros');
    }

    // 4. PARA USUARIOS MIXTOS: Obtener liquidaciones adicionales según sus roles
    if (($isContabilidadRole || $isSupervisorRole) && !$isEncargadoRole && !$isAutorizarMode && !$isRevisarMode) {
        $misLiquidaciones = $this->liquidacionModel->getAllLiquidaciones($_SESSION['user_id']);
        error_log('Liquidaciones propias para usuario mixto (ID: ' . $_SESSION['user_id'] . '): ' . count($misLiquidaciones) . ' registros');
        
        // Inicializar el array si no existe
        if (!isset($encargadoLiquidaciones)) {
            $encargadoLiquidaciones = [];
        }
        $encargadoLiquidaciones = array_merge($encargadoLiquidaciones, $misLiquidaciones);
    }

    // COMBINAR LIQUIDACIONES - PRIORIZAR SEGÚN MODO
    if ($isAutorizarMode) {
        // En modo autorizar, mostrar solo liquidaciones de supervisor (incluyendo usuarios mixtos)
        $liquidaciones = $supervisorLiquidaciones;
        error_log('Modo autorizar: mostrando ' . count($liquidaciones) . ' liquidaciones de supervisor');
    } elseif ($isRevisarMode) {
        // En modo revisar, mostrar solo liquidaciones de contabilidad (incluyendo usuarios mixtos)
        $liquidaciones = $contabilidadLiquidaciones;
        error_log('Modo revisar: mostrando ' . count($liquidaciones) . ' liquidaciones de contabilidad');
    } else {
        // Modo normal: combinar todas las liquidaciones según roles
        if ($isEncargadoRole) {
            // Para usuarios encargados (puros o mixtos), mostrar sus propias liquidaciones
            $liquidaciones = array_merge($encargadoLiquidaciones, $supervisorLiquidaciones, $contabilidadLiquidaciones);
            error_log('Liquidaciones combinadas para ENCARGADO (mixto): ' . count($liquidaciones) . ' registros');
        } else {
            // Para otros roles, mostrar según sus permisos
            $liquidaciones = array_merge($supervisorLiquidaciones, $contabilidadLiquidaciones);
            error_log('Liquidaciones combinadas para NO ENCARGADO: ' . count($liquidaciones) . ' registros');
        }
        
        // PARA USUARIOS MIXTOS: Asegurar que ven sus propias liquidaciones incluso si no son encargados puros
        if (($isContabilidadRole || $isSupervisorRole) && !$isEncargadoRole) {
            $misLiquidaciones = $this->liquidacionModel->getAllLiquidaciones($_SESSION['user_id']);
            $liquidaciones = array_merge($liquidaciones, $misLiquidaciones);
            error_log('Usuario mixto (no encargado): añadiendo ' . count($misLiquidaciones) . ' liquidaciones propias');
        }
    }

    // Remove duplicates by ID
    $liquidaciones = array_values(array_reduce($liquidaciones, function ($carry, $liquidacion) {
        $carry[$liquidacion['id']] = $liquidacion;
        return $carry;
    }, []));
    error_log('Liquidaciones combinadas (tras eliminar duplicados): ' . count($liquidaciones) . ' registros');

    // For non-supervisor/non-contabilidad/non-encargado roles, fetch liquidations by id_usuario
    if (!$isSupervisorRole && !$isContabilidadRole && !$isEncargadoRole) {
        $liquidaciones = $this->liquidacionModel->getAllLiquidaciones();
        error_log('Liquidaciones obtenidas: ' . count($liquidaciones) . ' registros para el usuario ID ' . $_SESSION['user_id']);

        $liquidaciones = array_filter($liquidaciones, function ($liquidacion) use ($usuario) {
            return $liquidacion['id_usuario'] == $usuario['id'];
        });
        error_log('Liquidaciones filtradas para usuario no SUPERVISOR/CONTABILIDAD/ENCARGADO: ' . count($liquidaciones) . ' registros');
    }

    foreach ($liquidaciones as &$liquidacion) {
        $liquidacion['detalles'] = $this->detalleModel->getDetallesByLiquidacionId($liquidacion['id']);
    }
    unset($liquidacion);

    // Fetch corrected details based on role
    $correctedDetalles = [];
    if ($isSupervisorRole && $isAutorizarMode) {
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
            'isContabilidadLike' => $isContabilidadRole,
            'isSupervisorLike' => $isSupervisorRole,
            'isEncargadoLike' => $isEncargadoRole,
            'userRole' => $usuario['rol']
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        error_log('Rendering liquidaciones view');
        require '../views/liquidaciones/list.html';
    }

    exit;
}

public function createLiquidacion() {
    if (!isset($_SESSION['user_id'])) {
        error_log('Error: No hay session user_id en createLiquidacion');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuario) {
        error_log('Usuario no encontrado para ID: ' . $_SESSION['user_id']);
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
        error_log('Error: No tienes permiso para crear liquidaciones');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para crear liquidaciones'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $this->pdo->beginTransaction();

            // Obtener datos del formulario
            $fecha_creacion = $_POST['fecha_creacion'] ?? '';
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;
            $monto_total = 0; // Inicializamos en 0, se calculará después si hay detalles
            $estado = 'EN_PROCESO';
            $id_usuario = $_SESSION['user_id'];

            // Validar campos obligatorios
            if (empty($fecha_creacion)) {
                throw new Exception('El campo fecha_creacion es obligatorio y debe ser válido.');
            }

            // Validar fechas
            $fechaActual = new DateTime();
            if (!empty($fecha_creacion) && new DateTime($fecha_creacion) > $fechaActual) {
                throw new Exception('La fecha de creación no puede ser posterior a la fecha actual.');
            }
            if (!empty($fecha_inicio) && new DateTime($fecha_inicio) > $fechaActual) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha actual.');
            }
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $fechaInicioDate = new DateTime($fecha_inicio);
                $fechaFinDate = new DateTime($fecha_fin);
                if ($fechaInicioDate > $fechaFinDate) {
                    throw new Exception('La fecha de inicio no puede ser mayor que la fecha de fin.');
                }
            }

            // Obtener id_caja_chica desde la tabla usuarios
            if (empty($usuario['id_caja_chica'])) {
                throw new Exception('No se encontró una caja chica activa asociada al usuario.');
            }
            $id_caja_chica = $usuario['id_caja_chica'];

            // Verificar que la caja chica existe y está activa
            $stmt = $this->pdo->prepare("
                SELECT id, id_supervisor, id_contador 
                FROM cajas_chicas 
                WHERE id = ? AND estado = 'ACTIVA'
            ");
            $stmt->execute([$id_caja_chica]);
            $caja = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$caja) {
                throw new Exception('La caja chica asociada al usuario no está activa o no existe.');
            }

            // Crear la liquidación
            $stmt = $this->pdo->prepare("
                INSERT INTO liquidaciones (id_caja_chica, fecha_creacion, fecha_inicio, fecha_fin, monto_total, estado, id_usuario, id_supervisor, id_contador)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([
                $id_caja_chica,
                $fecha_creacion,
                $fecha_inicio,
                $fecha_fin,
                $monto_total,
                $estado,
                $id_usuario,
                $caja['id_supervisor'],
                $caja['id_contador']
            ]);

            if (!$result) {
                throw new Exception('Error al crear la liquidación en la base de datos.');
            }

            $lastInsertId = $this->pdo->lastInsertId();

            // Registrar auditoría
            $this->auditoriaModel->createAuditoria(
                $lastInsertId,
                null,
                $_SESSION['user_id'],
                'CREADO',
                "Liquidación creada por usuario ID: {$id_usuario}, Caja Chica ID: {$id_caja_chica}"
            );

            $this->pdo->commit();
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(201);
            echo json_encode(['message' => 'Liquidación creada con éxito', 'id' => $lastInsertId], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error en createLiquidacion: ' . $e->getMessage());
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Renderizar formulario si no es POST
    $cajasChicas = (new CajaChica())->getAllCajasChicas();
    ob_start();
    require '../views/liquidaciones/form.html';
    $html = ob_get_clean();
    echo $html;
    exit;
}

public function updateLiquidacion($id) {
    if (!isset($_SESSION['user_id'])) {
        error_log('Error: No hay session user_id en updateLiquidacion');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
    if (!$usuario) {
        error_log('Usuario no encontrado para ID: ' . $_SESSION['user_id']);
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (
        !$usuarioModel->tienePermiso($usuario, 'create_liquidaciones') &&
        !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones') &&
        !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')
    ) {
        error_log('Error: No tienes permiso para actualizar liquidaciones');
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para actualizar liquidaciones'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $this->pdo->beginTransaction();

            // Obtener datos del formulario
            $fecha_creacion = $_POST['fecha_creacion'] ?? '';
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;
            $estado = $_POST['estado'] ?? 'EN_PROCESO';
            $id_usuario = $_SESSION['user_id'];

            // Obtener la liquidación actual
            $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
            if (!$liquidacion) {
                throw new Exception('Liquidación no encontrada');
            }

            // Verificar permisos para editar
            if ($usuario['id'] != $liquidacion['id_usuario'] && !$usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') && !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
                throw new Exception('No tienes permiso para editar esta liquidación');
            }

            // Mantener el monto_total actual
            $monto_total = $liquidacion['monto_total'];

            // Obtener id_caja_chica desde la tabla usuarios o de la liquidación existente
            $id_caja_chica = $liquidacion['id_caja_chica']; // Usar el id_caja_chica existente por defecto
            if ($usuario['id'] == $liquidacion['id_usuario']) {
                // Si el usuario es el creador, verificar id_caja_chica desde usuarios
                if (empty($usuario['id_caja_chica'])) {
                    throw new Exception('No se encontró una caja chica activa asociada al usuario.');
                }
                $id_caja_chica = $usuario['id_caja_chica'];

                // Verificar que la caja chica existe y está activa
                $stmt = $this->pdo->prepare("
                    SELECT id, id_supervisor, id_contador 
                    FROM cajas_chicas 
                    WHERE id = ? AND estado = 'ACTIVA'
                ");
                $stmt->execute([$id_caja_chica]);
                $caja = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$caja) {
                    throw new Exception('La caja chica asociada al usuario no está activa o no existe.');
                }
                $id_supervisor = $caja['id_supervisor'];
                $id_contador = $caja['id_contador'];
            } else {
                // Si es supervisor o contador, mantener id_supervisor e id_contador de la liquidación
                $id_supervisor = $liquidacion['id_supervisor'];
                $id_contador = $liquidacion['id_contador'];
            }

            // Validar estado según rol
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

            // Validar fechas
            if (empty($fecha_creacion)) {
                throw new Exception('El campo fecha_creacion es obligatorio.');
            }
            $fechaActual = new DateTime();
            if (!empty($fecha_creacion) && new DateTime($fecha_creacion) > $fechaActual) {
                throw new Exception('La fecha de creación no puede ser posterior a la fecha actual.');
            }
            if (!empty($fecha_inicio) && new DateTime($fecha_inicio) > $fechaActual) {
                throw new Exception('La fecha de inicio no puede ser posterior a la fecha actual.');
            }
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $fechaInicioDate = new DateTime($fecha_inicio);
                $fechaFinDate = new DateTime($fecha_fin);
                if ($fechaInicioDate > $fechaFinDate) {
                    throw new Exception('La fecha de inicio no puede ser mayor que la fecha de fin.');
                }
            }

            // Actualizar la liquidación
            $stmt = $this->pdo->prepare("
                UPDATE liquidaciones
                SET id_caja_chica = ?, fecha_creacion = ?, fecha_inicio = ?, fecha_fin = ?, monto_total = ?, estado = ?, id_supervisor = ?, id_contador = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $id_caja_chica,
                $fecha_creacion,
                $fecha_inicio,
                $fecha_fin,
                $monto_total,
                $estado,
                $id_supervisor,
                $id_contador,
                $id
            ]);

            if (!$result) {
                throw new Exception('Error al actualizar la liquidación en la base de datos.');
            }

            // Registrar auditoría
            $this->auditoriaModel->createAuditoria(
                $id,
                null,
                $_SESSION['user_id'],
                'ACTUALIZADO',
                "Liquidación actualizada por usuario ID: {$id_usuario}, Caja Chica ID: {$id_caja_chica}, Estado: {$estado}"
            );

            $this->pdo->commit();
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['message' => 'Liquidación actualizada con éxito'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Error en updateLiquidacion: ' . $e->getMessage());
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Renderizar formulario si no es POST
    $liquidacion = $this->liquidacionModel->getLiquidacionById($id);
    if (!$liquidacion) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(404);
        echo json_encode(['error' => 'Liquidación no encontrada'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cajasChicas = (new CajaChica())->getAllCajasChicas();
    ob_start();
    require '../views/liquidaciones/form.html';
    $html = ob_get_clean();
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

        // Delete the liquidation
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

public function autorizar($id) {
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

    // Obtener información detallada del rol
    $rol = strtoupper($usuario['rol'] ?? 'INVALID_ROLE');
    $id_rol = $usuario['id_rol'] ?? null;
    
    $isSupervisorRole = false;
    $isContabilidadRole = false;
    $isEncargadoRole = false;
    
    if ($id_rol) {
        $rolModel = new Role();
        $roleData = $rolModel->getRolById($id_rol);
        if ($roleData) {
            $roleName = strtoupper($roleData['nombre'] ?? '');
            $roleDescription = strtoupper($roleData['descripcion'] ?? '');
            
            $isSupervisorRole = strpos($roleName, 'SUPERVISOR') !== false || 
                               strpos($roleDescription, 'SUPERVISOR') !== false;
            $isContabilidadRole = $roleName === 'CONTABILIDAD' ||
                                $id_rol == 4 ||
                                strpos($roleName, 'CONTADOR') !== false ||
                                strpos($roleName, 'CONTABILIDAD') !== false ||
                                strpos($roleDescription, 'CONTADOR') !== false ||
                                strpos($roleDescription, 'CONTABILIDAD') !== false;
            
            // Detectar rol de encargado
            $isEncargadoRole = strpos($roleName, 'ENCARGADO') !== false || 
                              strpos($roleDescription, 'ENCARGADO') !== false ||
                              strpos($roleName, 'CAJA_CHICA') !== false ||
                              strpos($roleDescription, 'CAJA_CHICA') !== false ||
                              strpos($roleName, 'ENCARGADO_CAJA_CHICA') !== false;
        }
    }

    // VERIFICAR PERMISOS FLEXIBLES PARA ROLES MIXTOS
$tienePermisoAutorizar = false;

// Si es supervisor (puro o mixto)
if ($isSupervisorRole) {
    $tienePermisoAutorizar = $usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones');
}

// Si es contabilidad (puro o mixto) y está en modo revisar
if ($isContabilidadRole && isset($_GET['mode']) && $_GET['mode'] === 'revisar') {
    $tienePermisoAutorizar = $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones');
}

// Si es encargado con rol mixto (encargado + supervisor)
if ($isEncargadoRole && $isSupervisorRole) {
    $tienePermisoAutorizar = $usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones');
}

// Si es encargado con rol mixto (encargado + contabilidad) y está en modo revisar
if ($isEncargadoRole && $isContabilidadRole && isset($_GET['mode']) && $_GET['mode'] === 'revisar') {
    $tienePermisoAutorizar = $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones');
}

    if (!$tienePermisoAutorizar) {
        error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para autorizar/revisar liquidaciones. Rol: " . ($usuario['rol'] ?? 'N/A') . 
                 ", es supervisor: " . ($isSupervisorRole ? 'SÍ' : 'NO') . 
                 ", es contabilidad: " . ($isContabilidadRole ? 'SÍ' : 'NO') . 
                 ", es encargado: " . ($isEncargadoRole ? 'SÍ' : 'NO'));
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para autorizar/revisar liquidaciones'], JSON_UNESCAPED_UNICODE);
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

    // Determinar el estado esperado basado en el rol REAL del usuario
    $expectedEstado = '';
    if ($isSupervisorRole || ($isEncargadoRole && $isSupervisorRole)) {
        $expectedEstado = 'PENDIENTE_AUTORIZACION';
    } elseif ($isContabilidadRole || ($isEncargadoRole && $isContabilidadRole)) {
        $expectedEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
    }

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
    $centroCostoModel = new CentroCosto();
    foreach ($detalles as &$detalle) {
        $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
        $centroCosto = $centroCostoModel->getCentroCostoById($detalle['id_centro_costo']);
        $detalle['cuenta_contable_nombre'] = htmlspecialchars($cuentaContable['nombre'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $detalle['nombre_centro_costo'] = htmlspecialchars($centroCosto['nombre'] . ' / ' . $centroCosto['codigo'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $detalle['subtotal'] = floatval($detalle['p_unitario'] ?? $detalle['total_factura']);
        $detalle['iva'] = floatval($detalle['iva'] ?? 0);
        $detalle['idp'] = floatval($detalle['idp'] ?? 0);
        $detalle['inguat'] = floatval($detalle['inguat'] ?? 0);
        $detalle['total_factura'] = floatval($detalle['total_factura']);
        $detalle['tipo_documento'] = htmlspecialchars($detalle['tipo_documento'] ?? 'FACTURA', ENT_QUOTES, 'UTF-8');
        $detalle['no_factura'] = htmlspecialchars($detalle['no_factura'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nombre_proveedor'] = htmlspecialchars($detalle['nombre_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nit_proveedor'] = htmlspecialchars($detalle['nit_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['dpi'] = htmlspecialchars($detalle['dpi'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cantidad'] = htmlspecialchars($detalle['cantidad'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['serie'] = htmlspecialchars($detalle['serie'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['t_gasto'] = htmlspecialchars($detalle['t_gasto'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['tipo_combustible'] = htmlspecialchars($detalle['tipo_combustible'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['fecha'] = htmlspecialchars($detalle['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['estado'] = htmlspecialchars($detalle['estado'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['comentarios'] = htmlspecialchars($detalle['comentarios'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['correccion_comentario'] = htmlspecialchars($detalle['correccion_comentario'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['rutas_archivos'] = $detalle['rutas_archivos'] ?? '';
        $detalle['grupo_id'] = htmlspecialchars($detalle['grupo_id'] ?? '0', ENT_QUOTES, 'UTF-8');
    }
    unset($detalle);

    $cajaChicaModel = new CajaChica();
    $cajaChica = $cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
    $nombre_caja_chica = $cajaChica['nombre'] ?? 'N/A';

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
                $processedGrupoIds = [];
        
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
                    
                    $grupoId = $detalle['grupo_id'] ?? '0';
                    $estado = $detalle['estado'] ?? '';
                    
                    // Saltar si ya está en corrección
                    if ($estado === 'EN_CORRECCION') {
                        error_log("Detalle ID $detalleId ya está en EN_CORRECCION");
                        $skipReasons[$detalleId] = "Ya en corrección";
                        continue;
                    }
                    
                    // Para grupos, procesar todos los detalles del grupo
                    if ($grupoId !== '0' && $grupoId !== 0) {
                        if (in_array($grupoId, $processedGrupoIds)) {
                            error_log("Grupo ID $grupoId ya procesado");
                            continue;
                        }
                        $processedGrupoIds[] = $grupoId;
        
                        // Obtener todos los detalles del grupo
                        $detallesGrupo = $detalleModel->getDetallesByGrupoId($grupoId, $id);
                        
                        foreach ($detallesGrupo as $grupoDetalle) {
                            $grupoDetalleId = intval($grupoDetalle['id']);
                            $grupoEstado = $grupoDetalle['estado'] ?? '';
                            
                            if ($grupoEstado === 'EN_CORRECCION') {
                                error_log("Detalle ID $grupoDetalleId ya está en EN_CORRECCION");
                                $skipReasons[$grupoDetalleId] = "Ya en corrección";
                                continue;
                            }
                            
                            $comment = $correccionComentarios[$detalleId] ?? '';
                            if (empty($comment)) {
                                error_log("Comentario vacío para detalle ID $detalleId");
                                throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                            }
                            
                            error_log("Actualizando detalle ID $grupoDetalleId a EN_CORRECCION con comentario='$comment'");
                            $this->detalleModel->updateEstadoWithComment($grupoDetalleId, 'EN_CORRECCION', $rol, $comment, $isSupervisorRole ? $_SESSION['user_id'] : null);
                            $this->auditoriaModel->createAuditoria($id, $grupoDetalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por " . ($isSupervisorRole ? "supervisor ID {$_SESSION['user_id']}" : "contador"));
                            $numCorrections++;
                        }
                    } 
                    // Para detalles individuales, procesar solo este detalle
                    else {
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
                    error_log("Todos los detalles en EN_CORRECCION, actualizando liquidación ID $id to EN_PROCESO");
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
                if ($isSupervisorRole || ($isEncargadoRole && $isSupervisorRole)) {
                    $nuevoEstado = 'PENDIENTE_REVISION_CONTABILIDAD';
                    $auditoriaAccion = 'AUTORIZADO_POR_SUPERVISOR';
                    $message = 'Liquidación autorizada por supervisor';
                } elseif ($isContabilidadRole || ($isEncargadoRole && $isContabilidadRole)) {
                    $nuevoEstado = 'FINALIZADO';
                    $auditoriaAccion = 'AUTORIZADO_POR_CONTABILIDAD';
                    $message = 'Liquidación finalizada por contabilidad';
                }
            } elseif ($accion === 'RECHAZADO') {
                if ($isSupervisorRole || ($isEncargadoRole && $isSupervisorRole)) {
                    $nuevoEstado = 'RECHAZADO_AUTORIZACION';
                    $auditoriaAccion = 'RECHAZADO_POR_SUPERVISOR';
                    $message = 'Liquidación rechazada por supervisor';
                } elseif ($isContabilidadRole || ($isEncargadoRole && $isContabilidadRole)) {
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
            $processedGrupoIds = [];
            foreach ($detalles as $detalle) {
                $detalleId = $detalle['id'];
                if (in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] !== 'EN_CORRECCION') {
                    $grupoId = $detalle['grupo_id'] ?? '0';
                    if (in_array($grupoId, $processedGrupoIds)) {
                        continue;
                    }
                    $processedGrupoIds[] = $grupoId;
                    $detallesGrupo = ($grupoId && $grupoId !== '0') ? $detalleModel->getDetallesByGrupoId($grupoId, $id) : [$detalle];
                    foreach ($detallesGrupo as $grupoDetalle) {
                        $grupoDetalleId = $grupoDetalle['id'];
                        if ($grupoDetalle['estado'] !== 'EN_CORRECCION') {
                            $detailsToCorrect[] = $grupoDetalleId;
                            $comment = $correccionComentarios[$detalleId] ?? '';
                            if (empty($comment)) {
                                throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                            }
                            $this->detalleModel->updateEstadoWithComment($grupoDetalleId, 'EN_CORRECCION', $rol, $comment, $isSupervisorRole ? $_SESSION['user_id'] : null);
                            $this->auditoriaModel->createAuditoria($id, $grupoDetalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por " . ($isSupervisorRole ? "supervisor ID {$_SESSION['user_id']}" : "contador"));
                        }
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
            } elseif (($isSupervisorRole || ($isEncargadoRole && $isSupervisorRole)) && $accion === 'APROBADO' && $hasApprovedDetails) {
                $this->liquidacionModel->updateEstado($id, 'PENDIENTE_REVISION_CONTABILIDAD', null, null);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            } elseif (($isContabilidadRole || ($isEncargadoRole && $isContabilidadRole)) && $accion === 'APROBADO' && $hasNonCorrectionDetails) {
                $this->liquidacionModel->updateEstado($id, 'FINALIZADO');
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            } else {
                $this->liquidacionModel->updateEstado($id, $nuevoEstado);
                $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], $auditoriaAccion, $motivo);
            }

            $isExported = $this->liquidacionModel->isExported($id);
            if ($isExported && ($isContabilidadRole || ($isEncargadoRole && $isContabilidadRole)) && $accion === 'APROBADO') {
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

    foreach ($detalles as &$detalle) {
        $detalle['id'] = htmlspecialchars($detalle['id'], ENT_QUOTES, 'UTF-8');
        $detalle['estado'] = htmlspecialchars($detalle['estado'], ENT_QUOTES, 'UTF-8');
        $detalle['id_contador'] = htmlspecialchars($detalle['id_contador'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cuenta_contable_nombre'] = htmlspecialchars($detalle['cuenta_contable_nombre'], ENT_QUOTES, 'UTF-8');
        $detalle['nombre_centro_costo'] = htmlspecialchars($detalle['nombre_centro_costo'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $detalle['subtotal'] = floatval($detalle['p_unitario'] ?? $detalle['total_factura']);
        $detalle['iva'] = floatval($detalle['iva'] ?? 0);
        $detalle['idp'] = floatval($detalle['idp'] ?? 0);
        $detalle['inguat'] = floatval($detalle['inguat'] ?? 0);
        $detalle['total_factura'] = floatval($detalle['total_factura']);
        $detalle['tipo_documento'] = htmlspecialchars($detalle['tipo_documento'] ?? 'FACTURA', ENT_QUOTES, 'UTF-8');
        $detalle['no_factura'] = htmlspecialchars($detalle['no_factura'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nombre_proveedor'] = htmlspecialchars($detalle['nombre_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nit_proveedor'] = htmlspecialchars($detalle['nit_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['dpi'] = htmlspecialchars($detalle['dpi'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cantidad'] = htmlspecialchars($detalle['cantidad'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['serie'] = htmlspecialchars($detalle['serie'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['t_gasto'] = htmlspecialchars($detalle['t_gasto'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['tipo_combustible'] = htmlspecialchars($detalle['tipo_combustible'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['fecha'] = htmlspecialchars($detalle['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['comentarios'] = htmlspecialchars($detalle['comentarios'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['correccion_comentario'] = htmlspecialchars($detalle['correccion_comentario'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['rutas_archivos'] = $detalle['rutas_archivos'] ?? '';
        $detalle['grupo_id'] = htmlspecialchars($detalle['grupo_id'] ?? '0', ENT_QUOTES, 'UTF-8');
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

public function revisar($id) {
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

    // Obtener información detallada del rol
    $rol = strtoupper($usuario['rol'] ?? 'INVALID_ROLE');
    $id_rol = $usuario['id_rol'] ?? null;
    
    $isContabilidadRole = false;
    $isEncargadoRole = false;
    
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
            
            // Detectar rol de encargado
            $isEncargadoRole = strpos($roleName, 'ENCARGADO') !== false || 
                              strpos($roleDescription, 'ENCARGADO') !== false ||
                              strpos($roleName, 'CAJA_CHICA') !== false ||
                              strpos($roleDescription, 'CAJA_CHICA') !== false ||
                              strpos($roleName, 'ENCARGADO_CAJA_CHICA') !== false;
        }
    }

    // VERIFICAR PERMISOS FLEXIBLES PARA ROLES MIXTOS
    $tienePermisoRevisar = false;
    
    // Si es contabilidad (puro o mixto)
    if ($isContabilidadRole) {
        $tienePermisoRevisar = $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones');
    }
    
    // Si es encargado con rol mixto (encargado + contabilidad)
    if ($isEncargadoRole && $isContabilidadRole) {
        $tienePermisoRevisar = $usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones');
    }

    if (!$tienePermisoRevisar) {
        error_log("Usuario ID {$_SESSION['user_id']} no tiene permiso para revisar liquidaciones. Rol: " . ($usuario['rol'] ?? 'N/A') . 
                 ", es contabilidad: " . ($isContabilidadRole ? 'SÍ' : 'NO') . 
                 ", es encargado: " . ($isEncargadoRole ? 'SÍ' : 'NO'));
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para revisar liquidaciones'], JSON_UNESCAPED_UNICODE);
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
    $centroCostoModel = new CentroCosto();
    foreach ($detalles as &$detalle) {
        $cuentaContable = $cuentaContableModel->getCuentaContableById($detalle['id_cuenta_contable']);
        $centroCosto = $centroCostoModel->getCentroCostoById($detalle['id_centro_costo']);
        $detalle['cuenta_contable_nombre'] = htmlspecialchars($cuentaContable['nombre'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $detalle['nombre_centro_costo'] = htmlspecialchars($centroCosto['nombre'] . ' / ' . $centroCosto['codigo'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $detalle['subtotal'] = floatval($detalle['p_unitario'] ?? $detalle['total_factura']);
        $detalle['iva'] = floatval($detalle['iva'] ?? 0);
        $detalle['idp'] = floatval($detalle['idp'] ?? 0);
        $detalle['inguat'] = floatval($detalle['inguat'] ?? 0);
        $detalle['total_factura'] = floatval($detalle['total_factura']);
        $detalle['tipo_documento'] = htmlspecialchars($detalle['tipo_documento'] ?? 'FACTURA', ENT_QUOTES, 'UTF-8');
        $detalle['no_factura'] = htmlspecialchars($detalle['no_factura'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nombre_proveedor'] = htmlspecialchars($detalle['nombre_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nit_proveedor'] = htmlspecialchars($detalle['nit_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['dpi'] = htmlspecialchars($detalle['dpi'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cantidad'] = htmlspecialchars($detalle['cantidad'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['serie'] = htmlspecialchars($detalle['serie'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['t_gasto'] = htmlspecialchars($detalle['t_gasto'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['tipo_combustible'] = htmlspecialchars($detalle['tipo_combustible'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['fecha'] = htmlspecialchars($detalle['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['estado'] = htmlspecialchars($detalle['estado'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['comentarios'] = htmlspecialchars($detalle['comentarios'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['correccion_comentario'] = htmlspecialchars($detalle['correccion_comentario'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['rutas_archivos'] = $detalle['rutas_archivos'] ?? '';
        $detalle['grupo_id'] = htmlspecialchars($detalle['grupo_id'] ?? '0', ENT_QUOTES, 'UTF-8');
    }
    unset($detalle);

    $cajaChicaModel = new CajaChica();
    $cajaChica = $cajaChicaModel->getCajaChicaById($liquidacion['id_caja_chica']);
    $nombre_caja_chica = $cajaChica['nombre'] ?? 'N/A';

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
                $processedGrupoIds = [];
        
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
                    
                    $grupoId = $detalle['grupo_id'] ?? '0';
                    $estado = $detalle['estado'] ?? '';
                    
                    // Saltar si ya está en corrección
                    if ($estado === 'EN_CORRECCION') {
                        error_log("Detalle ID $detalleId ya está en EN_CORRECCION");
                        $skipReasons[$detalleId] = "Ya en corrección";
                        continue;
                    }
                    
                    // Para grupos, procesar todos los detalles del grupo
                    if ($grupoId !== '0' && $grupoId !== 0) {
                        if (in_array($grupoId, $processedGrupoIds)) {
                            error_log("Grupo ID $grupoId ya procesado");
                            continue;
                        }
                        $processedGrupoIds[] = $grupoId;
        
                        // Obtener todos los detalles del grupo
                        $detallesGrupo = $detalleModel->getDetallesByGrupoId($grupoId, $id);
                        
                        foreach ($detallesGrupo as $grupoDetalle) {
                            $grupoDetalleId = intval($grupoDetalle['id']);
                            $grupoEstado = $grupoDetalle['estado'] ?? '';
                            
                            if ($grupoEstado === 'EN_CORRECCION') {
                                error_log("Detalle ID $grupoDetalleId ya está en EN_CORRECCION");
                                $skipReasons[$grupoDetalleId] = "Ya en corrección";
                                continue;
                            }
                            
                            $comment = $correccionComentarios[$detalleId] ?? '';
                            if (empty($comment)) {
                                error_log("Comentario vacío para detalle ID $detalleId");
                                throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                            }
                            
                            error_log("Actualizando detalle ID $grupoDetalleId a EN_CORRECCION con comentario='$comment'");
                            $this->detalleModel->updateEstadoWithComment($grupoDetalleId, 'EN_CORRECCION', $rol, $comment, null);
                            $this->auditoriaModel->createAuditoria($id, $grupoDetalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por contador");
                            $numCorrections++;
                        }
                    } 
                    // Para detalles individuales, procesar solo este detalle
                    else {
                        $comment = $correccionComentarios[$detalleId] ?? '';
                        if (empty($comment)) {
                            error_log("Comentario vacío para detalle ID $detalleId");
                            throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                        }
                        
                        error_log("Actualizando detalle ID $detalleId a EN_CORRECCION con comentario='$comment'");
                        $this->detalleModel->updateEstadoWithComment($detalleId, 'EN_CORRECCION', $rol, $comment, null);
                        $this->auditoriaModel->createAuditoria($id, $detalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment por contador");
                        $numCorrections++;
                    }
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

            // CORRECCIÓN CRÍTICA: Mantener la lógica de estados de la versión anterior
            if ($accion === 'APROBADO') {
                $nuevoEstado = $isContabilidadRole ? 'PENDIENTE_REVISION_CONTABILIDAD' : 'FINALIZADO';
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
            $processedGrupoIds = [];
            foreach ($allDetalles as $detalle) {
                $detalleId = $detalle['id'];
                if (in_array($detalleId, $detallesNoSeleccionados) && $detalle['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD') {
                    $grupoId = $detalle['grupo_id'] ?? '0';
                    if (in_array($grupoId, $processedGrupoIds)) {
                        continue;
                    }
                    $processedGrupoIds[] = $grupoId;
                    $detallesGrupo = ($grupoId && $grupoId !== '0') ? $detalleModel->getDetallesByGrupoId($grupoId, $id) : [$detalle];
                    foreach ($detallesGrupo as $grupoDetalle) {
                        $grupoDetalleId = $grupoDetalle['id'];
                        if ($grupoDetalle['estado'] !== 'EN_CORRECCION') {
                            $detailsToCorrect[] = $grupoDetalleId;
                            $comment = $correccionComentarios[$detalleId] ?? '';
                            if (empty($comment)) {
                                throw new Exception("Comentario de corrección requerido para el detalle ID $detalleId");
                            }
                            $idContador = $isContabilidadRole ? $_SESSION['user_id'] : ($liquidacion['id_contador'] ?? null);
                            $detalleModel->updateEstadoWithComment($grupoDetalleId, 'EN_CORRECCION', $rol, $comment, null, $idContador);
                            $this->auditoriaModel->createAuditoria($id, $grupoDetalleId, $_SESSION['user_id'], 'ENVIADO_A_CORRECCION', "Detalle enviado a corrección con comentario: $comment");
                        }
                    }
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

    foreach ($detalles as &$detalle) {
        $detalle['id'] = htmlspecialchars($detalle['id'], ENT_QUOTES, 'UTF-8');
        $detalle['estado'] = htmlspecialchars($detalle['estado'], ENT_QUOTES, 'UTF-8');
        $detalle['id_contador'] = htmlspecialchars($detalle['id_contador'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cuenta_contable_nombre'] = htmlspecialchars($detalle['cuenta_contable_nombre'], ENT_QUOTES, 'UTF-8');
        $detalle['nombre_centro_costo'] = htmlspecialchars($detalle['nombre_centro_costo'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $detalle['subtotal'] = floatval($detalle['p_unitario'] ?? $detalle['total_factura']);
        $detalle['iva'] = floatval($detalle['iva'] ?? 0);
        $detalle['idp'] = floatval($detalle['idp'] ?? 0);
        $detalle['inguat'] = floatval($detalle['inguat'] ?? 0);
        $detalle['total_factura'] = floatval($detalle['total_factura']);
        $detalle['tipo_documento'] = htmlspecialchars($detalle['tipo_documento'] ?? 'FACTURA', ENT_QUOTES, 'UTF-8');
        $detalle['no_factura'] = htmlspecialchars($detalle['no_factura'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nombre_proveedor'] = htmlspecialchars($detalle['nombre_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['nit_proveedor'] = htmlspecialchars($detalle['nit_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['dpi'] = htmlspecialchars($detalle['dpi'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['cantidad'] = htmlspecialchars($detalle['cantidad'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['serie'] = htmlspecialchars($detalle['serie'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['t_gasto'] = htmlspecialchars($detalle['t_gasto'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['tipo_combustible'] = htmlspecialchars($detalle['tipo_combustible'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['fecha'] = htmlspecialchars($detalle['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['comentarios'] = htmlspecialchars($detalle['comentarios'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['correccion_comentario'] = htmlspecialchars($detalle['correccion_comentario'] ?? '', ENT_QUOTES, 'UTF-8');
        $detalle['rutas_archivos'] = $detalle['rutas_archivos'] ?? '';
        $detalle['grupo_id'] = htmlspecialchars($detalle['grupo_id'] ?? '0', ENT_QUOTES, 'UTF-8');
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

//EXPORTA A SAP REAL
private function login_sap($db)
    {
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

// Funciones para errores al exportar 
private function manejarErroresSapYReintentar($errorCode, $errorMessage, $nitProveedor, $nombreProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
{
    error_log("Manejando error SAP: $errorCode - $errorMessage para NIT: $nitProveedor");
    
    // Si el código es -1116, intentar extraer el código real del mensaje
    if ($errorCode == -1116) {
        if (strpos($errorMessage, '2021032504') !== false) {
            $errorCode = 2021032504;
            error_log("Código real extraído del mensaje: 2021032504");
        } elseif (strpos($errorMessage, '18000018') !== false) {
            $errorCode = 18000018;
            error_log("Código real extraído del mensaje: 18000018");
        } elseif (strpos($errorMessage, '20170505') !== false) {
            $errorCode = 20170505;
            error_log("Código real extraído del mensaje: 20170505");
        }
    }
    
    switch ($errorCode) {
        case 2021032504: // NIT Pequeño Contribuyente
            error_log("Error de NIT Pequeño Contribuyente detectado para factura {$noFactura}");
            return $this->manejarErrorPequeñoContribuyente($nitProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath);
            
        case 18000018: // NIT no existe
            error_log("NIT no encontrado para factura {$noFactura}");
            return $this->manejarErrorNitNoExiste($nitProveedor, $nombreProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath);
            
        case 20170505: // No se permiten descuentos en esta factura
            error_log("Error de descuentos detectado para factura {$noFactura}");
            return $this->manejarErrorDescuentosNoPermitidos($jsonContent, $cookie, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath);
            
        default:
            error_log("Error no manejable: $errorCode");
            return [
                'success' => false,
                'error' => $errorMessage,
                'manejable' => false
            ];
    }
}

private function manejarErrorPequeñoContribuyente($nitProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
{
    try {
        // Consultar información del NIT en @NIT_PN
        $conn = $this->CONEXION_HANA("GT_AGROCENTRO_2016");
        $sql = 'SELECT "U_Validador" FROM "@NIT_PN" WHERE "U_NIT" = ?';
        
        $stmt = odbc_prepare($conn, $sql);
        if (!$stmt) {
            $sql = 'SELECT "U_Validador" FROM "GT_AGROCENTRO_2016"."@NIT_PN" WHERE "U_NIT" = ?';
            $stmt = odbc_prepare($conn, $sql);
        }
        
        if ($stmt && odbc_execute($stmt, [$nitProveedor])) {
            if ($row = odbc_fetch_array($stmt)) {
                $uValidador = $row['U_Validador'];
                error_log("U_Validador actual para NIT {$nitProveedor}: {$uValidador}");
                
                if ($uValidador === 'S') {
                    // Actualizar U_Validador a 'N'
                    $updateSql = 'UPDATE "@NIT_PN" SET "U_Validador" = ? WHERE "U_NIT" = ?';
                    $updateStmt = odbc_prepare($conn, $updateSql);
                    
                    if (!$updateStmt) {
                        $updateSql = 'UPDATE "GT_AGROCENTRO_2016"."@NIT_PN" SET "U_Validador" = ? WHERE "U_NIT" = ?';
                        $updateStmt = odbc_prepare($conn, $updateSql);
                    }
                    
                    if ($updateStmt && odbc_execute($updateStmt, ['N', $nitProveedor])) {
                        error_log("U_Validador actualizado exitosamente a 'N' para NIT {$nitProveedor}");
                        odbc_close($conn);
                        
                        // Reintentar envío a SAP
                        return $this->reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "U_Validador actualizado");
                    } else {
                        error_log("Error al ejecutar update de U_Validador: " . odbc_errormsg($conn));
                    }
                } else {
                    error_log("U_Validador ya es 'N' para NIT {$nitProveedor}");
                }
            } else {
                error_log("NIT {$nitProveedor} no encontrado en @NIT_PN");
                
                // Si no existe, insertarlo
                $codigo = 13333;
                $maxIntentos = 1000;
                $codigoEncontrado = false;
                
                for ($i = 0; $i < $maxIntentos; $i++) {
                    $codigoProbable = $codigo + $i;
                    
                    $sqlCheck = 'SELECT COUNT(*) as count FROM "@NIT_PN" WHERE "Code" = ?';
                    $stmtCheck = odbc_prepare($conn, $sqlCheck);
                    
                    if ($stmtCheck && odbc_execute($stmtCheck, [(string)$codigoProbable])) {
                        if ($rowCheck = odbc_fetch_array($stmtCheck)) {
                            if (isset($rowCheck['count']) && $rowCheck['count'] == 0) {
                                $codigo = $codigoProbable;
                                $codigoEncontrado = true;
                                break;
                            }
                        }
                    }
                }
                
                if ($codigoEncontrado) {
                    // Insertar el NIT con U_Validador = 'N'
                    $sqlInsert = 'INSERT INTO "@NIT_PN" ("Code", "Name", "U_NIT", "U_Razon", "U_Validador") VALUES (?, ?, ?, ?, ?)';
                    $stmtInsert = odbc_prepare($conn, $sqlInsert);
                    
                    if ($stmtInsert && odbc_execute($stmtInsert, [(string)$codigo, (string)$codigo, $nitProveedor, 'Proveedor Automático', 'N'])) {
                        error_log("NIT {$nitProveedor} insertado con U_Validador = 'N'");
                        odbc_close($conn);
                        
                        // Reintentar envío a SAP
                        return $this->reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "NIT insertado con U_Validador = 'N'");
                    }
                }
            }
        }
        
        odbc_close($conn);
        
        return [
            'success' => false,
            'error' => "No se pudo actualizar U_Validador para NIT {$nitProveedor}",
            'manejable' => false
        ];
        
    } catch (Exception $e) {
        error_log("Error al manejar pequeño contribuyente: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'manejable' => false
        ];
    }
}

private function manejarErrorNitNoExiste($nitProveedor, $nombreProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
{
    try {
        // Conectar a HANA para insertar directamente en @NIT_PN
        $conn = $this->CONEXION_HANA("GT_AGROCENTRO_2016");
        
        // Buscar el próximo código disponible empezando desde 13333
        $codigo = 13333;
        $maxIntentos = 1000;
        $codigoEncontrado = false;
        
        error_log("Buscando código disponible en @NIT_PN para NIT: $nitProveedor");
        
        for ($i = 0; $i < $maxIntentos; $i++) {
            $codigoProbable = $codigo + $i;
            
            // Verificar si el código ya existe
            $sqlCheck = 'SELECT "Code" FROM "@NIT_PN" WHERE "Code" = ?';
            $stmtCheck = odbc_prepare($conn, $sqlCheck);
            
            if (!$stmtCheck) {
                $sqlCheck = 'SELECT "Code" FROM "GT_AGROCENTRO_2016"."@NIT_PN" WHERE "Code" = ?';
                $stmtCheck = odbc_prepare($conn, $sqlCheck);
            }
            
            if ($stmtCheck) {
                // Convertir a string ya que SAP espera strings
                $codigoStr = (string)$codigoProbable;
                $execCheck = odbc_execute($stmtCheck, [$codigoStr]);
                
                if ($execCheck) {
                    // Si no hay resultados, el código está disponible
                    if (!odbc_fetch_array($stmtCheck)) {
                        $codigo = $codigoProbable;
                        $codigoEncontrado = true;
                        error_log("Código disponible encontrado: $codigo");
                        break;
                    } else {
                        error_log("Código $codigoProbable ya existe, probando siguiente...");
                    }
                } else {
                    error_log("Error al verificar código $codigoProbable: " . odbc_errormsg($conn));
                }
            }
        }
        
        if (!$codigoEncontrado) {
            odbc_close($conn);
            throw new Exception("No se pudo encontrar un código disponible en @NIT_PN después de $maxIntentos intentos");
        }
        
        error_log("Insertando en @NIT_PN: Code=$codigo, U_NIT=$nitProveedor, U_Razon=$nombreProveedor");
        
        // Insertar directamente en la tabla @NIT_PN
        $sqlInsert = 'INSERT INTO "@NIT_PN" ("Code", "Name", "U_NIT", "U_Razon", "U_Validador") VALUES (?, ?, ?, ?, ?)';
        $stmtInsert = odbc_prepare($conn, $sqlInsert);
        
        if (!$stmtInsert) {
            $sqlInsert = 'INSERT INTO "GT_AGROCENTRO_2016"."@NIT_PN" ("Code", "Name", "U_NIT", "U_Razon", "U_Validador") VALUES (?, ?, ?, ?, ?)';
            $stmtInsert = odbc_prepare($conn, $sqlInsert);
        }
        
        if (!$stmtInsert) {
            odbc_close($conn);
            throw new Exception("Error al preparar inserción en @NIT_PN: " . odbc_errormsg($conn));
        }
        
        // Code y Name deben ser iguales (solo números convertidos a string)
        $codeStr = (string)$codigo;
        $nameStr = (string)$codigo;
        $uValidador = 'N'; // Por defecto 'N' para no pequeño contribuyente
        
        // Limitar la longitud de U_Razon si es necesario
        $uRazon = substr($nombreProveedor, 0, 100);
        
        $execInsert = odbc_execute($stmtInsert, [$codeStr, $nameStr, $nitProveedor, $uRazon, $uValidador]);
        
        if (!$execInsert) {
            $errorMsg = "Error al insertar en @NIT_PN: " . odbc_errormsg($conn);
            error_log($errorMsg);
            
            // Si es error de duplicado, intentar con otro código
            if (strpos(odbc_errormsg($conn), 'unique constraint') !== false) {
                error_log("Código $codigo ya existe, intentando con siguiente...");
                odbc_close($conn);
                
                // Llamar recursivamente con el siguiente código
                return $this->manejarErrorNitNoExiste(
                    $nitProveedor, 
                    $nombreProveedor, 
                    $noFactura, 
                    $cookie, 
                    $jsonContent, 
                    $sapUrl, 
                    $detalles, 
                    $detalleLiquidacionModel, 
                    $id, 
                    $groupKey, 
                    $groupedDetalles, 
                    $jsonFilePath
                );
            }
            
            odbc_close($conn);
            throw new Exception($errorMsg);
        }
        
        odbc_close($conn);
        
        error_log("NIT insertado exitosamente en @NIT_PN: $nitProveedor con código $codigo");
        
        // Reintentar envío a SAP después de insertar en la tabla
        return $this->reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "NIT insertado en @NIT_PN");

    } catch (Exception $e) {
        error_log("Error al insertar NIT en @NIT_PN: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'manejable' => false
        ];
    }
}

private function manejarErrorDescuentosNoPermitidos($jsonContent, $cookie, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
{
    try {
        error_log("Manejando error de descuentos no permitidos (20170505)");
        
        // Decodificar el JSON para modificar los valores incorrectos
        $invoiceData = json_decode($jsonContent, true);
        
        // Si hay múltiples detalles en este grupo, recalcular el DocTotal
        if (count($detalles) > 1) {
            $nuevoDocTotal = 0;
            foreach ($detalles as $detalle) {
                $nuevoDocTotal += floatval($detalle['total_factura']);
            }
            
            error_log("Recalculando DocTotal: {$invoiceData['DocTotal']} -> {$nuevoDocTotal} (para " . count($detalles) . " detalles)");
            $invoiceData['DocTotal'] = $nuevoDocTotal;
            
            // También asegurarse de que cada línea tenga el PriceAfterVAT correcto
            if (isset($invoiceData['DocumentLines']) && is_array($invoiceData['DocumentLines'])) {
                foreach ($invoiceData['DocumentLines'] as $index => &$line) {
                    if (isset($detalles[$index])) {
                        $line['PriceAfterVAT'] = floatval($detalles[$index]['total_factura']);
                        error_log("Ajustando línea {$index}: PriceAfterVAT = {$line['PriceAfterVAT']}");
                    }
                }
            }
        }
        
        // Forzar TotalDiscount a 0 si existe
        if (isset($invoiceData['TotalDiscount'])) {
            error_log("TotalDiscount encontrado: {$invoiceData['TotalDiscount']}, forzando a 0");
            $invoiceData['TotalDiscount'] = 0;
        }
        
        // También verificar en las líneas del documento
        if (isset($invoiceData['DocumentLines']) && is_array($invoiceData['DocumentLines'])) {
            foreach ($invoiceData['DocumentLines'] as &$line) {
                if (isset($line['DiscountPercent'])) {
                    error_log("DiscountPercent encontrado en línea: {$line['DiscountPercent']}, forzando a 0");
                    $line['DiscountPercent'] = 0;
                }
                if (isset($line['DiscountAmount'])) {
                    error_log("DiscountAmount encontrado en línea: {$line['DiscountAmount']}, forzando a 0");
                    $line['DiscountAmount'] = 0;
                }
            }
        }
        
        // Codificar de nuevo a JSON
        $modifiedJsonContent = json_encode($invoiceData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        // Guardar el JSON modificado
        if (file_put_contents($jsonFilePath, "\xEF\xBB\xBF" . $modifiedJsonContent) === false) {
            throw new Exception("No se pudo escribir el archivo JSON modificado: $jsonFilePath");
        }
        
        error_log("JSON modificado guardado en: $jsonFilePath");
        
        // Reintentar envío a SAP con el JSON modificado
        return $this->reintentarEnvioSAP($cookie, $modifiedJsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "DocTotal y descuentos corregidos");

    } catch (Exception $e) {
        error_log("Error al manejar descuentos no permitidos: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'manejable' => false
        ];
    }
}

private function reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, $accionRealizada)
{
    error_log("Reintentando envío a SAP después de: $accionRealizada");
    
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
        error_log("Error en reintento: $curlError");
        return [
            'success' => false,
            'error' => "Error de conexión SAP al reintentar: $curlError",
            'manejable' => false
        ];
    }

    $sapResponse = json_decode($response, true);
    if ($httpCode >= 400 || json_last_error() !== JSON_ERROR_NONE) {
        $errorMsg = "Error SAP al reintentar: HTTP $httpCode";
        if (isset($sapResponse['error']['message']['value'])) {
            $errorMsg .= " - {$sapResponse['error']['message']['value']}";
        }
        error_log($errorMsg);
        return [
            'success' => false,
            'error' => $errorMsg,
            'manejable' => false
        ];
    }

    // Éxito en el reintento
    error_log("Reintento exitoso después de: $accionRealizada");
    
    foreach ($detalles as $detalle) {
        $detalleLiquidacionModel->updateEstado($detalle['id'], 'FINALIZADO');
        $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Factura exportada a SAP después de corrección: {$accionRealizada}");
    }

    return [
        'success' => true,
        'message' => "Grupo {$groupKey} enviado a SAP exitosamente después de corrección",
        'filePath' => $jsonFilePath,
        'detalle_ids' => array_map(function($detalle) { return $detalle['id']; }, $detalles),
        'sap_response' => $sapResponse,
        'manejable' => true
    ];
}

public function exportar($id, $docDate = null)
{
    ob_start();
    error_log("Iniciando exportar para id: $id" . ($docDate ? " con docDate: $docDate" : ""));

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

        // Collect unique NITs from detalle_liquidaciones
        $nitsToSearch = array_unique(array_map(function ($dl) {
            return !empty(trim($dl['nit_proveedor'])) ? trim($dl['nit_proveedor']) : '321052';
        }, $pendingDetalles));
        error_log("NITs a buscar en @NIT_PN: " . implode(', ', $nitsToSearch));

        // Query @NIT_PN table in HANA for the collected NITs
        error_log("Realizando consulta a tabla @NIT_PN para NITs específicos");
        $nitPnData = [];
        try {
            $conn = $this->CONEXION_HANA("GT_AGROCENTRO_2016");
            $placeholders = implode(',', array_fill(0, count($nitsToSearch), '?'));
            $sql = 'SELECT "Code","Name","U_Razon","U_NIT","U_Validador" FROM "@NIT_PN" WHERE "U_NIT" IN (' . $placeholders . ')';
            
            $stmt = odbc_prepare($conn, $sql);
            if (!$stmt) {
                error_log("Primer intento fallido, probando con esquema GT_AGROCENTRO_2016");
                $sql = 'SELECT "Code","Name","U_Razon","U_NIT","U_Validador" FROM "GT_AGROCENTRO_2016"."@NIT_PN" WHERE "U_NIT" IN (' . $placeholders . ')';
                $stmt = odbc_prepare($conn, $sql);
                if (!$stmt) {
                    throw new Exception("Error al preparar la consulta: " . odbc_errormsg($conn));
                }
            }

            $exec = odbc_execute($stmt, $nitsToSearch);
            if (!$exec) {
                throw new Exception("Error al ejecutar la consulta: " . odbc_errormsg($conn));
            }

            while ($row = odbc_fetch_array($stmt)) {
                $nitPnData[] = array_map(function ($value) {
                    return is_string($value) ? utf8_encode($value) : $value;
                }, $row);
                error_log("Resultado @NIT_PN: Code={$row['Code']}, Name={$row['Name']}, U_Razon={$row['U_Razon']}, U_NIT={$row['U_NIT']}, U_Validador={$row['U_Validador']}");
            }

            odbc_close($conn);

            error_log("Consulta @NIT_PN exitosa. Resultados: " . count($nitPnData));
            if (empty($nitPnData)) {
                error_log("No se encontraron resultados para los NITs: " . implode(', ', $nitsToSearch));
                $nitPnData = ['info' => 'No se encontraron registros en @NIT_PN para los NITs especificados'];
            }
        } catch (Exception $e) {
            error_log("Error en consulta @NIT_PN: " . $e->getMessage());
            $nitPnData = ['error' => $e->getMessage()];
        }
        $nitPnResults = $nitPnData;

        // Start transaction
        $this->pdo->beginTransaction();

        // Initialize CentroCosto model
        error_log("Cargando modelo CentroCosto");
        if (!class_exists('CentroCosto')) {
            throw new Exception('Clase CentroCosto no encontrada');
        }
        $centroCostoModel = new CentroCosto();

        $results = [];
        $validationResults = [];
        $validDetalles = [];
        $jsonDir = __DIR__ . "/json";
        if (!file_exists($jsonDir)) {
            if (!mkdir($jsonDir, 0777, true)) {
                throw new Exception('No se pudo crear el directorio JSON: ' . $jsonDir);
            }
        }
        if (!is_writable($jsonDir)) {
            throw new Exception('El directorio JSON no es escribible: ' . $jsonDir);
        }
        $timestamp = date('Ymd\THis');

        // Group detalles by no_factura and grupo_id
        $groupedDetalles = [];
        foreach ($pendingDetalles as $index => $dl) {
            $groupKey = $dl['grupo_id'] == 0 ? $dl['no_factura'] . '_' . $dl['id'] : $dl['no_factura'] . '_' . $dl['grupo_id'];
            if (!isset($groupedDetalles[$groupKey])) {
                $groupedDetalles[$groupKey] = [
                    'detalles' => [],
                    'indices' => [],
                    'ids' => [],
                    'no_factura' => $dl['no_factura'],
                    'grupo_id' => $dl['grupo_id']
                ];
            }
            $groupedDetalles[$groupKey]['detalles'][] = $dl;
            $groupedDetalles[$groupKey]['indices'][] = $index;
            $groupedDetalles[$groupKey]['ids'][] = $dl['id'];
        }

        // Validate grouped invoices
        foreach ($groupedDetalles as $groupKey => $group) {
            $detalles = $group['detalles'];
            $indices = $group['indices'];
            $noFactura = $group['no_factura'];
            try {
                error_log("Validando grupo {$groupKey} (Factura: {$noFactura}, Grupo ID: {$group['grupo_id']}) con " . count($detalles) . " detalles");

                foreach ($detalles as $dl) {
                    $index = array_search($dl['id'], $group['ids']);
                    $costingCode = null;
                    if (!empty($dl['id_centro_costo'])) {
                        $centroCosto = $centroCostoModel->getCentroCostoById($dl['id_centro_costo']);
                        if ($centroCosto && !empty($centroCosto['codigo'])) {
                            $costingCode = trim($centroCosto['codigo']);
                            error_log("CostingCode encontrado para id_centro_costo {$dl['id_centro_costo']}: $costingCode");
                        } else {
                            error_log("No se encontró código para id_centro_costo {$dl['id_centro_costo']} o centro no existe");
                            throw new Exception("id_centro_costo {$dl['id_centro_costo']} no tiene un CostingCode válido para factura {$noFactura}");
                        }
                    } else {
                        error_log("id_centro_costo no especificado para detalle id {$dl['id']}");
                        throw new Exception("id_centro_costo no especificado para factura {$noFactura}");
                    }

                    if ($dl['t_gasto'] === 'Alimentos' && floatval($dl['propina']) > 0) {
                        if (empty($dl['id_cuenta_contable_propina'])) {
                            error_log("id_cuenta_contable_propina no especificado para factura {$noFactura} con t_gasto=Alimentos y propina={$dl['propina']}");
                            throw new Exception("id_cuenta_contable_propina no especificado para factura {$noFactura} con propina");
                        }
                    }

                    $tipoDocMap = [
                        'FACTURA' => 'FN',
                        'FACTURA ELECTRONICA' => 'FEL',
                        'FACTURA PEQUEÑO CONTRIBUYENTE' => 'FP',
                        'RECIBO FISCAL' => 'RF',
                        'OTROS DOCUMENTOS' => 'OT',
                        'FPC' => 'FP',
                        'REF' => 'RF',
                        'DECLARACION ADUANERA' => 'DA',
                        'FACTURA ELECTRONICA TIPO FACE' => 'FCE',
                        'FACTURA DEL EXTERIOR' => 'FDE',
                        'POLIZA' => 'ID'
                    ];

                    $validTipoDocValues = ['DA', 'FCE', 'FE', 'NC', 'FPC', 'FC', 'FDE', 'FEL', 'OT', 'REF', 'ID', 'MC', 'MI'];

                    $tipoDocForUF = '';
                    $tipoDocForLines = '';
                    $stmt = $this->pdo->prepare("SELECT TipoDoc FROM tipos_documentos WHERE name = ?");
                    $stmt->execute([$dl['tipo_documento']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result && !empty($result['TipoDoc'])) {
                        $tipoDocForLines = $result['TipoDoc'];
                        $tipoDocForUF = $tipoDocMap[$tipoDocForLines] ?? $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
                        error_log("TipoDoc encontrado para tipo_documento {$dl['tipo_documento']}: $tipoDocForLines (U_F_Tipo: $tipoDocForUF)");
                    } else {
                        $tipoDocForLines = strtoupper($dl['tipo_documento']);
                        $tipoDocForUF = $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
                        error_log("No se encontró TipoDoc para tipo_documento {$dl['tipo_documento']}, usando valor mapeado: U_TipoDoc=$tipoDocForLines, U_F_Tipo=$tipoDocForUF");
                    }

                    if (!in_array($tipoDocForLines, $validTipoDocValues)) {
                        throw new Exception("U_TipoDoc inválido: $tipoDocForLines para factura {$noFactura}");
                    }
                    if (!in_array($tipoDocForUF, ['FN', 'FP', 'RF', 'DA', 'FCE', 'FDE', 'FEL', 'OT', 'ID', 'MC', 'MI'])) {
                        throw new Exception("U_F_Tipo inválido: $tipoDocForUF para factura {$noFactura}");
                    }
                }

                $documentLines = [];
                $docTotal = 0;
                foreach ($documentLines as $line) {
                    $docTotal += floatval($line['PriceAfterVAT']);
                }
                error_log("DocTotal recalculado para factura {$noFactura}: {$docTotal} (suma de " . count($documentLines) . " líneas)");
                foreach ($detalles as $detalle) {
                    $docTotal += floatval($detalle['total_factura']);
                }
                error_log("DocTotal calculado para factura {$noFactura}: {$docTotal} (suma de " . count($detalles) . " detalles)");
                $tipoDocumento = strtoupper($detalles[0]['tipo_documento'] ?? 'FACTURA');
                $tipoA = in_array($detalles[0]['t_gasto'], ['Gasto Operativo', 'Hospedaje']) ? 'S' : ($detalles[0]['t_gasto'] === 'Combustible' ? 'C' : 'B');
                
                foreach ($detalles as $dl) {
                    $costingCode = trim($centroCostoModel->getCentroCostoById($dl['id_centro_costo'])['codigo']);
                    $accountCode = $dl['id_cuenta_contable'] ?? null;
                
                    // Preparar el ItemDescription
                    $itemDescription = $dl['t_gasto'];
                    $comments = !empty(trim($dl['comentarios'])) ? trim($dl['comentarios']) : '';
                    
                    $casosEspeciales = ['INGUAT', 'Propina', 'IDP'];
                    if (!empty($comments) && !in_array($dl['t_gasto'], $casosEspeciales)) {
                        $itemDescription .= " - " . substr($comments, 0, 100);
                    }
                    
                    if ($tipoDocumento === 'FACTURA' || $tipoDocumento === 'FACTURA ELECTRONICA' || $tipoDocumento === 'FACTURA PEQUEÑO CONTRIBUYENTE') {
                        $subtotal = floatval($dl['p_unitario']);
                        $iva = floatval($dl['iva']);
                        $idp = floatval($dl['idp']);
                        $inguat = floatval($dl['inguat']);
                        $propina = floatval($dl['propina']);
                        
                        // LÍNEA PRINCIPAL - Para FACTURA PEQUEÑO CONTRIBUYENTE usar EXE, para otras IVA
                         if ($subtotal > 0) {
                             if ($tipoDocumento === 'FACTURA PEQUEÑO CONTRIBUYENTE') {
                                 // Para pequeño contribuyente: solo subtotal con EXE
                                 $documentLines[] = [
                                     "LineType" => count($documentLines),
                                     "ItemDescription" => $itemDescription,
                                     "PriceAfterVAT" => $subtotal,
                                     "TaxCode" => "EXE",
                                     "CostingCode" => $costingCode,
                                     "AccountCode" => $accountCode,
                                     "U_TipoDoc" => $tipoDocForLines,
                                     "U_TipoA" => $tipoA
                                 ];
                                 error_log("Agregada línea principal PEQUEÑO CONTRIBUYENTE: $itemDescription, PriceAfterVAT: $subtotal, TaxCode: EXE");
                             } else {
                                 // Para facturas normales: subtotal + IVA con IVA
                                 $documentLines[] = [
                                     "LineType" => count($documentLines),
                                     "ItemDescription" => $itemDescription,
                                     "PriceAfterVAT" => ($subtotal + $iva),
                                     "TaxCode" => "IVA",
                                     "CostingCode" => $costingCode,
                                     "AccountCode" => $accountCode,
                                     "U_TipoDoc" => $tipoDocForLines,
                                     "U_TipoA" => $tipoA
                                 ];
                                 error_log("Agregada línea principal FACTURA NORMAL: $itemDescription, PriceAfterVAT: " . ($subtotal + $iva) . ", TaxCode: IVA");
                             }
                         }
                        
                        // LÍNEAS ADICIONALES - Siempre incluir si existen
                        $impuestos = [
                            ['valor' => $idp, 'desc' => 'IDP', 'cuenta' => $dl['id_cuenta_contable_idp'] ?? $accountCode, 'tipoA' => 'P'],
                            ['valor' => $inguat, 'desc' => 'INGUAT', 'cuenta' => $detalle['id_cuenta_contable_inguat'] ?? $accountCode, 'tipoA' => 'H'],
                        ];
                        
                        if ($dl['t_gasto'] === 'Alimentos') {
                            $impuestos[] = ['valor' => $propina, 'desc' => 'Propina', 'cuenta' => $dl['id_cuenta_contable_propina'], 'tipoA' => 'E'];
                        }
                        
                        foreach ($impuestos as $impuesto) {
                            if ($impuesto['valor'] > 0) {
                                $cuentaInguat = $impuesto['cuenta'];
                 // Si es INGUAT y el centro de costo comienza con "T", usar cuenta diferente
        if ($impuesto['desc'] === 'INGUAT' && strtoupper(substr($costingCode, 0, 1)) === 'T') {
            $cuentaInguat = '611001003'; // Cuenta para centros de costo que empiezan con T
            error_log("Cambiando cuenta INGUAT a $cuentaInguat para centro de costo T: $costingCode");
        }
                                $documentLines[] = [
                                    "LineType" => count($documentLines),
                                    "ItemDescription" => $impuesto['desc'],
                                    "PriceAfterVAT" => $impuesto['valor'],
                                    "TaxCode" => "EXE",
                                    "CostingCode" => $costingCode,
                                    "AccountCode" => $impuesto['cuenta'],
                                    "U_TipoDoc" => $tipoDocForLines,
                                    "U_TipoA" => $impuesto['tipoA']
                                ];
                            }
                        }
                        
                    } else {
                        // Otros tipos de documento
                        if (floatval($dl['total_factura']) > 0) {
                            $documentLines[] = [
                                "LineType" => count($documentLines),
                                "ItemDescription" => $itemDescription . " ({$tipoDocumento})",
                                "PriceAfterVAT" => floatval($dl['total_factura']),
                                "TaxCode" => "EXE",
                                "CostingCode" => $costingCode,
                                "AccountCode" => $accountCode,
                                "U_TipoDoc" => $tipoDocForLines,
                                "U_TipoA" => $tipoA
                            ];
                        }
                    }
                }

                if (empty($documentLines)) {
                    throw new Exception("No se generaron líneas para el documento: sin valores financieros válidos para {$tipoDocumento} en factura {$noFactura}");
                }

                foreach ($indices as $index) {
                    $validationResults[] = [
                        'index' => $index,
                        'detalle_id' => $detalles[array_search($index, $indices)]['id'],
                        'no_factura' => $noFactura,
                        'valid' => true
                    ];
                }
                $validDetalles[$groupKey] = $detalles;
            } catch (Exception $e) {
                error_log("Error de validación en grupo {$groupKey} (Factura: {$noFactura}): {$e->getMessage()}");
                foreach ($indices as $index) {
                    $dl = $detalles[array_search($index, $indices)];
                    $validationResults[] = [
                        'index' => $index,
                        'detalle_id' => $dl['id'],
                        'no_factura' => $noFactura,
                        'valid' => false,
                        'error' => $e->getMessage()
                    ];
                    $detalleLiquidacionModel->updateEstado($dl['id'], 'EN_CORRECCION');
                    $this->auditoriaModel->createAuditoria($id, $dl['id'], $_SESSION['user_id'], 'ERROR_VALIDACION_EXPORTACION', "Error de validación: {$e->getMessage()} para factura {$noFactura}");
                }
            }
        }

        // SAP Login
        error_log("Intentando login en SAP");
        $loginResult = $this->login_sap('GT_AGROCENTRO_2016');
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

        // Process valid invoices for SAP export
        $atLeastOneProcessed = false;
        $successCount = 0;
        foreach ($validDetalles as $groupKey => $detalles) {
            $indices = $groupedDetalles[$groupKey]['indices'];
            $noFactura = $groupedDetalles[$groupKey]['no_factura'];
            try {
                error_log("Procesando exportación para grupo {$groupKey} (Factura: {$noFactura}, Grupo ID: {$groupedDetalles[$groupKey]['grupo_id']}) con " . count($detalles) . " detalles");
                
                $dl = $detalles[0];
                $docDate = $docDate ?? date('Y-m-d', strtotime($dl['fecha']));
                // LÓGICA PARA FECHAS SEGÚN MES CONTABLE
                 $fechaActual = new DateTime();
                 $mesContableActual = $fechaActual->format('Y-m');

                 // Usar fecha_documento si existe, de lo contrario usar la fecha normal del documento
                 $fechaParaComparar = !empty($dl['fecha_documento']) ? new DateTime($dl['fecha_documento']) : new DateTime($dl['fecha']);
                 error_log("Factura {$noFactura}: Fecha para comparar: " . $fechaParaComparar->format('Y-m-d') . " (fecha_documento: " . ($dl['fecha_documento'] ?? 'No disponible') . ")");

                 // Determinar si es del mismo mes contable o mes anterior
                 if ($fechaParaComparar->format('Y-m') === $mesContableActual) {
                     // Mismo mes contable: todas las fechas iguales a la fecha del documento
                     $docDate = $fechaParaComparar->format('Y-m-d');
                     $taxDate = $fechaParaComparar->format('Y-m-d');
                     $docDueDate = $fechaParaComparar->format('Y-m-d');
                     error_log("Factura {$noFactura}: Mismo mes contable - DocDate: $docDate, TaxDate: $taxDate, DocDueDate: $docDueDate");
                 } else {
                     // Mes anterior: 
                     // - DocDate = primer día del mes contable actual (fecha de contabilización)
                     // - TaxDate = primer día del mes contable actual (fecha del documento)
                     // - DocDueDate = fecha del documento original (fecha de vencimiento)
    
                     $primerDiaMesActual = (new DateTime())->modify('first day of this month')->format('Y-m-d');
                     $docDate = $primerDiaMesActual;
                     $taxDate = $primerDiaMesActual;
                     $docDueDate = $fechaParaComparar->format('Y-m-d');
    
                     error_log("Factura {$noFactura}: Mes anterior - DocDate: $docDate, TaxDate: $taxDate, DocDueDate: $docDueDate");
                 }

                 // Si se proporciona un docDate específico, usarlo (para sobreescritura manual)
                 if ($docDateParam !== null) {
                     $docDate = $docDateParam;
                     $taxDate = $docDateParam;
                     $docDueDate = $docDateParam;
                     error_log("Factura {$noFactura}: Usando fecha proporcionada - DocDate: $docDate, TaxDate: $taxDate, DocDueDate: $docDueDate");
                 }
                $numAtCard = !empty(trim($noFactura)) ? substr(trim($noFactura), 0, 50) : "DLIQ-{$id}-{$timestamp}";
                $documentLines = [];
                $docTotal = floatval($dl['total_factura']);

                if (empty($dl['fecha']) || !strtotime($dl['fecha'])) {
                    error_log("Fecha inválida para factura {$noFactura}: {$dl['fecha']}, usando fecha actual");
                    $fecha = new DateTime();
                } else {
                    $fecha = new DateTime($dl['fecha']);
                }
                $fechaParaDec = clone $fecha;
                $u_f_dec = $fechaParaDec->modify('first day of this month')->format('Y-m-d');
                $u_f_dec_d = strtoupper($fecha->format('M-Y'));

                $tipoDocumento = strtoupper($dl['tipo_documento'] ?? 'FACTURA');
                $tipoA = in_array($dl['t_gasto'], ['Gasto Operativo', 'Hospedaje']) ? 'S' : ($dl['t_gasto'] === 'Combustible' ? 'C' : 'B');
                $tipoDocForUF = '';
                $tipoDocForLines = '';
                $stmt = $this->pdo->prepare("SELECT TipoDoc FROM tipos_documentos WHERE name = ?");
                $stmt->execute([$dl['tipo_documento']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result && !empty($result['TipoDoc'])) {
                    $tipoDocForLines = $result['TipoDoc'];
                    $tipoDocForUF = $tipoDocMap[$tipoDocForLines] ?? $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
                } else {
                    $tipoDocForLines = strtoupper($dl['tipo_documento']);
                    $tipoDocForUF = $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
                }

                // Busca todas las instancias donde se construyen las líneas del documento
// y reemplaza "CostingCode" por "CostingCode2" cuando el código comience con "T"

                 foreach ($detalles as $detalle) {
    $costingCode = trim($centroCostoModel->getCentroCostoById($detalle['id_centro_costo'])['codigo']);
    $accountCode = $detalle['id_cuenta_contable'] ?? null;
    
    // Determinar si usar CostingCode o CostingCode2 basado en el prefijo
    $costingField = 'CostingCode';
    if (strtoupper(substr($costingCode, 0, 1)) === 'T') {
        $costingField = 'CostingCode2';
    }
    
    // Preparar el ItemDescription
    $itemDescription = $detalle['t_gasto'];
    $comments = !empty(trim($detalle['comentarios'])) ? trim($detalle['comentarios']) : '';
    
    $casosEspeciales = ['INGUAT', 'Propina', 'IDP'];
    if (!empty($comments) && !in_array($detalle['t_gasto'], $casosEspeciales)) {
        $itemDescription .= " - " . substr($comments, 0, 100);
    }
    
    if ($tipoDocumento === 'FACTURA' || $tipoDocumento === 'FACTURA ELECTRONICA' || $tipoDocumento === 'FACTURA PEQUEÑO CONTRIBUYENTE') {
        $subtotal = floatval($detalle['p_unitario']);
        $iva = floatval($detalle['iva']);
        $idp = floatval($detalle['idp']);
        $inguat = floatval($detalle['inguat']);
        $propina = floatval($detalle['propina']);
        
        // LÍNEA PRINCIPAL
        if ($subtotal > 0) {
            if ($tipoDocumento === 'FACTURA PEQUEÑO CONTRIBUYENTE') {
                $documentLines[] = [
                    "LineType" => count($documentLines),
                    "ItemDescription" => $itemDescription,
                    "PriceAfterVAT" => $subtotal,
                    "TaxCode" => "EXE",
                    $costingField => $costingCode, // Usar el campo dinámico
                    "AccountCode" => $accountCode,
                    "U_TipoDoc" => $tipoDocForLines,
                    "U_TipoA" => $tipoA
                ];
            } else {
                $documentLines[] = [
                    "LineType" => count($documentLines),
                    "ItemDescription" => $itemDescription,
                    "PriceAfterVAT" => ($subtotal + $iva),
                    "TaxCode" => "IVA",
                    $costingField => $costingCode, // Usar el campo dinámico
                    "AccountCode" => $accountCode,
                    "U_TipoDoc" => $tipoDocForLines,
                    "U_TipoA" => $tipoA
                ];
            }
        }
        
        // LÍNEAS ADICIONALES
        $impuestos = [
            ['valor' => $idp, 'desc' => 'IDP', 'cuenta' => $detalle['id_cuenta_contable_idp'] ?? $accountCode, 'tipoA' => 'P'],
            ['valor' => $inguat, 'desc' => 'INGUAT', 'cuenta' => $detalle['id_cuenta_contable_inguat'] ?? $accountCode, 'tipoA' => 'H'],
        ];
        
        if ($detalle['t_gasto'] === 'Alimentos') {
            $impuestos[] = ['valor' => $propina, 'desc' => 'Propina', 'cuenta' => $detalle['id_cuenta_contable_propina'], 'tipoA' => 'E'];
        }
        
        foreach ($impuestos as $impuesto) {
            if ($impuesto['valor'] > 0) {
                $cuentaInguat = $impuesto['cuenta'];
                 // Si es INGUAT y el centro de costo comienza con "T", usar cuenta diferente
        if ($impuesto['desc'] === 'INGUAT' && strtoupper(substr($costingCode, 0, 1)) === 'T') {
            $cuentaInguat = '611001003'; // Cuenta para centros de costo que empiezan con T
            error_log("Cambiando cuenta INGUAT a $cuentaInguat para centro de costo T: $costingCode");
        }
                $documentLines[] = [
                    "LineType" => count($documentLines),
                    "ItemDescription" => $impuesto['desc'],
                    "PriceAfterVAT" => $impuesto['valor'],
                    "TaxCode" => "EXE",
                    $costingField => $costingCode, // Usar el campo dinámico
                    "AccountCode" => $impuesto['cuenta'],
                    "U_TipoDoc" => $tipoDocForLines,
                    "U_TipoA" => $impuesto['tipoA']
                ];
            }
        }
        
    } else {
        // Otros tipos de documento
        if (floatval($detalle['total_factura']) > 0) {
            $documentLines[] = [
                "LineType" => count($documentLines),
                "ItemDescription" => $itemDescription . " ({$tipoDocumento})",
                "PriceAfterVAT" => floatval($detalle['total_factura']),
                "TaxCode" => "EXE",
                $costingField => $costingCode, // Usar el campo dinámico
                "AccountCode" => $accountCode,
                "U_TipoDoc" => $tipoDocForLines,
                "U_TipoA" => $tipoA
            ];
        }
    }
                }
                $comments = !empty(trim($dl['comentarios'])) ? substr(trim($dl['comentarios']), 0, 254) : 'Sin comentarios';
                $nombreProveedor = !empty(trim($dl['nombre_proveedor'])) ? substr(trim($dl['nombre_proveedor']), 0, 254) : '';
                $nitProveedor = !empty(trim($dl['nit_proveedor'])) ? substr(trim($dl['nit_proveedor']), 0, 20) : '321052';

                $liquidacion = $liquidacionModel->getLiquidacionById($id);
                if (!$liquidacion || !isset($liquidacion['id_usuario'])) {
                    throw new Exception("No se pudo obtener el id_usuario de la liquidación ID $id");
                }

                $usuarioCreador = $usuarioModel->getUsuarioById($liquidacion['id_usuario']);
                $cardCode = !empty($usuarioCreador['clientes']) ? trim($usuarioCreador['clientes']) : 'CCHA0010';
                error_log("Código de cliente para usuario ID {$liquidacion['id_usuario']}: $cardCode");

                $purchaseInvoice = [
                    "DocType" => "dDocument_Service",
                    "CardCode" => $cardCode,
                    "U_CODIGO" => $cardCode,
                    "DocDate" => $docDate,
                    "TaxDate"=> $docDueDate,
                    "DocDueDate"=> $taxDate,
                    "Comments" => $comments,
                    "JournalMemo" => $comments,
                    "U_NIT" => $nitProveedor,
                    "U_NOMBRE" => $nombreProveedor,
                    "U_F_Tipo" => $tipoDocForUF,
                    "Series" => 82,
                    "DocTotal" => $docTotal,
                    "TotalDiscount" => 0.0,
                    "Reference1" => "{$id}-{$noFactura}",
                    "NumAtCard" => $numAtCard,
                    "U_F_DEC" => $u_f_dec,
                    "U_F_DEC_D" => $u_f_dec_d,
                    "DocCurrency" => "QTZ",
                    "DocRate" => 1,
                    "DocumentLines" => $documentLines
                ];

                foreach ($documentLines as &$line) {
                    $line['DiscountPercent'] = 0;
                    $line['DiscountAmount'] = 0;
                }

                $jsonFilePath = "$jsonDir/export_liquidacion_{$id}_{$groupKey}.json";
                $jsonContent = json_encode($purchaseInvoice, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if (file_put_contents($jsonFilePath, "\xEF\xBB\xBF" . $jsonContent) === false) {
                    throw new Exception("No se pudo escribir el archivo JSON: $jsonFilePath");
                }
                error_log("JSON file generated at: $jsonFilePath");

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
                    error_log("SAP Error for grupo {$groupKey} (Factura: {$noFactura}): $curlError");
                    throw new Exception("Error de conexión SAP para factura {$noFactura}: $curlError");
                }

                // Dentro del bloque donde procesas la respuesta de SAP:
                $sapResponse = json_decode($response, true);
                if ($httpCode >= 400 || json_last_error() !== JSON_ERROR_NONE) {
                    $errorMsg = "Error SAP para grupo {$groupKey} (Factura: {$noFactura}): HTTP $httpCode";
                    
                    // Verificar si la respuesta es un JSON válido
                    if (json_last_error() === JSON_ERROR_NONE && isset($sapResponse['error'])) {
                        $errorCode = $sapResponse['error']['code'] ?? 0;
                        
                        // Extraer el mensaje de error
                        $sapErrorMessage = $sapResponse['error']['message']['value'] ?? 
                                          (is_string($sapResponse['error']['message']) ? 
                                          $sapResponse['error']['message'] : 'Error desconocido');
                        
                        $errorMsg .= " - {$sapErrorMessage}";
                        
                        // Si el código es -1116, intentar extraer el código real del mensaje
                        if ($errorCode == -1116) {
                            if (preg_match('/\((\d+)\)/', $sapErrorMessage, $matches)) {
                                $errorCode = (int)$matches[1];
                                error_log("Código real extraído del mensaje: $errorCode");
                            }
                        }
                        
                        error_log("Código de error detectado: $errorCode, Mensaje: $sapErrorMessage");
                        
                        // Intentar manejar el error automáticamente
                        $manejoResultado = $this->manejarErroresSapYReintentar(
                            $errorCode,
                            $sapErrorMessage,
                            $nitProveedor,
                            $nombreProveedor,
                            $noFactura,
                            $cookie,
                            $jsonContent,
                            $sapUrl,
                            $detalles,
                            $detalleLiquidacionModel,
                            $id,
                            $groupKey,
                            $groupedDetalles,
                            $jsonFilePath
                        );

                        if ($manejoResultado['success']) {
                            // Éxito después del manejo del error
                            $results[] = [
                                'no_factura' => $noFactura,
                                'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
                                'success' => true,
                                'message' => $manejoResultado['message'],
                                'filePath' => $manejoResultado['filePath'],
                                'detalle_ids' => $manejoResultado['detalle_ids'],
                                'sap_response' => $manejoResultado['sap_response'],
                                'manejado' => true
                            ];
                            $atLeastOneProcessed = true;
                            continue;
                        } elseif ($manejoResultado['manejable'] === false) {
                            // Error no manejable, proceder con el manejo normal
                            $isDuplicateError = ($errorCode == -5002);
                            if ($isDuplicateError) {
                                foreach ($detalles as $detalle) {
                                    $detalleLiquidacionModel->updateEstado($detalle['id'], 'FINALIZADO');
                                    $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Factura exportada a SAP (duplicado): {$noFactura}, mensaje: {$errorMsg}");
                                }
                                $results[] = [
                                    'no_factura' => $noFactura,
                                    'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
                                    'success' => true,
                                    'message' => "Grupo {$groupKey} (Factura: {$noFactura}) procesada (duplicado, exportada de nuevo)",
                                    'filePath' => $jsonFilePath,
                                    'detalle_ids' => array_column($detalles, 'id'),
                                    'sap_response' => $sapResponse,
                                    'manejado' => false
                                ];
                                $atLeastOneProcessed = true;
                                continue;
                            }
                        }
                    } else {
                        // Respuesta no es JSON válido o no tiene la estructura esperada
                        $errorMsg .= " - Respuesta no válida de SAP";
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $errorMsg .= " (Error JSON: " . json_last_error_msg() . ")";
                        }
                        error_log("Respuesta SAP no válida: " . substr($response, 0, 500));
                    }
                    
                    throw new Exception($errorMsg);
                }

                foreach ($detalles as $detalle) {
                    $detalleLiquidacionModel->updateEstado($detalle['id'], 'FINALIZADO');
                    $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Factura exportada a SAP: {$noFactura}");
                }

                $results[] = [
                    'no_factura' => $noFactura,
                    'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
                    'success' => true,
                    'message' => "Grupo {$groupKey} (Factura: {$noFactura}) enviada a SAP exitosamente",
                    'filePath' => $jsonFilePath,
                    'detalle_ids' => array_column($detalles, 'id'),
                    'sap_response' => $sapResponse
                ];
                $atLeastOneProcessed = true;
            } catch (Exception $e) {
                error_log("Error processing grupo {$groupKey} (Factura: {$noFactura}): {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
                $results[] = [
                    'no_factura' => $noFactura,
                    'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
                    'success' => false,
                    'error' => $e->getMessage(),
                    'filePath' => $jsonFilePath ?? null,
                    'detalle_ids' => array_column($detalles, 'id')
                ];
                foreach ($detalles as $detalle) {
                    $detalleLiquidacionModel->updateEstado($detalle['id'], 'EN_CORRECCION');
                    $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'ERROR_EXPORTACION_SAP', "Error al exportar a SAP: {$e->getMessage()} para factura {$noFactura}");
                }
            }
        }

        // SAP Logout
        error_log("Intentando logout de SAP");
        $logoutResult = $this->logout_sap();
        if (!$logoutResult['success']) {
            error_log("SAP Logout Failed: {$logoutResult['error']}");
        }

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $erroresManejados = array_filter($results, function($r) {
            return isset($r['manejado']) && $r['manejado'] === true && $r['success'] === true;
        });
        $response = [
            'success' => $atLeastOneProcessed,
            'message' => $atLeastOneProcessed
                ? "Exportación completada: $successCount facturas procesadas exitosamente"
                : "Exportación fallida: ninguna factura procesada exitosamente",
            'results' => $results,
            'validationResults' => $validationResults,
            'nitPnResults' => $nitPnResults,
            'nitPnData' => $nitPnData,
            'erroresManejados' => $erroresManejados
        ];

        if ($atLeastOneProcessed) {
            $liquidacionModel->updateEstado($id, 'FINALIZADO');
            $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Exportación completada: $successCount facturas exportadas");
            $this->pdo->commit();
        } else {
            $this->pdo->rollBack();
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

//EXPORTA A SAP TEST

// private function login_sap($db)
//     {
//         $usuario = 'manager';
//         $contrasena = 'ACtest';
//         $sociedad = $db;

//         $curl = curl_init();

//         $urlServer = 'https://192.168.1.9:50000/b1s/v1/';
//         $sboObjType = 'Login';

//         curl_setopt_array($curl, [
//             CURLOPT_PORT => 50000,
//             CURLOPT_URL => $urlServer . $sboObjType,
//             CURLOPT_SSL_VERIFYHOST => false, // Insecure; use valid SSL in production
//             CURLOPT_SSL_VERIFYPEER => false, // Insecure; use valid SSL in production
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => "",
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 30,
//             CURLOPT_COOKIEJAR => __DIR__ . "/cookie.txt",
//             CURLOPT_COOKIEFILE => __DIR__ . "/cookie.txt",
//             CURLOPT_CUSTOMREQUEST => "POST",
//             CURLOPT_POSTFIELDS => json_encode([
//                 "UserName" => $usuario,
//                 "Password" => $contrasena,
//                 "CompanyDB" => $sociedad
//             ], JSON_UNESCAPED_UNICODE),
//             CURLOPT_HTTPHEADER => [
//                 "Content-Type: application/json",
//                 "Cache-Control: no-cache"
//             ],
//         ]);

//         $response = curl_exec($curl);
//         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//         $curlError = curl_error($curl);
//         curl_close($curl);

//         if ($response === false || $curlError) {
//             $errorMsg = $curlError ? "cURL Error: $curlError" : "No response received";
//             error_log("SAP Login Failed: $errorMsg");
//             return ['success' => false, 'error' => $errorMsg];
//         }

//         if ($httpCode !== 200) {
//             error_log("SAP Login Failed: HTTP $httpCode - $response");
//             return ['success' => false, 'error' => "HTTP $httpCode - $response"];
//         }

//         $sessionData = json_decode($response, true);
//         if (json_last_error() !== JSON_ERROR_NONE || !isset($sessionData['SessionId'])) {
//             error_log("SAP Login Failed: Invalid JSON or no SessionId - $response");
//             return ['success' => false, 'error' => 'Invalid JSON or no SessionId returned'];
//         }

//         return [
//             'success' => true,
//             'sessionId' => $sessionData['SessionId'],
//             'routeId' => $sessionData['RouteId'] ?? '.guid',
//             'response' => $sessionData
//         ];
// }

// private function logout_sap()
// {
//     $curl = curl_init();

//     $urlServer = 'https://192.168.1.9:50000/b1s/v1/';
//     $sboObjType = 'Logout';

//     curl_setopt_array($curl, [
//         CURLOPT_PORT => 50000,
//         CURLOPT_URL => $urlServer . $sboObjType,
//         CURLOPT_SSL_VERIFYHOST => false, // Insecure; use valid SSL in production
//         CURLOPT_SSL_VERIFYPEER => false, // Insecure; use valid SSL in production
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => "",
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 30,
//         CURLOPT_COOKIEJAR => __DIR__ . "/cookie.txt",
//         CURLOPT_COOKIEFILE => __DIR__ . "/cookie.txt",
//         CURLOPT_CUSTOMREQUEST => "POST",
//         CURLOPT_HTTPHEADER => [
//             "Content-Type: application/json",
//             "Cache-Control: no-cache"
//         ],
//     ]);

//     $response = curl_exec($curl);
//     $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//     $curlError = curl_error($curl);
//     curl_close($curl);

//     if ($response === false || $curlError) {
//         $errorMsg = $curlError ? "cURL Error: $curlError" : "No response received";
//         error_log("SAP Logout Failed: $errorMsg");
//         return ['success' => false, 'error' => $errorMsg];
//     }

//     if ($httpCode !== 204) {
//         error_log("SAP Logout Failed: HTTP $httpCode - $response");
//         return ['success' => false, 'error' => "HTTP $httpCode - $response"];
//     }

//     $cookieFile = __DIR__ . '/cookie.txt';
//     if (file_exists($cookieFile) && is_writable($cookieFile)) {
//         unlink($cookieFile); // Clean up cookie file
//     } elseif (file_exists($cookieFile)) {
//         error_log("Warning: Could not delete cookie file; not writable");
//     }

//     return ['success' => true];
// }

// Funciones para errores al exportar 

// private function manejarErroresSapYReintentar($errorCode, $errorMessage, $nitProveedor, $nombreProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
// {
//     error_log("Manejando error SAP: $errorCode - $errorMessage para NIT: $nitProveedor");
    
//     // DEBUG: Log completo del mensaje para diagnóstico
//     error_log("Mensaje de error completo: " . $errorMessage);
    
//     // Si el código es -1116, intentar extraer el código real del mensaje
//     if ($errorCode == -1116) {
//         error_log("Código -1116 detectado, buscando código real en mensaje: $errorMessage");
        
//         // Buscar patrones de códigos en el mensaje
//         if (preg_match('/\((\d+)\)/', $errorMessage, $matches)) {
//             $errorCode = (int)$matches[1];
//             error_log("Código real extraído del mensaje: $errorCode");
//         } else {
//             error_log("No se pudo extraer código del mensaje: $errorMessage");
//         }
//     }
    
//     // Mapeo de códigos de error
//     $codigosError = [
//         2021032504 => 'NIT Pequeño Contribuyente',
//         18000018 => 'NIT no existe',
//         20170505 => 'No se permiten descuentos'
//     ];
    
//     error_log("Procesando código de error: $errorCode - " . ($codigosError[$errorCode] ?? 'Desconocido'));
    
//     switch ($errorCode) {
//         case 2021032504: // NIT Pequeño Contribuyente
//             error_log("Error de NIT Pequeño Contribuyente detectado para factura {$noFactura}");
//             return $this->manejarErrorPequeñoContribuyente($nitProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath);
            
//         case 18000018: // NIT no existe
//             error_log("NIT no encontrado para factura {$noFactura}");
//             return $this->manejarErrorNitNoExiste($nitProveedor, $nombreProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath);
            
//         case 20170505: // No se permiten descuentos en esta factura
//             error_log("Error de descuentos detectado para factura {$noFactura}");
//             return $this->manejarErrorDescuentosNoPermitidos($jsonContent, $cookie, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath);
            
//         default:
//             error_log("Error no manejable ($errorCode): $errorMessage");
//             return [
//                 'success' => false,
//                 'error' => $errorMessage,
//                 'manejable' => false,
//                 'error_code' => $errorCode
//             ];
//     }
// }

// private function manejarErrorPequeñoContribuyente($nitProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
// {
//     try {
//         // Consultar información del NIT en @NIT_PN
//         $conn = $this->CONEXION_HANA("T_GT_AGROCENTRO_2016");
//         $sql = 'SELECT "U_Validador" FROM "@NIT_PN" WHERE "U_NIT" = ?';
        
//         $stmt = odbc_prepare($conn, $sql);
//         if (!$stmt) {
//             $sql = 'SELECT "U_Validador" FROM "T_GT_AGROCENTRO_2016"."@NIT_PN" WHERE "U_NIT" = ?';
//             $stmt = odbc_prepare($conn, $sql);
//         }
        
//         if ($stmt && odbc_execute($stmt, [$nitProveedor])) {
//             if ($row = odbc_fetch_array($stmt)) {
//                 $uValidador = $row['U_Validador'];
//                 error_log("U_Validador actual para NIT {$nitProveedor}: {$uValidador}");
                
//                 if ($uValidador === 'S') {
//                     // Actualizar U_Validador a 'N'
//                     $updateSql = 'UPDATE "@NIT_PN" SET "U_Validador" = ? WHERE "U_NIT" = ?';
//                     $updateStmt = odbc_prepare($conn, $updateSql);
                    
//                     if (!$updateStmt) {
//                         $updateSql = 'UPDATE "T_GT_AGROCENTRO_2016"."@NIT_PN" SET "U_Validador" = ? WHERE "U_NIT" = ?';
//                         $updateStmt = odbc_prepare($conn, $updateSql);
//                     }
                    
//                     if ($updateStmt && odbc_execute($updateStmt, ['N', $nitProveedor])) {
//                         error_log("U_Validador actualizado exitosamente a 'N' para NIT {$nitProveedor}");
//                         odbc_close($conn);
                        
//                         // Reintentar envío a SAP
//                         return $this->reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "U_Validador actualizado");
//                     } else {
//                         error_log("Error al ejecutar update de U_Validador: " . odbc_errormsg($conn));
//                     }
//                 } else {
//                     error_log("U_Validador ya es 'N' para NIT {$nitProveedor}");
//                 }
//             } else {
//                 error_log("NIT {$nitProveedor} no encontrado en @NIT_PN");
                
//                 // Si no existe, insertarlo
//                 $codigo = 13333;
//                 $maxIntentos = 1000;
//                 $codigoEncontrado = false;
                
//                 for ($i = 0; $i < $maxIntentos; $i++) {
//                     $codigoProbable = $codigo + $i;
                    
//                     $sqlCheck = 'SELECT COUNT(*) as count FROM "@NIT_PN" WHERE "Code" = ?';
//                     $stmtCheck = odbc_prepare($conn, $sqlCheck);
                    
//                     if ($stmtCheck && odbc_execute($stmtCheck, [(string)$codigoProbable])) {
//                         if ($rowCheck = odbc_fetch_array($stmtCheck)) {
//                             if (isset($rowCheck['count']) && $rowCheck['count'] == 0) {
//                                 $codigo = $codigoProbable;
//                                 $codigoEncontrado = true;
//                                 break;
//                             }
//                         }
//                     }
//                 }
                
//                 if ($codigoEncontrado) {
//                     // Insertar el NIT con U_Validador = 'N'
//                     $sqlInsert = 'INSERT INTO "@NIT_PN" ("Code", "Name", "U_NIT", "U_Razon", "U_Validador") VALUES (?, ?, ?, ?, ?)';
//                     $stmtInsert = odbc_prepare($conn, $sqlInsert);
                    
//                     if ($stmtInsert && odbc_execute($stmtInsert, [(string)$codigo, (string)$codigo, $nitProveedor, 'Proveedor Automático', 'N'])) {
//                         error_log("NIT {$nitProveedor} insertado con U_Validador = 'N'");
//                         odbc_close($conn);
                        
//                         // Reintentar envío a SAP
//                         return $this->reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "NIT insertado con U_Validador = 'N'");
//                     }
//                 }
//             }
//         }
        
//         odbc_close($conn);
        
//         return [
//             'success' => false,
//             'error' => "No se pudo actualizar U_Validador para NIT {$nitProveedor}",
//             'manejable' => false
//         ];
        
//     } catch (Exception $e) {
//         error_log("Error al manejar pequeño contribuyente: " . $e->getMessage());
//         return [
//             'success' => false,
//             'error' => $e->getMessage(),
//             'manejable' => false
//         ];
//     }
// }

// private function manejarErrorNitNoExiste($nitProveedor, $nombreProveedor, $noFactura, $cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
// {
//     try {
//         error_log("Intentando insertar NIT {$nitProveedor} en @NIT_PN");
        
//         // Conectar a HANA para insertar directamente en @NIT_PN
//         $conn = $this->CONEXION_HANA("T_GT_AGROCENTRO_2016");
//         if (!$conn) {
//             throw new Exception("No se pudo conectar a la base de datos HANA");
//         }
        
//         // Buscar el próximo código disponible empezando desde 13333
//         $codigo = 13333;
//         $maxIntentos = 1000;
//         $codigoEncontrado = false;
        
//         error_log("Buscando código disponible en @NIT_PN para NIT: $nitProveedor");
        
//         for ($i = 0; $i < $maxIntentos; $i++) {
//             $codigoProbable = $codigo + $i;
            
//             // Verificar si el código ya existe
//             $sqlCheck = 'SELECT "Code" FROM "@NIT_PN" WHERE "Code" = ?';
//             $stmtCheck = odbc_prepare($conn, $sqlCheck);
            
//             if (!$stmtCheck) {
//                 $sqlCheck = 'SELECT "Code" FROM "T_GT_AGROCENTRO_2016"."@NIT_PN" WHERE "Code" = ?';
//                 $stmtCheck = odbc_prepare($conn, $sqlCheck);
//             }
            
//             if ($stmtCheck) {
//                 // Convertir a string ya que SAP espera strings
//                 $codigoStr = (string)$codigoProbable;
//                 $execCheck = odbc_execute($stmtCheck, [$codigoStr]);
                
//                 if ($execCheck) {
//                     // Si no hay resultados, el código está disponible
//                     if (!odbc_fetch_array($stmtCheck)) {
//                         $codigo = $codigoProbable;
//                         $codigoEncontrado = true;
//                         error_log("Código disponible encontrado: $codigo");
//                         break;
//                     } else {
//                         error_log("Código $codigoProbable ya existe, probando siguiente...");
//                     }
//                 } else {
//                     error_log("Error al verificar código $codigoProbable: " . odbc_errormsg($conn));
//                 }
//             }
//         }
        
//         if (!$codigoEncontrado) {
//             odbc_close($conn);
//             throw new Exception("No se pudo encontrar un código disponible en @NIT_PN después de $maxIntentos intentos");
//         }
        
//         error_log("Insertando en @NIT_PN: Code=$codigo, U_NIT=$nitProveedor, U_Razon=$nombreProveedor");
        
//         // Insertar directamente en la tabla @NIT_PN
//         $sqlInsert = 'INSERT INTO "@NIT_PN" ("Code", "Name", "U_NIT", "U_Razon", "U_Validador") VALUES (?, ?, ?, ?, ?)';
//         $stmtInsert = odbc_prepare($conn, $sqlInsert);
        
//         if (!$stmtInsert) {
//             $sqlInsert = 'INSERT INTO "T_GT_AGROCENTRO_2016"."@NIT_PN" ("Code", "Name", "U_NIT", "U_Razon", "U_Validador") VALUES (?, ?, ?, ?, ?)';
//             $stmtInsert = odbc_prepare($conn, $sqlInsert);
//         }
        
//         if (!$stmtInsert) {
//             odbc_close($conn);
//             throw new Exception("Error al preparar inserción en @NIT_PN: " . odbc_errormsg($conn));
//         }
        
//         // Code y Name deben ser iguales (solo números convertidos a string)
//         $codeStr = (string)$codigo;
//         $nameStr = (string)$codigo;
//         $uValidador = 'N'; // Por defecto 'N' para no pequeño contribuyente
        
//         // Limitar la longitud de U_Razon si es necesario
//         $uRazon = substr($nombreProveedor, 0, 100);
        
//         $execInsert = odbc_execute($stmtInsert, [$codeStr, $nameStr, $nitProveedor, $uRazon, $uValidador]);
        
//         if (!$execInsert) {
//             $errorMsg = "Error al insertar en @NIT_PN: " . odbc_errormsg($conn);
//             error_log($errorMsg);
            
//             // Si es error de duplicado, intentar con otro código
//             if (strpos(odbc_errormsg($conn), 'unique constraint') !== false) {
//                 error_log("Código $codigo ya existe, intentando con siguiente...");
//                 odbc_close($conn);
                
//                 // Llamar recursivamente con el siguiente código
//                 return $this->manejarErrorNitNoExiste(
//                     $nitProveedor, 
//                     $nombreProveedor, 
//                     $noFactura, 
//                     $cookie, 
//                     $jsonContent, 
//                     $sapUrl, 
//                     $detalles, 
//                     $detalleLiquidacionModel, 
//                     $id, 
//                     $groupKey, 
//                     $groupedDetalles, 
//                     $jsonFilePath
//                 );
//             }
            
//             odbc_close($conn);
//             throw new Exception($errorMsg);
//         }
        
//         odbc_close($conn);
        
//         error_log("NIT insertado exitosamente en @NIT_PN: $nitProveedor con código $codigo");
        
//         // Reintentar envío a SAP después de insertar en la tabla
//         return $this->reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "NIT insertado en @NIT_PN");

//     } catch (Exception $e) {
//         error_log("Error al insertar NIT en @NIT_PN: " . $e->getMessage());
//         return [
//             'success' => false,
//             'error' => $e->getMessage(),
//             'manejable' => false
//         ];
//     }
// }

// private function manejarErrorDescuentosNoPermitidos($jsonContent, $cookie, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath)
// {
//     try {
//         error_log("Manejando error de descuentos no permitidos (20170505)");
        
//         // Decodificar el JSON para modificar los valores incorrectos
//         $invoiceData = json_decode($jsonContent, true);
        
//         // Si hay múltiples detalles en este grupo, recalcular el DocTotal
//         if (count($detalles) > 1) {
//             $nuevoDocTotal = 0;
//             foreach ($detalles as $detalle) {
//                 $nuevoDocTotal += floatval($detalle['total_factura']);
//             }
            
//             error_log("Recalculando DocTotal: {$invoiceData['DocTotal']} -> {$nuevoDocTotal} (para " . count($detalles) . " detalles)");
//             $invoiceData['DocTotal'] = $nuevoDocTotal;
            
//             // También asegurarse de que cada línea tenga el PriceAfterVAT correcto
//             if (isset($invoiceData['DocumentLines']) && is_array($invoiceData['DocumentLines'])) {
//                 foreach ($invoiceData['DocumentLines'] as $index => &$line) {
//                     if (isset($detalles[$index])) {
//                         $line['PriceAfterVAT'] = floatval($detalles[$index]['total_factura']);
//                         error_log("Ajustando línea {$index}: PriceAfterVAT = {$line['PriceAfterVAT']}");
//                     }
//                 }
//             }
//         }
        
//         // Forzar TotalDiscount a 0 si existe
//         if (isset($invoiceData['TotalDiscount'])) {
//             error_log("TotalDiscount encontrado: {$invoiceData['TotalDiscount']}, forzando a 0");
//             $invoiceData['TotalDiscount'] = 0;
//         }
        
//         // También verificar en las líneas del documento
//         if (isset($invoiceData['DocumentLines']) && is_array($invoiceData['DocumentLines'])) {
//             foreach ($invoiceData['DocumentLines'] as &$line) {
//                 if (isset($line['DiscountPercent'])) {
//                     error_log("DiscountPercent encontrado en línea: {$line['DiscountPercent']}, forzando a 0");
//                     $line['DiscountPercent'] = 0;
//                 }
//                 if (isset($line['DiscountAmount'])) {
//                     error_log("DiscountAmount encontrado en línea: {$line['DiscountAmount']}, forzando a 0");
//                     $line['DiscountAmount'] = 0;
//                 }
//             }
//         }
        
//         // Codificar de nuevo a JSON
//         $modifiedJsonContent = json_encode($invoiceData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
//         // Guardar el JSON modificado
//         if (file_put_contents($jsonFilePath, "\xEF\xBB\xBF" . $modifiedJsonContent) === false) {
//             throw new Exception("No se pudo escribir el archivo JSON modificado: $jsonFilePath");
//         }
        
//         error_log("JSON modificado guardado en: $jsonFilePath");
        
//         // Reintentar envío a SAP con el JSON modificado
//         return $this->reintentarEnvioSAP($cookie, $modifiedJsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, "DocTotal y descuentos corregidos");

//     } catch (Exception $e) {
//         error_log("Error al manejar descuentos no permitidos: " . $e->getMessage());
//         return [
//             'success' => false,
//             'error' => $e->getMessage(),
//             'manejable' => false
//         ];
//     }
// }

// private function reintentarEnvioSAP($cookie, $jsonContent, $sapUrl, $detalles, $detalleLiquidacionModel, $id, $groupKey, $groupedDetalles, $jsonFilePath, $accionRealizada)
// {
//     error_log("Reintentando envío a SAP después de: $accionRealizada");
    
//     $ch = curl_init($sapUrl);
//     curl_setopt_array($ch, [
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_POST => true,
//         CURLOPT_HTTPHEADER => [
//             'Content-Type: application/json',
//             'Cookie: ' . $cookie
//         ],
//         CURLOPT_POSTFIELDS => $jsonContent,
//         CURLOPT_SSL_VERIFYPEER => false,
//         CURLOPT_SSL_VERIFYHOST => false,
//     ]);

//     $response = curl_exec($ch);
//     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//     $curlError = curl_error($ch);
//     curl_close($ch);

//     if ($response === false || $curlError) {
//         error_log("Error en reintento: $curlError");
//         return [
//             'success' => false,
//             'error' => "Error de conexión SAP al reintentar: $curlError",
//             'manejable' => false
//         ];
//     }

//     $sapResponse = json_decode($response, true);
//     if ($httpCode >= 400 || json_last_error() !== JSON_ERROR_NONE) {
//         $errorMsg = "Error SAP al reintentar: HTTP $httpCode";
//         if (isset($sapResponse['error']['message']['value'])) {
//             $errorMsg .= " - {$sapResponse['error']['message']['value']}";
//         }
//         error_log($errorMsg);
//         return [
//             'success' => false,
//             'error' => $errorMsg,
//             'manejable' => false
//         ];
//     }

//     // Éxito en el reintento
//     error_log("Reintento exitoso después de: $accionRealizada");
    
//     foreach ($detalles as $detalle) {
//         $detalleLiquidacionModel->updateEstado($detalle['id'], 'FINALIZADO');
//         $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Factura exportada a SAP después de corrección: {$accionRealizada}");
//     }

//     return [
//         'success' => true,
//         'message' => "Grupo {$groupKey} enviado a SAP exitosamente después de corrección",
//         'filePath' => $jsonFilePath,
//         'detalle_ids' => array_map(function($detalle) { return $detalle['id']; }, $detalles),
//         'sap_response' => $sapResponse,
//         'manejable' => true
//     ];
// }

// public function exportar($id, $docDate = null)
// {
//     ob_start();
//     error_log("Iniciando exportar para id: $id" . ($docDate ? " con docDate: $docDate" : ""));

//     if (!isset($_SESSION['user_id'])) {
//         error_log('Error: No hay session user_id en exportar');
//         ob_end_clean();
//         header('Content-Type: application/json; charset=utf-8');
//         http_response_code(401);
//         echo json_encode(['error' => 'No autorizado'], JSON_UNESCAPED_UNICODE);
//         exit;
//     }

//     try {
//         // Validate user and permissions
//         error_log("Cargando modelo Usuario");
//         if (!class_exists('Usuario')) {
//             throw new Exception('Clase Usuario no encontrada');
//         }
//         $usuarioModel = new Usuario();
//         error_log("Obteniendo usuario por ID: {$_SESSION['user_id']}");
//         $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
//         if (!$usuario || !$usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')) {
//             error_log('Error: No tienes permiso para exportar liquidaciones');
//             ob_end_clean();
//             header('Content-Type: application/json; charset=utf-8');
//             http_response_code(403);
//             echo json_encode(['error' => 'No tienes permiso para exportar liquidaciones'], JSON_UNESCAPED_UNICODE);
//             exit;
//         }

//         // Load liquidacion
//         error_log("Cargando modelo Liquidacion");
//         if (!class_exists('Liquidacion')) {
//             throw new Exception('Clase Liquidacion no encontrada');
//         }
//         $liquidacionModel = new Liquidacion();
//         error_log("Obteniendo liquidación por ID: $id");
//         $liquidacion = $liquidacionModel->getLiquidacionById($id);
//         if (!$liquidacion) {
//             ob_end_clean();
//             header('Content-Type: application/json; charset=utf-8');
//             http_response_code(404);
//             echo json_encode(['error' => 'Liquidación no encontrada'], JSON_UNESCAPED_UNICODE);
//             exit;
//         }

//         // Check liquidacion state
//         $invalidStates = ['EN_CORRECCION', 'PENDIENTE_AUTORIZACION', 'EN_PROCESO'];
//         if (in_array($liquidacion['estado'], $invalidStates)) {
//             $stateMessage = "Solo se pueden exportar liquidaciones en estado PENDIENTE_REVISION_CONTABILIDAD o FINALIZADO, no en {$liquidacion['estado']}";
//             ob_end_clean();
//             header('Content-Type: application/json; charset=utf-8');
//             http_response_code(400);
//             echo json_encode(['error' => $stateMessage], JSON_UNESCAPED_UNICODE);
//             exit;
//         }

//         // Fetch detalle_liquidacion records
//         error_log("Cargando modelo DetalleLiquidacion");
//         if (!class_exists('DetalleLiquidacion')) {
//             throw new Exception('Clase DetalleLiquidacion no encontrada');
//         }
//         $detalleLiquidacionModel = new DetalleLiquidacion();
//         error_log("Obteniendo detalles por liquidación ID: $id");
//         $detalleLiquidaciones = $detalleLiquidacionModel->getDetallesByLiquidacionId($id);
//         if (empty($detalleLiquidaciones)) {
//             ob_end_clean();
//             header('Content-Type: application/json; charset=utf-8');
//             http_response_code(404);
//             echo json_encode(['error' => 'No se encontraron detalles de liquidación'], JSON_UNESCAPED_UNICODE);
//             exit;
//         }

//         // Filter detalle_liquidaciones to only include PENDIENTE_REVISION_CONTABILIDAD state
//         $pendingDetalles = array_filter($detalleLiquidaciones, function ($dl) {
//             return $dl['estado'] === 'PENDIENTE_REVISION_CONTABILIDAD';
//         });

//         if (empty($pendingDetalles)) {
//             ob_end_clean();
//             header('Content-Type: application/json; charset=utf-8');
//             http_response_code(400);
//             echo json_encode(['error' => 'No se encontraron detalles en estado PENDIENTE_REVISION_CONTABILIDAD para exportar'], JSON_UNESCAPED_UNICODE);
//             exit;
//         }

//         // Collect unique NITs from detalle_liquidaciones
//         $nitsToSearch = array_unique(array_map(function ($dl) {
//             return !empty(trim($dl['nit_proveedor'])) ? trim($dl['nit_proveedor']) : '321052';
//         }, $pendingDetalles));
//         error_log("NITs a buscar en @NIT_PN: " . implode(', ', $nitsToSearch));

//         // Query @NIT_PN table in HANA for the collected NITs
//         error_log("Realizando consulta a tabla @NIT_PN para NITs específicos");
//         $nitPnData = [];
//         try {
//             $conn = $this->CONEXION_HANA("T_GT_AGROCENTRO_2016");
//             $placeholders = implode(',', array_fill(0, count($nitsToSearch), '?'));
//             $sql = 'SELECT "Code","Name","U_Razon","U_NIT","U_Validador" FROM "@NIT_PN" WHERE "U_NIT" IN (' . $placeholders . ')';
            
//             $stmt = odbc_prepare($conn, $sql);
//             if (!$stmt) {
//                 error_log("Primer intento fallido, probando con esquema T_GT_AGROCENTRO_2016");
//                 $sql = 'SELECT "Code","Name","U_Razon","U_NIT","U_Validador" FROM "T_GT_AGROCENTRO_2016"."@NIT_PN" WHERE "U_NIT" IN (' . $placeholders . ')';
//                 $stmt = odbc_prepare($conn, $sql);
//                 if (!$stmt) {
//                     throw new Exception("Error al preparar la consulta: " . odbc_errormsg($conn));
//                 }
//             }

//             $exec = odbc_execute($stmt, $nitsToSearch);
//             if (!$exec) {
//                 throw new Exception("Error al ejecutar la consulta: " . odbc_errormsg($conn));
//             }

//             while ($row = odbc_fetch_array($stmt)) {
//                 $nitPnData[] = array_map(function ($value) {
//                     return is_string($value) ? utf8_encode($value) : $value;
//                 }, $row);
//                 error_log("Resultado @NIT_PN: Code={$row['Code']}, Name={$row['Name']}, U_Razon={$row['U_Razon']}, U_NIT={$row['U_NIT']}, U_Validador={$row['U_Validador']}");
//             }

//             odbc_close($conn);

//             error_log("Consulta @NIT_PN exitosa. Resultados: " . count($nitPnData));
//             if (empty($nitPnData)) {
//                 error_log("No se encontraron resultados para los NITs: " . implode(', ', $nitsToSearch));
//                 $nitPnData = ['info' => 'No se encontraron registros en @NIT_PN para los NITs especificados'];
//             }
//         } catch (Exception $e) {
//             error_log("Error en consulta @NIT_PN: " . $e->getMessage());
//             $nitPnData = ['error' => $e->getMessage()];
//         }
//         $nitPnResults = $nitPnData;

//         // Start transaction
//         $this->pdo->beginTransaction();

//         // Initialize CentroCosto model
//         error_log("Cargando modelo CentroCosto");
//         if (!class_exists('CentroCosto')) {
//             throw new Exception('Clase CentroCosto no encontrada');
//         }
//         $centroCostoModel = new CentroCosto();

//         $results = [];
//         $validationResults = [];
//         $validDetalles = [];
//         $jsonDir = __DIR__ . "/json";
//         if (!file_exists($jsonDir)) {
//             if (!mkdir($jsonDir, 0777, true)) {
//                 throw new Exception('No se pudo crear el directorio JSON: ' . $jsonDir);
//             }
//         }
//         if (!is_writable($jsonDir)) {
//             throw new Exception('El directorio JSON no es escribible: ' . $jsonDir);
//         }
//         $timestamp = date('Ymd\THis');

//         // Group detalles by no_factura and grupo_id
//         $groupedDetalles = [];
//         foreach ($pendingDetalles as $index => $dl) {
//             $groupKey = $dl['grupo_id'] == 0 ? $dl['no_factura'] . '_' . $dl['id'] : $dl['no_factura'] . '_' . $dl['grupo_id'];
//             if (!isset($groupedDetalles[$groupKey])) {
//                 $groupedDetalles[$groupKey] = [
//                     'detalles' => [],
//                     'indices' => [],
//                     'ids' => [],
//                     'no_factura' => $dl['no_factura'],
//                     'grupo_id' => $dl['grupo_id']
//                 ];
//             }
//             $groupedDetalles[$groupKey]['detalles'][] = $dl;
//             $groupedDetalles[$groupKey]['indices'][] = $index;
//             $groupedDetalles[$groupKey]['ids'][] = $dl['id'];
//         }

//         // Validate grouped invoices
//         foreach ($groupedDetalles as $groupKey => $group) {
//             $detalles = $group['detalles'];
//             $indices = $group['indices'];
//             $noFactura = $group['no_factura'];
//             try {
//                 error_log("Validando grupo {$groupKey} (Factura: {$noFactura}, Grupo ID: {$group['grupo_id']}) con " . count($detalles) . " detalles");

//                 foreach ($detalles as $dl) {
//                     $index = array_search($dl['id'], $group['ids']);
//                     $costingCode = null;
//                     if (!empty($dl['id_centro_costo'])) {
//                         $centroCosto = $centroCostoModel->getCentroCostoById($dl['id_centro_costo']);
//                         if ($centroCosto && !empty($centroCosto['codigo'])) {
//                             $costingCode = trim($centroCosto['codigo']);
//                             error_log("CostingCode encontrado para id_centro_costo {$dl['id_centro_costo']}: $costingCode");
//                         } else {
//                             error_log("No se encontró código para id_centro_costo {$dl['id_centro_costo']} o centro no existe");
//                             throw new Exception("id_centro_costo {$dl['id_centro_costo']} no tiene un CostingCode válido para factura {$noFactura}");
//                         }
//                     } else {
//                         error_log("id_centro_costo no especificado para detalle id {$dl['id']}");
//                         throw new Exception("id_centro_costo no especificado para factura {$noFactura}");
//                     }

//                     if ($dl['t_gasto'] === 'Alimentos' && floatval($dl['propina']) > 0) {
//                         if (empty($dl['id_cuenta_contable_propina'])) {
//                             error_log("id_cuenta_contable_propina no especificado para factura {$noFactura} con t_gasto=Alimentos y propina={$dl['propina']}");
//                             throw new Exception("id_cuenta_contable_propina no especificado para factura {$noFactura} con propina");
//                         }
//                     }

//                     $tipoDocMap = [
//                         'FACTURA' => 'FN',
//                         'FACTURA ELECTRONICA' => 'FEL',
//                         'FACTURA PEQUEÑO CONTRIBUYENTE' => 'FP',
//                         'RECIBO FISCAL' => 'RF',
//                         'OTROS DOCUMENTOS' => 'OT',
//                         'FPC' => 'FP',
//                         'REF' => 'RF',
//                         'DECLARACION ADUANERA' => 'DA',
//                         'FACTURA ELECTRONICA TIPO FACE' => 'FCE',
//                         'FACTURA DEL EXTERIOR' => 'FDE',
//                         'POLIZA' => 'ID'
//                     ];

//                     $validTipoDocValues = ['DA', 'FCE', 'FE', 'NC', 'FPC', 'FC', 'FDE', 'FEL', 'OT', 'REF', 'ID', 'MC', 'MI'];

//                     $tipoDocForUF = '';
//                     $tipoDocForLines = '';
//                     $stmt = $this->pdo->prepare("SELECT TipoDoc FROM tipos_documentos WHERE name = ?");
//                     $stmt->execute([$dl['tipo_documento']]);
//                     $result = $stmt->fetch(PDO::FETCH_ASSOC);
//                     if ($result && !empty($result['TipoDoc'])) {
//                         $tipoDocForLines = $result['TipoDoc'];
//                         $tipoDocForUF = $tipoDocMap[$tipoDocForLines] ?? $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
//                         error_log("TipoDoc encontrado para tipo_documento {$dl['tipo_documento']}: $tipoDocForLines (U_F_Tipo: $tipoDocForUF)");
//                     } else {
//                         $tipoDocForLines = strtoupper($dl['tipo_documento']);
//                         $tipoDocForUF = $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
//                         error_log("No se encontró TipoDoc para tipo_documento {$dl['tipo_documento']}, usando valor mapeado: U_TipoDoc=$tipoDocForLines, U_F_Tipo=$tipoDocForUF");
//                     }

//                     if (!in_array($tipoDocForLines, $validTipoDocValues)) {
//                         throw new Exception("U_TipoDoc inválido: $tipoDocForLines para factura {$noFactura}");
//                     }
//                     if (!in_array($tipoDocForUF, ['FN', 'FP', 'RF', 'DA', 'FCE', 'FDE', 'FEL', 'OT', 'ID', 'MC', 'MI'])) {
//                         throw new Exception("U_F_Tipo inválido: $tipoDocForUF para factura {$noFactura}");
//                     }
//                 }

//                 $documentLines = [];
//                 $docTotal = 0;
//                 foreach ($detalles as $detalle) {
//                     $docTotal += floatval($detalle['total_factura']);
//                 }
//                 error_log("DocTotal calculado para factura {$noFactura}: {$docTotal} (suma de " . count($detalles) . " detalles)");
//                 $tipoDocumento = strtoupper($detalles[0]['tipo_documento'] ?? 'FACTURA');
//                 $tipoA = in_array($detalles[0]['t_gasto'], ['Gasto Operativo', 'Hospedaje']) ? 'S' : ($detalles[0]['t_gasto'] === 'Combustible' ? 'C' : 'B');
                
//                 foreach ($detalles as $dl) {
//                     $costingCode = trim($centroCostoModel->getCentroCostoById($dl['id_centro_costo'])['codigo']);
//                     $accountCode = $dl['id_cuenta_contable'] ?? null;
//                     $docTotal = floatval($dl['total_factura']);
                    
//                     if ($tipoDocumento === 'FACTURA' || $tipoDocumento === 'FACTURA ELECTRONICA' || $tipoDocumento === 'FACTURA PEQUEÑO CONTRIBUYENTE') {
//                         $subtotal = floatval($dl['p_unitario']);
//                         $iva = floatval($dl['iva']);
//                         $idp = floatval($dl['idp']);
//                         $inguat = floatval($dl['inguat']);
//                         $propina = floatval($dl['propina']);
                        
//                         if ($iva > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => $dl['t_gasto'],
//                                 "PriceAfterVAT" => $subtotal + $iva,
//                                 "TaxCode" => "IVA",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => $tipoA
//                             ];
//                         }
                        
//                         if ($idp > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => "IDP",
//                                 "PriceAfterVAT" => $idp,
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $dl['id_cuenta_contable_idp'] ?? $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => "P"
//                             ];
//                         }
                        
//                         if ($inguat > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => "INGUAT",
//                                 "PriceAfterVAT" => $inguat,
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => "H"
//                             ];
//                         }
                        
//                         if ($dl['t_gasto'] === 'Alimentos' && $propina > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => "Propina",
//                                 "PriceAfterVAT" => $propina,
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $dl['id_cuenta_contable_propina'],
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => "E"
//                             ];
//                         }
//                     } else {
//                         if (floatval($dl['total_factura']) > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => $dl['t_gasto'] . " ({$tipoDocumento})",
//                                 "PriceAfterVAT" => floatval($dl['total_factura']),
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => $tipoA
//                             ];
//                         }
//                     }
//                 }

//                 if (empty($documentLines)) {
//                     throw new Exception("No se generaron líneas para el documento: sin valores financieros válidos para {$tipoDocumento} en factura {$noFactura}");
//                 }

//                 foreach ($indices as $index) {
//                     $validationResults[] = [
//                         'index' => $index,
//                         'detalle_id' => $detalles[array_search($index, $indices)]['id'],
//                         'no_factura' => $noFactura,
//                         'valid' => true
//                     ];
//                 }
//                 $validDetalles[$groupKey] = $detalles;
//             } catch (Exception $e) {
//                 error_log("Error de validación en grupo {$groupKey} (Factura: {$noFactura}): {$e->getMessage()}");
//                 foreach ($indices as $index) {
//                     $dl = $detalles[array_search($index, $indices)];
//                     $validationResults[] = [
//                         'index' => $index,
//                         'detalle_id' => $dl['id'],
//                         'no_factura' => $noFactura,
//                         'valid' => false,
//                         'error' => $e->getMessage()
//                     ];
//                     $detalleLiquidacionModel->updateEstado($dl['id'], 'EN_CORRECCION');
//                     $this->auditoriaModel->createAuditoria($id, $dl['id'], $_SESSION['user_id'], 'ERROR_VALIDACION_EXPORTACION', "Error de validación: {$e->getMessage()} para factura {$noFactura}");
//                 }
//             }
//         }

//         // SAP Login
//         error_log("Intentando login en SAP");
//         $loginResult = $this->login_sap('T_GT_AGROCENTRO_2016');
//         if (!$loginResult['success']) {
//             error_log("Login SAP Failed: {$loginResult['error']}");
//             $this->pdo->rollBack();
//             ob_end_clean();
//             header('Content-Type: application/json; charset=utf-8');
//             http_response_code(500);
//             echo json_encode(['error' => 'No es posible exportar por problemas en SAP, intente más tarde'], JSON_UNESCAPED_UNICODE);
//             exit;
//         }
//         $cookie = "B1SESSION={$loginResult['sessionId']}; ROUTEID={$loginResult['routeId']}";

//         // Process valid invoices for SAP export
//         $atLeastOneProcessed = false;
//         $successCount = 0;
//         foreach ($validDetalles as $groupKey => $detalles) {
//             $indices = $groupedDetalles[$groupKey]['indices'];
//             $noFactura = $groupedDetalles[$groupKey]['no_factura'];
//             try {
//                 error_log("Procesando exportación para grupo {$groupKey} (Factura: {$noFactura}, Grupo ID: {$groupedDetalles[$groupKey]['grupo_id']}) con " . count($detalles) . " detalles");
                
//                 $dl = $detalles[0];
//                 $docDate = $docDate ?? date('Y-m-d', strtotime($dl['fecha']));
//                 $numAtCard = !empty(trim($noFactura)) ? substr(trim($noFactura), 0, 50) : "DLIQ-{$id}-{$timestamp}";
//                 $documentLines = [];
//                 $docTotal = floatval($dl['total_factura']);

//                 if (empty($dl['fecha']) || !strtotime($dl['fecha'])) {
//                     error_log("Fecha inválida para factura {$noFactura}: {$dl['fecha']}, usando fecha actual");
//                     $fecha = new DateTime();
//                 } else {
//                     $fecha = new DateTime($dl['fecha']);
//                 }
//                 $fechaParaDec = clone $fecha;
//                 $u_f_dec = $fechaParaDec->modify('first day of this month')->format('Y-m-d');
//                 $u_f_dec_d = strtoupper($fecha->format('M-Y'));

//                 $tipoDocumento = strtoupper($dl['tipo_documento'] ?? 'FACTURA');
//                 $tipoA = in_array($dl['t_gasto'], ['Gasto Operativo', 'Hospedaje']) ? 'S' : ($dl['t_gasto'] === 'Combustible' ? 'C' : 'B');
//                 $tipoDocForUF = '';
//                 $tipoDocForLines = '';
//                 $stmt = $this->pdo->prepare("SELECT TipoDoc FROM tipos_documentos WHERE name = ?");
//                 $stmt->execute([$dl['tipo_documento']]);
//                 $result = $stmt->fetch(PDO::FETCH_ASSOC);
//                 if ($result && !empty($result['TipoDoc'])) {
//                     $tipoDocForLines = $result['TipoDoc'];
//                     $tipoDocForUF = $tipoDocMap[$tipoDocForLines] ?? $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
//                 } else {
//                     $tipoDocForLines = strtoupper($dl['tipo_documento']);
//                     $tipoDocForUF = $tipoDocMap[strtoupper($dl['tipo_documento'])] ?? '';
//                 }

//                 foreach ($detalles as $detalle) {
//                     $costingCode = trim($centroCostoModel->getCentroCostoById($detalle['id_centro_costo'])['codigo']);
//                     $accountCode = $detalle['id_cuenta_contable'] ?? null;
                    
//                     if ($tipoDocumento === 'FACTURA' || $tipoDocumento === 'FACTURA ELECTRONICA' || $tipoDocumento === 'FACTURA PEQUEÑO CONTRIBUYENTE') {
//                         $subtotal = floatval($detalle['p_unitario']);
//                         $iva = floatval($detalle['iva']);
//                         $idp = floatval($detalle['idp']);
//                         $inguat = floatval($detalle['inguat']);
//                         $propina = floatval($detalle['propina']);
                        
//                         if ($iva > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => $detalle['t_gasto'],
//                                 "PriceAfterVAT" => $subtotal + $iva,
//                                 "TaxCode" => "IVA",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => $tipoA
//                             ];
//                         }
                        
//                         if ($idp > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => "IDP",
//                                 "PriceAfterVAT" => $idp,
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $detalle['id_cuenta_contable_idp'] ?? $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => "P"
//                             ];
//                         }
                        
//                         if ($inguat > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => "INGUAT",
//                                 "PriceAfterVAT" => $inguat,
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => "H"
//                             ];
//                         }
                        
//                         if ($detalle['t_gasto'] === 'Alimentos' && $propina > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => "Propina",
//                                 "PriceAfterVAT" => $propina,
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $detalle['id_cuenta_contable_propina'],
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => "E"
//                             ];
//                         }
//                     } else {
//                         if (floatval($detalle['total_factura']) > 0) {
//                             $documentLines[] = [
//                                 "LineType" => count($documentLines),
//                                 "ItemDescription" => $detalle['t_gasto'] . " ({$tipoDocumento})",
//                                 "PriceAfterVAT" => floatval($detalle['total_factura']),
//                                 "TaxCode" => "EXE",
//                                 "CostingCode" => $costingCode,
//                                 "AccountCode" => $accountCode,
//                                 "U_TipoDoc" => $tipoDocForLines,
//                                 "U_TipoA" => $tipoA
//                             ];
//                         }
//                     }
//                 }

//                 $comments = !empty(trim($dl['comentarios'])) ? substr(trim($dl['comentarios']), 0, 254) : 'Sin comentarios';
//                 $nombreProveedor = !empty(trim($dl['nombre_proveedor'])) ? substr(trim($dl['nombre_proveedor']), 0, 254) : '';
//                 $nitProveedor = !empty(trim($dl['nit_proveedor'])) ? substr(trim($dl['nit_proveedor']), 0, 20) : '321052';

//                 $liquidacion = $liquidacionModel->getLiquidacionById($id);
//                 if (!$liquidacion || !isset($liquidacion['id_usuario'])) {
//                     throw new Exception("No se pudo obtener el id_usuario de la liquidación ID $id");
//                 }

//                 $usuarioCreador = $usuarioModel->getUsuarioById($liquidacion['id_usuario']);
//                 $cardCode = !empty($usuarioCreador['clientes']) ? trim($usuarioCreador['clientes']) : 'CCHA0010';
//                 error_log("Código de cliente para usuario ID {$liquidacion['id_usuario']}: $cardCode");

//                 $purchaseInvoice = [
//                     "DocType" => "dDocument_Service",
//                     "CardCode" => $cardCode,
//                     "U_CODIGO" => $cardCode,
//                     "DocDate" => $docDate,
//                     "Comments" => $comments,
//                     "JournalMemo" => $comments,
//                     "U_NIT" => $nitProveedor,
//                     "U_NOMBRE" => $nombreProveedor,
//                     "U_F_Tipo" => $tipoDocForUF,
//                     "Series" => 82,
//                     "DocTotal" => $docTotal,
//                     "TotalDiscount" => 0.0,
//                     "Reference1" => "{$id}-{$noFactura}",
//                     "NumAtCard" => $numAtCard,
//                     "U_F_DEC" => $u_f_dec,
//                     "U_F_DEC_D" => $u_f_dec_d,
//                     "DocCurrency" => "QTZ",
//                     "DocRate" => 1,
//                     "DocumentLines" => $documentLines
//                 ];

//                 foreach ($documentLines as &$line) {
//                     $line['DiscountPercent'] = 0;
//                     $line['DiscountAmount'] = 0;
//                 }

//                 $jsonFilePath = "$jsonDir/export_liquidacion_{$id}_{$groupKey}.json";
//                 $jsonContent = json_encode($purchaseInvoice, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//                 if (file_put_contents($jsonFilePath, "\xEF\xBB\xBF" . $jsonContent) === false) {
//                     throw new Exception("No se pudo escribir el archivo JSON: $jsonFilePath");
//                 }
//                 error_log("JSON file generated at: $jsonFilePath");

//                 $sapUrl = "https://192.168.1.9:50000/b1s/v1/PurchaseInvoices";
//                 $ch = curl_init($sapUrl);
//                 curl_setopt_array($ch, [
//                     CURLOPT_RETURNTRANSFER => true,
//                     CURLOPT_POST => true,
//                     CURLOPT_HTTPHEADER => [
//                         'Content-Type: application/json',
//                         'Cookie: ' . $cookie
//                     ],
//                     CURLOPT_POSTFIELDS => $jsonContent,
//                     CURLOPT_SSL_VERIFYPEER => false,
//                     CURLOPT_SSL_VERIFYHOST => false,
//                 ]);

//                 $response = curl_exec($ch);
//                 $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//                 $curlError = curl_error($ch);
//                 curl_close($ch);

//                 if ($response === false || $curlError) {
//                     error_log("SAP Error for grupo {$groupKey} (Factura: {$noFactura}): $curlError");
//                     throw new Exception("Error de conexión SAP para factura {$noFactura}: $curlError");
//                 }

//                 // Dentro del bloque donde procesas la respuesta de SAP:
// $sapResponse = json_decode($response, true);
// if ($httpCode >= 400 || json_last_error() !== JSON_ERROR_NONE) {
//     $errorMsg = "Error SAP para grupo {$groupKey} (Factura: {$noFactura}): HTTP $httpCode";
    
//     // Verificar si la respuesta es un JSON válido
//     if (json_last_error() === JSON_ERROR_NONE && isset($sapResponse['error'])) {
//         $errorCode = $sapResponse['error']['code'] ?? 0;
        
//         // Extraer el mensaje de error de manera más robusta
//         $sapErrorMessage = '';
//         if (isset($sapResponse['error']['message']['value'])) {
//             $sapErrorMessage = $sapResponse['error']['message']['value'];
//         } elseif (is_string($sapResponse['error']['message'])) {
//             $sapErrorMessage = $sapResponse['error']['message'];
//         } else {
//             $sapErrorMessage = 'Error desconocido';
//         }
        
//         $errorMsg .= " - {$sapErrorMessage}";
        
//         // DEBUG: Log completo de la respuesta para diagnóstico
//         error_log("Respuesta completa de SAP: " . json_encode($sapResponse, JSON_PRETTY_PRINT));
        
//         // Si el código es -1116, intentar extraer el código real del mensaje
//         if ($errorCode == -1116) {
//             error_log("Código -1116 detectado, buscando código real en mensaje: $sapErrorMessage");
            
//             if (preg_match('/\((\d+)\)/', $sapErrorMessage, $matches)) {
//                 $errorCode = (int)$matches[1];
//                 error_log("Código real extraído del mensaje: $errorCode");
//             } else {
//                 error_log("No se pudo extraer código del mensaje: $sapErrorMessage");
//                 // Si no podemos extraer el código, tratar como error no manejable
//                 throw new Exception($errorMsg);
//             }
//         }
        
//         error_log("Código de error detectado: $errorCode, Mensaje: $sapErrorMessage");
        
//         // Intentar manejar el error automáticamente
//         $manejoResultado = $this->manejarErroresSapYReintentar(
//             $errorCode,
//             $sapErrorMessage,
//             $nitProveedor,
//             $nombreProveedor,
//             $noFactura,
//             $cookie,
//             $jsonContent,
//             $sapUrl,
//             $detalles,
//             $detalleLiquidacionModel,
//             $id,
//             $groupKey,
//             $groupedDetalles,
//             $jsonFilePath
//         );

//         if ($manejoResultado['success']) {
//             // Éxito después del manejo del error
//             $results[] = [
//                 'no_factura' => $noFactura,
//                 'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
//                 'success' => true,
//                 'message' => $manejoResultado['message'],
//                 'filePath' => $manejoResultado['filePath'],
//                 'detalle_ids' => $manejoResultado['detalle_ids'],
//                 'sap_response' => $manejoResultado['sap_response'],
//                 'manejado' => true
//             ];
//             $atLeastOneProcessed = true;
//             continue;
//         } elseif ($manejoResultado['manejable'] === false) {
//             // Error no manejable, proceder con el manejo normal
//             $isDuplicateError = ($errorCode == -5002);
//             if ($isDuplicateError) {
//                 foreach ($detalles as $detalle) {
//                     $detalleLiquidacionModel->updateEstado($detalle['id'], 'FINALIZADO');
//                     $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Factura exportada a SAP (duplicado): {$noFactura}, mensaje: {$errorMsg}");
//                 }
//                 $results[] = [
//                     'no_factura' => $noFactura,
//                     'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
//                     'success' => true,
//                     'message' => "Grupo {$groupKey} (Factura: {$noFactura}) procesada (duplicado, exportada de nuevo)",
//                     'filePath' => $jsonFilePath,
//                     'detalle_ids' => array_column($detalles, 'id'),
//                     'sap_response' => $sapResponse,
//                     'manejado' => false
//                 ];
//                 $atLeastOneProcessed = true;
//                 continue;
//             }
//         }
//     } else {
//         // Respuesta no es JSON válido o no tiene la estructura esperada
//         $errorMsg .= " - Respuesta no válida de SAP";
//         if (json_last_error() !== JSON_ERROR_NONE) {
//             $errorMsg .= " (Error JSON: " . json_last_error_msg() . ")";
//         }
//         error_log("Respuesta SAP no válida: " . substr($response, 0, 500));
//     }
    
//     throw new Exception($errorMsg);
// }

//                 foreach ($detalles as $detalle) {
//                     $detalleLiquidacionModel->updateEstado($detalle['id'], 'FINALIZADO');
//                     $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Factura exportada a SAP: {$noFactura}");
//                 }

//                 $results[] = [
//                     'no_factura' => $noFactura,
//                     'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
//                     'success' => true,
//                     'message' => "Grupo {$groupKey} (Factura: {$noFactura}) enviada a SAP exitosamente",
//                     'filePath' => $jsonFilePath,
//                     'detalle_ids' => array_column($detalles, 'id'),
//                     'sap_response' => $sapResponse
//                 ];
//                 $atLeastOneProcessed = true;
//             } catch (Exception $e) {
//                 error_log("Error processing grupo {$groupKey} (Factura: {$noFactura}): {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
//                 $results[] = [
//                     'no_factura' => $noFactura,
//                     'grupo_id' => $groupedDetalles[$groupKey]['grupo_id'],
//                     'success' => false,
//                     'error' => $e->getMessage(),
//                     'filePath' => $jsonFilePath ?? null,
//                     'detalle_ids' => array_column($detalles, 'id')
//                 ];
//                 foreach ($detalles as $detalle) {
//                     $detalleLiquidacionModel->updateEstado($detalle['id'], 'EN_CORRECCION');
//                     $this->auditoriaModel->createAuditoria($id, $detalle['id'], $_SESSION['user_id'], 'ERROR_EXPORTACION_SAP', "Error al exportar a SAP: {$e->getMessage()} para factura {$noFactura}");
//                 }
//             }
//         }

//         // SAP Logout
//         error_log("Intentando logout de SAP");
//         $logoutResult = $this->logout_sap();
//         if (!$logoutResult['success']) {
//             error_log("SAP Logout Failed: {$logoutResult['error']}");
//         }

//         $successCount = count(array_filter($results, fn($r) => $r['success']));
//         $erroresManejados = array_filter($results, function($r) {
//             return isset($r['manejado']) && $r['manejado'] === true && $r['success'] === true;
//         });
//         $response = [
//             'success' => $atLeastOneProcessed,
//             'message' => $atLeastOneProcessed
//                 ? "Exportación completada: $successCount facturas procesadas exitosamente"
//                 : "Exportación fallida: ninguna factura procesada exitosamente",
//             'results' => $results,
//             'validationResults' => $validationResults,
//             'nitPnResults' => $nitPnResults,
//             'nitPnData' => $nitPnData,
//             'erroresManejados' => $erroresManejados
//         ];

//         if ($atLeastOneProcessed) {
//             $liquidacionModel->updateEstado($id, 'FINALIZADO');
//             $this->auditoriaModel->createAuditoria($id, null, $_SESSION['user_id'], 'EXPORTADO_A_SAP', "Exportación completada: $successCount facturas exportadas");
//             $this->pdo->commit();
//         } else {
//             $this->pdo->rollBack();
//         }

//         ob_end_clean();
//         header('Content-Type: application/json; charset=utf-8');
//         echo json_encode($response, JSON_UNESCAPED_UNICODE);
//         exit;

//     } catch (Exception $e) {
//         error_log('Error exporting: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
//         if (isset($loginResult) && $loginResult['success']) {
//             $this->logout_sap();
//         }
//         $this->pdo->rollBack();
//         ob_end_clean();
//         header('Content-Type: application/json; charset=utf-8');
//         http_response_code(500);
//         echo json_encode(['error' => 'No es posible exportar por problemas en SAP, intente más tarde'], JSON_UNESCAPED_UNICODE);
//         exit;
//     }
// }

// FINALIZACION 
    
public function manageFacturas($id) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    $usuario = $this->usuarioModel->getUsuarioById($_SESSION['user_id']);
    if ($usuario === false || !isset($usuario['rol']) || !$this->usuarioModel->tienePermiso($usuario, 'manage_facturas')) {
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
        $this->fetchHanaAccounts($_GET['id_centro_costo']);
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
                $serie = $_POST['serie'] ?? '';
                $numero_dte = $serie && strpos($no_factura, $serie) === 0 ? substr($no_factura, strlen($serie)) : $no_factura;
                $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                $dpi = $_POST['dpi'] ?? null;
                $fecha = $_POST['fecha'] ?? '';
                $fechaDocumento = $_POST['fecha_documento'] ?? null;
                $fechaActual = new DateTime();
                $fechaFactura = new DateTime($fecha);
                $t_gasto = $_POST['t_gasto'] ?? '';
                $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $total_factura = floatval($_POST['total_factura'] ?? 0);
                $iva = isset($_POST['iva']) && $_POST['iva'] !== '' ? floatval($_POST['iva']) : null;
                $idp = isset($_POST['idp']) && $_POST['idp'] !== '' ? floatval($_POST['idp']) : null;
                $inguat = isset($_POST['inguat']) && $_POST['inguat'] !== '' ? floatval($_POST['inguat']) : null;
                $propina = isset($_POST['propina']) && $_POST['propina'] !== '' ? floatval($_POST['propina']) : null;
                $id_centro_costo = is_array($_POST['id_centro_costo']) ? $_POST['id_centro_costo'] : [$_POST['id_centro_costo']];
                $porcentajes = is_array($_POST['porcentaje']) ? $_POST['porcentaje'] : [$_POST['porcentaje'] ?? 100];
                $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp'] ?? null;
                $nombre_cuenta_contable = $_POST['nombre_cuenta_contable'] ?? '';
                $id_cuenta_contable_propina = $_POST['id_cuenta_contable_propina'] ?? null;
                $nombre_cuenta_contable_propina = $_POST['nombre_cuenta_contable_propina'] ?? '';
                $cantidad = $_POST['cantidad'] ?? null;
                $serie = $_POST['serie'] ?? null;
                $comentarios = $_POST['comentarios'] ?? null;
                $estado = 'EN_PROCESO';

                // PROCESAR CUENTAS CONTABLES POR CENTRO DE COSTO
                $id_cuenta_contable_centro = [];
                $nombre_cuenta_contable_centro = [];

                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'id_cuenta_contable_centro_') === 0) {
                        $index = substr($key, strlen('id_cuenta_contable_centro_'));
                        $id_cuenta_contable_centro[$index] = $value;
                    }
                    if (strpos($key, 'nombre_cuenta_contable_centro_') === 0) {
                        $index = substr($key, strlen('nombre_cuenta_contable_centro_'));
                        $nombre_cuenta_contable_centro[$index] = $value;
                    }
                }

                // Si la fecha de la factura NO es del mes actual, guardar en fecha_documento
                if ($fechaFactura->format('Y-m') !== $fechaActual->format('Y-m')) {
                $fechaDocumento = $fechaFactura->format('Y-m-d');
                error_log("Fecha del documento guardada en fecha_documento: $fechaDocumento (mes diferente al actual)");
                } else {
                error_log("Fecha del documento NO guardada en fecha_documento (mes igual al actual)");
                }

                if (in_array($tipo_documento, ['RECIBO FISCAL', 'RECIBO INFORMATIVO'])) {
                    $nit_proveedor = null;
                } else {
                    $dpi = null;
                }

                if (empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                    error_log('Error: Missing or invalid required fields in create action. POST data: ' . print_r($_POST, true));
                    throw new Exception('Los campos obligatorios (tipo_documento, no_factura, nombre_proveedor, fecha, t_gasto, subtotal, total_factura) deben ser válidos.');
                }

                if (count($id_centro_costo) !== count($porcentajes)) {
                    throw new Exception('Los centros de costo y porcentajes no coinciden.');
                }
                $totalPorcentaje = array_sum($porcentajes);
                if (abs($totalPorcentaje - 100) > 0.01) {
                    throw new Exception('La suma de los porcentajes debe ser exactamente 100%.');
                }

                if ($t_gasto === 'Combustible') {
                    $id_cuenta_contable = $_POST['id_cuenta_contable']; // Combustibles y lubricantes
                    $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp']; // IDP
                    $id_cuenta_contable_inguat = null;
                } elseif ($t_gasto === 'Hospedaje') {
                    $id_cuenta_contable = $_POST['id_cuenta_contable']; // Viáticos locales
                    $id_cuenta_contable_inguat = '641001003'; // Cuenta fija para INGUAT
                } else {
                    $id_cuenta_contable = $_POST['id_cuenta_contable'];
                    $id_cuenta_contable_idp = null;
                    $id_cuenta_contable_inguat = null;
                }

                if ($tipo_documento === 'COMPROBANTE' && (empty($cantidad) || empty($serie))) {
                    throw new Exception('Cantidad y Serie son obligatorios para el tipo de documento Comprobante.');
                }

                // if (in_array($tipo_documento, ['RECIBO FISCAL', 'RECIBO INFORMATIVO']) && empty($dpi)) {
                //     throw new Exception('DPI es obligatorio para el tipo de documento ' . $tipo_documento . '.');
                // }

                if (in_array($tipo_documento, ['FACTURA', 'COMPROBANTE', 'DUCA']) && empty($nit_proveedor)) {
                    throw new Exception('NIT es obligatorio para el tipo de documento ' . $tipo_documento . '.');
                }

                if (in_array($tipo_documento, ['FACTURA', 'DUCA'])) {
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

                if ($no_factura) {
                    $stmt = $this->pdo->prepare("
                        SELECT COUNT(*) 
                        FROM detalle_liquidaciones 
                        WHERE no_factura = ? AND id_liquidacion != ?
                    ");
                    $stmt->execute([$no_factura, $id_liquidacion]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("La factura con número '$no_factura' ya está asociada a otra liquidación.");
                    }
                }

                // Validar y recalcular IVA para asegurar que no incluye propina
                $iva = $iva ?? 0;
                $idp = $idp ?? 0;
                $inguat = $inguat ?? 0;
                $propina = $propina ?? 0;

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

                $id_usuario = $liquidacion['id_usuario'];
                $detalleModel = new DetalleLiquidacion();
                $rutas_json = json_encode($rutas_archivos);

                // Determinar grupo_id
                $grupo_id = (count($id_centro_costo) == 1) ? 0 : $this->pdo->query("SELECT COALESCE(MAX(grupo_id), 0) + 1 FROM detalle_liquidaciones")->fetchColumn();
                error_log("Generado grupo_id: $grupo_id para " . count($id_centro_costo) . " centros de costo");

                // Crear detalles por cada centro de costo
                $detalle_ids = [];
                
                foreach ($id_centro_costo as $index => $centro_costo) {
                    $porcentaje = floatval($porcentajes[$index]);
                    $es_principal = ($index === 0) ? 1 : 0;

                    $cantidad = isset($_POST['cantidad']) && $_POST['cantidad'] !== '' ? floatval($_POST['cantidad']) : null;

                    // USAR CUENTA CONTABLE ESPECÍFICA PARA CADA CENTRO DE COSTO
                    if ($index === 0) {
                        // Para el primer centro de costo, usar la cuenta contable principal
                        $cuenta_contable_id = $id_cuenta_contable;
                        $cuenta_contable_nombre = $nombre_cuenta_contable;
                    } else {
                        // Para centros de costo adicionales, usar la cuenta específica
                        $cuenta_contable_id = $id_cuenta_contable_centro[$index] ?? $id_cuenta_contable;
                        $cuenta_contable_nombre = $nombre_cuenta_contable_centro[$index] ?? $nombre_cuenta_contable;
                    }

                    $detalle_id = $detalleModel->createDetalleLiquidacion(
                        $id_liquidacion, 
                        $tipo_documento, 
                        $no_factura, 
                        $nombre_proveedor, 
                        $nit_proveedor, 
                        $dpi, 
                        $fecha, 
                        $t_gasto, 
                        $subtotal * ($porcentaje / 100), 
                        $total_factura * ($porcentaje / 100), 
                        $estado, 
                        $centro_costo, 
                        $cantidad, 
                        $serie, 
                        $rutas_json, 
                        $iva * ($porcentaje / 100), 
                        $idp * ($porcentaje / 100), 
                        $inguat * ($porcentaje / 100), 
                        $propina * ($porcentaje / 100), 
                        $cuenta_contable_id, 
                        $tipo_combustible, 
                        $id_usuario, 
                        $comentarios,
                        $porcentaje,
                        $cuenta_contable_nombre,
                        $es_principal,
                        $grupo_id,
                        $id_cuenta_contable_propina,
                        $nombre_cuenta_contable_propina,
                        $id_cuenta_contable_idp,
                        $fechaDocumento,
                        $id_cuenta_contable_inguat
                    );

                    if (!$detalle_id) {
                        throw new Exception('Error al crear detalle de liquidación en la base de datos.');
                    }

                    $detalle_ids[] = $detalle_id;
                    error_log("Creado detalle ID $detalle_id con grupo_id $grupo_id para centro de costo $centro_costo con porcentaje $porcentaje, cuenta contable: $cuenta_contable_nombre");
                    error_log("Creado detalle ID $detalle_id con fecha_documento: " . ($fechaDocumento ?? 'NULL'));
                }
                

                if ($serie && $numero_dte) {
                    $serie = trim($serie);
                    $numero_dte = trim(str_replace('-', '', $numero_dte));
                    
                    error_log("Actualizando DTE al crear factura - Serie: '$serie', Numero DTE: '$numero_dte'");
                    
                    if ($this->dteModel->updateDteUsado($serie, $numero_dte)) {
                        error_log("DTE actualizado exitosamente a 'Y'");
                    } else {
                        error_log("Error: No se pudo actualizar el DTE a 'Y'");
                        // No lanzar excepción aquí para no interrumpir el proceso de creación
                    }
                }

                $this->auditoriaModel->createAuditoria($id_liquidacion, $detalle_ids[0], $_SESSION['user_id'], 'CREAR_DETALLE', "Factura creada: $no_factura para usuario ID $id_usuario");

                $detallesActualizados = $detalleModel->getDetallesByLiquidacionId($id_liquidacion);
                $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                $this->liquidacionModel->updateMontoTotal($id_liquidacion, $monto_total);

                $response = [
                    'message' => 'Factura creada correctamente',
                    'detalle_id' => $detalle_ids[0],
                    'grupo_id' => $grupo_id,
                    'rutas_archivos' => $rutas_archivos,
                    'monto_total' => $monto_total,
                    'cuenta_contable_nombre' => $nombre_cuenta_contable,
                    'centros_costo' => array_map(function($cc, $p) {
                        return ['id_centro_costo' => $cc, 'porcentaje' => floatval($p)];
                    }, $id_centro_costo, $porcentajes)
                ];
            } elseif ($action === 'update') {
                $detalle_id = $_POST['detalle_id'] ?? '';
                $tipo_documento = $_POST['tipo_documento'] ?? '';
                $no_factura = $_POST['no_factura'] ?? '';
                $serie = $_POST['serie'] ?? '';
                $numero_dte = $serie && strpos($no_factura, $serie) === 0 ? substr($no_factura, strlen($serie)) : $no_factura;
                $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                $dpi = $_POST['dpi'] ?? null;
                $fecha = $_POST['fecha'] ?? '';
                $fechaDocumento = $_POST['fecha_documento'] ?? null;
                $detalleExistente = $this->detalleModel->getDetalleById($detalle_id);
                $fechaActual = new DateTime();
                $fechaFactura = new DateTime($fecha);
                $t_gasto = $_POST['t_gasto'] ?? '';
                $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $total_factura = floatval($_POST['total_factura'] ?? 0);
                $iva = isset($_POST['iva']) && $_POST['iva'] !== '' ? floatval($_POST['iva']) : null;
                $idp = isset($_POST['idp']) && $_POST['idp'] !== '' ? floatval($_POST['idp']) : null;
                $inguat = isset($_POST['inguat']) && $_POST['inguat'] !== '' ? floatval($_POST['inguat']) : null;
                $propina = isset($_POST['propina']) && $_POST['propina'] !== '' ? floatval($_POST['propina']) : null;
                $id_centro_costo = is_array($_POST['id_centro_costo']) ? $_POST['id_centro_costo'] : [$_POST['id_centro_costo']];
                $porcentajes = is_array($_POST['porcentaje']) ? $_POST['porcentaje'] : [$_POST['porcentaje'] ?? 100];
                $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                $nombre_cuenta_contable = $_POST['nombre_cuenta_contable'] ?? '';
                $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp'] ?? null;
                $id_cuenta_contable_propina = $_POST['id_cuenta_contable_propina'] ?? null;
                $nombre_cuenta_contable_propina = $_POST['nombre_cuenta_contable_propina'] ?? '';
                $cantidad = isset($_POST['cantidad']) && $_POST['cantidad'] !== '' ? floatval($_POST['cantidad']) : null;
                $serie = $_POST['serie'] ?? null;
                $comentarios = $_POST['comentarios'] ?? null;
                $removed_files = isset($_POST['removed_files']) ? (is_array($_POST['removed_files']) ? $_POST['removed_files'] : [$_POST['removed_files']]) : [];

                // PROCESAR CUENTAS CONTABLES POR CENTRO DE COSTO
                $id_cuenta_contable_centro = [];
                $nombre_cuenta_contable_centro = [];

                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'id_cuenta_contable_centro_') === 0) {
                        $index = substr($key, strlen('id_cuenta_contable_centro_'));
                        $id_cuenta_contable_centro[$index] = $value;
                    }
                    if (strpos($key, 'nombre_cuenta_contable_centro_') === 0) {
                        $index = substr($key, strlen('nombre_cuenta_contable_centro_'));
                        $nombre_cuenta_contable_centro[$index] = $value;
                    }
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

                if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                    throw new Exception('Los campos obligatorios deben ser válidos.');
                }

                if (count($id_centro_costo) !== count($porcentajes)) {
                    throw new Exception('Los centros de costo y porcentajes no coinciden.');
                }
                $totalPorcentaje = array_sum($porcentajes);
                if (abs($totalPorcentaje - 100) > 0.01) {
                    throw new Exception('La suma de los porcentajes debe ser exactamente 100%.');
                }

                if (in_array($tipo_documento, ['RECIBO FISCAL', 'RECIBO INFORMATIVO'])) {
                    $nit_proveedor = null;
                } else {
                    $dpi = null;
                }

                if ($t_gasto === 'Combustible') {
                    $id_cuenta_contable = $_POST['id_cuenta_contable']; // Combustibles y lubricantes
                    $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp']; // IDP
                } else {
                    $id_cuenta_contable = $_POST['id_cuenta_contable'];
                    $id_cuenta_contable_idp = null;
                }

                if ($tipo_documento === 'COMPROBANTE' && (empty($cantidad) || empty($serie))) {
                    throw new Exception('Cantidad y Serie son obligatorios para el tipo de documento Comprobante.');
                }

                // if (in_array($tipo_documento, ['RECIBO FISCAL', 'RECIBO INFORMATIVO']) && empty($dpi)) {
                //     throw new Exception('DPI es obligatorio para el tipo de documento ' . $tipo_documento . '.');
                // }

                if (in_array($tipo_documento, ['FACTURA', 'COMPROBANTE', 'DUCA']) && empty($nit_proveedor)) {
                    throw new Exception('NIT es obligatorio para el tipo de documento ' . $tipo_documento . '.');
                }

                if (in_array($tipo_documento, ['FACTURA', 'DUCA'])) {
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

                if ($no_factura) {
                    $stmt = $this->pdo->prepare("
                        SELECT COUNT(*) 
                        FROM detalle_liquidaciones 
                        WHERE no_factura = ? AND id_liquidacion != ? AND id != ?
                    ");
                    $stmt->execute([$no_factura, $id, $detalle_id]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("La factura con número '$no_factura' ya está asociada a otra liquidación.");
                    }
                }

                // Validar y recalcular IVA para asegurar que no incluye propina
                $iva = $iva ?? 0;
                $idp = $idp ?? 0;
                $inguat = $inguat ?? 0;
                $propina = $propina ?? 0;

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

                $detalle = $this->detalleModel->getDetalleById($detalle_id);
                if (!$detalle) {
                    throw new Exception('Detalle no encontrado.');
                }

                // Obtener grupo_id
                $grupo_id = $detalle['grupo_id'];
                // Si grupo_id > 0, obtener detalles del grupo; si grupo_id = 0, solo usar el detalle actual
                $detalles_grupo = [];
                if ($grupo_id > 0) {
                    $stmt = $this->pdo->prepare("SELECT id, id_centro_costo, porcentaje FROM detalle_liquidaciones WHERE grupo_id = ? AND id_liquidacion = ?");
                    $stmt->execute([$grupo_id, $id]);
                    $detalles_grupo = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $detalles_grupo = [['id' => $detalle_id, 'id_centro_costo' => $detalle['id_centro_costo'], 'porcentaje' => $detalle['porcentaje']]];
                }

                // Manejo de archivos
                $existingRutas = json_decode($detalle['rutas_archivos'], true) ?? [];
                foreach ($removed_files as $file_to_remove) {
                    if (($key = array_search($file_to_remove, $existingRutas)) !== false) {
                        unset($existingRutas[$key]);
                        $filePath = '../' . $file_to_remove;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
                $rutas_archivo = array_values(array_merge($existingRutas, $rutas_archivos));
                $rutas_json = json_encode($rutas_archivo);

                // Determinar nuevo grupo_id si cambió el número de centros de costo
                $new_grupo_id = (count($id_centro_costo) == 1) ? 0 : ($grupo_id > 0 ? $grupo_id : $this->pdo->query("SELECT COALESCE(MAX(grupo_id), 0) + 1 FROM detalle_liquidaciones")->fetchColumn());
                error_log("Grupo_id para actualización: $new_grupo_id (original grupo_id: $grupo_id, centros de costo: " . count($id_centro_costo) . ")");

                // Actualizar o crear detalles según los centros de costo
                $detalleModel = new DetalleLiquidacion();
                $detalle_ids = [];
                foreach ($id_centro_costo as $index => $centro_costo) {
                    $porcentaje = floatval($porcentajes[$index]);
                    $es_principal = ($index === 0) ? 1 : 0;

                    // USAR CUENTA CONTABLE ESPECÍFICA PARA CADA CENTRO DE COSTO
                    if ($index === 0) {
                        // Para el primer centro de costo, usar la cuenta contable principal
                        $cuenta_contable_id = $id_cuenta_contable;
                        $cuenta_contable_nombre = $nombre_cuenta_contable;
                    } else {
                        // Para centros de costo adicionales, usar la cuenta específica
                        $cuenta_contable_id = $id_cuenta_contable_centro[$index] ?? $id_cuenta_contable;
                        $cuenta_contable_nombre = $nombre_cuenta_contable_centro[$index] ?? $nombre_cuenta_contable;
                    }

                    // Buscar si se puede encontrar un existing no usado
                    $existing_detalle = null;
                    foreach ($detalles_grupo as $d) {
                        if ($d['id_centro_costo'] == $centro_costo && !in_array($d['id'], $detalle_ids)) {
                            $existing_detalle = $d;
                            break;
                        }
                    }

                    if ($index === 0) {
                        // Siempre actualizar el detalle principal para el primer centro de costo
                        $stmt = $this->pdo->prepare("
                            UPDATE detalle_liquidaciones
                            SET
                                tipo_documento = :tipo_documento,
                                no_factura = :no_factura,
                                nombre_proveedor = :nombre_proveedor,
                                nit_proveedor = :nit_proveedor,
                                dpi = :dpi,
                                fecha = :fecha,
                                fecha_documento = :fecha_documento,
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
                                propina = :propina,
                                id_cuenta_contable = :id_cuenta_contable,
                                nombre_cuenta_contable = :nombre_cuenta_contable,
                                id_cuenta_contable_propina = :id_cuenta_contable_propina,
                                nombre_cuenta_contable_propina = :nombre_cuenta_contable_propina,
                                id_cuenta_contable_idp = :id_cuenta_contable_idp,
                                tipo_combustible = :tipo_combustible,
                                comentarios = :comentarios,
                                porcentaje = :porcentaje,
                                es_principal = :es_principal,
                                grupo_id = :grupo_id,
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
                            ':fecha_documento' => $fechaDocumento,
                            ':t_gasto' => $t_gasto,
                            ':subtotal' => $subtotal * ($porcentaje / 100),
                            ':total_factura' => $total_factura * ($porcentaje / 100),
                            ':id_centro_costo' => $centro_costo,
                            ':cantidad' => $cantidad,
                            ':serie' => $serie,
                            ':rutas_json' => $rutas_json,
                            ':iva' => $iva * ($porcentaje / 100),
                            ':idp' => $idp * ($porcentaje / 100),
                            ':inguat' => $inguat * ($porcentaje / 100),
                            ':propina' => $propina * ($porcentaje / 100),
                            ':id_cuenta_contable' => $cuenta_contable_id,
                            ':nombre_cuenta_contable' => $cuenta_contable_nombre,
                            ':id_cuenta_contable_propina' => $id_cuenta_contable_propina,
                            ':nombre_cuenta_contable_propina' => $nombre_cuenta_contable_propina,
                            ':id_cuenta_contable_idp' => $id_cuenta_contable_idp,
                            ':tipo_combustible' => $tipo_combustible,
                            ':comentarios' => $comentarios,
                            ':porcentaje' => $porcentaje,
                            ':es_principal' => $es_principal,
                            ':grupo_id' => $new_grupo_id,
                            ':detalle_id' => $detalle_id,
                        ]);
                        $detalle_ids[] = $detalle_id;
                        error_log("Actualizado detalle principal ID $detalle_id con fecha_documento: " . ($fechaDocumento ?? 'NULL'));
                        error_log("Actualizado detalle principal ID $detalle_id con grupo_id $new_grupo_id para centro de costo $centro_costo con porcentaje $porcentaje, cuenta contable: $cuenta_contable_nombre");
                    } elseif ($existing_detalle) {
                        // Actualizar detalle secundario existente
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
                                propina = :propina,
                                id_cuenta_contable = :id_cuenta_contable,
                                nombre_cuenta_contable = :nombre_cuenta_contable,
                                id_cuenta_contable_propina = :id_cuenta_contable_propina,
                                nombre_cuenta_contable_propina = :nombre_cuenta_contable_propina,
                                id_cuenta_contable_idp = :id_cuenta_contable_idp,
                                tipo_combustible = :tipo_combustible,
                                comentarios = :comentarios,
                                porcentaje = :porcentaje,
                                es_principal = :es_principal,
                                grupo_id = :grupo_id,
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
                            ':subtotal' => $subtotal * ($porcentaje / 100),
                            ':total_factura' => $total_factura * ($porcentaje / 100),
                            ':id_centro_costo' => $centro_costo,
                            ':cantidad' => $cantidad,
                            ':serie' => $serie,
                            ':rutas_json' => $rutas_json,
                            ':iva' => $iva * ($porcentaje / 100),
                            ':idp' => $idp * ($porcentaje / 100),
                            ':inguat' => $inguat * ($porcentaje / 100),
                            ':propina' => $propina * ($porcentaje / 100),
                            ':id_cuenta_contable' => $cuenta_contable_id,
                            ':nombre_cuenta_contable' => $cuenta_contable_nombre,
                            ':id_cuenta_contable_propina' => $id_cuenta_contable_propina,
                            ':nombre_cuenta_contable_propina' => $nombre_cuenta_contable_propina,
                            ':id_cuenta_contable_idp' => $id_cuenta_contable_idp,
                            ':tipo_combustible' => $tipo_combustible,
                            ':comentarios' => $comentarios,
                            ':porcentaje' => $porcentaje,
                            ':es_principal' => $es_principal,
                            ':grupo_id' => $new_grupo_id,
                            ':detalle_id' => $existing_detalle['id'],
                        ]);
                        $detalle_ids[] = $existing_detalle['id'];
                        error_log("Actualizado detalle secundario ID {$existing_detalle['id']} con grupo_id $new_grupo_id para centro de costo $centro_costo con porcentaje $porcentaje, cuenta contable: $cuenta_contable_nombre");
                    } else {
                        // Crear nuevo detalle
                        $new_detalle_id = $detalleModel->createDetalleLiquidacion(
                            $id, 
                            $tipo_documento, 
                            $no_factura, 
                            $nombre_proveedor, 
                            $nit_proveedor, 
                            $dpi, 
                            $fecha, 
                            $t_gasto, 
                            $subtotal * ($porcentaje / 100), 
                            $total_factura * ($porcentaje / 100), 
                            'EN_PROCESO', 
                            $centro_costo, 
                            $cantidad, 
                            $serie, 
                            $rutas_json, 
                            $iva * ($porcentaje / 100), 
                            $idp * ($porcentaje / 100), 
                            $inguat * ($porcentaje / 100),
                            $propina * ($porcentaje / 100), 
                            $cuenta_contable_id, 
                            $tipo_combustible, 
                            $detalle['id_usuario'], 
                            $comentarios,
                            $porcentaje,
                            $cuenta_contable_nombre,
                            $es_principal,
                            $new_grupo_id,
                            $id_cuenta_contable_propina,
                            $nombre_cuenta_contable_propina,
                            $id_cuenta_contable_idp,
                            $fechaDocumento
                        );
                        if (!$new_detalle_id) {
                            throw new Exception('Error al crear nuevo detalle de liquidación.');
                        }
                        $detalle_ids[] = $new_detalle_id;
                        error_log("Creado detalle secundario ID $new_detalle_id con fecha_documento: " . ($fechaDocumento ?? 'NULL'));
                        error_log("Creado detalle secundario ID $new_detalle_id con grupo_id $new_grupo_id para centro de costo $centro_costo con porcentaje $porcentaje, cuenta contable: $cuenta_contable_nombre");
                    }
                }

                // Eliminar detalles que ya no están en la lista (basado en IDs no usados)
                foreach ($detalles_grupo as $old_detalle) {
                    if (!in_array($old_detalle['id'], $detalle_ids)) {
                        $detalleModel->deleteDetalleLiquidacion($old_detalle['id']);
                        $this->auditoriaModel->createAuditoria($id, $old_detalle['id'], $_SESSION['user_id'], 'ELIMINAR_DETALLE', "Detalle eliminado para factura: $no_factura, centro de costo ID: {$old_detalle['id_centro_costo']}");
                    }
                }

                if ($serie && $numero_dte && ($serie != $detalle['serie'] || $numero_dte != $detalle['no_factura'])) {
                    if ($detalle['serie'] && $detalle['no_factura']) {
                        $stmt = $this->pdo->prepare("UPDATE dte SET usado = 'N' WHERE serie = ? AND numero_dte = ?");
                        $stmt->execute([$detalle['serie'], $detalle['no_factura']]);
                    }
                    $stmt = $this->pdo->prepare("UPDATE dte SET usado = 'Y' WHERE serie = ? AND numero_dte = ?");
                    $stmt->execute([$serie, $numero_dte]);
                }

                $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE', "Factura actualizada: $no_factura");

                $detallesActualizados = $detalleModel->getDetallesByLiquidacionId($id);
                $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                $this->liquidacionModel->updateMontoTotal($id, $monto_total);

                $response = [
                    'message' => 'Factura actualizada correctamente',
                    'detalle_id' => $detalle_id,
                    'grupo_id' => $new_grupo_id,
                    'rutas_archivos' => $rutas_archivo,
                    'monto_total' => $monto_total,
                    'cuenta_contable_nombre' => $nombre_cuenta_contable,
                    'centros_costo' => array_map(function($cc, $p) {
                        return ['id_centro_costo' => $cc, 'porcentaje' => floatval($p)];
                    }, $id_centro_costo, $porcentajes)
                ];
            } elseif ($action === 'delete') {
                $detalle_id = $_POST['detalle_id'] ?? '';
                $serie = $_POST['serie'] ?? '';
                $no_factura = $_POST['no_factura'] ?? '';
                $numero_dte = $serie && strpos($no_factura, $serie) === 0 ? substr($no_factura, strlen($serie)) : $no_factura;

                if (empty($detalle_id)) {
                    throw new Exception('ID de detalle no proporcionado.');
                }

                $detalle = $this->detalleModel->getDetalleById($detalle_id);
                if (!$detalle) {
                    throw new Exception('Detalle no encontrado.');
                }

                // Obtener grupo_id y eliminar detalles según corresponda
                $grupo_id = $detalle['grupo_id'];
                $detalleModel = new DetalleLiquidacion();
                if ($grupo_id == 0) {
                    // Eliminar solo el detalle actual
                    if ($detalleModel->deleteDetalleLiquidacion($detalle_id)) {
                        $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ELIMINAR_DETALLE', "Factura eliminada: {$detalle['no_factura']}, detalle ID: $detalle_id");
                    } else {
                        throw new Exception('Error al eliminar detalle de liquidación ID: ' . $detalle_id);
                    }
                } else {
                    // Eliminar todos los detalles del grupo
                    $stmt = $this->pdo->prepare("SELECT id FROM detalle_liquidaciones WHERE grupo_id = ? AND id_liquidacion = ?");
                    $stmt->execute([$grupo_id, $id]);
                    $detalles_grupo = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($detalles_grupo as $detalle_grupo) {
                        if ($detalleModel->deleteDetalleLiquidacion($detalle_grupo['id'])) {
                            $this->auditoriaModel->createAuditoria($id, $detalle_grupo['id'], $_SESSION['user_id'], 'ELIMINAR_DETALLE', "Factura eliminada: {$detalle['no_factura']}, detalle ID: {$detalle_grupo['id']}");
                        } else {
                            throw new Exception('Error al eliminar detalle de liquidación ID: ' . $detalle_grupo['id']);
                        }
                    }
                }

                if ($serie && $numero_dte) {
                    $serie = trim($serie);
                    $numero_dte = trim(str_replace('-', '', $numero_dte));
                    
                    error_log("Actualizando DTE al crear factura - Serie: '$serie', Numero DTE: '$numero_dte'");
                    
                    if ($this->dteModel->updateDteUsado($serie, $numero_dte)) {
                        error_log("DTE actualizado exitosamente a 'Y'");
                    } else {
                        error_log("Error: No se pudo actualizar el DTE a 'Y'");
                        // No lanzar excepción aquí para no interrumpir el proceso de creación
                    }
                }

                $detallesActualizados = $detalleModel->getDetallesByLiquidacionId($id);
                $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                $this->liquidacionModel->updateMontoTotal($id, $monto_total);

                $response = [
                    'message' => 'Grupo de facturas eliminado correctamente',
                    'monto_total' => $monto_total
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

    $centroCostoModel = new CentroCosto();
    $centroCostoLiquidacion = $centroCostoModel->getCentroCostoById($liquidacion['id_centros_de_costos']);
    $nombreCentroCostoLiquidacion = $centroCostoLiquidacion ? $centroCostoLiquidacion['nombre'] : 'N/A';

    $centroCostoCajaChica = $centroCostoModel->getCentroCostoById($cajaChica['id_centro_costo']);
    $nombreCentroCostoCajaChica = $centroCostoCajaChica ? $centroCostoCajaChica['nombre'] : 'N/A';
    $codigoCentroCostoCajaChica = $centroCostoCajaChica ? $centroCostoCajaChica['codigo'] : 'N/A';

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
        $centroCosto = $centroCostoModel->getCentroCostoById($detalle['id_centro_costo']);
        $detalle['nombre_centro_costo'] = $centroCosto ? $centroCosto['nombre'] . ' / ' . $centroCosto['codigo'] : 'N/A';
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
        $select_centros_costos .= "<option value='{$centro['id']}' $selected>{$centro['nombre']} / {$centro['codigo']}</option>";
    }

    $monto_total = array_sum(array_column($detalles, 'total_factura'));

    $data = [
        'id' => $liquidacion['id'],
        'nombre_caja_chica' => $cajaChica['nombre'],
        'id_caja_chica' => $liquidacion['id_caja_chica'],
        'cliente_nombre_caja_chica' => $cajaChica['cliente_nombre_caja_chica'] ?? 'No asignado',
        'clientes' => $cajaChica['clientes'] ?? 'No asignado',
        'centro_costo_caja_chica_id' => $cajaChica['id_centro_costo'],
        'centro_costo_caja_chica_nombre' => $nombreCentroCostoCajaChica,
        'centro_costo_caja_chica_codigo' => $codigoCentroCostoCajaChica,
        'centro_costo_liquidacion_id' => $liquidacion['id_centros_de_costos'],
        'centro_costo_liquidacion_nombre' => $nombreCentroCostoLiquidacion,
        'fecha_inicio' => $liquidacion['fecha_inicio'],
        'fecha_fin' => $liquidacion['fecha_fin'],
        'updated_at' => $liquidacion['updated_at'],
        'suggested_centro_costo_id' => $suggestedCentroCostoId,
        'monto_total' => $monto_total,
        'detalles' => $detalles
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $action = $_POST['action'] ?? '';
        try {
            $this->pdo->beginTransaction();

            // Handle file uploads
            $rutas_archivos = [];
            $uploadDir = '../Uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024;

            // Procesar archivos existentes y eliminados
            $existingFiles = isset($_POST['existing_files']) ? (is_array($_POST['existing_files']) ? $_POST['existing_files'] : json_decode($_POST['existing_files'], true)) : [];
            $removedFiles = isset($_POST['removed_files']) ? (is_array($_POST['removed_files']) ? $_POST['removed_files'] : json_decode($_POST['removed_files'], true)) : [];

            // Procesar nuevos archivos subidos
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
                        $rutas_archivos[] = 'Uploads/' . basename($filePath);
                    } elseif ($_FILES['archivos']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                        throw new Exception('Error al subir el archivo: ' . $name);
                    }
                }
            }

            if ($action === 'update') {
                // Extract and validate form data
                $detalle_id = $_POST['detalle_id'] ?? '';
                $tipo_documento = $_POST['tipo_documento'] ?? '';
                $no_factura = (string)($_POST['no_factura'] ?? '');
                $serie = (string)($_POST['serie'] ?? '');
                $numero_dte = $serie && strpos($no_factura, $serie) === 0 ? substr($no_factura, strlen($serie)) : $no_factura;
                $nombre_proveedor = $_POST['nombre_proveedor'] ?? '';
                $nit_proveedor = $_POST['nit_proveedor'] ?? null;
                $dpi = $_POST['dpi'] ?? null;
                $fecha = $_POST['fecha'] ?? '';
                $t_gasto = $_POST['t_gasto'] ?? '';
                $tipo_combustible = $_POST['tipo_combustible'] ?? null;
                $subtotal = floatval($_POST['subtotal'] ?? 0);
                $total_factura = floatval($_POST['total_factura'] ?? 0);
                $iva = isset($_POST['iva']) && $_POST['iva'] !== '' ? floatval($_POST['iva']) : null;
                $idp = isset($_POST['idp']) && $_POST['idp'] !== '' ? floatval($_POST['idp']) : null;
                $inguat = isset($_POST['inguat']) && $_POST['inguat'] !== '' ? floatval($_POST['inguat']) : null;
                $id_centro_costo = is_array($_POST['id_centro_costo']) ? $_POST['id_centro_costo'] : [$_POST['id_centro_costo']];
                $porcentajes = is_array($_POST['porcentaje']) ? $_POST['porcentaje'] : [$_POST['porcentaje'] ?? 100];
                $id_cuenta_contable = $_POST['id_cuenta_contable'] ?? null;
                $id_cuenta_contable_idp = $_POST['id_cuenta_contable_idp'] ?? null;
                $nombre_cuenta_contable = $_POST['nombre_cuenta_contable'] ?? '';
                $cantidad = isset($_POST['cantidad']) && $_POST['cantidad'] !== '' ? floatval($_POST['cantidad']) : null;
                $correccion_comentario = $_POST['correccion_comentario'] ?? '';
                $comentarios = $_POST['comentarios'] ?? null;
                $propina = isset($_POST['propina']) && $_POST['propina'] !== '' ? floatval($_POST['propina']) : null;
                $id_cuenta_contable_propina = $_POST['id_cuenta_contable_propina'] ?? null;
                $nombre_cuenta_contable_propina = $_POST['nombre_cuenta_contable_propina'] ?? '';

                // PROCESAR CUENTAS CONTABLES POR CENTRO DE COSTO
                $id_cuenta_contable_centro = [];
                $nombre_cuenta_contable_centro = [];

                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'id_cuenta_contable_centro_') === 0) {
                        $index = substr($key, strlen('id_cuenta_contable_centro_'));
                        $id_cuenta_contable_centro[$index] = $value;
                    }
                    if (strpos($key, 'nombre_cuenta_contable_centro_') === 0) {
                        $index = substr($key, strlen('nombre_cuenta_contable_centro_'));
                        $nombre_cuenta_contable_centro[$index] = $value;
                    }
                }

                // Validate required fields
                if (empty($detalle_id) || empty($tipo_documento) || empty($no_factura) || empty($nombre_proveedor) || empty($fecha) || empty($t_gasto) || !is_numeric($subtotal) || !is_numeric($total_factura)) {
                    throw new Exception('Los campos obligatorios deben ser válidos.');
                }

                if (empty($id_centro_costo)) {
                    throw new Exception('El Centro de Costo es obligatorio.');
                }

                if (empty($id_cuenta_contable)) {
                    throw new Exception('La Cuenta Contable es obligatoria.');
                }

                // Validate centros de costo and porcentajes
                if (count($id_centro_costo) !== count($porcentajes)) {
                    throw new Exception('Los centros de costo y porcentajes no coinciden.');
                }
                $totalPorcentaje = array_sum($porcentajes);
                if (abs($totalPorcentaje - 100) > 0.01) {
                    throw new Exception('La suma de los porcentajes debe ser exactamente 100%.');
                }

                // Document-specific validations
                if (in_array($tipo_documento, ['RECIBO', 'RECIBO FISCAL', 'RECIBO INFORMATIVO'])) {
                    $nit_proveedor = null;
                } else {
                    $dpi = null;
                }

                if ($tipo_documento === 'COMPROBANTE' && (empty($cantidad) || empty($serie))) {
                    throw new Exception('Cantidad y Serie son obligatorios para el tipo de documento Comprobante.');
                }

                // if (in_array($tipo_documento, ['RECIBO', 'RECIBO FISCAL', 'RECIBO INFORMATIVO']) && empty($dpi)) {
                //     throw new Exception('DPI es obligatorio para el tipo de documento ' . $tipo_documento . '.');
                // }

                if (in_array($tipo_documento, ['FACTURA', 'COMPROBANTE', 'DUCA']) && empty($nit_proveedor)) {
                    throw new Exception('NIT es obligatorio para el tipo de documento ' . $tipo_documento . '.');
                }

                if (in_array($tipo_documento, ['FACTURA', 'DUCA'])) {
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

                // Validate date range
                $fechaFactura = new DateTime($fecha);
                $fechaInicio = new DateTime($liquidacion['fecha_inicio']);
                $fechaFin = new DateTime($liquidacion['fecha_fin']);
                if ($fechaFactura < $fechaInicio || $fechaFactura > $fechaFin) {
                    throw new Exception("La fecha de la factura debe estar entre {$liquidacion['fecha_inicio']} y {$liquidacion['fecha_fin']}.");
                }

                // Validate detalle
                $detalle = $this->detalleModel->getDetalleById($detalle_id);
                if (!$detalle || $detalle['estado'] !== 'EN_CORRECCION') {
                    throw new Exception('El detalle no está en estado EN_CORRECCION o no existe.');
                }

                // Obtener grupo_id
                $grupo_id = $detalle['grupo_id'];
                $detalles_grupo = [];
                if ($grupo_id > 0) {
                    $stmt = $this->pdo->prepare("SELECT id, id_centro_costo, porcentaje FROM detalle_liquidaciones WHERE grupo_id = ? AND id_liquidacion = ?");
                    $stmt->execute([$grupo_id, $id]);
                    $detalles_grupo = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $detalles_grupo = [['id' => $detalle_id, 'id_centro_costo' => $detalle['id_centro_costo'], 'porcentaje' => $detalle['porcentaje']]];
                }

                // Validar DTE si serie y numero_dte están presentes
                // if ($serie && $numero_dte) {
                //     $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM dte WHERE serie = ? AND numero_dte = ?");
                //     $stmt->execute([$serie, $numero_dte]);
                //     if ($stmt->fetchColumn() == 0) {
                //         throw new Exception("El DTE con serie '$serie' y número '$numero_dte' no existe en la base de datos.");
                //     }
                // }

                // Verificar duplicados de no_factura
                if ($no_factura) {
                    $stmt = $this->pdo->prepare("
                        SELECT COUNT(*) 
                        FROM detalle_liquidaciones 
                        WHERE no_factura = ? AND id_liquidacion != ? AND id != ?
                    ");
                    $stmt->execute([$no_factura, $id, $detalle_id]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("La factura con número '$no_factura' ya está asociada a otra liquidación.");
                    }
                }

                // Merge existing files with new files, excluding removed files
                $rutas_archivos = array_unique(array_merge($existingFiles, $rutas_archivos));
                $rutas_archivos = array_filter($rutas_archivos, function ($ruta) use ($removedFiles) {
                    return !in_array($ruta, $removedFiles);
                });
                $rutas_json = json_encode(array_values($rutas_archivos));

                // Determinar nuevo grupo_id
                $new_grupo_id = (count($id_centro_costo) == 1) ? 0 : ($grupo_id > 0 ? $grupo_id : $this->pdo->query("SELECT COALESCE(MAX(grupo_id), 0) + 1 FROM detalle_liquidaciones")->fetchColumn());
                error_log("Grupo_id para actualización: $new_grupo_id (original grupo_id: $grupo_id, centros de costo: " . count($id_centro_costo) . ")");

                // Actualizar o crear detalles
                $detalleModel = new DetalleLiquidacion();
                $detalle_ids = [];
                $used_detalle_ids = []; // Para rastrear los IDs de detalles usados

                // Mapear detalles existentes a los nuevos centros de costo por índice
                foreach ($id_centro_costo as $index => $centro_costo) {
                    $porcentaje = floatval($porcentajes[$index]);
                    $es_principal = ($index === 0) ? 1 : 0;

                // USAR CUENTA CONTABLE ESPECÍFICA PARA CADA CENTRO DE COSTO
                if ($index === 0) {
                // Para el primer centro de costo, usar la cuenta contable principal
                $cuenta_contable_id = $id_cuenta_contable;
                $cuenta_contable_nombre = $nombre_cuenta_contable;
                } else {
                // Para centros de costo adicionales, usar la cuenta específica
                $cuenta_contable_id = $id_cuenta_contable_centro[$index] ?? $id_cuenta_contable;
                $cuenta_contable_nombre = $nombre_cuenta_contable_centro[$index] ?? $nombre_cuenta_contable;
                }

                    // Determinar el ID del detalle a usar
                    $current_detalle_id = null;
                    if ($index === 0) {
                        $current_detalle_id = $detalle_id; // Primer detalle siempre usa el detalle_id principal
                    } elseif ($index < count($detalles_grupo)) {
                        // Asignar un detalle existente basado en el índice, si está disponible
                        $current_detalle_id = $detalles_grupo[$index]['id'];
                    }

                    if ($current_detalle_id) {
                        // Actualizar detalle existente
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
                                nombre_cuenta_contable = :nombre_cuenta_contable,
                                id_cuenta_contable_idp = :id_cuenta_contable_idp,
                                tipo_combustible = :tipo_combustible,
                                correccion_comentario = :correccion_comentario,
                                comentarios = :comentarios,
                                porcentaje = :porcentaje,
                                es_principal = :es_principal,
                                grupo_id = :grupo_id,
                                propina = :propina,
                                id_cuenta_contable_propina = :id_cuenta_contable_propina,
                                nombre_cuenta_contable_propina = :nombre_cuenta_contable_propina,
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
                            ':subtotal' => $subtotal * ($porcentaje / 100),
                            ':total_factura' => $total_factura * ($porcentaje / 100),
                            ':id_centro_costo' => $centro_costo,
                            ':cantidad' => $cantidad,
                            ':serie' => $serie,
                            ':rutas_json' => $rutas_json,
                            ':iva' => $iva ? ($iva * ($porcentaje / 100)) : null,
                            ':idp' => $idp ? ($idp * ($porcentaje / 100)) : null,
                            ':inguat' => $inguat ? ($inguat * ($porcentaje / 100)) : null,
                            ':propina' => $propina ? ($propina * ($porcentaje / 100)) : null,
                            ':id_cuenta_contable_propina' => $id_cuenta_contable_propina,
                            ':nombre_cuenta_contable_propina' => $nombre_cuenta_contable_propina,
                            ':id_cuenta_contable_idp' => $id_cuenta_contable_idp,
                            ':id_cuenta_contable' => $id_cuenta_contable,
                            ':nombre_cuenta_contable' => $nombre_cuenta_contable,
                            ':tipo_combustible' => $tipo_combustible,
                            ':correccion_comentario' => $correccion_comentario,
                            ':comentarios' => $comentarios,
                            ':porcentaje' => $porcentaje,
                            ':es_principal' => $es_principal,
                            ':grupo_id' => $new_grupo_id,
                            ':detalle_id' => $current_detalle_id,
                        ]);
                        $detalle_ids[] = $current_detalle_id;
                        $used_detalle_ids[] = $current_detalle_id;
                        error_log("Actualizado detalle ID $current_detalle_id con grupo_id $new_grupo_id para centro de costo $centro_costo con porcentaje $porcentaje");
                    } else {
                        // Crear nuevo detalle
                        $new_detalle_id = $detalleModel->createDetalleLiquidacion(
                            $id,
                            $tipo_documento,
                            $no_factura,
                            $nombre_proveedor,
                            $nit_proveedor,
                            $dpi,
                            $fecha,
                            $t_gasto,
                            $subtotal * ($porcentaje / 100),
                            $total_factura * ($porcentaje / 100),
                            'EN_CORRECCION',
                            $centro_costo,
                            $cantidad,
                            $serie,
                            $rutas_json,
                            $iva ? ($iva * ($porcentaje / 100)) : null,
                            $idp ? ($idp * ($porcentaje / 100)) : null,
                            $inguat ? ($inguat * ($porcentaje / 100)) : null,
                            $id_cuenta_contable,
                            $tipo_combustible,
                            $detalle['id_usuario'],
                            $correccion_comentario,
                            $porcentaje,
                            $nombre_cuenta_contable,
                            $es_principal,
                            $new_grupo_id,
                            $comentarios
                        );
                        if (!$new_detalle_id) {
                            throw new Exception('Error al crear nuevo detalle de liquidación.');
                        }
                        $detalle_ids[] = $new_detalle_id;
                        error_log("Creado detalle secundario ID $new_detalle_id con grupo_id $new_grupo_id para centro de costo $centro_costo con porcentaje $porcentaje");
                    }
                }

                // Eliminar detalles sobrantes solo si hay menos centros de costo que antes
                if ($grupo_id > 0 && count($id_centro_costo) < count($detalles_grupo)) {
                    foreach ($detalles_grupo as $old_detalle) {
                        if (!in_array($old_detalle['id'], $used_detalle_ids)) {
                            $detalleModel->deleteDetalleLiquidacion($old_detalle['id']);
                            $this->auditoriaModel->createAuditoria($id, $old_detalle['id'], $_SESSION['user_id'], 'ELIMINAR_DETALLE_EN_CORRECCION', "Detalle eliminado para factura: $no_factura, centro de costo ID: {$old_detalle['id_centro_costo']}");
                            error_log("Eliminado detalle ID {$old_detalle['id']} para centro de costo {$old_detalle['id_centro_costo']} (no incluido en los nuevos centros de costo)");
                        }
                    }
                }

                // Actualizar usado en la tabla dte
                if ($serie && $numero_dte && ($serie != $detalle['serie'] || $numero_dte != $detalle['no_factura'])) {
                    if ($detalle['serie'] && $detalle['no_factura']) {
                        $stmt = $this->pdo->prepare("UPDATE dte SET usado = 'N' WHERE serie = ? AND numero_dte = ?");
                        $stmt->execute([$detalle['serie'], $detalle['no_factura']]);
                    }
                    $stmt = $this->pdo->prepare("UPDATE dte SET usado = 'Y' WHERE serie = ? AND numero_dte = ?");
                    $stmt->execute([$serie, $numero_dte]);
                }

                $this->auditoriaModel->createAuditoria($id, $detalle_id, $_SESSION['user_id'], 'ACTUALIZAR_DETALLE_EN_CORRECCION', "Factura actualizada en corrección: $no_factura");

                // Update total amount for liquidation
                $detallesActualizados = $detalleModel->getDetallesByLiquidacionId($id);
                $monto_total = array_sum(array_column($detallesActualizados, 'total_factura'));
                $this->liquidacionModel->updateMontoTotal($id, $monto_total);

                $response = [
                    'message' => 'Factura actualizada correctamente.',
                    'detalle_id' => $detalle_id,
                    'detalle_ids' => $detalle_ids, // Incluir todos los IDs de detalles
                    'grupo_id' => $new_grupo_id,
                    'rutas_archivos' => $rutas_archivos,
                    'monto_total' => $monto_total,
                    'cuenta_contable_nombre' => $nombre_cuenta_contable,
                    'centros_costo' => array_map(function($cc, $p) {
                        return ['id_centro_costo' => $cc, 'porcentaje' => floatval($p)];
                    }, $id_centro_costo, $porcentajes),
                    'comentarios' => $comentarios
                ];

                $this->pdo->commit();
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                throw new Exception('Acción no válida.');
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            if (!empty($rutas_archivos)) {
                foreach ($rutas_archivos as $ruta) {
                    $filePath = '../' . str_replace('\\', '/', $ruta);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            error_log('Error en updateCorreccion: ' . $e->getMessage() . '. POST data: ' . print_r($_POST, true));
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
        $detalle['nombre_centro_costo'] = $centroCosto['nombre'] . ' / ' . $centroCosto['codigo'] ?? 'N/A';
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
        $select_centros_costos .= "<option value='{$centro['id']}' $selected>{$centro['nombre']} / {$centro['codigo']}</option>";
    }

    $data = $liquidacion;
    $data['nombre_caja_chica'] = $cajaChica['nombre'];
    $data['clientes'] = $cajaChica['clientes'] ?? null;
    $data['originating_roles'] = $originating_roles;

    require '../views/liquidaciones/correccion.html';
    exit;
}

    public function submitCorreccion($id)
{
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

    // Validar y normalizar el rol
    $normalized_role = null;
    if ($submitted_role === null || $submitted_role === '') {
        error_log("submitCorreccion: No se proporcionó originating_role");
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(400);
        echo json_encode(['error' => 'No se proporcionó un rol de origen'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $submitted_role_upper = strtoupper($submitted_role);
    if ($submitted_role_upper === 'CONTABILIDAD') {
        $normalized_role = 'CONTABILIDAD';
    } elseif (stripos($submitted_role_upper, 'SUPERVISOR') !== false) {
        $normalized_role = 'SUPERVISOR';
    } else {
        error_log("submitCorreccion: Rol de origen no válido: " . ($submitted_role ?? 'N/A'));
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(400);
        echo json_encode(['error' => 'Rol de origen no válido. Debe ser CONTABILIDAD o SUPERVISOR'], JSON_UNESCAPED_UNICODE);
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
            $normalized_original_role = strtoupper($original_role);
            $normalized_original_role = stripos($normalized_original_role, 'SUPERVISOR') !== false ? 'SUPERVISOR' : $normalized_original_role;

            if ($normalized_original_role !== $normalized_role) {
                error_log("submitCorreccion: Saltando detalle ID {$detalle['id']} (original_role: $original_role, normalized: $normalized_original_role) no coincide con $normalized_role");
                continue;
            }

            $nuevoEstado = $normalized_role === 'SUPERVISOR' ? 'PENDIENTE_AUTORIZACION' : 'PENDIENTE_REVISION_CONTABILIDAD';
            error_log("submitCorreccion: Actualizando detalle ID {$detalle['id']} a $nuevoEstado (original_role: $original_role)");
            $this->detalleModel->updateEstado($detalle['id'], $nuevoEstado);
            if ($normalized_role === 'SUPERVISOR') {
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
public function fetchHanaAccounts($id_centro_costo) {
    ob_start();
    error_log("Starting fetchHanaAccounts with id_centro_costo=$id_centro_costo");
    
    if (!isset($_SESSION['user_id'])) {
        ob_end_clean();
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Sesión no válida. Por favor, inicia sesión.']);
        exit;
    }

    try {
        // Validar centro de costo
        $centroCostoModel = new CentroCosto();
        $centro = $centroCostoModel->getCentroCostoById($id_centro_costo);
        if (!$centro) {
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Centro de costo no encontrado']);
            exit;
        }

        // Obtener cuentas desde SAP HANA
        $sociedad = $_SESSION['sociedad'] ?? 'GT_AGROCENTRO_2016';
        $cuenta = $centro['tipo'] ?? '5';
        error_log("Fetching HANA accounts for sociedad=$sociedad, cuenta=$cuenta");
        $hana_cuentas = $this->ctrObtenerCuentas($sociedad, $cuenta);

        $cuentas_array = [];
        if ($hana_cuentas && $hana_cuentas !== 'sin_datos') {
            $cuentas_list = explode('|', trim($hana_cuentas, '|'));
            foreach ($cuentas_list as $cuenta_item) {
                if (!empty($cuenta_item)) {
                    list($code, $name) = explode('-', $cuenta_item, 2);
                    $name = mb_convert_encoding($name, 'UTF-8', mb_detect_encoding($name));
                    $cuentas_array[] = ['id' => $code, 'nombre' => $name];
                }
            }
        } else {
            error_log("No HANA accounts found for id_centro_costo=$id_centro_costo, cuenta=$cuenta");
        }

        // Eliminar duplicados
        $cuentas_array = array_values(array_unique($cuentas_array, SORT_REGULAR));

        error_log("fetchHanaAccounts: id_centro_costo=$id_centro_costo, cuentas=" . json_encode($cuentas_array));

        ob_end_clean();
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($cuentas_array);
    } catch (Exception $e) {
        ob_end_clean();
        error_log("Error in fetchHanaAccounts: id_centro_costo=$id_centro_costo, error=" . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener cuentas contables desde SAP HANA: ' . $e->getMessage()]);
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