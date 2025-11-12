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
        <!-- Header del menú -->
        <div class="menu-header">
            <div class="menu-logo">
                <div class="logo-icon">AC</div>
                <div class="logo-text">
                    <strong>AgroCaja</strong>
                    <span>Chica</span>
                </div>
            </div>
        </div>

        <ul>
            <li class="menu-item-single">
                <a href="index.php?controller=dashboard&action=index">
                    <span class="menu-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Gestión -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'create_liquidaciones') || 
                      $usuarioModel->tienePermiso($usuario, 'create_detalles') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_cajachica') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_impuestos') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_centros_costos') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_facturas') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_dte')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">
                        <span class="menu-icon">⚙️</span>
                        <span>Gestión</span>
                    </span>
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
                        <?php if ($usuarioModel->tienePermiso($usuario, 'manage_dte')): ?>
                            <li><a href="index.php?controller=dte&action=index">Carga de DTE</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Autorización -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones') || 
                      $usuarioModel->tienePermiso($usuario, 'autorizar_facturas')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">
                        <span class="menu-icon">✅</span>
                        <span>Autorización</span>
                    </span>
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
                    <span class="menu-group-title">
                        <span class="menu-icon">🔍</span>
                        <span>Revisión</span>
                    </span>
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
                    <span class="menu-group-title">
                        <span class="menu-icon">✏️</span>
                        <span>Correcciones</span>
                    </span>
                    <ul class="menu-subgroup">
                        <li><a href="index.php?controller=liquidacion&action=listCorrecciones">Liquidaciones</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Reportes y Auditoría -->
            <?php if ($usuarioModel->tienePermiso($usuario, 'manage_reportes') || 
                      $usuarioModel->tienePermiso($usuario, 'manage_auditoria')): ?>
                <li class="menu-group">
                    <span class="menu-group-title">
                        <span class="menu-icon">📈</span>
                        <span>Reportes y Auditoría</span>
                    </span>
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
                    <span class="menu-group-title">
                        <span class="menu-icon">👥</span>
                        <span>Administración</span>
                    </span>
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
            <li class="menu-item-single logout">
                <a href="index.php?controller=login&action=logout">
                    <span class="menu-icon">🚪</span>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>

        <!-- Footer del menú -->
        <div class="menu-footer">
            <p>© 2025 AgroCaja Chica</p>
        </div>
    </nav>

    <!-- Overlay para cerrar el menú en móviles -->
    <div class="menu-overlay"></div>
</div>

<style>
/* Reset y variables */
* {
    box-sizing: border-box;
}

/* Contenedor del menú */
.menu-container {
    position: relative;
    width: 100%;
}

/* Botón de hamburguesa */
.menu-toggle {
    display: block;
    width: auto;
    background: linear-gradient(135deg, #2d6a4f 0%, #1a4d3e 100%);
    border: none;
    cursor: pointer;
    padding: 12px;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1001;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(45, 106, 79, 0.3);
    transition: all 0.3s ease;
}

.menu-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(45, 106, 79, 0.4);
}

.menu-toggle .bar {
    display: block;
    width: 25px;
    height: 3px;
    background-color: white;
    margin: 5px 0;
    transition: all 0.3s ease;
    border-radius: 2px;
}

/* Menú principal */
.main-menu {
    background: linear-gradient(180deg, #1a4d3e 0%, #2d6a4f 100%);
    padding: 0;
    margin: 0;
    transition: transform 0.3s ease-in-out;
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    overflow-y: auto;
    transform: translateX(-100%);
    z-index: 1000;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
}

.main-menu::-webkit-scrollbar {
    width: 6px;
}

.main-menu::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.main-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.main-menu.active {
    transform: translateX(0);
}

/* Header del menú */
.menu-header {
    background: rgba(0, 0, 0, 0.2);
    padding: 25px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.menu-logo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-icon {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2d6a4f;
    font-weight: bold;
    font-size: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.logo-text {
    display: flex;
    flex-direction: column;
    color: white;
    line-height: 1.2;
}

.logo-text strong {
    font-size: 18px;
    font-weight: 700;
}

.logo-text span {
    font-size: 13px;
    opacity: 0.8;
}

/* Lista del menú */
.main-menu > ul {
    list-style: none;
    margin: 0;
    padding: 15px 0;
    display: flex;
    flex-direction: column;
    flex: 1;
}

/* Items individuales del menú */
.menu-item-single {
    margin: 5px 10px;
}

.menu-item-single > a {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: white;
    font-weight: 500;
    padding: 14px 18px;
    transition: all 0.3s ease;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.05);
}

.menu-item-single > a:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(5px);
}

/* Grupos de menú */
.menu-group {
    margin: 10px 10px 15px 10px;
}

.menu-group-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #a8d5ba;
    font-size: 13px;
    font-weight: 700;
    padding: 12px 18px;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    cursor: default;
}

/* Iconos del menú */
.menu-icon {
    font-size: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
}

/* Subgrupos */
.menu-subgroup {
    list-style: none;
    padding: 8px 0 0 0;
    margin: 0;
}

.menu-subgroup li {
    margin: 3px 0;
}

.menu-subgroup a {
    display: block;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.85);
    font-weight: 400;
    padding: 10px 18px 10px 52px;
    transition: all 0.3s ease;
    border-radius: 8px;
    font-size: 14px;
    position: relative;
}

.menu-subgroup a::before {
    content: '•';
    position: absolute;
    left: 38px;
    color: rgba(255, 255, 255, 0.4);
}

.menu-subgroup a:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding-left: 56px;
}

/* Item de cerrar sesión */
.menu-item-single.logout {
    margin-top: auto;
}

.menu-item-single.logout > a {
    background: rgba(231, 76, 60, 0.15);
    border: 1px solid rgba(231, 76, 60, 0.3);
}

.menu-item-single.logout > a:hover {
    background: rgba(231, 76, 60, 0.3);
}

/* Footer del menú */
.menu-footer {
    padding: 15px 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.2);
}

.menu-footer p {
    color: rgba(255, 255, 255, 0.5);
    font-size: 11px;
    text-align: center;
    margin: 0;
}

/* Overlay para móviles */
.menu-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.menu-overlay.active {
    display: block;
    opacity: 1;
}

/* Animación del botón hamburguesa */
.menu-toggle.active .bar:nth-child(1) {
    transform: rotate(45deg) translate(8px, 8px);
}

.menu-toggle.active .bar:nth-child(2) {
    opacity: 0;
}

.menu-toggle.active .bar:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -7px);
}

/* Ajuste del body cuando el menú está abierto */
body {
    margin-left: 0;
    transition: margin-left 0.3s ease-in-out;
}

body.menu-open {
    margin-left: 280px;
}

/* Responsive */
@media (max-width: 1024px) {
    body.menu-open {
        margin-left: 0;
    }

    .menu-overlay.active {
        display: block;
    }
}

@media (max-width: 768px) {
    .main-menu {
        width: 260px;
    }

    .menu-toggle {
        top: 10px;
        left: 10px;
        padding: 10px;
    }

    .menu-header {
        padding: 20px 15px;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }

    .logo-text strong {
        font-size: 16px;
    }

    .logo-text span {
        font-size: 12px;
    }

    .menu-group-title {
        font-size: 12px;
        padding: 10px 15px;
    }

    .menu-subgroup a {
        padding: 9px 15px 9px 48px;
        font-size: 13px;
    }

    .menu-subgroup a::before {
        left: 34px;
    }
}

@media (max-width: 480px) {
    .main-menu {
        width: 240px;
    }
}
</style>

<script>
// JavaScript para manejar el comportamiento del botón de hamburguesa
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainMenu = document.querySelector('.main-menu');
    const menuOverlay = document.querySelector('.menu-overlay');
    const body = document.body;
  
    // Toggle del menú
    menuToggle.addEventListener('click', () => {
        mainMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
        menuOverlay.classList.toggle('active');
        body.classList.toggle('menu-open');
    });

    // Cerrar menú al hacer click en el overlay
    menuOverlay.addEventListener('click', () => {
        mainMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        menuOverlay.classList.remove('active');
        body.classList.remove('menu-open');
    });

    // Cerrar el menú al hacer clic en un enlace
    const menuLinks = document.querySelectorAll('.main-menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            mainMenu.classList.remove('active');
            menuToggle.classList.remove('active');
            menuOverlay.classList.remove('active');
            body.classList.remove('menu-open');
        });
    });
});
</script>