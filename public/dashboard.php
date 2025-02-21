
<?php
session_start();

// Verificar si el usuario está autenticado
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mostrar la vista de bienvenida
require_once __DIR__ . '/../app/views/dashboard.view.php';