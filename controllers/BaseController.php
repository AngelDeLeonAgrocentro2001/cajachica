<?php
require_once '../models/Base.php';
require_once '../models/Usuario.php';

class BaseController {
    private $baseModel;

    public function __construct() {
        $this->baseModel = new Base();
    }

    public function listBases() {
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
    
        // Permitir acceso a usuarios con manage_cuentas_contables o manage_facturas
        if (!$usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables') && !$usuarioModel->tienePermiso($usuario, 'manage_facturas')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para listar bases']);
            exit;
        }
    
        $bases = $this->baseModel->getAllBases();
        header('Content-Type: application/json');
        echo json_encode($bases);
        exit;
    }
}