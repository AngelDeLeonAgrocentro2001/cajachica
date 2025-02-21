<?php
// public/index.php
require_once __DIR__ . '/../app/controllers/AuthController.php';

$auth = new AuthController();

// Redirigir al dashboard si está logueado, o al login si no
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;