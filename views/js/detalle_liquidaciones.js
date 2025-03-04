async function loadDetallesLiquidacion() {
    const tbody = document.querySelector('#detallesTable tbody');
    if (!tbody) {
        console.log('Tabla #detallesTable no encontrada, omitiendo carga de detalles');
        return;
    }

    try {
        const response = await fetch('index.php?controller=detalleliquidacion&action=list', {
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
        const detalles = await response.json();
        tbody.innerHTML = '';
        if (detalles.length > 0) {
            detalles.forEach(detalle => {
                tbody.innerHTML += `
                    <tr>
                        <td>${detalle.id}</td>
                        <td>${detalle.id_liquidacion}</td>
                        <td>${detalle.no_factura}</td>
                        <td>${detalle.nombre_proveedor}</td>
                        <td>${detalle.fecha}</td>
                        <td>${detalle.bien_servicio}</td>
                        <td>${detalle.t_gasto}</td>
                        <td>${detalle.p_unitario}</td>
                        <td>${detalle.total_factura}</td>
                        <td>${detalle.estado}</td>
                        <td>${detalle.rutas_archivos || ''}</td>
                        <td>
                            <button onclick="showEditForm(${detalle.id})">Editar</button>
                            <button onclick="deleteDetalleLiquidacion(${detalle.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="12">No hay detalles de liquidaciones registradas.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar detalles:', error.message);
        alert('No se pudo cargar la lista de detalles: ' + error.message + '. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

// Función para verificar permisos (placeholder)
function hasCreateDetallesPermission() {
    return true; // Simula que el usuario tiene permisos
}

async function createDetalleLiquidacion(data) {
    const response = await fetch('index.php?controller=detalleliquidacion&action=create', {
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
            throw new Error(`Error al crear detalle: ${errorData.error || text}`);
        } catch (parseError) {
            throw new Error(`Error al crear detalle: Respuesta no es JSON válida - ${text}`);
        }
    }
    return response.json();
}

async function updateDetalleLiquidacion(id, data) {
    const response = await fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`, {
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
            throw new Error(`Error al actualizar detalle: ${errorData.error || text}`);
        } catch (parseError) {
            throw new Error(`Error al actualizar detalle: Respuesta no es JSON válida - ${text}`);
        }
    }
    return response.json();
}

async function deleteDetalleLiquidacion(id) {
    const response = await fetch(`index.php?controller=detalleliquidacion&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al eliminar detalle: ${errorData.error || await response.text()}`);
    }
    if (response.ok) loadDetallesLiquidacion();
}

function showCreateForm() {
    const detalleForm = document.getElementById('detalleForm');
    if (!detalleForm) {
        console.error('Elemento #detalleForm no encontrado en la página');
        alert('Error: No se encontró el contenedor para el formulario. Por favor, recarga la página.');
        return;
    }

    fetch('index.php?controller=detalleliquidacion&action=create')
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Error al cargar formulario: ${response.status} - ${text}`);
                });
            }
            return response.text();
        })
        .then(html => {
            detalleForm.innerHTML = html;
            detalleForm.style.display = 'block';
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar formulario:', error.message);
            alert('No se pudo cargar el formulario: ' + error.message + '. Por favor, intenta de nuevo.');
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Error al cargar formulario: ${response.status} - ${text}`);
                });
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('detalleForm').innerHTML = html;
            document.getElementById('detalleForm').style.display = 'block';
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar formulario:', error.message);
            alert('No se pudo cargar el formulario: ' + error.message + '. Por favor, intenta de nuevo.');
        });
}

function cancelForm() {
    document.getElementById('detalleForm').style.display = 'none';
    document.getElementById('detalleForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('detalleFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> con ID #detalleFormInner. Verifica el HTML cargado:', document.getElementById('detalleForm')?.innerHTML || 'No se encontró #detalleForm');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

    const fields = {
        id_liquidacion: { required: true },
        no_factura: { required: true },
        nombre_proveedor: { required: true },
        fecha: { required: true },
        bien_servicio: { required: true },
        t_gasto: { required: true },
        p_unitario: { required: true, type: 'number', min: 0 },
        total_factura: { required: true, type: 'number', min: 0 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select, textarea').forEach(field => {
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
        form.querySelectorAll('input, select, textarea').forEach(field => {
            if (!validateField({ target: field })) isValid = false;
        });

        if (isValid) {
            const formData = new FormData(form);
            const id = formData.get('id');
            try {
                if (id) {
                    const result = await updateDetalleLiquidacion(id, formData);
                    if (result.message) {
                        window.location.reload();
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                } else {
                    const result = await createDetalleLiquidacion(formData);
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

// Ejecutar loadDetallesLiquidacion solo si estamos en la página de lista
if (document.querySelector('#detallesTable')) {
    loadDetallesLiquidacion();
}