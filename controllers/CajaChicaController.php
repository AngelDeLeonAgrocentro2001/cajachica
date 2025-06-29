<?php
require_once '../models/CajaChica.php';
require_once '../models/Usuario.php';
require_once '../models/CentroCosto.php';

class CajaChicaController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
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
                $nombre = $_POST['nombre'] ?? '';
                $monto_asignado = $_POST['monto_asignado'] ?? 0;
                $id_usuario_encargado = $_POST['id_usuario_encargado'] ?? '';
                $id_supervisor = $_POST['id_supervisor'] ?? '';
                $id_contador = $_POST['id_contador'] ?? '';
                $id_centro_costo = $_POST['id_centro_costo'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVA';
    
                if (empty($nombre) || !is_numeric($monto_asignado) || empty($id_usuario_encargado) || empty($id_supervisor) || empty($id_contador) || empty($id_centro_costo)) {
                    throw new Exception('Todos los campos son obligatorios');
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
                if ($cajaChica->createCajaChica($nombre, $monto_asignado, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado)) {
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
    
        $encargados = $usuarioModel->getUsuariosByRol('ENCARGADO_CAJA_CHICA');
        $supervisores = $usuarioModel->getUsuariosBySupervisorRole();
        $contadores = $usuarioModel->getUsuariosByContadorRole();
        $centroCostoModel = new CentroCosto();
        $centrosCostos = $centroCostoModel->getAllCentrosCostos();
    
        $selectEncargados = '';
        foreach ($encargados as $encargado) {
            $selectEncargados .= "<option value='{$encargado['id']}'>{$encargado['nombre']}</option>";
        }
    
        $selectSupervisores = '';
        foreach ($supervisores as $supervisor) {
            $selectSupervisores .= "<option value='{$supervisor['id']}'>{$supervisor['nombre']}</option>";
        }
    
        $selectContadores = '';
        foreach ($contadores as $contador) {
            $selectContadores .= "<option value='{$contador['id']}'>{$contador['nombre']}</option>";
        }
    
        $selectCentrosCostos = '';
        foreach ($centrosCostos as $centro) {
            $selectCentrosCostos .= "<option value='{$centro['id']}'>{$centro['nombre']}</option>";
        }
    
        ob_start();
        require '../views/cajas_chicas/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_encargados}}', $selectEncargados, $html);
        $html = str_replace('{{select_supervisores}}', $selectSupervisores, $html);
        $html = str_replace('{{select_contadores}}', $selectContadores, $html);
        $html = str_replace('{{select_centros_costos}}', $selectCentrosCostos, $html);
        echo $html;
        exit;
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
                $nombre = $_POST['nombre'] ?? '';
                $monto_asignado = $_POST['monto_asignado'] ?? 0;
                $monto_disponible = $_POST['monto_disponible'] ?? 0;
                $id_usuario_encargado = $_POST['id_usuario_encargado'] ?? '';
                $id_supervisor = $_POST['id_supervisor'] ?? '';
                $id_contador = $_POST['id_contador'] ?? '';
                $id_centro_costo = $_POST['id_centro_costo'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVA';
    
                if (empty($nombre) || !is_numeric($monto_asignado) || !is_numeric($monto_disponible) || empty($id_usuario_encargado) || empty($id_supervisor) || empty($id_contador) || empty($id_centro_costo)) {
                    throw new Exception('Todos los campos son obligatorios');
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
                if ($cajaChica->updateCajaChica($id, $nombre, $monto_asignado, $monto_disponible, $id_usuario_encargado, $id_supervisor, $id_contador, $id_centro_costo, $estado)) {
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
    
        $encargados = $usuarioModel->getUsuariosByRol('ENCARGADO_CAJA_CHICA');
        $supervisores = $usuarioModel->getUsuariosBySupervisorRole();
        $contadores = $usuarioModel->getUsuariosByContadorRole();
        $centroCostoModel = new CentroCosto();
        $centrosCostos = $centroCostoModel->getAllCentrosCostos();
    
        $selectEncargados = '';
        foreach ($encargados as $encargado) {
            $selected = $data['id_usuario_encargado'] == $encargado['id'] ? 'selected' : '';
            $selectEncargados .= "<option value='{$encargado['id']}' {$selected}>{$encargado['nombre']}</option>";
        }
    
        $selectSupervisores = '';
        foreach ($supervisores as $supervisor) {
            $selected = $data['id_supervisor'] == $supervisor['id'] ? 'selected' : '';
            $selectSupervisores .= "<option value='{$supervisor['id']}' {$selected}>{$supervisor['nombre']}</option>";
        }
    
        $selectContadores = '';
        foreach ($contadores as $contador) {
            $selected = $data['id_contador'] == $contador['id'] ? 'selected' : '';
            $selectContadores .= "<option value='{$contador['id']}' {$selected}>{$contador['nombre']}</option>";
        }
    
        $selectCentrosCostos = '';
        foreach ($centrosCostos as $centro) {
            $selected = $data['id_centro_costo'] == $centro['id'] ? 'selected' : '';
            $selectCentrosCostos .= "<option value='{$centro['id']}' {$selected}>{$centro['nombre']}</option>";
        }
    
        ob_start();
        require '../views/cajas_chicas/form.html';
        $html = ob_get_clean();
        $html = str_replace('{{select_encargados}}', $selectEncargados, $html);
        $html = str_replace('{{select_supervisores}}', $selectSupervisores, $html);
        $html = str_replace('{{select_contadores}}', $selectContadores, $html);
        $html = str_replace('{{select_centros_costos}}', $selectCentrosCostos, $html);
        echo $html;
        exit;
    }

    public function deleteCajaChica($id) {
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
            echo json_encode(['error' => 'No tienes permiso para eliminar cajas chicas']);
            exit;
        }

        $cajaChica = new CajaChica();
        if ($cajaChica->deleteCajaChica($id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Caja chica eliminada']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar caja chica']);
        }
        exit;
    }
}