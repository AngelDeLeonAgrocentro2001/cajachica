<?php

require_once __DIR__ . '/../partials/menu.php'; // Incluir el menú
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Archivo DTE</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    transition: margin-left 0.3s ease-in-out;
}

body.menu-open {
    margin-left: 250px;
}

.content-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 400px;
    text-align: center;
    margin: 50px auto;
}

h1 {
    font-size: 24px;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

label {
    font-weight: bold;
}

input[type="file"] {
    padding: 10px;
}

button {
    background-color: #28a745;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #218838;
}

#message {
    margin-top: 20px;
    color: #333;
}

@media (max-width: 768px) {
    body.menu-open {
        margin-left: 0;
    }

    .content-container {
        width: 90%;
        margin: 70px auto;
    }
}
    </style>
</head>
<body>
    <div class="content-container">
        <h1>Subir Archivo Excel DTE</h1>
        <form id="uploadForm" enctype="multipart/form-data">
            <label for="excel_file">Seleccionar archivo Excel:</label>
            <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
            <button type="submit">Subir y Procesar</button>
        </form>
        <div id="message"></div>
    </div>
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');

            fetch('/index.php?controller=dte&action=uploadExcel', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.textContent = data.message;
                messageDiv.style.color = data.success ? 'green' : 'red';
            })
            .catch(error => {
                messageDiv.textContent = 'Error al conectar con el servidor.';
                messageDiv.style.color = 'red';
            });
        });
    </script>
</body>
</html>
