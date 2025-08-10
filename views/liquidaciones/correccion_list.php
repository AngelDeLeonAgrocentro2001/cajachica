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
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e3a8a;
            --danger-color: #dc2626;
            --success-color: #16a34a;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-color: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-color);
            line-height: 1.5;
            margin: 0;
            padding: 1rem;
        }

        h1 {
            text-align: center;
            color: var(--text-color);
            margin: 2rem 0;
            font-size: 1.875rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
            background-color: var(--card-background);
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 600;
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: white;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .btn-editar {
            background-color: var(--primary-color);
        }

        .btn-editar:hover {
            background-color: var(--secondary-color);
            transform: translateY(-1px);
        }

        p {
            text-align: center;
            color: var(--text-muted);
            font-size: 1rem;
            margin: 1rem 0;
        }

        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }

            .container {
                padding: 1rem;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 0.5rem;
                background-color: var(--card-background);
                box-shadow: var(--shadow-sm);
            }

            td {
                border: none;
                position: relative;
                padding: 0.5rem 0.75rem;
                padding-left: 50%;
                text-align: right;
                font-size: 0.875rem;
                
            }

            td:before {
                content: attr(data-label);
                position: absolute;
                left: 0.75rem;
                width: 45%;
                padding-right: 0.5rem;
                font-weight: 600;
                color: var(--text-color);
                text-align: left;
            }

            h1 {
                font-size: 1.5rem;
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
                    <!-- <th>Estado</th> -->
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
                        <!-- <td data-label="Estado"><?php echo htmlspecialchars($liquidacion['estado'] ?? 'EN_PROCESO'); ?></td> -->
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