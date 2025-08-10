const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    const id = urlParams.get('id');
    if (action === 'list' || action === 'revisar') {
        loadDetalles();
    }
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

async function loadDetalles() {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');

    if (!['list', 'revisar'].includes(action)) {
        return;
    }

    const tbody = document.querySelector('#detallesLiquidacionesTable tbody');
    if (!tbody) {
        console.error('Tabla #detallesLiquidacionesTable tbody no encontrada en el DOM');
        return;
    }

    try {
        const mode = urlParams.get('mode') || '';
        let url = `index.php?controller=detalleliquidacion&action=list${mode ? '&mode=' + mode : ''}`;
        if (action === 'revisar') {
            url = `index.php?controller=detalleliquidacion&action=revisar`;
        }

        const response = await fetch(url, {
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
        const detalles = Array.isArray(data) ? data : [];
        tbody.innerHTML = '';
        if (detalles.length > 0) {
            detalles.forEach(detalle => {
                const archivos = detalle.rutas_archivos && typeof detalle.rutas_archivos === 'string' ? JSON.parse(detalle.rutas_archivos) : (Array.isArray(detalle.rutas_archivos) ? detalle.rutas_archivos : []);
                const archivosLinks = archivos.length > 0 ? archivos.map(ruta => `<a href="../uploads/${ruta}" target="_blank">Ver</a>`).join(' ') : 'Sin archivos';
                let actions = '';
                if (userPermissions.revisar_liquidaciones) {
                    actions += `
                      <button><a href="index.php?controller=detalleliquidacion&action=revisar&id=${detalle.id}" class="revisar-btn">Revisar</a></button>
                      
                    `;
                }
                if (userPermissions.create_detalles) {
                    actions += `
                        <button onclick="showEditForm(${detalle.id})" class="edit-btn">Editar</button>
                        <button onclick="deleteDetalle(${detalle.id})" class="delete-btn">Borrar</button>
                    `;
                }
                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${detalle.id}</td>
                        <td data-label="Liquidación">${detalle.liquidacion || 'N/A'}</td>
                        <td data-label="Factura">${detalle.no_factura}</td>
                        <td data-label="Proveedor">${detalle.nombre_proveedor || 'N/A'}</td>
                        <td data-label="Fecha">${detalle.fecha || 'N/A'}</td>
                        <td data-label="Bien/Servicio">${detalle.bien_servicio || 'N/A'}</td>
                        <td data-label="Tipo de Gasto">${detalle.t_gasto || 'N/A'}</td>
                        <td data-label="Precio Unitario">${parseFloat(detalle.p_unitario || 0).toFixed(2)}</td>
                        <td data-label="Total Factura">${parseFloat(detalle.total_factura || 0).toFixed(2)}</td>
                        <td data-label="Estado">${detalle.estado || 'N/A'}</td>
                        <td data-label="Archivos">${archivosLinks}</td>
                        <td data-label="Acciones">${actions}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="12">No hay detalles de liquidaciones disponibles.</td></tr>';
        }
        return detalles;
    } catch (error) {
        console.error('Error al cargar detalles:', error);
        tbody.innerHTML = '<tr><td colspan="12">Error al cargar la lista de detalles. Intenta de nuevo.</td></tr>';
    }
}

async function showRevisarForm(id) {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario.');
        return;
    }

    try {
        const response = await fetch(`index.php?controller=detalleliquidacion&action=revisar&id=${id}`, {
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

        const form = document.querySelector('#modalForm #detalleFormInner');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                try {
                    const submitResponse = await fetch(`index.php?controller=detalleliquidacion&action=revisar&id=${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!submitResponse.ok) {
                        const errorData = await submitResponse.json();
                        throw new Error(errorData.error || 'Error al registrar la revisión');
                    }
                    const result = await submitResponse.json();
                    alert(result.message || 'Revisión registrada correctamente');
                    closeModal();
                    loadDetalles();
                } catch (error) {
                    console.error('Error al registrar revisión:', error);
                    alert(error.message || 'Error al registrar la revisión.');
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar el formulario de revisión:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=detalleliquidacion&action=create', {
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
        const response = await fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`, {
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

async function createDetalle(data) {
    const dataObj = {};
    for (let [key, value] of data.entries()) {
        if (key !== 'archivos[]') {
            dataObj[key] = value;
        }
    }
    console.log('Datos enviados al servidor:', dataObj);
    console.log('Archivos enviados:', data.getAll('archivos[]'));

    const response = await fetch('index.php?controller=detalleliquidacion&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const text = await response.text();
    console.log('Respuesta del servidor:', text);

    try {
        const result = JSON.parse(text);
        if (!response.ok) {
            throw new Error(result.error || 'Error al crear detalle');
        }
        return result;
    } catch (parseError) {
        throw new Error('Error al procesar la respuesta del servidor: ' + text);
    }
}

async function updateDetalle(id, data) {
    const dataObj = {};
    for (let [key, value] of data.entries()) {
        if (key !== 'archivos[]') {
            dataObj[key] = value;
        }
    }
    console.log('Datos enviados al servidor:', dataObj);
    console.log('Archivos enviados:', data.getAll('archivos[]'));

    const response = await fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const text = await response.text();
    console.log('Respuesta del servidor:', text);

    try {
        const result = JSON.parse(text);
        if (!response.ok) {
            throw new Error(result.error || 'Error al actualizar detalle');
        }
        return result;
    } catch (parseError) {
        throw new Error('Error al procesar la respuesta del servidor: ' + text);
    }
}

async function deleteDetalle(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este detalle de liquidación?')) return;

    try {
        const response = await fetch(`index.php?controller=detalleliquidacion&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar detalle');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Detalle de liquidación eliminado');
        loadDetalles();
    } catch (error) {
        console.error('Error al eliminar detalle:', error);
        alert(error.message || 'Error al eliminar detalle. Intenta de nuevo.');
    }
}

async function revisarDetalle(id) {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch(`index.php?controller=detalleliquidacion&action=revisar&id=${id}`, {
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

        // Añadir manejador de eventos para el formulario de revisión
        const form = document.querySelector('#modalForm #detalleFormInner');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                try {
                    const submitResponse = await fetch(`index.php?controller=detalleliquidacion&action=revisar&id=${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!submitResponse.ok) {
                        const errorData = await submitResponse.json();
                        throw new Error(errorData.error || 'Error al registrar la revisión');
                    }
                    const result = await submitResponse.json();
                    alert(result.message || 'Revisión registrada correctamente');
                    closeModal();
                    loadDetalles();
                } catch (error) {
                    console.error('Error al registrar revisión:', error);
                    alert(error.message || 'Error al registrar la revisión. Intenta de nuevo.');
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar el formulario de revisión:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addFormValidations(id = null) {
    const form = document.querySelector('#modalForm #detalleFormInner'); // Cambiado de detalleLiquidacionFormInner a detalleFormInner
    if (!form) {
        console.error('No se encontró un elemento <form> con id="detalleFormInner" dentro de #modalForm');
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
        estado: { required: true },
        archivos: { maxSize: 5 * 1024 * 1024, allowedTypes: ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'] } // 5 MB
    };

    form.querySelectorAll('input, select').forEach(field => {
        if (field.type !== 'file') {
            field.addEventListener('input', validateField);
        }
    });

    // Validación específica para el campo de archivos
    const fileInput = form.querySelector('input[name="archivos"]');
    if (fileInput) {
        fileInput.addEventListener('change', validateFiles);
    }

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
        return true;
    }

    async function validateFiles(e) {
        const files = e.target.files;
        const errorElement = form.querySelector(`.error[data-field="archivos"]`) || document.createElement('div');
        errorElement.className = 'error';
        errorElement.setAttribute('data-field', 'archivos');
        if (!form.contains(errorElement)) {
            e.target.parentNode.appendChild(errorElement);
        }

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        const maxSize = fields.archivos.maxSize;
        const allowedTypes = fields.archivos.allowedTypes;
        const errors = [];

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Validar tipo de archivo
            if (!allowedTypes.includes(file.type)) {
                errors.push(`El archivo ${file.name} tiene un tipo no permitido. Solo se permiten PDF, PNG, JPG y JPEG.`);
            }

            // Validar tamaño de archivo
            if (file.size > maxSize) {
                errors.push(`El archivo ${file.name} excede el tamaño máximo permitido de 5 MB.`);
            }
        }

        if (errors.length > 0) {
            // Crear una lista de errores
            const errorList = document.createElement('ul');
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            errorElement.innerHTML = ''; // Limpiar contenido previo
            errorElement.appendChild(errorList);
            errorElement.style.display = 'block';
            e.target.classList.add('invalid');
            return false;
        }

        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;

        // Validar campos de texto, select, etc.
        const fieldValidations = await Promise.all(
            Array.from(form.querySelectorAll('input, select')).map(field => {
                if (field.type !== 'file') {
                    return validateField({ target: field });
                }
                return true;
            })
        );

        // Validar archivos
        const fileValidation = fileInput ? await validateFiles({ target: fileInput }) : true;

        isValid = fieldValidations.every(valid => valid) && fileValidation;

        if (isValid) {
            const formData = new FormData(form);
            try {
                const action = id ? updateDetalle(id, formData) : createDetalle(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadDetalles();
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.innerHTML = ''; // Limpiar contenido previo
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }

                // Si el error viene del servidor, puede contener saltos de línea (\n)
                const errorMessage = error.message || 'Error al enviar el formulario. Intenta de nuevo.';
                if (errorMessage.includes('\n')) {
                    const errorLines = errorMessage.split('\n').filter(line => line.trim() !== '');
                    const errorList = document.createElement('ul');
                    errorLines.forEach(line => {
                        const li = document.createElement('li');
                        li.textContent = line.replace(/^- /, ''); // Eliminar el prefijo "- " para evitar duplicados
                        errorList.appendChild(li);
                    });
                    errorElement.appendChild(errorList);
                } else {
                    errorElement.textContent = errorMessage;
                }

                errorElement.style.display = 'block';
            }
        }
    });
}
