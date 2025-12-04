<?php
require_once __DIR__ . '/../partials/menu.php'; // Incluir el menú
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión DTE</title>
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

    body.menu-open {
      margin-left: 250px;
    }

    .contenedor {
      background-color: rgba(5, 119, 32, 0.25);
      min-height: 100vh;
      padding: 2rem;
    }

    .content-wrapper {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .content-container {
      background: var(--card-background);
      padding: 2rem;
      border-radius: 16px;
      box-shadow: var(--shadow-lg);
      border-top: 5px solid var(--primary-color);
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h1,
    h2 {
      color: var(--primary-dark);
      margin-bottom: 1.25rem;
      font-weight: 700;
    }

    h1 {
      font-size: 1.75rem;
    }

    h2 {
      font-size: 1.5rem;
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

    input[type="file"],
    input[type="text"],
    input[type="date"] {
      padding: 0.75rem;
      border: 2px solid var(--border-color);
      border-radius: 10px;
      font-size: 0.9rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    input[type="file"]:focus,
    input[type="text"]:focus,
    input[type="date"]:focus {
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

    .search-section {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: flex-end;
    }

    .form-group {
      flex: 1;
      min-width: 200px;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .table-container {
      overflow-x: auto;
      margin-top: 1.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: var(--shadow-sm);
      border-radius: 8px;
      overflow: hidden;
    }

    thead {
      background: var(--primary-color);
      color: white;
    }

    th,
    td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }

    th {
      font-weight: 600;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    tbody tr:hover {
      background-color: rgba(45, 106, 79, 0.05);
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    .no-results {
      text-align: center;
      padding: 3rem;
      color: var(--text-muted);
      font-style: italic;
    }

    .badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .badge-success {
      background-color: rgba(22, 163, 74, 0.1);
      color: var(--success-color);
    }

    #message {
      margin-top: 1.25rem;
      color: var(--text-muted);
      font-size: 0.95rem;
    }

    .loading {
      text-align: center;
      padding: 2rem;
      color: var(--text-muted);
    }

    @media (max-width: 768px) {
      body.menu-open {
        margin-left: 0;
      }

      .contenedor {
        padding: 1rem;
      }

      .content-container {
        padding: 1.5rem;
      }

      h1 {
        font-size: 1.5rem;
      }

      h2 {
        font-size: 1.25rem;
      }

      .search-section {
        flex-direction: column;
      }

      .form-group {
        width: 100%;
      }

      table {
        font-size: 0.85rem;
      }

      th,
      td {
        padding: 0.75rem 0.5rem;
      }
    }

    .badge-danger {
      background-color: rgba(220, 38, 38, 0.1);
      color: var(--danger-color);
    }
  </style>
</head>

<body>
  <div class="contenedor">
    <div class="content-wrapper">

      <!-- Sección de Carga -->
      <div class="content-container">
        <h1>📤 Subir Archivo Excel DTE</h1>
        <form id="uploadForm" enctype="multipart/form-data">
          <label for="excel_file">Seleccionar archivo Excel:</label>
          <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
          <button type="submit">Subir y Procesar</button>
        </form>
        <div id="message"></div>
      </div>

      <!-- Sección de Búsqueda y Visualización -->
      <div class="content-container">
        <h2>📋 Archivos DTE Subidos</h2>

        <div class="search-section">
          <div class="form-group">
            <label for="search_nit">Buscar por NIT:</label>
            <input type="text" id="search_nit" placeholder="Ingrese NIT del emisor">
          </div>
          <div class="form-group">
            <label for="search_serie">Buscar por Serie:</label>
            <input type="text" id="search_serie" placeholder="Ingrese Serie">
          </div>
          <div class="form-group">
            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" id="fecha_inicio">
          </div>
          <div class="form-group">
            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" id="fecha_fin">
          </div>
          <button onclick="searchDtes()" style="align-self: flex-end;">🔍 Buscar</button>
          <button onclick="clearSearch()" style="align-self: flex-end; background: var(--text-muted);">🔄
            Limpiar</button>
        </div>

        <div class="table-container">
          <div id="loading" class="loading" style="display: none;">
            Cargando datos...
          </div>
          <table id="dtesTable" style="display: none;">
            <thead>
              <tr>
                <th>Fecha Emisión</th>
                <th>No. Autorización</th>
                <th>Serie</th>
                <th>No. DTE</th>
                <th>Nombre Emisor</th>
                <th>NIT Emisor</th>
                <th>Gran Total</th>
                <th>IVA</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody id="dtesTableBody">
            </tbody>
          </table>
          <div id="noResults" class="no-results" style="display: none;">
            No se encontraron resultados. Utilice los filtros de búsqueda.
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    // Script para subir archivos (sin cambios)
    document.getElementById('uploadForm').addEventListener('submit', function (e) {
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

      fetch('../public/index.php?controller=dte&action=uploadExcel', {
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
              document.getElementById('uploadForm').reset();
              // Recargar la tabla si hay búsqueda activa
              if (document.getElementById('search_nit').value) {
                searchDtes();
              }
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

    // Funciones para búsqueda de DTEs
    // Funciones para búsqueda de DTEs
    function searchDtes() {
      const nit = document.getElementById('search_nit').value.trim();
      const serie = document.getElementById('search_serie').value.trim();
      const fechaInicio = document.getElementById('fecha_inicio').value;
      const fechaFin = document.getElementById('fecha_fin').value;

      if (!nit && !serie) {
        Swal.fire({
          icon: 'warning',
          title: 'Campo Requerido',
          text: 'Por favor ingrese al menos un NIT o Serie para buscar.',
          confirmButtonText: 'Aceptar'
        });
        return;
      }

      showLoading();

      // Construir la URL correctamente
      let url = `../public/index.php?controller=dte&action=searchByNit`;
      let params = [];

      if (nit) params.push(`nit=${encodeURIComponent(nit)}`);
      if (serie) params.push(`serie=${encodeURIComponent(serie)}`);
      if (fechaInicio) params.push(`fecha_inicio=${fechaInicio}`);
      if (fechaFin) params.push(`fecha_fin=${fechaFin}`);

      if (params.length > 0) {
        url += '&' + params.join('&');
      }

      fetch(url)
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          hideLoading();
          if (data.error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.error,
              confirmButtonText: 'Aceptar'
            });
            return;
          }
          displayResults(data);
        })
        .catch(error => {
          hideLoading();
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al buscar los DTEs.',
            confirmButtonText: 'Aceptar'
          });
        });
    }

    function displayResults(dtes) {
      const tableBody = document.getElementById('dtesTableBody');
      const table = document.getElementById('dtesTable');
      const noResults = document.getElementById('noResults');

      tableBody.innerHTML = '';

      if (dtes.length === 0) {
        table.style.display = 'none';
        noResults.style.display = 'block';
        return;
      }

      table.style.display = 'table';
      noResults.style.display = 'none';

      dtes.forEach(dte => {
        // Determinar el estado basado en el campo 'usado'
        let estadoBadge = '';
        let estadoText = '';

        if (dte.usado === 'Y' || dte.usado === 'y') {
          estadoBadge = 'badge-danger';
          estadoText = 'Desactivado';
        } else {
          estadoBadge = 'badge-success';
          estadoText = 'Activo';
        }

        const row = document.createElement('tr');
        // En la función displayResults, modificar el HTML de la fila:
        row.innerHTML = `
    <td>${formatDate(dte.fecha_emision)}</td>
    <td>${dte.numero_autorizacion || '-'}</td>
    <td>${dte.serie || '-'}</td>
    <td>${dte.numero_dte || '-'}</td>
    <td>${dte.nombre_emisor || '-'}</td>
    <td>${dte.nit_emisor || '-'}</td>
    <td>Q ${formatNumber(dte.gran_total)}</td>
    <td>Q ${formatNumber(dte.iva)}</td>
    <td><span class="badge ${estadoBadge}">${estadoText}</span></td>
    <td>${dte.usado || '-'}</td> <!-- Opcional: mostrar el valor del campo usado -->
`;
        tableBody.appendChild(row);
      });
    }

    function clearSearch() {
      document.getElementById('search_nit').value = '';
      document.getElementById('fecha_inicio').value = '';
      document.getElementById('fecha_fin').value = '';
      document.getElementById('dtesTable').style.display = 'none';
      document.getElementById('noResults').style.display = 'none';
    }

    function showLoading() {
      document.getElementById('loading').style.display = 'block';
      document.getElementById('dtesTable').style.display = 'none';
      document.getElementById('noResults').style.display = 'none';
    }

    function hideLoading() {
      document.getElementById('loading').style.display = 'none';
    }

    function formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-GT', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      });
    }

    function formatNumber(number) {
      if (!number) return '0.00';
      return parseFloat(number).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Permitir búsqueda con Enter en ambos campos
    document.getElementById('search_nit').addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        searchDtes();
      }
    });

    document.getElementById('search_serie').addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        searchDtes();
      }
    });
  </script>
</body>

</html>