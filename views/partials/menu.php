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
           <?php if ($usuarioModel->tienePermiso($usuario, 'create_liquidaciones')): ?>
        <li><a href="index.php?controller=liquidacion&action=list">Gestión de Liquidaciones</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'create_detalles')): ?>
        <li><a href="index.php?controller=detalleliquidacion&action=list">Gestión de Liquidaciones Detalles</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_cajachica')): ?>
        <li><a href="index.php?controller=cajachica&action=list">Gestión de Cajas Chicas</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_impuestos')): ?>
        <li><a href="index.php?controller=impuesto&action=list">Gestión de Impuestos</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_cuentas_contables')): ?>
        <li><a href="index.php?controller=cuentacontable&action=list">Gestión de Cuentas Contables</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_tipos_gastos')): ?>
        <li><a href="index.php?controller=tipogasto&action=list">Gestión de Tipos de Gastos</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_centros_costos')): ?>
           <li><a href="index.php?controller=centrocosto&action=list">Gestión de Centros de Costos</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_roles')): ?>
        <li><a href="index.php?controller=rol&action=list">Gestión de Roles</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_usuarios')): ?>
        <li><a href="index.php?controller=usuario&action=list">Gestión de Usuarios</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'autorizar_liquidaciones')): ?>
        <li><a href="index.php?controller=liquidacion&action=list&mode=autorizar">Autorizar Liquidaciones</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_liquidaciones')): ?>
        <li><a href="index.php?controller=liquidacion&action=list&mode=revisar">Revisar Liquidaciones</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_detalles_liquidaciones')): ?>
        <li><a href="index.php?controller=detalleliquidacion&action=revisar">Revisar Detalles de Liquidaciones</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_correcciones')): ?>
        <li><a href="index.php?controller=liquidacion&action=listCorrecciones">Corrección de Liquidaciones</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_reportes')): ?>
        <li><a href="index.php?controller=reportes&action=list">Generar Reportes</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_auditoria')): ?>
        <li><a href="index.php?controller=auditoria&action=list">Consultar Auditoría</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_accesos')): ?>
        <li><a href="index.php?controller=acceso&action=list">Administración de Accesos</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'manage_facturas')): ?>
        <li><a href="index.php?controller=factura&action=list">Gestión de Facturas</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'autorizar_facturas')): ?>
        <li><a href="index.php?controller=factura&action=list&mode=autorizar">Autorizar Facturas</a></li>
           <?php endif; ?>
           <?php if ($usuarioModel->tienePermiso($usuario, 'revisar_facturas')): ?>
        <li><a href="index.php?controller=factura&action=list&mode=revisar">Revisar Facturas</a></li>
           <?php endif; ?>
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
    display: block; /* Visible en todos los dispositivos */
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
    background-color: #2c3e50; /* Fondo oscuro y elegante */
    padding: 0;
    margin: 0;
    border-bottom: 2px solid #1a252f;
    transition: transform 0.3s ease-in-out;
    position: fixed;
    top: 0;
    left: 0;
    width: 250px; /* Ancho del menú lateral */
    height: 100vh; /* Ocupa toda la altura */
    overflow-y: auto; /* Habilita el scroll si hay muchos elementos */
    transform: translateX(-100%); /* Oculto por defecto en todos los dispositivos */
    z-index: 1000;
}

/* Mostrar el menú cuando está activo */
.main-menu.active {
    transform: translateX(0); /* Mostrar el menú */
}

/* Estilo de la lista del menú */
.main-menu ul {
    list-style: none;
    margin: 0;
    padding: 20px 0;
    display: flex;
    flex-direction: column; /* Elementos en columna */
}

/* Estilo de los elementos del menú */
.main-menu li {
    margin: 5px 0;
}

/* Estilo de los enlaces */
.main-menu a {
    display: block;
    text-decoration: none;
    color: #ecf0f1; /* Texto claro */
    font-weight: 500;
    padding: 15px 20px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Hover en los enlaces */
.main-menu a:hover {
    background-color: #3498db; /* Fondo azul al pasar el mouse */
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
    margin-left: 0; /* Sin margen por defecto */
    transition: margin-left 0.3s ease-in-out;
}

/* Ajustar el margen del cuerpo cuando el menú está visible */
body.menu-open {
    margin-left: 250px; /* Espacio para el menú cuando está abierto */
}

/* Estilo responsivo */
@media (max-width: 768px) {
    body.menu-open {
        margin-left: 0; /* En pantallas pequeñas, el menú es un overlay */
    }

    .main-menu {
        width: 200px; /* Menú más estrecho en pantallas pequeñas */
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