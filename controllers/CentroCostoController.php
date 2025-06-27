<?php
require_once '../models/CentroCosto.php';
require_once '../models/Usuario.php';

class CentroCostoController {
    private $centroCostoModel;

    public function __construct() {
        $this->centroCostoModel = new CentroCosto();
    }

    // Define CONEXION_HANA method
    public function CONEXION_HANA($db_name) {
        $driver = "HDBODBC";
        $servername = "192.168.1.9:30015"; // Adjust to your SAP HANA server
        $username = "SAPDBA"; // Adjust to your username
        $password = "B1Adminh"; // Adjust to your password
        $conn = odbc_connect("Driver=$driver;ServerNode=$servername;Database=$db_name;", $username, $password, SQL_CUR_USE_ODBC);
        if (!$conn) {
            error_log("Error al conectar a HANA: " . odbc_errormsg());
            throw new Exception("Error al conectar a la base de datos HANA: " . odbc_errormsg());
        }
        odbc_exec($conn, "SET CHARACTER SET UTF8");
        return $conn;
    }

    public function listCentrosCostos() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        if (!$usuarioModel->tienePermiso($usuario, 'manage_centros_costos') && !$usuarioModel->tienePermiso($usuario, 'manage_facturas')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para listar centros de costos']);
            exit;
        }

        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
        $baseId = isset($_GET['base_id']) ? intval($_GET['base_id']) : null;

        $centrosCostos = $this->centroCostoModel->getAllCentrosCostos($searchTerm, $baseId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($centrosCostos);
        } else {
            require '../views/centros_costos/list.html';
        }
        exit;
    }

    public function createCentroCosto() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear centros de costos']);
            exit;
        }
    
        $data = $_POST;
        error_log("Datos recibidos para crear centro de costos: " . print_r($data, true));
        $required_fields = ['codigo', 'nombre', 'tipo'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                error_log("Campo requerido faltante: $field");
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => ucfirst($field) . ' es obligatorio']);
                exit;
            }
        }
    
        $codigoExists = $this->centroCostoModel->checkCodigoExists($data['codigo']);
        error_log("Resultado de checkCodigoExists antes de insertar: " . ($codigoExists ? 'true' : 'false'));
        if ($codigoExists) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El código ya existe según la verificación previa. Por favor, usa un código diferente']);
            exit;
        }
    
        try {
            $result = $this->centroCostoModel->createCentroCosto($data);
            error_log("Resultado de createCentroCosto: " . ($result ? 'Éxito' : 'Fallo'));
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Centro de costos creado']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear el centro de costos']);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            error_log("Error PDO al crear centro de costos: " . $e->getMessage());
            if ($e->getCode() == 23000) {
                $existingCode = $this->centroCostoModel->checkCodigoExists($data['codigo']);
                error_log("Código '" . $data['codigo'] . "' encontrado después de fallo: " . ($existingCode ? 'Sí' : 'No'));
                $stmt = $this->centroCostoModel->getPdo()->prepare("SELECT codigo FROM centros_costos WHERE TRIM(codigo) = ?");
                $stmt->execute([trim($data['codigo'])]);
                $conflictingCode = $stmt->fetchColumn();
                error_log("Código en conflicto encontrado: " . ($conflictingCode ? $conflictingCode : 'No encontrado'));
                echo json_encode(['error' => 'El código ya existe. Código en conflicto: ' . ($conflictingCode ? $conflictingCode : 'desconocido') . '. Por favor, usa un código diferente']);
            } else {
                echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
        }
        exit;
    }

    public function updateCentroCosto($id) {
        error_log("Iniciando updateCentroCosto para ID: $id");
        error_log("Sesión actual: " . print_r($_SESSION, true));
    
        if (!isset($_SESSION['user_id'])) {
            error_log("No hay sesión activa. Redirigiendo a login.");
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        error_log("Usuario obtenido: " . print_r($usuario, true));
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) {
            error_log("Usuario no autorizado. Rol: " . ($usuario['rol'] ?? 'No definido'));
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar centros de costos']);
            exit;
        }
    
        $data = $_POST;
        error_log("Datos recibidos para actualizar: " . print_r($data, true));
        $required_fields = ['nombre', 'estado'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => ucfirst($field) . ' es obligatorio']);
                exit;
            }
        }
    
        $tipo = $data['tipo'] ?? null;
        try {
            if ($this->centroCostoModel->updateCentroCosto($id, $data['nombre'], $data['estado'], $tipo)) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Centro de costos actualizado']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar el centro de costos']);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        exit;
    }

    public function deleteCentroCosto($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar centros de costos']);
            exit;
        }
    
        try {
            if ($this->centroCostoModel->deleteCentroCosto($id)) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Centro de costos eliminado']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al eliminar el centro de costos']);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            if ($e->getCode() == '23000') {
                echo json_encode(['error' => 'No se puede eliminar el centro de costos porque está asociado a una o más facturas.']);
            } else {
                echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
        }
        exit;
    }

    public function updateForm($id) {
        error_log("Iniciando updateForm para ID: $id");
        if (!isset($_SESSION['user_id'])) {
            error_log("No hay sesión activa en updateForm.");
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        error_log("Usuario obtenido en updateForm: " . print_r($usuario, true));
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) {
            error_log("Usuario no autorizado en updateForm. Rol: " . ($usuario['rol'] ?? 'No definido'));
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar centros de costos']);
            exit;
        }
    
        $centroCosto = $this->centroCostoModel->getCentroCostoById($id);
        error_log("Centro de costos obtenido para ID $id: " . print_r($centroCosto, true));
        if ($centroCosto === false) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Centro de costos no encontrado']);
            exit;
        }
    
        ob_start();
        require '../views/centros_costos/update_form.html';
        $html = ob_get_clean();
        echo $html;
    }

    public function createForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear centros de costos']);
            exit;
        }

        ob_start();
        require '../views/centros_costos/form.html';
        $html = ob_get_clean();
        echo $html;
        exit;
    }

    public function checkCodigo() {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para verificar códigos']);
            exit;
        }
    
        if (!isset($_GET['codigo']) || empty(trim($_GET['codigo']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Código no proporcionado']);
            exit;
        }
    
        $codigo = trim($_GET['codigo']);
        error_log("Verificando código: '$codigo'");
        try {
            $exists = $this->centroCostoModel->checkCodigoExists($codigo);
            error_log("Resultado de checkCodigoExists para '$codigo': " . ($exists ? 'true' : 'false'));
            echo json_encode(['exists' => $exists]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al verificar el código: ' . $e->getMessage()]);
        }
        exit;
    }

    public function getCentrosCostos($base_id = null) {
        ob_start(); // Start output buffering
        error_log("Starting getCentrosCostos with base_id=" . ($base_id ?? 'null'));
        error_log("Session data: " . print_r($_SESSION, true));

        if (!isset($_SESSION['user_id'])) {
            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Sesión no válida. Por favor, inicia sesión.']);
            exit;
        }

        try {
            // Initialize centro costo model
            $centroCostoModel = new CentroCosto();
            $pdo = Database::getInstance()->getPdo();
            if (!$pdo) {
                throw new Exception("Failed to get PDO instance");
            }
            $centros_array = [];

            // Fetch cost centers from local database first
            $query = "SELECT id, codigo, nombre, tipo FROM centros_costos WHERE estado = 'ACTIVO'";
            $params = [];
            if ($base_id !== null) {
                $query .= " AND base_id = ?";
                $params[] = $base_id;
            }
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $local_centros = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Local cost centers fetched: " . print_r($local_centros, true));

            foreach ($local_centros as $centro) {
                $centros_array[] = [
                    'id' => $centro['id'],
                    'codigo' => $centro['codigo'],
                    'nombre' => $centro['nombre'],
                    'tipo' => $centro['tipo']
                ];
            }

            // Fetch cost centers from HANA
            $sociedad = $_SESSION['sociedad'] ?? 'GT_AGROCENTRO_2016';
            $query_hana = 'SELECT T0."PrcCode" as "Codigo Centro de costo", T0."PrcName", T0."GrpCode" FROM ' . $sociedad . '.OPRC T0';
            error_log("Ejecutando consulta HANA para centros de costos: " . $query_hana);
            $conexion = $this->CONEXION_HANA($sociedad);
            $result = odbc_exec($conexion, $query_hana);

            if ($result) {
                while ($centro = odbc_fetch_object($result)) {
                    $codigo = trim($centro->{'Codigo Centro de costo'});
                    $nombre = trim($centro->PrcName);
                    $tipo = trim($centro->GrpCode) ?: '5'; // Default to '5' if GrpCode is null

                    // Convert to UTF-8 if necessary
                    if (!mb_check_encoding($nombre, 'UTF-8')) {
                        $nombre = mb_convert_encoding($nombre, 'UTF-8', 'ISO-8859-1');
                        error_log("Nombre de centro de costo convertido a UTF-8: $codigo - $nombre");
                    }

                    // Check if the cost center exists with the same codigo
                    $stmt = $pdo->prepare("
                        SELECT id, nombre, base_id 
                        FROM centros_costos 
                        WHERE codigo = ?
                    ");
                    $stmt->execute([$codigo]);
                    $existingCentro = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$existingCentro) {
                        // Insert new cost center
                        $stmt = $pdo->prepare("
                            INSERT INTO centros_costos (codigo, nombre, estado, tipo, base_id)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $estado = 'ACTIVO';
                        $insert_base_id = $base_id !== null ? $base_id : 1; // Default base_id to 1 if not provided
                        $stmt->execute([$codigo, $nombre, $estado, $tipo, $insert_base_id]);
                        $id = $pdo->lastInsertId();
                        $centros_array[] = [
                            'id' => $id,
                            'codigo' => $codigo,
                            'nombre' => $nombre,
                            'tipo' => $tipo
                        ];
                        error_log("Inserted new cost center: codigo=$codigo, nombre=$nombre, base_id=$insert_base_id, tipo=$tipo");
                    } elseif ($existingCentro['base_id'] == ($base_id ?? $existingCentro['base_id']) && $existingCentro['nombre'] == $nombre) {
                        // Cost center exists for this base_id and matches name, include it
                        if (!in_array([
                            'id' => $existingCentro['id'],
                            'codigo' => $codigo,
                            'nombre' => $existingCentro['nombre'],
                            'tipo' => $tipo
                        ], $centros_array)) {
                            $centros_array[] = [
                                'id' => $existingCentro['id'],
                                'codigo' => $codigo,
                                'nombre' => $existingCentro['nombre'],
                                'tipo' => $tipo
                            ];
                        }
                    } else {
                        // Cost center exists for a different base_id or name mismatch, log and skip
                        error_log("Skipping duplicate cost center: codigo=$codigo exists for base_id={$existingCentro['base_id']}, requested base_id=" . ($base_id ?? 'null'));
                        continue;
                    }
                }
                odbc_free_result($result);
            } else {
                error_log("Error al ejecutar la consulta HANA: " . odbc_errormsg($conexion));
                throw new Exception("Error al ejecutar la consulta en la base de datos HANA: " . odbc_errormsg($conexion));
            }
            odbc_close($conexion);

            // Remove duplicates
            $centros_array = array_values(array_unique($centros_array, SORT_REGULAR));

            // Log the response
            error_log("getCentrosCostos: base_id=" . ($base_id ?? 'null') . ", centros=" . json_encode($centros_array));

            ob_end_clean();
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode($centros_array);
        } catch (Exception $e) {
            ob_end_clean();
            error_log("Error in getCentrosCostos: base_id=" . ($base_id ?? 'null') . ", error=" . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener centros de costos: ' . $e->getMessage()]);
        }
        exit;
    }
}