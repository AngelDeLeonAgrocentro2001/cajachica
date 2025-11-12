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
  <style>
    :root {
      --primary-color: #2d6a4f;
      --primary-dark: #1b4332;
      --primary-light: #95d5b2;
      --secondary-color: #40916c;
      --danger-color: #c0392b;
      --success-color: #16a34a;
      --background-color: #f5f7fa;
      --card-background: #ffffff;
      --text-color: #1e293b;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --shadow-sm: 0 2px 8px rgba(45, 106, 79, 0.08);
      --shadow-md: 0 4px 16px rgba(45, 106, 79, 0.12);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: linear-gradient(135deg, #f5f7fa, #e8f5e9);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text-color);
      line-height: 1.6;
      padding: 1rem;
    }

    h1 {
      text-align: center;
      color: var(--primary-dark);
      margin: 2rem 0;
      font-size: 2rem;
      font-weight: 700;
      position: relative;
      padding-bottom: 0.5rem;
    }

    h1::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
      border-radius: 2px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 1.5rem;
      background: var(--card-background);
      border-radius: 1rem;
      box-shadow: var(--shadow-md);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
      overflow-x: auto;
    }

    th, td {
      padding: 0.875rem 1rem;
      text-align: left;
      font-size: 0.9rem;
      border-bottom: 1px solid var(--border-color);
    }

    th {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: white;
      font-weight: 600;
    }

    tr {
      transition: background-color 0.2s ease;
    }

    tr:hover {
      background-color: rgba(45, 106, 79, 0.05);
    }

    .btn {
      display: inline-block;
      padding: 0.5rem 1rem;
      text-decoration: none;
      color: white;
      border-radius: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
    }

    .btn-editar {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }

    .btn-editar:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    p {
      text-align: center;
      color: var(--text-muted);
      font-size: 1rem;
      margin: 1rem 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body { padding: 0.5rem; }

      .container { padding: 1rem; }

      table, thead, tbody, th, td, tr { display: block; }

      thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
      }

      tr {
        margin-bottom: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        background: var(--card-background);
        box-shadow: var(--shadow-sm);
        padding: 0.5rem 0;
      }

      td {
        border: none;
        position: relative;
        padding: 0.75rem 1rem;
        padding-left: 50%;
        text-align: right;
        font-size: 0.875rem;
      }

      td:before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        width: 45%;
        font-weight: 600;
        color: var(--primary-color);
        text-align: left;
      }

      h1 { font-size: 1.5rem; }
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
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($filteredLiquidaciones as $liquidacion): ?>
          <tr>
            <td data-label="ID Liquidación"><?= htmlspecialchars($liquidacion['id']); ?></td>
            <td data-label="Caja Chica"><?= htmlspecialchars($liquidacion['nombre_caja_chica'] ?? 'N/A'); ?></td>
            <td data-label="Fecha Creación"><?= htmlspecialchars($liquidacion['fecha_creacion'] ?? 'N/A'); ?></td>
            <td data-label="Monto Total"><?= htmlspecialchars(number_format($liquidacion['monto_total'] ?? 0, 2)); ?></td>
            <td data-label="Acciones">
              <?php if ($usuarioModel->tienePermiso($usuario, 'manage_correcciones')): ?>
                <a href="index.php?controller=liquidacion&action=updateCorreccion&id=<?= $liquidacion['id']; ?>" class="btn btn-editar">Editar</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script>
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
