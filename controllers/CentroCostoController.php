<?php
require_once '../models/CentroCosto.php';
require_once '../models/Usuario.php';

class CentroCostoController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listCentrosCostos() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $centroCosto = new CentroCosto();
        $estadoFiltro = $_GET['estado'] ?? 'ACTIVO'; // Filtro por estado (por defecto 'ACTIVO')
        $centrosCostos = $centroCosto->getAllCentrosCostos($estadoFiltro);
    
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
        if (!$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) { // Cambiado de manage_cuentas_contables a manage_centros_costos
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para crear centros de costos']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';
    
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
    
                $centroCosto = new CentroCosto();
                if ($centroCosto->createCentroCosto($nombre, $descripcion, $estado)) {
                    header('Content-Type: application/json');
                    http_response_code(201);
                    echo json_encode(['message' => 'Centro de costos creado']);
                } else {
                    throw new Exception('Error al crear centro de costos');
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        ob_start();
        require '../views/centros_costos/form.html';
        $html = ob_get_clean();
        echo $html;
        exit;
    }

    public function updateCentroCosto($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
        if (!$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para actualizar centros de costos']);
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $estado = $_POST['estado'] ?? 'ACTIVO';
    
                if (empty($nombre)) {
                    throw new Exception('El nombre es obligatorio');
                }
    
                $centroCosto = new CentroCosto();
                if ($centroCosto->updateCentroCosto($id, $nombre, $descripcion, $estado)) {
                    header('Content-Type: application/json');
                    echo json_encode(['message' => 'Centro de costos actualizado']);
                } else {
                    throw new Exception('Error al actualizar centro de costos');
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    
        $centroCosto = new CentroCosto();
        $data = $centroCosto->getCentroCostoById($id);
        if (!$data) {
            echo "<h2>Error: Centro de costos no encontrado</h2>";
            echo "<p>No se pudo cargar el centro de costos con ID " . htmlspecialchars($id) . ".</p>";
            echo '<a href="index.php?controller=centrocosto&action=list">Volver a Lista</a>';
            exit;
        }
    
        // Pasar los datos al formulario
        ob_start();
        ?>
        <h3 id="formTitle">Editar Centro de Costos</h3>
        <form id="centroCostoFormInner">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($data['nombre']); ?>" required>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($data['descripcion'] ?? ''); ?></textarea>
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" required>
                <option value="ACTIVO" <?php echo $data['estado'] === 'ACTIVO' ? 'selected' : ''; ?>>Activo</option>
                <option value="INACTIVO" <?php echo $data['estado'] === 'INACTIVO' ? 'selected' : ''; ?>>Inactivo</option>
            </select>
            <div class="buttons">
                <button type="submit">Guardar</button>
                <button type="button" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
        <?php
        $html = ob_get_clean();
        echo $html;
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
        if (!$usuarioModel->tienePermiso($usuario, 'manage_centros_costos')) { // Cambiado de manage_cuentas_contables a manage_centros_costos
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permiso para eliminar centros de costos']);
            exit;
        }
    
        $centroCosto = new CentroCosto();
        if ($centroCosto->deleteCentroCosto($id)) {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Centro de costos eliminado']);
        } else {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Error al eliminar centro de costos']);
        }
        exit;
    }
}