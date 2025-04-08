<?php
require_once '../models/CentroCosto.php';
require_once '../models/Usuario.php';

class CentroCostoController {
    private $centroCostoModel;

    public function __construct() {
        $this->centroCostoModel = new CentroCosto();
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
            // Verificar si el error es una violación de clave foránea (SQLSTATE[23000])
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
}