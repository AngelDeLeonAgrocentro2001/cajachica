<?php
// Asegurarse de que la sesión esté iniciada
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=login&action=login');
    exit;
}

require_once '../models/Usuario.php';
$usuarioModel = new Usuario();
$usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
?>

<nav class="main-menu">
    <ul>
        <li><a href="index.php?controller=dashboard&action=index">Dashboard</a></li>
        <?php if ($usuarioModel->tienePermiso($usuario, 'create_liquidaciones')): ?>
            <li><a href="index.php?controller=liquidacion&action=list">Gestión de Liquidaciones</a></li>
            <li><a href="index.php?controller=detalleliquidacion&action=list">Gestión de Detalles</a></li>
            <li><a href="index.php?controller=cajachica&action=list">Gestión de Cajas Chicas</a></li>
        <?php endif; ?>
        <?php if ($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')): ?>
            <li><a href="index.php?controller=liquidacion&action=list&mode=autorizar">Autorizar Liquidaciones</a></li>
        <?php endif; ?>
        <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')): ?>
            <li><a href="index.php?controller=liquidacion&action=list&mode=revisar">Revisar Liquidaciones</a></li>
        <?php endif; ?>
        <?php
        $allowedRolesReportes = [Usuario::ROL_ADMIN, Usuario::ROL_CONTABILIDAD, Usuario::ROL_SUPERVISOR];
        if (in_array($usuario['rol'], $allowedRolesReportes)): ?>
            <li><a href="index.php?controller=reportes&action=list">Generar Reportes</a></li>
        <?php endif; ?>
        <?php
        $allowedRolesAuditoria = [Usuario::ROL_ADMIN, Usuario::ROL_CONTABILIDAD];
        if (in_array($usuario['rol'], $allowedRolesAuditoria)): ?>
            <li><a href="index.php?controller=auditoria&action=list">Consultar Auditoría</a></li>
        <?php endif; ?>
        <?php if ($usuario['rol'] === Usuario::ROL_ADMIN): ?>
            <li><a href="index.php?controller=usuario&action=list">Gestión de Usuarios</a></li>
        <?php endif; ?>
        <li><a href="index.php?controller=login&action=logout">Cerrar Sesión</a></li>
    </ul>
</nav>

<style>
    .main-menu {
        background-color: #f8f9fa;
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    .main-menu ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
    }
    .main-menu li {
        margin: 0 15px;
    }
    .main-menu a {
        text-decoration: none;
        color: #007BFF;
        font-weight: bold;
    }
    .main-menu a:hover {
        text-decoration: underline;
    }
</style>