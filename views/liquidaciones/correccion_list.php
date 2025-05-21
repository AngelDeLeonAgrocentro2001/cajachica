<?php
// Fetch the current user
$usuarioModel = new Usuario();
$usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
$currentUserId = $_SESSION['user_id'];

// Filter liquidations to only show those created by the current user
$filteredLiquidaciones = [];
if (isset($liquidaciones) && !empty($liquidaciones)) {
    foreach ($liquidaciones as $liquidacion) {
        if ($liquidacion['id_usuario'] == $currentUserId) {
            $filteredLiquidaciones[] = $liquidacion;
        }
    }
}

// If no liquidations are available for the user to see
if (empty($filteredLiquidaciones)) {
    echo '<p>No hay liquidaciones en corrección que puedas ver.</p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corrección de Liquidaciones</title>
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Ensure this path is correct -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-editar {
            background-color: #3498db;
        }
        .btn-editar:hover {
            background-color: #2980b9;
        }
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }
            td:before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
    <script>
        // Define user permissions and role for JavaScript
        const userPermissions = {
            manage_correcciones: <?php echo json_encode($usuarioModel->tienePermiso($usuario, 'manage_correcciones')); ?>
        };
        const userRole = <?php echo json_encode($usuario['rol'] ?? ''); ?>;
    </script>
</head>
<body>
    <div class="container">
        <h1>Corrección de Liquidaciones</h1>
        <table>
            <thead>
                <tr>
                    <th>ID Liquidación</th>
                    <th>Caja Chica</th>
                    <th>Fecha Creación</th>
                    <th>Monto Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filteredLiquidaciones as $liquidacion): ?>
                    <tr>
                        <td data-label="ID Liquidación"><?php echo htmlspecialchars($liquidacion['id']); ?></td>
                        <td data-label="Caja Chica"><?php echo htmlspecialchars($liquidacion['nombre_caja_chica'] ?? 'N/A'); ?></td>
                        <td data-label="Fecha Creación"><?php echo htmlspecialchars($liquidacion['fecha_creacion'] ?? 'N/A'); ?></td>
                        <td data-label="Monto Total"><?php echo htmlspecialchars(number_format($liquidacion['monto_total'] ?? 0, 2)); ?></td>
                        <td data-label="Estado"><?php echo htmlspecialchars($liquidacion['estado'] ?? 'EN_PROCESO'); ?></td>
                        <td data-label="Acciones">
                            <?php if ($usuarioModel->tienePermiso($usuario, 'manage_correcciones')): ?>
                                <a href="index.php?controller=liquidacion&action=updateCorreccion&id=<?php echo $liquidacion['id']; ?>" class="btn btn-editar">Editar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Ensure userPermissions and userRole are available globally
        window.userPermissions = window.userPermissions || {
            manage_correcciones: <?php echo json_encode($usuarioModel->tienePermiso($usuario, 'manage_correcciones')); ?>
        };
        window.userRole = window.userRole || <?php echo json_encode($usuario['rol'] ?? ''); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            console.log('Página de corrección de liquidaciones cargada');
        });
    </script>
</body>
</html>