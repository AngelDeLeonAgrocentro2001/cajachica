<?php
require_once '../models/Auditoria.php';
require_once '../models/Usuario.php';

class AuditoriaController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function list() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en AuditoriaController::list');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $allowedRoles = [Usuario::ROL_ADMIN, Usuario::ROL_CONTABILIDAD];
        if (!in_array($usuario['rol'], $allowedRoles)) {
            error_log('Error: No tienes permiso para consultar auditoría');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para consultar auditoría']);
            exit;
        }

        // Obtener datos para los filtros
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAllUsuarios();
        $selectUsuarios = '<option value="">Todos</option>';
        foreach ($usuarios as $u) {
            $selectUsuarios .= "<option value='{$u['id']}'>{$u['nombre']}</option>";
        }

        $tiposAcciones = [
            'CREADO', 'ACTUALIZADO', 'ELIMINADO', 'AUTORIZADO_POR_SUPERVISOR', 
            'RECHAZADO_POR_SUPERVISOR', 'AUTORIZADO_POR_CONTABILIDAD', 
            'RECHAZADO_POR_CONTABILIDAD', 'DESCARTADO', 'PENDIENTE_CORRECCIÓN', 
            'EXPORTADO', 'REPORTE_GENERADO', 'CREAR_USUARIO', 'ACTUALIZAR_USUARIO', 
            'ELIMINAR_USUARIO', 'CREAR_FACTURA', 'ACTUALIZAR_FACTURA', 'ELIMINAR_FACTURA',
            'AUTORIZAR_FACTURA', 'RECHAZAR_FACTURA', 'PAGAR_FACTURA', 'RECHAZAR_FACTURA_CONTABILIDAD'
        ];
        $selectTiposAcciones = '<option value="">Todos</option>';
        foreach ($tiposAcciones as $accion) {
            $selectTiposAcciones .= "<option value='{$accion}'>{$accion}</option>";
        }

        require '../views/auditoria/list.html';
        exit;
    }

    public function getAuditoria() {
        if (!isset($_SESSION['user_id'])) {
            error_log('Error: No hay session user_id en AuditoriaController::getAuditoria');
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        $allowedRoles = [Usuario::ROL_ADMIN, Usuario::ROL_CONTABILIDAD];
        if (!in_array($usuario['rol'], $allowedRoles)) {
            error_log('Error: No tienes permiso para consultar auditoría');
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para consultar auditoría']);
            exit;
        }

        $filters = [];
        if (isset($_GET['id_usuario']) && $_GET['id_usuario'] !== '') {
            $filters['id_usuario'] = $_GET['id_usuario'];
        }
        if (isset($_GET['tipo_accion']) && $_GET['tipo_accion'] !== '') {
            $filters['tipo_accion'] = $_GET['tipo_accion'];
        }
        if (isset($_GET['fecha_inicio']) && $_GET['fecha_inicio'] !== '') {
            $filters['fecha_inicio'] = $_GET['fecha_inicio'];
        }
        if (isset($_GET['fecha_fin']) && $_GET['fecha_fin'] !== '') {
            $filters['fecha_fin'] = $_GET['fecha_fin'];
        }

        $auditoriaModel = new Auditoria();
        $auditoria = $auditoriaModel->getAuditoria($filters);

        header('Content-Type: application/json');
        echo json_encode($auditoria);
        exit;
    }
}