const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

// Store liquidations data globally to access in autorizarLiquidacion
let liquidacionesData = [];
let correctedDetallesData = [];

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.userPermissions === 'undefined' || typeof window.userRole === 'undefined') {
        console.error('Error: userPermissions o userRole no están definidos.');
        alert('Error: No se pudieron cargar los permisos o el rol del usuario. Contacta al administrador.');
        return;
    }

    loadLiquidaciones();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && modal) {
        showEditForm(id);
    }
});

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
    }
}

async function loadLiquidaciones() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const mode = urlParams.get('mode') || '';
        const fetchUrl = mode ? `index.php?controller=liquidacion&action=list&mode=${mode}` : 'index.php?controller=liquidacion&action=list';

        const response = await fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            if (response.status === 403) {
                alert('No tienes permiso para ver esta lista. Serás redirigido.');
                window.location.href = 'index.php?controller=dashboard&action=index';
                return;
            }
            throw new Error(errorData.error || `Error HTTP: ${response.status}`);
        }
        const data = await response.json();
        liquidacionesData = data.liquidaciones;
        correctedDetallesData = data.corrected_detalles || [];

        const tbody = document.querySelector('#liquidacionesTable tbody');
        tbody.innerHTML = '';
        if (liquidacionesData.length > 0) {
            liquidacionesData.forEach(liquidacion => {
                const actions = [];
                const hasCorrections = liquidacion.detalles && Array.isArray(liquidacion.detalles) && liquidacion.detalles.some(detalle => detalle.estado === 'EN_CORRECTION');

                if (window.userPermissions.create_liquidaciones) {
                    if (liquidacion.estado === 'EN_PROCESO') {
                        actions.push(`<button onclick="showEditForm(${liquidacion.id})" class="edit-btn">Editar</button>`);
                        actions.push(`<button onclick="deleteLiquidation(${liquidacion.id})" class="delete-btn">Eliminar</button>`);
                        actions.push(`<button onclick="manageFacturas(${liquidacion.id})" class="edit-btn">Agregar Facturas</button>`);
                        actions.push(`<button onclick="finalizarLiquidacion(${liquidacion.id})" class="finalize-btn" ${hasCorrections ? 'disabled' : ''}>Finalizar</button>`);
                    }
                    if (['PENDIENTE_AUTORIZACION', 'PENDIENTE_REVISION_CONTABILIDAD', 'FINALIZADO', 'RECHAZADO_AUTORIZACION', 'RECHAZADO_POR_CONTABILIDAD'].includes(liquidacion.estado)) {
                        actions.push(`<button onclick="verLiquidacion(${liquidacion.id})" class="view-btn">Ver Liquidación</button>`);
                    }
                }

                if (window.userPermissions.autorizar_liquidaciones && window.userRole === 'SUPERVISOR') {
                    if (liquidacion.estado === 'PENDIENTE_AUTORIZACION') {
                        actions.push(`<button onclick="autorizarLiquidacion(${liquidacion.id}, 'autorizar')" class="edit-btn">Autorizar</button>`);
                    }
                }
                if (window.userPermissions.revisar_liquidaciones && window.userRole === 'CONTABILIDAD') {
                    if (liquidacion.estado === 'PENDIENTE_REVISION_CONTABILIDAD') {
                        actions.push(`<button onclick="autorizarLiquidacion(${liquidacion.id}, 'revisar')" class="edit-btn">Revisar</button>`);
                    }
                    if (liquidacion.estado === 'FINALIZADO') {
                        actions.push(`<button onclick="exportToSap(${liquidacion.id})" class="export-btn">Exportar a SAP</button>`);
                    }
                }

                const actionsHtml = actions.join(' ');
                const estado = liquidacion.estado && liquidacion.estado !== 'N/A' ? liquidacion.estado : 'EN_PROCESO';

                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${liquidacion.id}</td>
                        <td data-label="Caja Chica">${liquidacion.nombre_caja_chica || 'N/A'}</td>
                        <td data-label="Fecha Creación">${liquidacion.fecha_creacion || 'N/A'}</td>
                        <td data-label="Fecha Inicio">${liquidacion.fecha_inicio || 'N/A'}</td>
                        <td data-label="Fecha Fin">${liquidacion.fecha_fin || 'N/A'}</td>
                        <td data-label="Monto Total">${parseFloat(liquidacion.monto_total || 0).toFixed(2)}</td>
                        <td data-label="Estado">${estado}</td>
                        <td data-label="Acciones">${actionsHtml}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="8">No hay liquidaciones disponibles.</td></tr>';
        }

        const correctedDetallesSection = document.querySelector('#correctedDetallesSection');
        const correctedDetallesTbody = document.querySelector('#correctedDetallesTable tbody');
        if (window.userRole === 'SUPERVISOR' && mode === 'autorizar' && correctedDetallesData.length > 0) {
            correctedDetallesSection.style.display = 'block';
            correctedDetallesTbody.innerHTML = '';
            correctedDetallesData.forEach(detalle => {
                const actions = [];
                if (window.userPermissions.autorizar_liquidaciones && window.userRole === 'SUPERVISOR') {
                    actions.push(`<button onclick="autorizarDetalle(${detalle.id}, ${detalle.liquidacion_id}, 'autorizar')" class="edit-btn">Autorizar</button>`);
                    actions.push(`<button onclick="autorizarDetalle(${detalle.id}, ${detalle.liquidacion_id}, 'rechazar')" class="delete-btn">Rechazar</button>`);
                    actions.push(`<button onclick="autorizarDetalle(${detalle.id}, ${detalle.liquidacion_id}, 'descartar')" class="finalize-btn">Descartar</button>`);
                }
                const actionsHtml = actions.join(' ');

                let archivosHtml = 'N/A';
                if (detalle.rutas_archivos) {
                    try {
                        const rutas = typeof detalle.rutas_archivos === 'string' ? JSON.parse(detalle.rutas_archivos) : detalle.rutas_archivos;
                        if (Array.isArray(rutas) && rutas.length > 0) {
                            archivosHtml = rutas.map(ruta => {
                                const normalizedPath = ruta.startsWith('uploads/') ? ruta : `uploads/${ruta.replace(/^\/+/, '')}`;
                                return `<div><a href="../${normalizedPath}" target="_blank">Ver Archivo</a></div>`;
                            }).join('');
                        } else if (typeof rutas === 'string' && rutas.trim().length > 0) {
                            const normalizedPath = rutas.startsWith('uploads/') ? rutas : `uploads/${rutas.replace(/^\/+/, '')}`;
                            archivosHtml = `<div><a href="../${normalizedPath}" target="_blank">Ver Archivo</a></div>`;
                        }
                    } catch (e) {
                        console.warn('Error parsing rutas_archivos, treating as single path:', e);
                        if (detalle.rutas_archivos.trim().length > 0) {
                            const normalizedPath = detalle.rutas_archivos.startsWith('uploads/') ? detalle.rutas_archivos : `uploads/${detalle.rutas_archivos.replace(/^\/+/, '')}`;
                            archivosHtml = `<div><a href="../${normalizedPath}" target="_blank">Ver Archivo</a></div>`;
                        }
                    }
                }

                correctedDetallesTbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${detalle.id || 'N/A'}</td>
                        <td data-label="Tipo de Documento">${detalle.tipo_documento || 'N/A'}</td>
                        <td data-label="No. Factura">${detalle.no_factura || 'N/A'}</td>
                        <td data-label="Proveedor">${detalle.nombre_proveedor || 'N/A'}</td>
                        <td data-label="NIT">${detalle.nit_proveedor || 'N/A'}</td>
                        <td data-label="DPI">${detalle.dpi || 'N/A'}</td>
                        <td data-label="Cantidad">${detalle.cantidad || 'N/A'}</td>
                        <td data-label="Serie">${detalle.serie || 'N/A'}</td>
                        <td data-label="Centro de Costo">${detalle.nombre_centro_costo || 'N/A'}</td>
                        <td data-label="Tipo de Gasto">${detalle.t_gasto || 'N/A'}</td>
                        <td data-label="Tipo de Combustible">${detalle.tipo_combustible || 'N/A'}</td>
                        <td data-label="Cuenta Contable">${detalle.cuenta_contable_nombre || 'N/A'}</td>
                        <td data-label="Fecha">${detalle.fecha || 'N/A'}</td>
                        <td data-label="Subtotal">${parseFloat(detalle.subtotal || 0).toFixed(2)}</td>
                        <td data-label="IVA">${parseFloat(detalle.iva || 0).toFixed(2)}</td>
                        <td data-label="IDP">${parseFloat(detalle.idp || 0).toFixed(2)}</td>
                        <td data-label="INGUAT">${parseFloat(detalle.inguat || 0).toFixed(2)}</td>
                        <td data-label="Total Bruto">${parseFloat(detalle.total_factura || 0).toFixed(2)}</td>
                        <td data-label="Estado">${detalle.estado || 'N/A'}</td>
                        <td data-label="Archivos">${archivosHtml}</td>
                        <td data-label="Acciones">${actionsHtml}</td>
                    </tr>
                `;
            });
        } else {
            correctedDetallesSection.style.display = 'none';
        }
    } catch (error) {
        console.error('Error al cargar liquidaciones:', error);
        alert('Error al cargar las liquidaciones. Intenta de nuevo.');
    }
}

function verLiquidacion(id) {
    window.location.href = `index.php?controller=liquidacion&action=ver&id=${id}`;
}

function manageFacturas(id) {
    window.location.href = `index.php?controller=liquidacion&action=manageFacturas&id=${id}`;
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=liquidacion&action=create', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addFormValidations();
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function showEditForm(id) {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch(`index.php?controller=liquidacion&action=update&id=${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addFormValidations(id);
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function createLiquidation(data) {
    const response = await fetch('index.php?controller=liquidacion&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    const result = await response.json();
    if (!response.ok) {
        throw new Error(result.error || 'Error al crear liquidación');
    }
    return result;
}

async function updateLiquidation(id, data) {
    const response = await fetch(`index.php?controller=liquidacion&action=update&id=${id}`, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    const result = await response.json();
    if (!response.ok) {
        throw new Error(result.error || 'Error al actualizar liquidación');
    }
    return result;
}

async function deleteLiquidation(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta liquidación?')) return;

    try {
        const response = await fetch(`index.php?controller=liquidacion&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Error al eliminar la liquidación');
        }

        alert(result.message || 'Liquidación eliminada correctamente');
        loadLiquidaciones();
    } catch (error) {
        console.error('Error al eliminar liquidación:', error);
        alert(error.message || 'Error al eliminar la liquidación. Intenta de nuevo.');
    }
}

async function autorizarLiquidacion(id, mode) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentMode = urlParams.get('mode') || '';
    window.location.href = `index.php?controller=liquidacion&action=${mode}&id=${id}`;
}

async function autorizarDetalle(detalleId, liquidacionId, action) {
    if (!confirm(`¿Estás seguro de que deseas ${action} este detalle?`)) return;

    try {
        // Fetch liquidation state and detail information
        const stateResponse = await fetch(`index.php?controller=liquidacion&action=getEstado&id=${liquidacionId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!stateResponse.ok) {
            const errorText = await stateResponse.text();
            throw new Error(`Error al obtener el estado de la liquidación: ${errorText}`);
        }
        const stateData = await stateResponse.json();
        const estadoLiquidacion = stateData.estado || 'N/A';

        const detailResponse = await fetch(`index.php?controller=liquidacion&action=getDetalleInfo&id=${detalleId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!detailResponse.ok) {
            const errorText = await detailResponse.text();
            throw new Error(`Error al obtener la información del detalle: ${errorText}`);
        }
        const detailData = await detailResponse.json();
        const correccionComentario = detailData.correccion_comentario || '';

        // Check for SUPERVISOR role, FINALIZADO state, and presence of correccion_comentario
        if (window.userRole === 'SUPERVISOR' && estadoLiquidacion === 'FINALIZADO' && correccionComentario.trim().length > 0) {
            // Fetch EN_PROCESO liquidations
            const enProcesoResponse = await fetch(`index.php?controller=liquidacion&action=getEnProcesoLiquidaciones`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!enProcesoResponse.ok) {
                const errorText = await enProcesoResponse.text();
                throw new Error(`Error al obtener liquidaciones en proceso: ${errorText}`);
            }
            const enProcesoData = await enProcesoResponse.json();
            const enProcesoLiquidaciones = enProcesoData.liquidaciones || [];

            // Create a custom dialog for user choice
            const choice = await new Promise(resolve => {
                let message = 'Ya no se puede autorizar ya que la liquidación fue finalizada. ¿Qué deseas hacer?\n';
                message += '1. Iniciar una nueva liquidación con este detalle\n';
                if (enProcesoLiquidaciones.length > 0) {
                    message += '2. Agregar el detalle a una liquidación en proceso\n';
                }
                message += `${enProcesoLiquidaciones.length > 0 ? '3' : '2'}. Eliminar el detalle (si tiene comentario de corrección)`;

                const userChoice = prompt(message);
                resolve(userChoice);
            });

            const choiceNum = parseInt(choice);
            if (enProcesoLiquidaciones.length > 0) {
                if (choiceNum === 1) {
                    // Option 1: Start a new liquidation
                    const newLiquidacionResponse = await fetch(`index.php?controller=liquidacion&action=createWithDetail`, {
                        method: 'POST',
                        body: JSON.stringify({ detalle_id: detalleId }),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const contentType = newLiquidacionResponse.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await newLiquidacionResponse.text();
                        throw new Error(`Respuesta no es JSON válida: ${text}`);
                    }
                    const newLiquidacionResult = await newLiquidacionResponse.json();
                    if (!newLiquidacionResponse.ok) {
                        throw new Error(newLiquidacionResult.error || 'Error al crear una nueva liquidación');
                    }
                    alert('Nueva liquidación creada correctamente');
                } else if (choiceNum === 2) {
                    // Option 2: Add to an existing EN_PROCESO liquidation
                    const enProcesoOptions = enProcesoLiquidaciones.map((liq, index) => `${index + 1}. Liquidación ID: ${liq.id} (Caja Chica: ${liq.nombre_caja_chica})`).join('\n');
                    const selectedIndex = parseInt(prompt(`Selecciona una liquidación en proceso:\n${enProcesoOptions}`)) - 1;
                    if (selectedIndex >= 0 && selectedIndex < enProcesoLiquidaciones.length) {
                        const selectedLiquidacionId = enProcesoLiquidaciones[selectedIndex].id;
                        const addDetailResponse = await fetch(`index.php?controller=liquidacion&action=addDetailToLiquidacion`, {
                            method: 'POST',
                            body: JSON.stringify({ detalle_id: detalleId, liquidacion_id: selectedLiquidacionId }),
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const contentType = addDetailResponse.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await addDetailResponse.text();
                            throw new Error(`Respuesta no es JSON válida: ${text}`);
                        }
                        const addDetailResult = await addDetailResponse.json();
                        if (!addDetailResponse.ok) {
                            throw new Error(addDetailResult.error || 'Error al agregar el detalle a la liquidación');
                        }
                        alert('Detalle agregado a la liquidación en proceso correctamente');
                    } else {
                        alert('Selección inválida. Operación cancelada.');
                        return;
                    }
                } else if (choiceNum === 3) {
                    // Option 3: Delete the detail if it has a correccion_comentario
                    const deleteDetailResponse = await fetch(`index.php?controller=liquidacion&action=deleteDetail&id=${detalleId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const contentType = deleteDetailResponse.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await deleteDetailResponse.text();
                        throw new Error(`Respuesta no es JSON válida: ${text}`);
                    }
                    const deleteDetailResult = await deleteDetailResponse.json();
                    if (!deleteDetailResponse.ok) {
                        throw new Error(deleteDetailResult.error || 'Error al eliminar el detalle');
                    }
                    alert('Detalle eliminado correctamente');
                } else {
                    alert('Opción inválida. Operación cancelada.');
                    return;
                }
            } else {
                if (choiceNum === 1) {
                    // Option 1: Start a new liquidation
                    const newLiquidacionResponse = await fetch(`index.php?controller=liquidacion&action=createWithDetail`, {
                        method: 'POST',
                        body: JSON.stringify({ detalle_id: detalleId }),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const contentType = newLiquidacionResponse.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await newLiquidacionResponse.text();
                        throw new Error(`Respuesta no es JSON válida: ${text}`);
                    }
                    const newLiquidacionResult = await newLiquidacionResponse.json();
                    if (!newLiquidacionResponse.ok) {
                        throw new Error(newLiquidacionResult.error || 'Error al crear una nueva liquidación');
                    }
                    alert('Nueva liquidación creada correctamente');
                } else if (choiceNum === 2) {
                    // Option 3: Delete the detail if it has a correccion_comentario
                    const deleteDetailResponse = await fetch(`index.php?controller=liquidacion&action=deleteDetail&id=${detalleId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const contentType = deleteDetailResponse.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await deleteDetailResponse.text();
                        throw new Error(`Respuesta no es JSON válida: ${text}`);
                    }
                    const deleteDetailResult = await deleteDetailResponse.json();
                    if (!deleteDetailResponse.ok) {
                        throw new Error(deleteDetailResult.error || 'Error al eliminar el detalle');
                    }
                    alert('Detalle eliminado correctamente');
                } else {
                    alert('Opción inválida. Operación cancelada.');
                    return;
                }
            }

            loadLiquidaciones();
            return; // Exit after handling the FINALIZADO case
        }

        // Proceed with normal authorization flow
        const formData = new FormData();
        formData.append('detalle_id', detalleId);
        formData.append('action', action);
        formData.append('motivo', prompt('Por favor, ingresa el motivo de esta acción:'));

        const processResponse = await fetch(`index.php?controller=liquidacion&action=autorizarDetalle&id=${liquidacionId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const contentType = processResponse.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await processResponse.text();
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }
        const result = await processResponse.json();
        if (!processResponse.ok) {
            throw new Error(result.error || 'Error al procesar el detalle');
        }

        alert(result.message || `Detalle ${action} correctamente`);
        loadLiquidaciones();
    } catch (error) {
        console.error('Error al procesar el detalle:', error);
        alert(error.message || 'Error al procesar el detalle. Intenta de nuevo.');
    }
}

async function exportToSap(id) {
    try {
        const response = await fetch(`index.php?controller=liquidacion&action=exportar&id=${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            if (response.status === 400 && errorData.error === 'Esta liquidación ya ha sido exportada') {
                const confirmExport = confirm('Esta liquidación ya fue exportada. ¿Deseas volver a exportarla?');
                if (confirmExport) {
                    const forceExportResponse = await fetch(`index.php?controller=liquidacion&action=exportar&id=${id}&force=true`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!forceExportResponse.ok) {
                        const forceErrorData = await forceExportResponse.json();
                        throw new Error(forceErrorData.error || `Error HTTP: ${forceExportResponse.status}`);
                    }
                    const blob = await forceExportResponse.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `liquidacion_${id}_${new Date().toISOString().replace(/[:.]/g, '-')}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    alert('Exportación a SAP completada. Revisa tu carpeta de descargas.');
                    loadLiquidaciones();
                    return;
                } else {
                    return;
                }
            }
            throw new Error(errorData.error || `Error HTTP: ${response.status}`);
        }
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `liquidacion_${id}_${new Date().toISOString().replace(/[:.]/g, '-')}.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        alert('Exportación a SAP completada. Revisa tu carpeta de descargas.');
        loadLiquidaciones();
    } catch (error) {
        console.error('Error al exportar a SAP:', error);
        alert(error.message || 'Error al exportar a SAP. Intenta de nuevo.');
    }
}

async function finalizarLiquidacion(id) {
    if (!confirm('¿Estás seguro de que deseas finalizar esta liquidación?')) {
        return;
    }

    try {
        const response = await fetch(`index.php?controller=liquidacion&action=finalizar&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Error al finalizar la liquidación');
        }

        const channel = new BroadcastChannel('liquidacion-estado');
        channel.postMessage({ id: id, action: 'estado-cambiado' });

        alert(result.message || 'Liquidación finalizada correctamente');
        window.location.href = 'index.php?controller=liquidacion&action=list';
    } catch (error) {
        console.error('Error al finalizar la liquidación:', error);
        alert('Error al finalizar la liquidación: ' + error.message);
    }
}

function addFormValidations(id = null) {
    const form = document.querySelector('#modalForm #liquidacionFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="liquidacionFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        id_caja_chica: { required: true },
        fecha_creacion: { required: true },
        fecha_inicio: {},
        fecha_fin: {},
        monto_total: { required: true, type: 'number', min: 0 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value;
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`) || document.createElement('div');
        errorElement.className = 'error';
        errorElement.setAttribute('data-field', fieldName);
        if (!form.contains(errorElement)) {
            e.target.parentNode.appendChild(errorElement);
        }

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].type === 'number' && isNaN(value)) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe ser un número.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].min && value < fields[fieldName].min) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe ser mayor o igual a ${fields[fieldName].min}.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
        }

        if (fieldName === 'fecha_inicio' || fieldName === 'fecha_fin') {
            const fechaInicio = form.querySelector('[name="fecha_inicio"]').value;
            const fechaFin = form.querySelector('[name="fecha_fin"]').value;

            if (fechaInicio && fechaFin) {
                const fechaInicioDate = new Date(fechaInicio);
                const fechaFinDate = new Date(fechaFin);

                if (fechaInicioDate > fechaFinDate) {
                    errorElement.textContent = 'La fecha de inicio no puede ser mayor que la fecha de fin.';
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
        }

        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        const validations = await Promise.all(
            Array.from(form.querySelectorAll('input, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            try {
                const action = id ? updateLiquidation(id, formData) : createLiquidation(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadLiquidaciones();
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al enviar el formulario. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
            }
        }
    });
}