<?php
require_once '../models/CuentaContable.php';
require_once '../models/Usuario.php';

class CuentaContableController {
    private $cuentaContableModel;

    public function __construct() {
        $this->cuentaContableModel = new CuentaContable();
    }

    public function listCuentas() {
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

        if (!$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables') && !$usuarioModel->tienePermiso($usuario, 'manage_facturas')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para listar cuentas contables']);
            exit;
        }

        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
        $baseId = isset($_GET['base_id']) ? intval($_GET['base_id']) : null;

        $cuentas = $this->cuentaContableModel->getAllCuentas($searchTerm, $baseId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($cuentas);
        } else {
            // Si no es una solicitud AJAX, renderizamos la vista
            require '../views/cuentacontable/list.html';
        }
        exit;
    }

    public function createCuenta() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear cuentas contables']);
            exit;
        }
    
        $data = $_POST;
        error_log("Datos recibidos para crear cuenta: " . print_r($data, true)); // Depuración
        $required_fields = ['codigo', 'nombre', 'tipo'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                error_log("Campo requerido faltante: $field"); // Depuración
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => ucfirst($field) . ' es obligatorio']);
                exit;
            }
        }
    
        // Verificar si el código ya existe antes de intentar insertar
        $codigoExists = $this->cuentaContableModel->checkCodigoExists($data['codigo']);
        error_log("Resultado de checkCodigoExists antes de insertar: " . ($codigoExists ? 'true' : 'false')); // Depuración
        if ($codigoExists) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El código ya existe según la verificación previa. Por favor, usa un código diferente']);
            exit;
        }
    
        try {
            $result = $this->cuentaContableModel->createCuenta($data);
            error_log("Resultado de createCuenta: " . ($result ? 'Éxito' : 'Fallo')); // Depuración
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Cuenta contable creada']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al crear la cuenta contable']);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            error_log("Error PDO al crear cuenta: " . $e->getMessage()); // Depuración
            if ($e->getCode() == 23000) {
                // Verificar nuevamente el código para confirmar
                $existingCode = $this->cuentaContableModel->checkCodigoExists($data['codigo']);
                error_log("Código '" . $data['codigo'] . "' encontrado después de fallo: " . ($existingCode ? 'Sí' : 'No')); // Depuración
                // Obtener el código exacto que está causando el conflicto
                $stmt = $this->cuentaContableModel->getPdo()->prepare("SELECT codigo FROM cuentas_contables WHERE TRIM(codigo) = ?");
                $stmt->execute([trim($data['codigo'])]);
                $conflictingCode = $stmt->fetchColumn();
                error_log("Código en conflicto encontrado: " . ($conflictingCode ? $conflictingCode : 'No encontrado')); // Depuración
                echo json_encode(['error' => 'El código ya existe. Código en conflicto: ' . ($conflictingCode ? $conflictingCode : 'desconocido') . '. Por favor, usa un código diferente']);
            } else {
                echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
        }
        exit;
    }

    public function updateCuenta($id) {
        error_log("Iniciando updateCuenta para ID: $id"); // Depuración
        error_log("Sesión actual: " . print_r($_SESSION, true)); // Depuración
    
        if (!isset($_SESSION['user_id'])) {
            error_log("No hay sesión activa. Redirigiendo a login."); // Depuración
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        error_log("Usuario obtenido: " . print_r($usuario, true)); // Depuración
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            error_log("Usuario no autorizado. Rol: " . ($usuario['rol'] ?? 'No definido')); // Depuración
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar cuentas contables']);
            exit;
        }
    
        $data = $_POST;
        error_log("Datos recibidos para actualizar: " . print_r($data, true)); // Depuración
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
            if ($this->cuentaContableModel->updateCuenta($id, $data['nombre'], $data['estado'], $tipo)) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Cuenta contable actualizada']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al actualizar la cuenta contable']);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        exit;
    }

    public function deleteCuenta($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar cuentas contables']);
            exit;
        }

        try {
            if ($this->cuentaContableModel->deleteCuenta($id)) {
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Cuenta contable eliminada']);
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Error al eliminar la cuenta contable']);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
        exit;
    }

    public function updateForm($id) {
        error_log("Iniciando updateForm para ID: $id"); // Depuración
        if (!isset($_SESSION['user_id'])) {
            error_log("No hay sesión activa en updateForm."); // Depuración
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        error_log("Usuario obtenido en updateForm: " . print_r($usuario, true)); // Depuración
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            error_log("Usuario no autorizado en updateForm. Rol: " . ($usuario['rol'] ?? 'No definido')); // Depuración
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar cuentas contables']);
            exit;
        }
    
        $cuenta = $this->cuentaContableModel->getCuentaById($id);
        error_log("Cuenta obtenida para ID $id: " . print_r($cuenta, true)); // Depuración
        if ($cuenta === false) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Cuenta contable no encontrada']);
            exit;
        }
    
        ob_start();
        require '../views/cuentacontable/update_form.html';
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
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear cuentas contables']);
            exit;
        }

        ob_start();
        require '../views/cuentacontable/form.html';
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
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')) {
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
        error_log("Verificando código: '$codigo'"); // Depuración
        try {
            $exists = $this->cuentaContableModel->checkCodigoExists($codigo);
            error_log("Resultado de checkCodigoExists para '$codigo': " . ($exists ? 'true' : 'false')); // Depuración
            echo json_encode(['exists' => $exists]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al verificar el código: ' . $e->getMessage()]);
        }
        exit;
    }
}