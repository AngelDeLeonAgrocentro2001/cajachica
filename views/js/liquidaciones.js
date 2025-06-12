const modal = document.querySelector("#modal");
const modalForm = document.querySelector("#modalForm");
const supervisorModal = document.querySelector("#supervisorModal");
const supervisorSelect = document.querySelector("#supervisorSelect");

// Store liquidations data globally to access in autorizarLiquidacion
let liquidacionesData = [];
let correctedDetallesData = [];
let isShowingCorrected = false; // Track the current view state
let currentPage = 1;
const itemsPerPage = 50;
let filteredLiquidacionesData = []; // Store filtered data after search
let currentLiquidacionId = null; // Store the ID of the liquidation being finalized
let supervisores = []; // Store the list of supervisors

document.addEventListener("DOMContentLoaded", () => {
  if (
    typeof window.userPermissions === "undefined" ||
    typeof window.userRole === "undefined" ||
    typeof window.currentUserId === "undefined"
  ) {
    console.error(
      "Error: userPermissions, userRole, o currentUserId no están definidos."
    );
    alert(
      "Error: No se pudieron cargar los permisos, el rol o el ID del usuario. Contacta al administrador."
    );
    return;
  }

  loadLiquidaciones();

  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get("id");
  if (id && modal) {
    showEditForm(id);
  }
});

function closeModal() {
  if (modal) {
    modal.classList.remove("active");
    modalForm.innerHTML = "";
  }
}

function showFinalizedDetailModal(message, options, callback) {
  const finalizedModal = document.getElementById("finalizedDetailModal");
  const messageElement = document.getElementById("finalizedDetailMessage");
  const optionsContainer = document.getElementById("finalizedDetailOptions");

  messageElement.textContent = message;
  optionsContainer.innerHTML = "";

  options.forEach((option, index) => {
    const button = document.createElement("button");
    button.className = "modal-option";
    button.style.cssText =
      "background-color: #3498db; color: #fff; padding: 10px; border: none; border-radius: 4px; cursor: pointer; width: 100%;font-size:1.2rem;";
    button.textContent = option.text;
    button.setAttribute("data-choice", index + 1);
    button.onclick = () => {
      closeFinalizedDetailModal();
      callback(index + 1);
    };
    optionsContainer.appendChild(button);
  });

  finalizedModal.style.display = "flex";
}

function closeFinalizedDetailModal() {
  const finalizedModal = document.getElementById("finalizedDetailModal");
  finalizedModal.style.display = "none";
}

function showSelectLiquidationModal(liquidations, callback) {
  const selectModal = document.getElementById("selectLiquidationModal");
  const optionsContainer = document.getElementById("liquidationOptions");

  optionsContainer.innerHTML = "";

  liquidations.forEach((liq, index) => {
    const button = document.createElement("button");
    button.className = "modal-option";
    button.style.cssText =
      "background-color: #3498db; color: #fff; padding: 10px; border: none; border-radius: 4px; cursor: pointer; width: 100%;font-size:1.2rem;";
    button.textContent = `${index + 1}. Liquidación ID: ${
      liq.id
    } (Caja Chica: ${liq.nombre_caja_chica})`;
    button.setAttribute("data-index", index);
    button.onclick = () => {
      closeSelectLiquidationModal();
      callback(index);
    };
    optionsContainer.appendChild(button);
  });

  selectModal.style.display = "flex";
}

function closeSelectLiquidationModal() {
  const selectModal = document.getElementById("selectLiquidationModal");
  selectModal.style.display = "none";
}

async function loadLiquidaciones() {
  try {
      const urlParams = new URLSearchParams(window.location.search);
      const mode = urlParams.get("mode") || "";
      const fetchUrl = mode
          ? `index.php?controller=liquidacion&action=list&mode=${mode}`
          : "index.php?controller=liquidacion&action=list";

      const response = await fetch(fetchUrl, {
          headers: {
              "X-Requested-With": "XMLHttpRequest",
          },
      });
      if (!response.ok) {
          const errorData = await response.json();
          if (response.status === 403) {
              alert("No tienes permiso para ver esta lista. Serás redirigido.");
              window.location.href = "index.php?controller=dashboard&action=index";
              return;
          }
          throw new Error(errorData.error || `Error HTTP: ${response.status}`);
      }
      const data = await response.json();
      liquidacionesData = data.liquidaciones;
      correctedDetallesData = data.corrected_detalles || [];
      window.isContabilidadLike = data.isContabilidadLike || false; // Store flag

      // Filtro adicional para el rol CONTABILIDAD
      if (window.isContabilidadLike) {
          liquidacionesData = liquidacionesData.filter(
              (liquidacion) =>
                  !liquidacion.id_contador ||
                  liquidacion.id_contador == window.currentUserId
          );
          console.log(
              `Liquidaciones filtradas para CONTABILIDAD (ID: ${window.currentUserId}): ${liquidacionesData.length} registros`
          );
          liquidacionesData.forEach((liquidacion) => {
              console.log(
                  `Liquidacion ID: ${liquidacion.id}, id_contador: ${liquidacion.id_contador}, Estado: ${liquidacion.estado}`
              );
          });
      }

      filteredLiquidacionesData = [...liquidacionesData];
      currentPage = 1;
      renderLiquidations();
      renderCorrectedDetalles();
  } catch (error) {
      console.error("Error al cargar liquidaciones:", error);
      alert("Error al cargar las liquidaciones. Intenta de nuevo.");
  }
}

function filterLiquidations() {
  const searchId = document.getElementById("searchId").value.trim();
  const searchCajaChica = document
    .getElementById("searchCajaChica")
    .value.trim()
    .toLowerCase();
  const searchFechaInicio = document.getElementById("searchFechaInicio").value;
  const searchFechaFin = document.getElementById("searchFechaFin").value;

  let filtered = liquidacionesData;

  // Apply role-based filters first (same as before)
  if (
    window.userRole === "SUPERVISOR" &&
    new URLSearchParams(window.location.search).get("mode") === "autorizar"
  ) {
    if (isShowingCorrected) {
      filtered = liquidacionesData.filter((liquidacion) => {
        return (
          liquidacion.detalles &&
          liquidacion.detalles.some(
            (detalle) => detalle.estado === "EN_CORRECTION"
          )
        );
      });
    } else {
      filtered = liquidacionesData.filter(
        (liquidacion) => liquidacion.estado === "PENDIENTE_AUTORIZACION"
      );
    }
  }

  // Apply search filters
  filtered = filtered.filter((liquidacion) => {
    let matches = true;

    // Filter by ID
    if (searchId) {
      matches = matches && liquidacion.id.toString() === searchId;
    }

    // Filter by Caja Chica
    if (searchCajaChica) {
      const nombreCajaChica = (
        liquidacion.nombre_caja_chica || ""
      ).toLowerCase();
      matches = matches && nombreCajaChica.includes(searchCajaChica);
    }

    // Filter by Fecha Inicio
    if (searchFechaInicio) {
      const fechaInicio = liquidacion.fecha_inicio
        ? new Date(liquidacion.fecha_inicio)
        : null;
      const searchDate = new Date(searchFechaInicio);
      matches =
        matches &&
        fechaInicio &&
        fechaInicio.toISOString().split("T")[0] ===
          searchDate.toISOString().split("T")[0];
    }

    // Filter by Fecha Fin
    if (searchFechaFin) {
      const fechaFin = liquidacion.fecha_fin
        ? new Date(liquidacion.fecha_fin)
        : null;
      const searchDate = new Date(searchFechaFin);
      matches =
        matches &&
        fechaFin &&
        fechaFin.toISOString().split("T")[0] ===
          searchDate.toISOString().split("T")[0];
    }

    return matches;
  });

  return filtered;
}

function applySearch() {
  filteredLiquidacionesData = filterLiquidations();
  currentPage = 1; // Reset to first page on search
  renderLiquidations();
}

function resetSearch() {
  document.getElementById("searchId").value = "";
  document.getElementById("searchCajaChica").value = "";
  document.getElementById("searchFechaInicio").value = "";
  document.getElementById("searchFechaFin").value = "";
  filteredLiquidacionesData = [...liquidacionesData];
  currentPage = 1; // Reset to first page on reset
  renderLiquidations();
}

function renderLiquidations() {
  const tbody = document.querySelector("#liquidacionesTable tbody");
  tbody.innerHTML = "";

  // Paginate the filtered data
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const paginatedLiquidaciones = filteredLiquidacionesData.slice(
      startIndex,
      endIndex
  );

  if (paginatedLiquidaciones.length > 0) {
      paginatedLiquidaciones.forEach((liquidacion) => {
          const actions = [];
          const hasCorrections =
              liquidacion.detalles &&
              Array.isArray(liquidacion.detalles) &&
              liquidacion.detalles.some(
                  (detalle) => detalle.estado === "EN_CORRECTION"
              );
          const isCreator = liquidacion.id_usuario == window.currentUserId;

          // Actions for users who created the liquidation (non-SUPERVISOR/CONTABILIDAD)
          if (
              window.userPermissions.create_liquidaciones &&
              !window.userRole.toUpperCase().includes("SUPERVISOR") &&
              window.userRole !== "CONTABILIDAD" &&
              isCreator
          ) {
              if (liquidacion.estado === "EN_PROCESO") {
                  actions.push(
                      `<button onclick="showEditForm(${liquidacion.id})" class="edit-btn">Editar</button>`
                  );
                  actions.push(
                      `<button onclick="deleteLiquidation(${liquidacion.id})" class="delete-btn">Eliminar</button>`
                  );
                  actions.push(
                      `<button onclick="manageFacturas(${liquidacion.id})" class="edit-btn">Agregar Facturas</button>`
                  );
                  actions.push(
                      `<button onclick="finalizarLiquidacion(${
                          liquidacion.id
                      })" class="finalize-btn" ${
                          hasCorrections ? "disabled" : ""
                      }>Finalizar</button>`
                  );
              }
          }

          // Common actions for all users (view liquidation)
          if (
              [
                  "PENDIENTE_AUTORIZACION",
                  "PENDIENTE_REVISION_CONTABILIDAD",
                  "FINALIZADO",
                  "RECHAZADO_AUTORIZACION",
                  "RECHAZADO_POR_CONTABILIDAD",
              ].includes(liquidacion.estado)
          ) {
              actions.push(
                  `<button onclick="verLiquidacion(${liquidacion.id})" class="view-btn">Ver Liquidación</button>`
              );
          }

          // Actions for SUPERVISOR roles
          if (
              window.userPermissions.autorizar_liquidaciones &&
              window.userRole.toUpperCase().includes("SUPERVISOR")
          ) {
              if (liquidacion.estado === "PENDIENTE_AUTORIZACION") {
                  actions.push(
                      `<button onclick="autorizarLiquidacion(${liquidacion.id}, 'autorizar')" class="edit-btn">Autorizar</button>`
                  );
              }
          }

          // Actions for Contabilidad-like roles
          if (
              window.userPermissions.revisar_liquidaciones &&
              window.isContabilidadLike
          ) {
              if (liquidacion.estado === "PENDIENTE_REVISION_CONTABILIDAD") {
                  actions.push(
                      `<button onclick="autorizarLiquidacion(${liquidacion.id}, 'revisar')" class="edit-btn">Revisar</button>`
                  );
              }
              // if (liquidacion.estado === "FINALIZADO") {
              //     actions.push(
              //         `<button onclick="exportToSap(${liquidacion.id})" class="export-btn">Exportar a SAP</button>`
              //     );
              // }
          }

          const actionsHtml = actions.join(" ");
          const estado =
              liquidacion.estado && liquidacion.estado !== "N/A"
                  ? liquidacion.estado
                  : "EN_PROCESO";

          tbody.innerHTML += `
              <tr>
                  <td data-label="ID">${liquidacion.id}</td>
                  <td data-label="Caja Chica">${
                      liquidacion.nombre_caja_chica || "N/A"
                  }</td>
                  <td data-label="Fecha Creación">${
                      liquidacion.fecha_creacion || "N/A"
                  }</td>
                  <td data-label="Fecha Inicio">${
                      liquidacion.fecha_inicio || "N/A"
                  }</td>
                  <td data-label="Fecha Fin">${liquidacion.fecha_fin || "N/A"}</td>
                  <td data-label="Monto Total">${parseFloat(
                      liquidacion.monto_total || 0
                  ).toFixed(2)}</td>
                  <td data-label="Estado">${estado}</td>
                  <td data-label="Acciones" style="
      display: flex;
      flex-direction: column;
      gap: 7px;
  ">${actionsHtml}</td>
              </tr>
          `;
      });
  } else {
      tbody.innerHTML =
          '<tr><td colspan="8">No hay liquidaciones disponibles.</td></tr>';
  }

  // Render pagination controls
  renderPagination();
}

function renderPagination() {
  const paginationControls = document.getElementById("paginationControls");
  const totalItems = filteredLiquidacionesData.length;
  const totalPages = Math.ceil(totalItems / itemsPerPage);

  paginationControls.innerHTML = "";

  if (totalPages <= 1) return; // No pagination needed for 1 page

  // Previous button
  const prevButton = document.createElement("button");
  prevButton.textContent = "Anterior";
  prevButton.disabled = currentPage === 1;
  prevButton.onclick = () => {
    if (currentPage > 1) {
      currentPage--;
      renderLiquidations();
    }
  };
  paginationControls.appendChild(prevButton);

  // Page info
  const pageInfo = document.createElement("span");
  pageInfo.textContent = `Página ${currentPage} de ${totalPages}`;
  paginationControls.appendChild(pageInfo);

  // Next button
  const nextButton = document.createElement("button");
  nextButton.textContent = "Siguiente";
  nextButton.disabled = currentPage === totalPages;
  nextButton.onclick = () => {
    if (currentPage < totalPages) {
      currentPage++;
      renderLiquidations();
    }
  };
  paginationControls.appendChild(nextButton);
}

function renderCorrectedDetalles() {
  const correctedDetallesSection = document.querySelector("#correctedDetallesSection");
  const correctedDetallesTbody = document.querySelector("#correctedDetallesTable tbody");
  const mode = new URLSearchParams(window.location.search).get("mode") || "";

  if (
    window.userRole.toUpperCase().includes("SUPERVISOR") &&
    mode === "autorizar" &&
    correctedDetallesData.length > 0 &&
    isShowingCorrected
  ) {
    correctedDetallesSection.style.display = "block";
    correctedDetallesTbody.innerHTML = "";
    correctedDetallesData.forEach((detalle) => {
      const actions = [];
      if (
        window.userPermissions.autorizar_liquidaciones &&
        window.userRole.toUpperCase().includes("SUPERVISOR")
      ) {
        actions.push(
          `<button onclick="autorizarDetalle(${detalle.id}, ${detalle.liquidacion_id}, 'autorizar')" class="edit-btn">Autorizar</button>`
        );
        actions.push(
          `<button onclick="autorizarDetalle(${detalle.id}, ${detalle.liquidacion_id}, 'rechazar')" class="delete-btn">Rechazar</button>`
        );
        actions.push(
          `<button onclick="autorizarDetalle(${detalle.id}, ${detalle.liquidacion_id}, 'descartar')" class="finalize-btn">Descartar</button>`
        );
      }
      const actionsHtml = actions.join(" ");

      let archivosHtml = "N/A";
      if (detalle.rutas_archivos && detalle.rutas_archivos.length > 0) {
        try {
          const rutas = Array.isArray(detalle.rutas_archivos)
            ? detalle.rutas_archivos
            : JSON.parse(detalle.rutas_archivos);

          if (Array.isArray(rutas) && rutas.length > 0) {
            archivosHtml = rutas
              .map((ruta) => {
                // Normalize path: convert backslashes to forward slashes, ensure proper format
                let normalizedPath = ruta
                  .replace(/\\/g, "/") // Convert \ to /
                  .replace(/^\/+/, ""); // Remove leading slashes
                // Ensure path starts with Uploads/, but don't prepend if already correct
                if (!normalizedPath.startsWith("Uploads/")) {
                  normalizedPath = `Uploads/${normalizedPath}`;
                }
                return `<div><a href="/agrocaja-chica/${normalizedPath}" target="_blank">Ver Archivos</a></div>`;
              })
              .join("");
          }
        } catch (e) {
          console.warn("Error parsing rutas_archivos, treating as single path:", e);
          if (typeof detalle.rutas_archivos === "string" && detalle.rutas_archivos.trim().length > 0) {
            let normalizedPath = detalle.rutas_archivos
              .replace(/\\/g, "/") // Convert \ to /
              .replace(/^\/+/, ""); // Remove leading slashes
            if (!normalizedPath.startsWith("Uploads/")) {
              normalizedPath = `Uploads/${normalizedPath}`;
            }
            archivosHtml = `<div><a href="/agrocaja-chica/${normalizedPath}" target="_blank">Ver Documentos</a></div>`;
          }
        }
      }

      correctedDetallesTbody.innerHTML += `
        <tr>
            <td data-label="ID">${detalle.id || "N/A"}</td>
            <td data-label="Tipo de Documento">${detalle.tipo_documento || "N/A"}</td>
            <td data-label="No Factura">${detalle.no_factura || "N/A"}</td>
            <td data-label="Proveedor">${detalle.nombre_proveedor || "N/A"}</td>
            <td data-label="NIT">${detalle.nit_proveedor || "N/A"}</td>
            <td data-label="DPI">${detalle.dpi || "N/A"}</td>
            <td data-label="Cantidad">${detalle.cantidad || "N/A"}</td>
            <td data-label="Serie">${detalle.serie || "N/A"}</td>
            <td data-label="Centro Costo">${detalle.nombre_centro_costo || "N/A"}</td>
            <td data-label="Tipo de Gasto">${detalle.t_gasto || "N/A"}</td>
            <td data-label="Tipo de Combustible">${detalle.tipo_combustible || "N/A"}</td>
            <td data-label="Cuenta Contable">${detalle.cuenta_contable_nombre || "N/A"}</td>
            <td data-label="Fecha">${detalle.fecha || "N/A"}</td>
            <td data-label="Subtotal">${parseFloat(detalle.subtotal || 0).toFixed(2)}</td>
            <td data-label="IVA">${parseFloat(detalle.iva || 0).toFixed(2)}</td>
            <td data-label="IDP">${parseFloat(detalle.idp || 0).toFixed(2)}</td>
            <td data-label="INGUAT">${parseFloat(detalle.inguat || 0).toFixed(2)}</td>
            <td data-label="Total Bruto">${parseFloat(detalle.total_factura || 0).toFixed(2)}</td>
            <td data-label="Estado">${detalle.estado || "N/A"}</td>
            <td data-label="Archivos">${archivosHtml}</td>
            <td data-label="Acciones">${actionsHtml}</td>
        </tr>
      `;
    });
  } else {
    correctedDetallesSection.style.display = "none";
  }
}

function toggleLiquidationView() {
  isShowingCorrected = !isShowingCorrected;
  const toggleBtn = document.querySelector("#toggleViewBtn");
  const liquidacionesSection = document.querySelector("#liquidacionesSection");

  if (isShowingCorrected) {
    toggleBtn.textContent = "Ver Liquidaciones Pendientes de Autorización";
    liquidacionesSection.style.display = "none";
  } else {
    toggleBtn.textContent = "Ver Liquidaciones Corregidas";
    liquidacionesSection.style.display = "block";
  }

  applySearch(); // Reapply search to update the filtered data based on the new view
  renderCorrectedDetalles();
}

function verLiquidacion(id) {
  window.location.href = `index.php?controller=liquidacion&action=ver&id=${id}`;
}

function manageFacturas(id) {
  window.location.href = `index.php?controller=liquidacion&action=manageFacturas&id=${id}`;
}

async function showCreateForm() {
  if (!modal || !modalForm) {
    console.error("Modal o modalForm no encontrados en el DOM");
    alert(
      "Error: No se encontró el contenedor del formulario. Intenta de nuevo."
    );
    return;
  }

  try {
    const response = await fetch(
      "index.php?controller=liquidacion&action=create",
      {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      }
    );
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
    }
    const html = await response.text();
    if (!html.includes("<form")) {
      throw new Error("El servidor no devolvió un formulario válido");
    }
    modalForm.innerHTML = html;
    modal.classList.add("active");
    addFormValidations();
  } catch (error) {
    console.error("Error al cargar el formulario:", error);
    modalForm.innerHTML = `<div class="error">${error.message}</div>`;
    modal.classList.add("active");
  }
}

async function showEditForm(id) {
  if (!modal || !modalForm) {
    console.error("Modal o modalForm no encontrados en el DOM");
    alert(
      "Error: No se encontró el contenedor del formulario. Intenta de nuevo."
    );
    return;
  }

  try {
    const response = await fetch(
      `index.php?controller=liquidacion&action=update&id=${id}`,
      {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      }
    );
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
    }
    const html = await response.text();
    if (!html.includes("<form")) {
      throw new Error("El servidor no devolvió un formulario válido");
    }
    modalForm.innerHTML = html;
    modal.classList.add("active");
    addFormValidations(id);
  } catch (error) {
    console.error("Error al cargar el formulario:", error);
    modalForm.innerHTML = `<div class="error">${error.message}</div>`;
    modal.classList.add("active");
  }
}

async function createLiquidation(data) {
  const response = await fetch(
    "index.php?controller=liquidacion&action=create",
    {
      method: "POST",
      body: data,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    }
  );
  const result = await response.json();
  if (!response.ok) {
    throw new Error(result.error || "Error al crear liquidación");
  }
  return result;
}

async function updateLiquidation(id, data) {
  const response = await fetch(
    `index.php?controller=liquidacion&action=update&id=${id}`,
    {
      method: "POST",
      body: data,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    }
  );
  const result = await response.json();
  if (!response.ok) {
    throw new Error(result.error || "Error al actualizar liquidación");
  }
  return result;
}

async function deleteLiquidation(id) {
  if (!confirm("¿Estás seguro de que deseas eliminar esta liquidación?"))
    return;

  try {
    const response = await fetch(
      `index.php?controller=liquidacion&action=delete&id=${id}`,
      {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
      }
    );

    // Check if the response has content before attempting to parse JSON
    const contentType = response.headers.get("content-type");
    if (
      !response.ok ||
      !contentType ||
      !contentType.includes("application/json")
    ) {
      const text = await response.text();
      throw new Error(`Error del servidor: ${text || response.statusText}`);
    }

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.error || "Error al eliminar la liquidación");
    }

    alert(result.message || "Liquidación eliminada correctamente");
    loadLiquidaciones();
  } catch (error) {
    console.error("Error al eliminar liquidación:", error);
    alert(
      error.message || "Error al eliminar la liquidación. Intenta de nuevo."
    );
  }
}

async function autorizarLiquidacion(id, mode) {
  const urlParams = new URLSearchParams(window.location.search);
  const currentMode = urlParams.get("mode") || "";
  window.location.href = `index.php?controller=liquidacion&action=${mode}&id=${id}`;
}

async function autorizarDetalle(detalleId, liquidacionId, action) {
  if (!confirm(`¿Estás seguro de que deseas ${action} este detalle?`)) return;

  try {
    // Fetch liquidation state and detail information
    const stateResponse = await fetch(
      `index.php?controller=liquidacion&action=getEstado&id=${liquidacionId}`,
      {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      }
    );
    if (!stateResponse.ok) {
      const errorText = await stateResponse.text();
      throw new Error(
        `Error al obtener el estado de la liquidación: ${errorText}`
      );
    }
    const stateData = await stateResponse.json();
    const estadoLiquidacion = stateData.estado || "N/A";

    const detailResponse = await fetch(
      `index.php?controller=liquidacion&action=getDetalleInfo&id=${detalleId}`,
      {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      }
    );
    if (!detailResponse.ok) {
      const errorText = await detailResponse.text();
      throw new Error(
        `Error al obtener la información del detalle: ${errorText}`
      );
    }
    const detailData = await detailResponse.json();
    const correccionComentario = detailData.correccion_comentario || "";
    const idUsuarioDetalle = detailData.id_usuario || null; // Detail creator
    const estadoDetalle = (detailData.estado || "N/A").toUpperCase();

    console.log('Detail info:', { detalleId, liquidacionId, action, estadoLiquidacion, estadoDetalle, correccionComentario, userRole: window.userRole, currentUserId: window.userId, detailUserId: idUsuarioDetalle });

    if (!idUsuarioDetalle) {
      throw new Error("No se encontró el usuario asociado al detalle.");
    }

    if (!window.userId) {
      throw new Error("No se encontró el ID del usuario actual.");
    }

    // Show finalized liquidation modal for all details if liquidation is FINALIZADO
    if (
      window.userRole.toUpperCase().includes("SUPERVISOR") &&
      estadoLiquidacion === "FINALIZADO"
    ) {
      const enProcesoResponse = await fetch(
        `index.php?controller=liquidacion&action=getEnProcesoLiquidaciones&user_id=${idUsuarioDetalle}`,
        {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        }
      );
      let enProcesoLiquidaciones = [];
      if (enProcesoResponse.ok) {
        const enProcesoData = await enProcesoResponse.json();
        enProcesoLiquidaciones = enProcesoData.liquidaciones || [];
      } else {
        const errorText = await enProcesoResponse.text();
        throw new Error(`Error al obtener liquidaciones en proceso: ${errorText}`);
      }

      let message = `No se puede autorizar ya que la liquidación fue finalizada. ¿Qué deseas hacer con el detalle ID ${detalleId}?`;
      const options = [
        { text: "Iniciar una nueva liquidación con este detalle" },
      ];
      if (enProcesoLiquidaciones.length > 0) {
        options.push({
          text: "Agregar el detalle a una liquidación en proceso",
        });
      }
      options.push({
        text: "Eliminar el detalle (si tiene comentario de corrección)",
      });

      console.log('Modal options:', options);

      return new Promise((resolve) => {
        showFinalizedDetailModal(message, options, async (choiceNum) => {
          // Use 1-based indexing to match submitCorrections
          if (enProcesoLiquidaciones.length > 0) {
            if (choiceNum === 1) {
              const newLiquidacionResponse = await fetch(
                `index.php?controller=liquidacion&action=createWithDetail`,
                {
                  method: "POST",
                  body: JSON.stringify({
                    detalle_id: detalleId,
                    user_id: idUsuarioDetalle,
                  }),
                  headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                  },
                }
              );
              const newLiquidacionResult = await newLiquidacionResponse.json();
              if (!newLiquidacionResponse.ok) {
                throw new Error(
                  newLiquidacionResult.error || "Error al crear una nueva liquidación"
                );
              }
              alert("Nueva liquidación creada correctamente");
              window.location.reload();
            } else if (choiceNum === 2) {
              return new Promise((innerResolve) => {
                showSelectLiquidationModal(
                  enProcesoLiquidaciones,
                  async (selectedIndex) => {
                    if (
                      selectedIndex >= 0 &&
                      selectedIndex < enProcesoLiquidaciones.length
                    ) {
                      const selectedLiquidacionId =
                        enProcesoLiquidaciones[selectedIndex].id;
                      const addDetailResponse = await fetch(
                        `index.php?controller=liquidacion&action=addDetailToLiquidacion`,
                        {
                          method: "POST",
                          body: JSON.stringify({
                            detalle_id: detalleId,
                            liquidacion_id: selectedLiquidacionId,
                            user_id: idUsuarioDetalle,
                          }),
                          headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                          },
                        }
                      );
                      const addDetailResult = await addDetailResponse.json();
                      if (!addDetailResponse.ok) {
                        throw new Error(
                          addDetailResult.error ||
                            "Error al agregar el detalle a la liquidación"
                        );
                      }
                      alert(
                        "Detalle agregado a la liquidación en proceso correctamente"
                      );
                      window.location.reload();
                    } else {
                      alert("Selección inválida. Operación cancelada.");
                      innerResolve();
                    }
                  }
                );
              });
            } else if (choiceNum === 3) {
              const deleteDetailResponse = await fetch(
                `index.php?controller=liquidacion&action=deleteDetail&id=${detalleId}`,
                {
                  method: "POST",
                  headers: {
                    "X-Requested-With": "XMLHttpRequest",
                  },
                }
              );
              const deleteDetailResult = await deleteDetailResponse.json();
              if (!deleteDetailResponse.ok) {
                throw new Error(
                  deleteDetailResult.error || "Error al eliminar el detalle"
                );
              }
              alert("Detalle eliminado correctamente");
              window.location.reload();
            } else {
              alert("Opción inválida. Operación cancelada.");
              resolve();
            }
          } else {
            if (choiceNum === 1) {
              const newLiquidacionResponse = await fetch(
                `index.php?controller=liquidacion&action=createWithDetail`,
                {
                  method: "POST",
                  body: JSON.stringify({
                    detalle_id: detalleId,
                    user_id: idUsuarioDetalle,
                  }),
                  headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                  },
                }
              );
              const newLiquidacionResult = await newLiquidacionResponse.json();
              if (!newLiquidacionResponse.ok) {
                throw new Error(
                  newLiquidacionResult.error || "Error al crear una nueva liquidación"
                );
              }
              alert("Nueva liquidación creada correctamente");
              window.location.reload();
            } else if (choiceNum === 2) {
              const deleteDetailResponse = await fetch(
                `index.php?controller=liquidacion&action=deleteDetail&id=${detalleId}`,
                {
                  method: "POST",
                  headers: {
                    "X-Requested-With": "XMLHttpRequest",
                  },
                }
              );
              const deleteDetailResult = await deleteDetailResponse.json();
              if (!deleteDetailResponse.ok) {
                throw new Error(
                  deleteDetailResult.error || "Error al eliminar el detalle"
                );
              }
              alert("Detalle eliminado correctamente");
              window.location.reload();
            } else {
              alert("Opción inválida. Operación cancelada.");
              resolve();
            }
          }
          resolve();
        });
      });
    }

    const formData = new FormData();
    formData.append("detalle_id", detalleId);
    formData.append("action", action);
    formData.append(
      "motivo",
      prompt("Por favor, ingresa el motivo de esta acción:") || ""
    );

    const processResponse = await fetch(
      `index.php?controller=liquidacion&action=autorizarDetalle&id=${liquidacionId}`,
      {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      }
    );

    let result;
    const contentType = processResponse.headers.get("content-type");
    if (contentType && contentType.includes("application/json")) {
      result = await processResponse.json();
      if (!processResponse.ok) {
        throw new Error(result.error || "Error al procesar el detalle");
      }
    } else {
      const text = await processResponse.text();
      throw new Error(`Respuesta no es JSON válida: ${text}`);
    }

    alert(result.message || `Detalle ${detalleId} ${action} correctamente`);
    loadLiquidaciones();
  } catch (error) {
    console.error("Error al procesar el detalle:", error);
    alert(error.message || "Error al procesar el detalle. Intenta de nuevo.");
  }
}

async function exportToSap(id) {
    try {
        const response = await fetch(
            `index.php?controller=liquidacion&action=exportar&id=${id}`,
            {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        );

        // Log raw response for debugging
        const rawResponse = await response.text();
        console.log('Raw response:', rawResponse);

        if (!response.ok) {
            let errorData;
            try {
                errorData = JSON.parse(rawResponse);
            } catch (e) {
                throw new Error(`Error del servidor: ${response.status} - ${rawResponse}`);
            }

            if (
                response.status === 400 &&
                errorData.error === 'Esta liquidación ya ha sido exportada'
            ) {
                const confirmExport = confirm(
                    'Esta liquidación ya fue exportada. ¿Deseas volver a exportarla?'
                );
                if (confirmExport) {
                    const forceExportResponse = await fetch(
                        `index.php?controller=liquidacion&action=exportar&id=${id}&force=true`,
                        {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );
                    const forceRawResponse = await forceExportResponse.text();
                    console.log('Force export raw response:', forceRawResponse);
                    if (!forceExportResponse.ok) {
                        let forceErrorData;
                        try {
                            forceErrorData = JSON.parse(forceRawResponse);
                        } catch (e) {
                            throw new Error(`Error del servidor: ${forceExportResponse.status} - ${forceRawResponse}`);
                        }
                        throw new Error(
                            forceErrorData.error ||
                                `Error HTTP: ${forceExportResponse.status}`
                        );
                    }
                    // Handle the force export response as a download
                    const forceBlob = new Blob([forceRawResponse], { type: 'application/json' });
                    const forceUrl = window.URL.createObjectURL(forceBlob);
                    const forceLink = document.createElement('a');
                    forceLink.href = forceUrl;
                    forceLink.download = `export_liquidacion_${id}.json`;
                    document.body.appendChild(forceLink);
                    forceLink.click();
                    document.body.removeChild(forceLink);
                    window.URL.revokeObjectURL(forceUrl);
                    alert('Archivo JSON descargado exitosamente.');
                    loadLiquidaciones();
                    return;
                } else {
                    return;
                }
            }
            throw new Error(errorData.error || `Error HTTP: ${response.status}`);
        }

        // Handle the response as a downloadable file
        const blob = new Blob([rawResponse], { type: 'application/json' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `export_liquidacion_${id}.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        alert('Archivo JSON descargado exitosamente.');
        loadLiquidaciones();

    } catch (error) {
        console.error('Error al exportar a SAP:', error);
        alert(
            error.message ||
                'Error al exportar a SAP. Revisa los logs del servidor.'
        );
    }
}

// Fetch supervisors from the backend
async function fetchSupervisores() {
  try {
      const response = await fetch("index.php?controller=usuario&action=getSupervisores", {
          headers: {
              "X-Requested-With": "XMLHttpRequest",
          },
      });
      if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.error || `Error HTTP: ${response.status}`);
      }
      const supervisores = await response.json();
      return supervisores;
  } catch (error) {
      console.error("Error al cargar supervisores:", error);
      alert("Error al cargar la lista de supervisores: " + error.message);
      return [];
  }
}

async function fetchContadores() {
  try {
      const response = await fetch("index.php?controller=usuario&action=getContadores", {
          headers: {
              "X-Requested-With": "XMLHttpRequest",
          },
      });
      if (!response.ok) {
          let errorData;
          try {
              errorData = await response.json();
          } catch (jsonError) {
              // Handle non-JSON response
              const text = await response.text();
              console.error("Non-JSON response:", text);
              throw new Error(`HTTP error: ${response.status} - Invalid response format`);
          }
          throw new Error(errorData.error || `HTTP error: ${response.status}`);
      }
      const contadores = await response.json();
      console.log("Contadores disponibles:", contadores); // Debugging
      return contadores;
  } catch (error) {
      console.error("Error al cargar contadores:", error);
      alert("Error al cargar la lista de contadores: " + error.message);
      return [];
  }
}

// Ejemplo de uso
async function showContadoresModal() {
  const contadoresList = await fetchContadores();
  // Aquí podrías mostrar un modal con la lista de contadores, similar a showSupervisorModal
}

// Show the supervisor selection modal
async function showSupervisorModal() {
  const supervisorModal = document.getElementById('supervisorModal');
  const supervisorSelect = document.getElementById('supervisorSelect');

  if (!supervisorModal || !supervisorSelect) {
      console.error("Supervisor modal o select no encontrados en el DOM");
      alert("Error: No se encontró el modal de selección de supervisores. Intenta de nuevo.");
      return;
  }

  // Fetch supervisors and populate the dropdown
  const supervisoresList = await fetchSupervisores();
  supervisorSelect.innerHTML = '<option value="">Selecciona un supervisor...</option>';

  if (supervisoresList.length === 0) {
      supervisorSelect.innerHTML += '<option value="" disabled>No hay supervisores disponibles</option>';
  } else {
      supervisoresList.forEach((supervisor) => {
          const option = document.createElement("option");
          option.value = supervisor.id;
          option.textContent = `${supervisor.nombre} (${supervisor.email})`;
          supervisorSelect.appendChild(option);
      });
  }

  supervisorModal.classList.add("active");
}

// Close the supervisor selection modal
function closeSupervisorModal() {
  if (supervisorModal) {
    supervisorModal.classList.remove("active");
    supervisorSelect.value = ""; // Reset the selection
    currentLiquidacionId = null; // Clear the current liquidation ID
  }
}

// Confirm the supervisor selection and finalize the liquidation
async function confirmSupervisorSelection() {
  const supervisorId = supervisorSelect.value;
  if (!supervisorId) {
    alert("Por favor, selecciona un supervisor antes de continuar.");
    return;
  }

  if (!currentLiquidacionId) {
    alert("Error: No se ha seleccionado una liquidación para finalizar.");
    closeSupervisorModal();
    return;
  }

  try {
    const formData = new FormData();
    formData.append("supervisor_id", supervisorId);

    const response = await fetch(
      `index.php?controller=liquidacion&action=finalizar&id=${currentLiquidacionId}`,
      {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      }
    );

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.error || "Error al finalizar la liquidación");
    }

    const channel = new BroadcastChannel("liquidacion-estado");
    channel.postMessage({
      id: currentLiquidacionId,
      action: "estado-cambiado",
    });

    alert(result.message || "Liquidación finalizada correctamente");
    closeSupervisorModal();
    window.location.href = "index.php?controller=liquidacion&action=list";
  } catch (error) {
    console.error("Error al finalizar la liquidación:", error);
    alert("Error al finalizar la liquidación: " + error.message);
    closeSupervisorModal();
  }
}

async function finalizarLiquidacion(id) {
  if (!confirm("¿Estás seguro de que deseas finalizar esta liquidación?")) {
    return;
  }

  currentLiquidacionId = id; // Store the liquidation ID
  await showSupervisorModal(); // Show the modal to select a supervisor
}

function addFormValidations(id = null) {
  const form = document.querySelector("#modalForm #liquidacionFormInner");
  if (!form) {
    console.error(
      'No se encontró un elemento <form> con id="liquidacionFormInner" dentro de #modalForm'
    );
    return;
  }

  const fields = {
    id_caja_chica: { required: true },
    fecha_creacion: { required: true },
    fecha_inicio: {},
    fecha_fin: {},
    monto_total: { required: true, type: "number", min: 0 },
    estado: { required: true },
  };

  form.querySelectorAll("input, select").forEach((field) => {
    field.addEventListener("input", validateField);
  });

  async function validateField(e) {
    const fieldName = e.target.name;
    const value = e.target.value;
    const errorElement =
      form.querySelector(`.error[data-field="${fieldName}"]`) ||
      document.createElement("div");
    errorElement.className = "error";
    errorElement.setAttribute("data-field", fieldName);
    if (!form.contains(errorElement)) {
      e.target.parentNode.appendChild(errorElement);
    }

    errorElement.style.display = "none";
    e.target.classList.remove("invalid");

    if (fields[fieldName]) {
      if (fields[fieldName].required && !value) {
        errorElement.textContent = `${
          fieldName.charAt(0).toUpperCase() +
          fieldName.slice(1).replace(/_/g, " ")
        } es obligatorio.`;
        errorElement.style.display = "block";
        e.target.classList.add("invalid");
        return false;
      }
      if (fields[fieldName].type === "number" && isNaN(value)) {
        errorElement.textContent = `${
          fieldName.charAt(0).toUpperCase() +
          fieldName.slice(1).replace(/_/g, " ")
        } debe ser un número.`;
        errorElement.style.display = "block";
        e.target.classList.add("invalid");
        return false;
      }
      if (fields[fieldName].min && value < fields[fieldName].min) {
        errorElement.textContent = `${
          fieldName.charAt(0).toUpperCase() +
          fieldName.slice(1).replace(/_/g, " ")
        } debe ser mayor o igual a ${fields[fieldName].min}.`;
        errorElement.style.display = "block";
        e.target.classList.add("invalid");
        return false;
      }
    }

    if (fieldName === "fecha_inicio" || fieldName === "fecha_fin") {
      const fechaInicio = form.querySelector('[name="fecha_inicio"]').value;
      const fechaFin = form.querySelector('[name="fecha_fin"]').value;

      if (fechaInicio && fechaFin) {
        const fechaInicioDate = new Date(fechaInicio);
        const fechaFinDate = new Date(fechaFin);

        if (fechaInicioDate > fechaFinDate) {
          errorElement.textContent =
            "La fecha de inicio no puede ser mayor que la fecha de fin.";
          errorElement.style.display = "block";
          e.target.classList.add("invalid");
          return false;
        }
      }
    }

    return true;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    let isValid = true;
    const validations = await Promise.all(
      Array.from(form.querySelectorAll("input, select")).map((field) =>
        validateField({ target: field })
      )
    );
    isValid = validations.every((valid) => valid);

    if (isValid) {
      const formData = new FormData(form);
      try {
        const action = id
          ? updateLiquidation(id, formData)
          : createLiquidation(formData);
        const result = await action;
        alert(result.message || "Operación exitosa");
        closeModal();
        loadLiquidaciones();
      } catch (error) {
        console.error("Error al enviar formulario:", error);
        const errorElement =
          form.querySelector(".error:not([data-field])") ||
          document.createElement("div");
        errorElement.className = "error";
        errorElement.textContent =
          error.message || "Error al enviar el formulario. Intenta de nuevo.";
        errorElement.style.display = "block";
        if (!form.contains(errorElement)) {
          form.appendChild(errorElement);
        }
      }
    }
  });
}