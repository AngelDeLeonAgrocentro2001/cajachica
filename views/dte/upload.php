<?php
require_once __DIR__ . '/../partials/menu.php'; // Incluir el menú
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Archivo DTE</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      :root {
        --primary-color: #2d6a4f;
        --primary-dark: #1a4d3e;
        --primary-light: #52b788;
        --secondary-color: #40916c;
        --accent-color: #f39c12;
        --danger-color: #dc2626;
        --success-color: #16a34a;
        --background-color: #f5f7fa;
        --background-gradient: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
        --card-background: #ffffff;
        --text-color: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --shadow-sm: 0 2px 8px rgba(45, 106, 79, 0.08);
        --shadow-md: 0 4px 16px rgba(45, 106, 79, 0.12);
        --shadow-lg: 0 8px 24px rgba(45, 106, 79, 0.15);
      }

      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }

      /* body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--background-gradient);
        color: var(--text-color);
        transition: margin-left 0.3s ease-in-out;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
      } */

      body.menu-open {
        margin-left: 250px;
      }

      .contenedor {
        background-color: rgba(5, 119, 32, 0.25);
    height: 100vh;
    display: flex
;
    justify-content: center;
    align-items: center;
      }

      .content-container {
        background: var(--card-background);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        width: 100%;
        max-width: 450px;
        text-align: center;
        border-top: 5px solid var(--primary-color);
        animation: fadeIn 0.5s ease;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }

      h1 {
        font-size: 1.75rem;
        color: var(--primary-dark);
        margin-bottom: 1.25rem;
        font-weight: 700;
      }

      form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }

      label {
        font-weight: 600;
        color: var(--text-color);
        text-align: left;
      }

      input[type="file"] {
        padding: 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
      }

      input[type="file"]:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(45, 106, 79, 0.1);
        background: white;
      }

      button {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 0.875rem 1.5rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
      }

      button:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
      }

      #message {
        margin-top: 1.25rem;
        color: var(--text-muted);
        font-size: 0.95rem;
      }

      @media (max-width: 768px) {
        body.menu-open {
          margin-left: 0;
        }

        .content-container {
          width: 95%;
          padding: 1.5rem;
          border-radius: 12px;
        }

        h1 {
          font-size: 1.5rem;
        }
      }

      @media (max-width: 480px) {
        .content-container {
          width: 100%;
          border-radius: 0;
          height: 100vh;
          justify-content: center;
          display: flex;
          flex-direction: column;
        }

        button {
          width: 100%;
        }

        input[type="file"] {
          font-size: 0.85rem;
        }
      }
    </style>
</head>
<body>
    <div class="contenedor">
      <div class="content-container">
        <h1>Subir Archivo Excel DTE</h1>
        <form id="uploadForm" enctype="multipart/form-data">
            <label for="excel_file">Seleccionar archivo Excel:</label>
            <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
            <button type="submit">Subir y Procesar</button>
        </form>
        <div id="message"></div>
      </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor, espera mientras se procesa el archivo.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');

            fetch('/index.php?controller=dte&action=uploadExcel', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        messageDiv.textContent = data.message;
                        messageDiv.style.color = 'green';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        messageDiv.textContent = data.message;
                        messageDiv.style.color = 'red';
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al conectar con el servidor.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    messageDiv.textContent = 'Error al conectar con el servidor.';
                    messageDiv.style.color = 'red';
                });
            });
        });
    </script>
</body>
</html>
