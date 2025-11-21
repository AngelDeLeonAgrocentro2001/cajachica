<?php
require_once '../models/CajaChica.php';
require_once '../models/Usuario.php';
require_once '../models/CentroCosto.php';

class CajaChicaController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function CONEXION_HANA($db_name) {
        $driver = "HDBODBC";
        $servername = "192.168.1.9:30015";
        $username = "SAPDBA";
        $password = "B1Adminh";
        $conn = odbc_connect("Driver=$driver;ServerNode=$servername;Database=$db_name;", $username, $password, SQL_CUR_USE_ODBC);
        if (!$conn) {
            error_log("Error al conectar a HANA: " . odbc_errormsg());
            throw new Exception("Error al conectar a la base de datos HANA: " . odbc_errormsg());
        }
        return $conn;
    }

    public function ctrObtenerClientes($db_name) {
        error_log("ctrObtenerClientes called with db_name=$db_name");
        $schema = $_SESSION['sociedad'] ?? $db_name;
        $qry = "SELECT T0.\"CardCode\", T0.\"CardName\" FROM \"$schema\".OCRD T0 WHERE T0.\"CardCode\" IN (
            'CCHA0001', 'CCHA0004', 'CCHA0005', 'CCHA0008', 'CCHA0009',
            'CCHA0010', 'CCHA0011', 'CCHA0012', 'CCHA0013', 'CCHA0014',
            'CCHA0015', 'CCHA0016', 'CCHA0017', 'CCHA0018', 'CCHA0019',
            'CCHA0020', 'CCHA0021', 'CCHC0003', 'CCHC0004', 'CCHC0005',
            'CCHC0006', 'CCHC0007', 'CCHC0009', 'CCHC0011', 'CCHC0014',
            'CCHC0015', 'CCHC0016', 'CCHC0018', 'CCHC0021', 'CCHC0031',
            'CCHC0035', 'CCHC0041', 'CCHC0044', 'CCHC0045', 'CCHC0047',
            'CCHC0049', 'CCHC0051', 'CCHC0055', 'CCHC0059', 'CCHC0061',
            'CCHC0068', 'CCHC0075', 'CCHC0079', 'CCHC0081', 'CCHC0085',
            'CCHC0087', 'CCHC0088', 'CCHC0089', 'CCHC0090', 'CCHC0091',
            'CCHC0092', 'CCHC0093', 'CCHC0094', 'CCHC0095', 'CCHC0096',
            'CCHC0097', 'CCHD0002', 'CCHD0004', 'CCHD0005', 'CCHD0006',
            'CCHD0008', 'CCHD0011', 'CCHD0012', 'CCHD0013', 'CCHD0019',
            'CCHD0020', 'CCHD0023', 'CCHD0024', 'PN0350', 'PN0354',
            'PN0561', 'PN2009', 'PN9237', 'PN9346','CCHA0022','CCHA0024',
            'CCHA0025','CCHC0099','CCHC0098','CCHC0100'
        )";
        error_log("Query constructed: $qry");
        $respuesta = $this->mdlObtenerClientes($qry, $db_name);
        return $respuesta;
    }

    public function mdlObtenerClientes($query, $db_name) {
        try {
            $conexion = $this->CONEXION_HANA($db_name);
            error_log("Ejecutando consulta HANA: " . $query);
            $prov = odbc_exec($conexion, $query);
            $json = "";
            if ($prov) {
                while ($cliente = odbc_fetch_object($prov)) {
                    error_log("Cliente obtenido (sin procesar): " . $cliente->CardCode . '-' . $cliente->CardName);
                    $cardName = trim($cliente->CardName);
                    if (!mb_check_encoding($cardName, 'UTF-8')) {
                        $cardName = mb_convert_encoding($cardName, 'UTF-8', 'ISO-8859-1');
                        error_log("Nombre de cliente convertido a UTF-8: " . $cliente->CardCode . '-' . $cardName);
                    }
                    $json .= "|" . $cliente->CardCode . '-' . $cardName;
                    error_log("Cliente procesado: " . $cliente->CardCode . '-' . $cardName);
                }
                odbc_free_result($prov);
            } else {
                $errorMsg = odbc_errormsg($conexion);
                error_log("Error al ejecutar la consulta HANA: " . $errorMsg);
                throw new Exception("Error al ejecutar la consulta en la base de datos HANA: " . $errorMsg);
            }
            odbc_close($conexion);
            if (empty($json)) {
                error_log("No se encontraron clientes en HANA para la consulta: " . $query);
                return 'sin_datos';
            }
            return $json;
        } catch (Exception $e) {
            error_log("Error en mdlObtenerClientes: " . $e->getMessage());
            throw $e;
        }
    }

    public function listCajasChicas() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $cajaChica = new CajaChica();
        $cajasChicas = $cajaChica->getAllCajasChicas();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($cajasChicas);
        } else {
            require '../views/cajas_chicas/list.html';
        }
        exit;
    }

    public function createCajaChica() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en createCajaChica');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log('Error: No tienes permiso para crear cajas chicas');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear cajas chicas']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre'] ?? '');
                $clientes = trim($_POST['card_name'] ?? '');
                $monto_asignado = floatval($_POST['monto_asignado'] ?? 0);
                $id_usuario_encargado = intval($_POST['id_usuario_encargado'] ?? 0);
                $id_supervisor = intval($_POST['id_supervisor'] ?? 0);
                $id_contador = intval($_POST['id_contador'] ?? 0);
                $id_centro_costo = intval($_POST['id_centro_costo'] ?? 0);
                $estado = trim($_POST['estado'] ?? 'ACTIVA');
    
                if (empty($nombre) || empty($clientes) || $monto_asignado <= 0 || $id_usuario_encargado <= 0 || $id_supervisor <= 0 || $id_contador <= 0 || $id_centro_costo <= 0) {
                    throw new Exception('Todos los campos son obligatorios y deben ser válidos');
                }
    
                if (!$usuarioModel->getUsuarioById($id_usuario_encargado)) {
                    throw new Exception('El usuario encargado no existe');
                }
                if (!$usuarioModel->getUsuarioById($id_supervisor)) {
                    throw new Exception('El supervisor no existe');
                }
                if (!$usuarioModel->getUsuarioById($id_contador)) {
                    throw new Exception('El contador no existe');
                }
    
                $centroCostoModel = new CentroCosto();
                if (!$centroCostoModel->getCentroCostoById($id_centro_costo)) {
                    throw new Exception('El centro de costos no existe');
                }
    
                $cajaChica = new CajaChica();
                if ($cajaChica->createCajaChica($nombre, $monto_asignado, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado, $clientes)) {
                    header('Content-Type: application/json');
                    http_response_code(201);
                    echo json_encode(['message' => 'Caja chica creada']);
                } else {
                    throw new Exception('Error al crear caja chica en la base de datos');
                }
            } catch (Exception $e) {
                error_log('Error en createCajaChica: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        // Fetch clients from SAP HANA
        try {
            $clientes = $this->ctrObtenerClientes('GT_AGROCENTRO_2016');
            $selectClientes = '';
            if ($clientes !== 'sin_datos') {
                $clientesArray = explode('|', trim($clientes, '|'));
                foreach ($clientesArray as $cliente) {
                    list($cardCode, $cardName) = explode('-', $cliente, 2);
                    $selectClientes .= "<option value='$cardCode'>$cardName</option>";
                }
            } else {
                $selectClientes = '<option value="">No se encontraron clientes</option>';
            }
        } catch (Exception $e) {
            error_log('Error al obtener clientes: ' . $e->getMessage());
            $selectClientes = '<option value="">Error al cargar clientes: ' . htmlspecialchars($e->getMessage()) . '</option>';
        }
    
        $usuarioModel = new Usuario();
        
        // Obtener encargados (incluyendo roles mixtos)
        $encargados = $this->getUsuariosConRolEncargado();
        
        // Obtener supervisores (incluyendo roles mixtos)
        $supervisores = $this->getUsuariosConRolSupervisor();
        
        // Obtener contadores (incluyendo roles mixtos)
        $contadores = $this->getUsuariosConRolContador();
        
        $centroCostoModel = new CentroCosto();
        $centrosCostos = $centroCostoModel->getAllCentrosCostos();
    
        $selectEncargados = '';
        foreach ($encargados as $encargado) {
            $selectEncargados .= "<option value='{$encargado['id']}'>{$encargado['nombre']} - {$encargado['rol']}</option>";
        }
    
        $selectSupervisores = '';
        foreach ($supervisores as $supervisor) {
            $selectSupervisores .= "<option value='{$supervisor['id']}'>{$supervisor['nombre']} - {$supervisor['rol']}</option>";
        }
    
        $selectContadores = '';
        foreach ($contadores as $contador) {
            $selectContadores .= "<option value='{$contador['id']}'>{$contador['nombre']} - {$contador['rol']}</option>";
        }
    
        $selectCentrosCostos = '';
        foreach ($centrosCostos as $centro) {
            $selectCentrosCostos .= "<option value='{$centro['id']}'>{$centro['nombre']} / {$centro['codigo']}</option>";
        }
    
        ob_start();
        require '../views/cajas_chicas/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_clientes}}', $selectClientes, $html);
        $html = str_replace('{{select_encargados}}', $selectEncargados, $html);
        $html = str_replace('{{select_supervisores}}', $selectSupervisores, $html);
        $html = str_replace('{{select_contadores}}', $selectContadores, $html);
        $html = str_replace('{{select_centros_costos}}', $selectCentrosCostos, $html);
        echo $html;
        exit;
    }
    
    // Añadir estas nuevas funciones al controlador
    private function getUsuariosConRolEncargado() {
        $usuarioModel = new Usuario();
        $todosUsuarios = $usuarioModel->getAllUsuarios();
        $encargados = [];
        
        foreach ($todosUsuarios as $usuario) {
            $rol = strtoupper($usuario['rol'] ?? '');
            $descripcion = strtoupper($usuario['descripcion'] ?? '');
            
            // Detectar si tiene rol de encargado (directo o mixto)
            $esEncargado = strpos($rol, 'ENCARGADO') !== false || 
                          strpos($rol, 'CAJA_CHICA') !== false ||
                          strpos($descripcion, 'ENCARGADO') !== false ||
                          strpos($descripcion, 'CAJA_CHICA') !== false;
            
            if ($esEncargado) {
                $encargados[] = $usuario;
            }
        }
        
        return $encargados;
    }
    
    private function getUsuariosConRolSupervisor() {
        $usuarioModel = new Usuario();
        $todosUsuarios = $usuarioModel->getAllUsuarios();
        $supervisores = [];
        
        foreach ($todosUsuarios as $usuario) {
            $rol = strtoupper($usuario['rol'] ?? '');
            $descripcion = strtoupper($usuario['descripcion'] ?? '');
            
            // Detectar si tiene rol de supervisor (directo o mixto)
            $esSupervisor = strpos($rol, 'SUPERVISOR') !== false || 
                           strpos($descripcion, 'SUPERVISOR') !== false;
            
            if ($esSupervisor) {
                $supervisores[] = $usuario;
            }
        }
        
        return $supervisores;
    }
    
    private function getUsuariosConRolContador() {
        $usuarioModel = new Usuario();
        $todosUsuarios = $usuarioModel->getAllUsuarios();
        $contadores = [];
        
        foreach ($todosUsuarios as $usuario) {
            $rol = strtoupper($usuario['rol'] ?? '');
            $descripcion = strtoupper($usuario['descripcion'] ?? '');
            
            // Detectar si tiene rol de contador (directo o mixto)
            $esContador = strpos($rol, 'CONTADOR') !== false || 
                         strpos($rol, 'CONTABILIDAD') !== false ||
                         strpos($descripcion, 'CONTADOR') !== false ||
                         strpos($descripcion, 'CONTABILIDAD') !== false;
            
            if ($esContador) {
                $contadores[] = $usuario;
            }
        }
        
        return $contadores;
    }

    public function updateCajaChica($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en updateCajaChica');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log('Error: No tienes permiso para actualizar cajas chicas');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar cajas chicas']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = trim($_POST['nombre'] ?? '');
                $clientes = trim($_POST['card_name'] ?? '');
                $monto_asignado = floatval($_POST['monto_asignado'] ?? 0);
                $monto_disponible = floatval($_POST['monto_disponible'] ?? 0);
                $id_usuario_encargado = intval($_POST['id_usuario_encargado'] ?? 0);
                $id_supervisor = intval($_POST['id_supervisor'] ?? 0);
                $id_contador = intval($_POST['id_contador'] ?? 0);
                $id_centro_costo = intval($_POST['id_centro_costo'] ?? 0);
                $estado = trim($_POST['estado'] ?? 'ACTIVA');
    
                if (empty($nombre) || empty($clientes) || $monto_asignado <= 0 || $monto_disponible < 0 || $id_usuario_encargado <= 0 || $id_supervisor <= 0 || $id_contador <= 0 || $id_centro_costo <= 0) {
                    throw new Exception('Todos los campos son obligatorios y deben ser válidos');
                }
    
                if (!$usuarioModel->getUsuarioById($id_usuario_encargado)) {
                    throw new Exception('El usuario encargado no existe');
                }
                if (!$usuarioModel->getUsuarioById($id_supervisor)) {
                    throw new Exception('El supervisor no existe');
                }
                if (!$usuarioModel->getUsuarioById($id_contador)) {
                    throw new Exception('El contador no existe');
                }
    
                $centroCostoModel = new CentroCosto();
                if (!$centroCostoModel->getCentroCostoById($id_centro_costo)) {
                    throw new Exception('El centro de costos no existe');
                }
    
                $cajaChica = new CajaChica();
                if ($cajaChica->updateCajaChica($id, $nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado, $clientes)) {
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Caja chica actualizada']);
                } else {
                    throw new Exception('Error al actualizar caja chica');
                }
            } catch (Exception $e) {
                error_log('Error en updateCajaChica: ' . $e->getMessage());
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $cajaChica = new CajaChica();
        $data = $cajaChica->getCajaChicaById($id);
        if (!$data) {
            echo "<h2>Error: Caja chica no encontrada</h2>";
            echo "<p>No se pudo cargar la caja chica con ID " . htmlspecialchars($id) . ".</p>";
            echo '<a href="index.php?controller=cajachica&action=list">Volver a Lista</a>';
            exit;
        }
    
        // Fetch clients from SAP HANA
        try {
            $clientes = $this->ctrObtenerClientes('GT_AGROCENTRO_2016');
            $selectClientes = '';
            if ($clientes !== 'sin_datos') {
                $clientesArray = explode('|', trim($clientes, '|'));
                foreach ($clientesArray as $cliente) {
                    list($cardCode, $cardName) = explode('-', $cliente, 2);
                    $selected = $data['clientes'] == $cardCode ? 'selected' : '';
                    $selectClientes .= "<option value='$cardCode' $selected>$cardName</option>";
                }
            } else {
                $selectClientes = '<option value="">No se encontraron clientes</option>';
            }
        } catch (Exception $e) {
            error_log('Error al obtener clientes: ' . $e->getMessage());
            $selectClientes = '<option value="">Error al cargar clientes: ' . htmlspecialchars($e->getMessage()) . '</option>';
        }
    
        $usuarioModel = new Usuario();
        
        // Obtener encargados (incluyendo roles mixtos)
        $encargados = $this->getUsuariosConRolEncargado();
        
        // Obtener supervisores (incluyendo roles mixtos)
        $supervisores = $this->getUsuariosConRolSupervisor();
        
        // Obtener contadores (incluyendo roles mixtos)
        $contadores = $this->getUsuariosConRolContador();
        
        $centroCostoModel = new CentroCosto();
        $centrosCostos = $centroCostoModel->getAllCentrosCostos();
    
        $selectEncargados = '';
        foreach ($encargados as $encargado) {
            $selected = $data['id_usuario_encargado'] == $encargado['id'] ? 'selected' : '';
            $selectEncargados .= "<option value='{$encargado['id']}' $selected>{$encargado['nombre']} - {$encargado['rol']}</option>";
        }
    
        $selectSupervisores = '';
        foreach ($supervisores as $supervisor) {
            $selected = $data['id_supervisor'] == $supervisor['id'] ? 'selected' : '';
            $selectSupervisores .= "<option value='{$supervisor['id']}' $selected>{$supervisor['nombre']} - {$supervisor['rol']}</option>";
        }
    
        $selectContadores = '';
        foreach ($contadores as $contador) {
            $selected = $data['id_contador'] == $contador['id'] ? 'selected' : '';
            $selectContadores .= "<option value='{$contador['id']}' $selected>{$contador['nombre']} - {$contador['rol']}</option>";
        }
    
        $selectCentrosCostos = '';
        foreach ($centrosCostos as $centro) {
            $selected = $data['id_centro_costo'] == $centro['id'] ? 'selected' : '';
            $selectCentrosCostos .= "<option value='{$centro['id']}' $selected>{$centro['nombre']} / {$centro['codigo']}</option>";
        }
    
        ob_start();
        require '../views/cajas_chicas/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_clientes}}', $selectClientes, $html);
        $html = str_replace('{{select_encargados}}', $selectEncargados, $html);
        $html = str_replace('{{select_supervisores}}', $selectSupervisores, $html);
        $html = str_replace('{{select_contadores}}', $selectContadores, $html);
        $html = str_replace('{{select_centros_costos}}', $selectCentrosCostos, $html);
        echo $html;
        exit;
    }

    public function deleteCajaChica($id) {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en deleteCajaChica');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'create_liquidaciones')) {
            error_log('Error: No tienes permiso para eliminar cajas chicas');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar cajas chicas']);
            exit;
        }

        try {
            $cajaChica = new CajaChica();
            if ($cajaChica->deleteCajaChica($id)) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Caja chica eliminada']);
            } else {
                throw new Exception('Error al eliminar caja chica');
            }
        } catch (Exception $e) {
            error_log('Error en deleteCajaChica: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}