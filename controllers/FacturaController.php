<?php
require_once '../models/Factura.php';
require_once '../models/Usuario.php';
require_once '../models/Auditoria.php';

class FacturaController {
    private $facturaModel;
    private $auditoriaModel;

    public function __construct() {
        $this->facturaModel = new Factura();
        $this->auditoriaModel = new Auditoria();
    }

    public function listFacturas() {
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

        $mode = isset($_GET['mode']) ? $_GET['mode'] : '';
        $requiredPermission = 'manage_facturas';
        if ($mode === 'autorizar') {
            $requiredPermission = 'autorizar_facturas';
        } elseif ($mode === 'revisar') {
            $requiredPermission = 'revisar_facturas';
        }

        if (!$usuarioModel->tienePermiso($usuario, $requiredPermission)) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para realizar esta acción']);
            exit;
        }

        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
        $cuentaId = isset($_GET['cuenta_id']) ? intval($_GET['cuenta_id']) : null;

        try {
            $facturas = $this->facturaModel->getAllFacturas($searchTerm, $cuentaId);
            if ($mode === 'autorizar') {
                $facturas = array_filter($facturas, function($factura) {
                    return $factura['estado'] === 'PENDIENTE';
                });
            } elseif ($mode === 'revisar') {
                $facturas = array_filter($facturas, function($factura) {
                    return $factura['estado'] === 'APROBADO';
                });
            }
            $facturas = array_values($facturas); // Reindexar el array después de filtrar
            error_log('Facturas obtenidas: ' . print_r($facturas, true));
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
            exit;
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($facturas);
        } else {
            require '../views/factura/list.html';
        }
        exit;
    }

    public function showForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
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

        ob_start();
        require '../views/factura/form.html';
        $html = ob_get_clean();
        echo $html;
        exit;
    }

    public function createFactura() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $data = [
            'cuenta_id' => isset($_POST['cuenta_id']) ? intval($_POST['cuenta_id']) : null,
            'base_id' => isset($_POST['base_id']) ? intval($_POST['base_id']) : null,
            'numero_factura' => isset($_POST['numero_factura']) ? trim($_POST['numero_factura']) : '',
            'fecha' => isset($_POST['fecha']) ? $_POST['fecha'] : '',
            'proveedor' => isset($_POST['proveedor']) ? trim($_POST['proveedor']) : '',
            'monto' => isset($_POST['monto']) ? floatval($_POST['monto']) : 0.0,
            'estado' => 'PENDIENTE' // Siempre se crea como PENDIENTE
        ];

        if (!$data['cuenta_id'] || !$data['base_id'] || !$data['numero_factura'] || !$data['fecha'] || !$data['proveedor'] || $data['monto'] <= 0) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Campos obligatorios faltantes o inválidos']);
            exit;
        }

        if ($this->facturaModel->numeroFacturaExists($data['numero_factura'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El número de factura ya está registrado']);
            exit;
        }

        $result = $this->facturaModel->createFactura($data);
        if ($result) {
            // Obtener el ID de la factura recién creada
            $facturaId = $this->facturaModel->getLastInsertId();
            // Registrar en auditoría
            $detalles = json_encode($data);
            $this->auditoriaModel->createAuditoria(null, $facturaId, $_SESSION['user_id'], 'CREAR_FACTURA', $detalles);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Factura creada exitosamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear la factura']);
        }
        exit;
    }

    public function checkNumeroFactura() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $numeroFactura = isset($_GET['numero_factura']) ? trim($_GET['numero_factura']) : '';
        $excludeId = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;

        if (!$numeroFactura) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Número de factura requerido']);
            exit;
        }

        $exists = $excludeId ? $this->facturaModel->numeroFacturaExistsForOther($numeroFactura, $excludeId) : $this->facturaModel->numeroFacturaExists($numeroFactura);
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
        exit;
    }

    public function deleteFactura() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'manage_facturas')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar facturas']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'ID de factura requerido']);
            exit;
        }

        $factura = $this->facturaModel->getFacturaById($id);
        if (!$factura) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Factura no encontrada']);
            exit;
        }

        // Registrar en auditoría antes de eliminar
        $detalles = json_encode($factura);
        $this->auditoriaModel->createAuditoria(null, $id, $_SESSION['user_id'], 'ELIMINAR_FACTURA', $detalles);

        $result = $this->facturaModel->deleteFactura($id);
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Factura eliminada exitosamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar la factura']);
        }
        exit;
    }

    public function getFactura() {
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

        $mode = isset($_GET['mode']) ? $_GET['mode'] : '';
        $requiredPermission = 'manage_facturas';
        if ($mode === 'autorizar') {
            $requiredPermission = 'autorizar_facturas';
        } elseif ($mode === 'revisar') {
            $requiredPermission = 'revisar_facturas';
        }

        if (!$usuarioModel->tienePermiso($usuario, $requiredPermission)) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para realizar esta acción']);
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'ID de factura requerido']);
            exit;
        }

        $factura = $this->facturaModel->getFacturaById($id);
        if (!$factura) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Factura no encontrada']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($factura);
        exit;
    }

    public function updateFactura() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if (!$id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'ID de factura requerido']);
            exit;
        }

        $factura = $this->facturaModel->getFacturaById($id);
        if (!$factura) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Factura no encontrada']);
            exit;
        }

        // Solo permitir edición si la factura está en estado PENDIENTE
        if ($factura['estado'] !== 'PENDIENTE') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No se puede editar una factura que no está en estado PENDIENTE']);
            exit;
        }

        $data = [
            'id' => $id,
            'cuenta_id' => isset($_POST['cuenta_id']) ? intval($_POST['cuenta_id']) : null,
            'base_id' => isset($_POST['base_id']) ? intval($_POST['base_id']) : null,
            'numero_factura' => isset($_POST['numero_factura']) ? trim($_POST['numero_factura']) : '',
            'fecha' => isset($_POST['fecha']) ? $_POST['fecha'] : '',
            'proveedor' => isset($_POST['proveedor']) ? trim($_POST['proveedor']) : '',
            'monto' => isset($_POST['monto']) ? floatval($_POST['monto']) : 0.0,
            'estado' => 'PENDIENTE' // Mantener como PENDIENTE al editar
        ];

        if (!$data['cuenta_id'] || !$data['base_id'] || !$data['numero_factura'] || !$data['fecha'] || !$data['proveedor'] || $data['monto'] <= 0) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Campos obligatorios faltantes o inválidos']);
            exit;
        }

        if ($this->facturaModel->numeroFacturaExistsForOther($data['numero_factura'], $id)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'El número de factura ya está registrado']);
            exit;
        }

        // Registrar en auditoría los cambios
        $detalles = json_encode([
            'antes' => $factura,
            'despues' => $data
        ]);
        $this->auditoriaModel->createAuditoria(null, $id, $_SESSION['user_id'], 'ACTUALIZAR_FACTURA', $detalles);

        $result = $this->facturaModel->updateFactura($data);
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Factura actualizada exitosamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar la factura']);
        }
        exit;
    }

    public function autorizarFactura($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'autorizar_facturas')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para autorizar facturas']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $factura = $this->facturaModel->getFacturaById($id);
        if (!$factura) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Factura no encontrada']);
            exit;
        }

        if ($factura['estado'] !== 'PENDIENTE') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden autorizar facturas en estado PENDIENTE']);
            exit;
        }

        $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
        $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

        if (!in_array($accion, ['APROBADO', 'RECHAZADO'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Acción inválida']);
            exit;
        }

        $nuevoEstado = $accion === 'APROBADO' ? 'APROBADO' : 'RECHAZADO';
        $result = $this->facturaModel->updateEstadoFactura($id, $nuevoEstado);
        if ($result) {
            // Registrar en auditoría
            $detalles = json_encode([
                'accion' => $accion,
                'comentario' => $comentario,
                'estado_anterior' => $factura['estado'],
                'estado_nuevo' => $nuevoEstado
            ]);
            $tipoAccion = $accion === 'APROBADO' ? 'AUTORIZAR_FACTURA' : 'RECHAZAR_FACTURA';
            $this->auditoriaModel->createAuditoria(null, $id, $_SESSION['user_id'], $tipoAccion, $detalles);

            header('Content-Type: application/json');
            echo json_encode(['message' => 'Factura ' . ($accion === 'APROBADO' ? 'aprobada' : 'rechazada') . ' exitosamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar el estado de la factura']);
        }
        exit;
    }

    public function revisarFactura($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if ($usuario === false || !isset($usuario['rol']) || !$usuarioModel->tienePermiso($usuario, 'revisar_facturas')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para revisar facturas']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $factura = $this->facturaModel->getFacturaById($id);
        if (!$factura) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Factura no encontrada']);
            exit;
        }

        if ($factura['estado'] !== 'APROBADO') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Solo se pueden revisar facturas en estado APROBADO']);
            exit;
        }

        $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
        $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

        if (!in_array($accion, ['PAGADA', 'RECHAZADO'])) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Acción inválida']);
            exit;
        }

        $nuevoEstado = $accion === 'PAGADA' ? 'PAGADA' : 'RECHAZADO';
        $result = $this->facturaModel->updateEstadoFactura($id, $nuevoEstado);
        if ($result) {
            // Registrar en auditoría
            $detalles = json_encode([
                'accion' => $accion,
                'comentario' => $comentario,
                'estado_anterior' => $factura['estado'],
                'estado_nuevo' => $nuevoEstado
            ]);
            $tipoAccion = $accion === 'PAGADA' ? 'PAGAR_FACTURA' : 'RECHAZAR_FACTURA_CONTABILIDAD';
            $this->auditoriaModel->createAuditoria(null, $id, $_SESSION['user_id'], $tipoAccion, $detalles);

            header('Content-Type: application/json');
            echo json_encode(['message' => 'Factura ' . ($accion === 'PAGADA' ? 'pagada' : 'rechazada') . ' exitosamente']);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar el estado de la factura']);
        }
        exit;
    }
}