<!-- app/views/dashboard.view.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - AgroCaja</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-card">
            <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p>ID de Usuario: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
            <p>Serás redirigido en unos segundos...</p>
            <div class="loader"></div>
            <a href="/public/logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>
    <script>
        // Redirección automática después de 3 segundos
        setTimeout(function() {
            window.location.href = "http://192.168.1.12/principal";
        }, 3000);
    </script>
</body>
</html>