async function loadLiquidaciones() {
    // Verificar si la tabla existe antes de continuar
    const tbody = document.querySelector('#liquidacionesTable tbody');
    if (!tbody) {
        console.log('Tabla #liquidacionesTable no encontrada, omitiendo carga de liquidaciones');
        return;
    }

    try {
        const urlParams = new URLSearchParams(window.location.search);
        const mode = urlParams.get('mode');
        const isAutorizarMode = mode === 'autorizar';
        const isRevisarMode = mode === 'revisar';
        console.log('Modo actual:', mode);
        console.log('isAutorizarMode:', isAutorizarMode);
        console.log('isRevisarMode:', isRevisarMode);

        const response = await fetch('index.php?controller=liquidacion&action=list', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
            } catch (parseError) {
                throw new Error(`Error HTTP: ${response.status} - Respuesta no es JSON válida: ${text}`);
            }
        }
        const liquidaciones = await response.json();
        console.log('Liquidaciones recibidas:', liquidaciones);

        tbody.innerHTML = '';
        if (liquidaciones.length > 0) {
            liquidaciones.forEach(liquidacion => {
                console.log('Procesando liquidación:', liquidacion);
                console.log('Estado de la liquidación:', liquidacion.estado);
                let acciones = '';
                if (isAutorizarMode) {
                    if (liquidacion.estado.trim() === 'PENDIENTE') {
                        acciones = `<a href="index.php?controller=liquidacion&action=autorizar&id=${liquidacion.id}">Autorizar/Rechazar</a>`;
                    } else if (liquidacion.estado === 'PENDIENTE_CORRECCIÓN') {
                        acciones = `Pendiente de corrección`;
                    } else if (liquidacion.estado === 'AUTORIZADO_POR_SUPERVISOR') {
                        acciones = `Autorizado por Supervisor`;
                    } else if (liquidacion.estado === 'RECHAZADO_POR_SUPERVISOR') {
                        acciones = `Rechazado por Supervisor`;
                    } else if (liquidacion.estado === 'AUTORIZADO_POR_CONTABILIDAD') {
                        acciones = `Autorizado por Contabilidad`;
                    } else if (liquidacion.estado === 'RECHAZADO_POR_CONTABILIDAD') {
                        acciones = `Rechazado por Contabilidad`;
                    } else {
                        acciones = `Estado no válido`;
                    }
                } else if (isRevisarMode) {
                    if (liquidacion.estado === 'AUTORIZADO_POR_SUPERVISOR') {
                        acciones = `<a href="index.php?controller=liquidacion&action=revisar&id=${liquidacion.id}">Revisar</a>`;
                    } else if (liquidacion.estado === 'PENDIENTE') {
                        acciones = `Pendiente de autorización del Supervisor`;
                    } else if (liquidacion.estado === 'PENDIENTE_CORRECCIÓN') {
                        acciones = `Pendiente de corrección`;
                    } else if (liquidacion.estado === 'RECHAZADO_POR_SUPERVISOR') {
                        acciones = `Rechazado por Supervisor`;
                    } else if (liquidacion.estado === 'AUTORIZADO_POR_CONTABILIDAD') {
                        if (liquidacion.exportado == 1) {
                            acciones = `Exportado`;
                        } else {
                            acciones = `<a href="index.php?controller=liquidacion&action=exportar&id=${liquidacion.id}">Exportar a SAP</a>`;
                        }
                    } else if (liquidacion.estado === 'RECHAZADO_POR_CONTABILIDAD') {
                        acciones = `Rechazado por Contabilidad`;
                    } else {
                        acciones = `Estado no válido`;
                    }
                } else {
                    acciones = `
                        <button onclick="showEditForm(${liquidacion.id})">Editar</button>
                        <button onclick="deleteLiquidacion(${liquidacion.id})">Eliminar</button>
                    `;
                }

                tbody.innerHTML += `
                    <tr>
                        <td>${liquidacion.id}</td>
                        <td>${liquidacion.id_caja_chica}</td>
                        <td>${liquidacion.fecha_creacion}</td>
                        <td>${liquidacion.monto_total}</td>
                        <td>${liquidacion.estado || 'Sin estado'}</td>
                        <td>${acciones}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6">No hay liquidaciones registradas.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar liquidaciones:', error.message);
        alert('No se pudo cargar la lista de liquidaciones: ' + error.message + '. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createLiquidacion(data) {
    const response = await fetch('index.php?controller=liquidacion&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const text = await response.text();
        try {
            const errorData = JSON.parse(text);
            throw new Error(`Error al crear liquidación: ${errorData.error || text}`);
        } catch (parseError) {
            throw new Error(`Error al crear liquidación: Respuesta no es JSON válida - ${text}`);
        }
    }
    return response.json();
}

async function updateLiquidacion(id, data) {
    const response = await fetch(`index.php?controller=liquidacion&action=update&id=${id}`, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const text = await response.text();
        try {
            const errorData = JSON.parse(text);
            throw new Error(`Error al actualizar liquidación: ${errorData.error || text}`);
        } catch (parseError) {
            throw new Error(`Error al actualizar liquidación: Respuesta no es JSON válida - ${text}`);
        }
    }
    return response.json();
}

async function deleteLiquidacion(id) {
    const response = await fetch(`index.php?controller=liquidacion&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al eliminar liquidación: ${errorData.error || await response.text()}`);
    }
    if (response.ok) loadLiquidaciones();
}

function showCreateForm() {
    fetch('index.php?controller=liquidacion&action=create')
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Error al cargar formulario: ${response.status} - ${text}`);
                });
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('liquidacionForm').innerHTML = html;
            document.getElementById('liquidacionForm').style.display = 'block';
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar formulario:', error.message);
            alert('No se pudo cargar el formulario: ' + error.message + '. Por favor, intenta de nuevo.');
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=liquidacion&action=update&id=${id}`)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Error al cargar formulario: ${response.status} - ${text}`);
                });
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('liquidacionForm').innerHTML = html;
            document.getElementById('liquidacionForm').style.display = 'block';
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar formulario:', error.message);
            alert('No se pudo cargar el formulario: ' + error.message + '. Por favor, intenta de nuevo.');
        });
}

function cancelForm() {
    document.getElementById('liquidacionForm').style.display = 'none';
    document.getElementById('liquidacionForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('liquidacionFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> con ID #liquidacionFormInner. Verifica el HTML cargado:', document.getElementById('liquidacionForm')?.innerHTML || 'No se encontró #liquidacionForm');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

    const fields = {
        id_caja_chica: { required: true },
        fecha_creacion: { required: true },
        monto_total: { required: true, type: 'number', min: 0 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value;
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`) || document.createElement('div');
        errorElement.className = 'error';
        errorElement.setAttribute('data-field', fieldName);

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].minLength && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].type === 'number' && isNaN(value)) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} debe ser un número.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].min && value < fields[fieldName].min) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} debe ser mayor o igual a ${fields[fieldName].min}.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            errorElement.style.display = 'none';
            e.target.classList.remove('invalid');
            return true;
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        form.querySelectorAll('input, select').forEach(field => {
            if (!validateField({ target: field })) isValid = false;
        });

        if (isValid) {
            const formData = new FormData(form);
            const id = formData.get('id');
            try {
                if (id) {
                    const result = await updateLiquidacion(id, formData);
                    if (result.message) {
                        window.location.reload();
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                } else {
                    const result = await createLiquidacion(formData);
                    if (result.message) {
                        window.location.reload();
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Error al enviar formulario:', error.message);
                console.log('Respuesta del servidor:', error.message);
                const errorElement = form.querySelector('.error') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al procesar la solicitud. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}

// Ejecutar loadLiquidaciones solo si estamos en la página de lista
if (document.querySelector('#liquidacionesTable')) {
    loadLiquidaciones();
}