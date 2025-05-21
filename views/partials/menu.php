<?php
// Asegurarse de que la sesión esté iniciada
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=login&action=login');
    exit;
}

require_once '../models/Usuario.php';
$usuarioModel = new Usuario();
$usuario = $usuarioModel->getUsuarioById($_SESSION['user_id']);
error_log("Usuario cargado para el menú: " . print_r($usuario, true));
?>

<div class="menu-container">
    <!-- Botón de hamburguesa para mostrar/ocultar el menú -->
    <button class="menu-toggle" aria-label="Toggle Menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>

    <nav class="main-menu">
        <ul>
            <li><a href="index.php?controller=dashboard&action=index">Dashboard</a></li>

            <!-- Gestión -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'create_liquidaciones') || 
                      $usuarioModel->tienePermiso($usuario, 'create_detalles') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_cajachica') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_impuestos') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_centros_costos') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_facturas')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">Gestión</span>
                    <ul class="menu-subgroup">
                        <?php if ($usuarioModel->tienePermiso($usuario, 'create_liquidaciones')): ?>
                            <li><a href="index.php?controller=liquidacion&action=list">Liquidaciones</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'create_detalles')): ?>
                            <li><a href="index.php?controller=detalleliquidacion&action=list">Detalles de Liquidaciones</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_cajachica')): ?>
                            <li><a href="index.php?controller=cajachica&action=list">Cajas Chicas</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_impuestos')): ?>
                            <li><a href="index.php?controller=impuesto&action=list">Impuestos</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')): ?>
                            <li><a href="index.php?controller=cuentacontable&action=list">Cuentas Contables</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos')): ?>
                            <li><a href="index.php?controller=tipogasto&action=list">Tipos de Gastos</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_centros_costos')): ?>
                            <li><a href="index.php?controller=centrocosto&action=list">Centros de Costos</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_facturas')): ?>
                            <li><a href="index.php?controller=factura&action=list">Facturas</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Autorización -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') || 
                      $usuarioModel->tienePermiso($usuario, 'autorizar_facturas')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">Autorización</span>
                    <ul class="menu-subgroup">
                        <?php if ($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')): ?>
                            <li><a href="index.php?controller=liquidacion&action=list&mode=autorizar">Liquidaciones</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'autorizar_facturas')): ?>
                            <li><a href="index.php?controller=factura&action=list&mode=autorizar">Facturas</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Revisión -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones') || 
                      $usuarioModel->tienePermiso($usuario, 'revisar_detalles_liquidaciones') || 
                      $usuarioModel->tienePermiso($usuario, 'revisar_facturas')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">Revisión</span>
                    <ul class="menu-subgroup">
                        <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')): ?>
                            <li><a href="index.php?controller=liquidacion&action=list&mode=revisar">Liquidaciones</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_detalles_liquidaciones')): ?>
                            <li><a href="index.php?controller=detalleliquidacion&action=revisar">Detalles de Liquidaciones</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_facturas')): ?>
                            <li><a href="index.php?controller=factura&action=list&mode=revisar">Facturas</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Correcciones -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'manage_correcciones')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">Correcciones</span>
                    <ul class="menu-subgroup">
                        <li><a href="index.php?controller=liquidacion&action=listCorrecciones">Liquidaciones</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Reportes y Auditoría -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'manage_reportes') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_auditoria')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">Reportes y Auditoría</span>
                    <ul class="menu-subgroup">
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_reportes')): ?>
                            <li><a href="index.php?controller=reportes&action=list">Reportes</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_auditoria')): ?>
                            <li><a href="index.php?controller=auditoria&action=list">Auditoría</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Administración -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'manage_roles') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_usuarios') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_accesos')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">Administración</span>
                    <ul class="menu-subgroup">
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_roles')): ?>
                            <li><a href="index.php?controller=rol&action=list">Roles</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_usuarios')): ?>
                            <li><a href="index.php?controller=usuario&action=list">Usuarios</a></li>
                        <?php endif; ?>
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_accesos')): ?>
                            <li><a href="index.php?controller=acceso&action=list">Accesos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Sesión -->
            <li><a href="index.php?controller=login&action=logout">Cerrar Sesión</a></li>
        </ul>
    </nav>
</div>

<style>
/* Estilos para el contenedor del menú */
.menu-container {
    position: relative;
    width: 100%;
}

/* Estilo del botón de hamburguesa */
.menu-toggle {
    display: block;
    background: rgba(255, 255, 255, 0.31);
    border: none;
    cursor: pointer;
    padding: 10px;
    position: fixed;
    top: 10px;
    left: 10px;
    z-index: 1001;
    width: auto;
}

.menu-toggle .bar {
    display: block;
    width: 25px;
    height: 3px;
    background-color: #333;
    margin: 5px 0;
    transition: all 0.3s ease;
}

/* Estilo del menú principal */
.main-menu {
    background-color: #2c3e50;
    padding: 0;
    margin: 0;
    border-right: 2px solid #1a252f;
    transition: transform 0.3s ease-in-out;
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    overflow-y: auto;
    transform: translateX(-100%);
    z-index: 1000;
}

.main-menu.active {
    transform: translateX(0);
}

/* Estilo de la lista del menú */
.main-menu ul {
    list-style: none;
    margin: 0;
    padding: 20px 0;
    display: flex;
    flex-direction: column;
}

/* Estilo de los grupos de menú */
.menu-group {
    margin-bottom: 15px;
}

.menu-group-title {
    display: block;
    color: #bdc3c7;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    background-color: #34495e;
}

/* Estilo de los subgrupos */
.menu-subgroup {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-subgroup li {
    margin: 2px 0;
}

/* Estilo de los enlaces */
.main-menu a {
    display: block;
    text-decoration: none;
    color: #ecf0f1;
    font-weight: 500;
    padding: 12px 20px 12px 40px; /* Más padding para submenús */
    transition: background-color 0.3s ease, color 0.3s ease;
}

.main-menu a:hover {
    background-color: #3498db;
    color: #fff;
}

/* Animación de las barras al abrir/cerrar */
.menu-toggle.active .bar:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.menu-toggle.active .bar:nth-child(2) {
    opacity: 0;
}

.menu-toggle.active .bar:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -7px);
}

/* Asegurar que el contenido no se solape con el menú */
body {
    margin-left: 0;
    transition: margin-left 0.3s ease-in-out;
}

body.menu-open {
    margin-left: 250px;
}

/* Estilo responsivo */
@media (max-width: 768px) {
    body.menu-open {
        margin-left: 0;
    }

    .main-menu {
        width: 200px;
    }

    .menu-group-title {
        font-size: 12px;
    }

    .main-menu a {
        padding: 10px 20px 10px 30px;
    }
}
</style>

<script>
// JavaScript para manejar el comportamiento del botón de hamburguesa
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainMenu = document.querySelector('.main-menu');
    const body = document.body;

    menuToggle.addEventListener('click', () => {
        mainMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
        body.classList.toggle('menu-open');
    });

    // Cerrar el menú al hacer clic en un enlace
    const menuLinks = document.querySelectorAll('.main-menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            mainMenu.classList.remove('active');
            menuToggle.classList.remove('active');
            body.classList.remove('menu-open');
        });
    });
});
</script>