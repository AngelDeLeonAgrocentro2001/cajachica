const modal = document.getElementById('modal');
const modalContent = document.getElementById('modalContent');
let debounceTimeout;
let currentFacturaId = null; // Variable para almacenar el ID de la factura que se está editando

document.addEventListener('DOMContentLoaded', () => {
    loadFacturas();
    loadCuentas();
    loadBases();

    const searchInput = document.querySelector('#search');
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            loadFacturas();
        }, 300);
    });

    const cuentaSelect = document.querySelector('#cuentaSelect');
    cuentaSelect.addEventListener('change', () => {
        loadFacturas();
    });
});

let facturas = [];

async function loadFacturas() {
    try {
        const searchTerm = document.querySelector('#search').value.trim();
        const cuentaId = document.querySelector('#cuentaSelect').value;
        const mode = new URLSearchParams(window.location.search).get('mode') || '';

        let url = `index.php?controller=factura&action=list${mode ? '&mode=' + mode : ''}`;
        if (searchTerm) {
            url += `&search=${encodeURIComponent(searchTerm)}`;
        }
        if (cuentaId) {
            url += `&cuenta_id=${encodeURIComponent(cuentaId)}`;
        }

        console.log('Solicitando facturas desde:', url);

        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const responseText = await response.text();
        console.log('Respuesta del servidor:', responseText);

        if (!response.ok) {
            let errorData;
            try {
                errorData = JSON.parse(responseText);
            } catch (e) {
                throw new Error(`Error HTTP: ${response.status} - Respuesta no es JSON válida: ${responseText}`);
            }
            if (response.status === 401) {
                throw new Error(errorData.error || 'No autorizado');
            }
            throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
        }

        const facturasData = JSON.parse(responseText);
        if (!Array.isArray(facturasData)) {
            throw new Error('La respuesta no es un array válido');
        }
        facturas = facturasData;

        const tbody = document.querySelector('#facturasTable tbody');
        tbody.innerHTML = '';
        if (facturas.length > 0) {
            facturas.forEach(factura => {
                const fechaObj = new Date(factura.fecha);
                const fecha = isNaN(fechaObj) ? 'N/A' : fechaObj.toLocaleDateString('es-ES');

                const monto = parseFloat(factura.monto);
                const montoFormateado = isNaN(monto) ? 'N/A' : '$' + monto.toFixed(2);

                let actionButtons = `
                    <button class="view-btn" onclick="showViewForm(${factura.id})">Ver</button>
                    <button class="delete-btn" onclick="deleteFactura(${factura.id})">Eliminar</button>
                `;

                if (mode === 'autorizar' && factura.estado === 'PENDIENTE') {
                    actionButtons = `<button class="authorize-btn" onclick="showAuthorizeForm(${factura.id})">Autorizar</button>`;
                } else if (mode === 'revisar' && factura.estado === 'APROBADO') {
                    actionButtons = `<button class="review-btn" onclick="showReviewForm(${factura.id})">Revisar</button>`;
                }

                tbody.innerHTML += `
                    <tr>
                        <td>${factura.numero_factura || 'N/A'}</td>
                        <td>${factura.cuenta_nombre || 'Sin cuenta'}</td>
                        <td>${factura.base_nombre || 'Sin base'}</td>
                        <td>${factura.proveedor || 'N/A'}</td>
                        <td>${fecha}</td>
                        <td>${montoFormateado}</td>
                        <td>${factura.estado || 'N/A'}</td>
                        <td>${actionButtons}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="8">No se encontraron facturas con el criterio de búsqueda.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar facturas:', error.message || error);
        alert('No se pudo cargar la lista de facturas. Por favor, intenta de nuevo. Detalle: ' + (error.message || 'Error desconocido'));
    }
}

async function loadCuentas() {
    try {
        const response = await fetch('index.php?controller=cuentacontable&action=list', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            let errorData;
            try {
                errorData = JSON.parse(text);
            } catch (e) {
                throw new Error(`Error HTTP: ${response.status} - Respuesta no es JSON válida: ${text}`);
            }
            throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
        }
        const cuentas = await response.json();
        const cuentaSelect = document.querySelector('#cuentaSelect');
        const cuentaFormSelect = document.querySelector('#cuenta_id');
        cuentaSelect.innerHTML = '<option value="">Seleccione una Cuenta</option>';
        if (cuentaFormSelect) {
            cuentaFormSelect.innerHTML = '<option value="">Seleccione una cuenta</option>';
            cuentas.forEach(cuenta => {
                const formOption = document.createElement('option');
                formOption.value = cuenta.id;
                formOption.textContent = cuenta.nombre;
                cuentaFormSelect.appendChild(formOption);
            });
        }
        cuentas.forEach(cuenta => {
            const option = document.createElement('option');
            option.value = cuenta.id;
            option.textContent = cuenta.nombre;
            cuentaSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar cuentas:', error);
        const cuentaSelect = document.querySelector('#cuentaSelect');
        const cuentaFormSelect = document.querySelector('#cuenta_id');
        cuentaSelect.innerHTML = '<option value="">Error al cargar cuentas</option>';
        if (cuentaFormSelect) {
            cuentaFormSelect.innerHTML = '<option value="">Error al cargar cuentas</option>';
        }
        if (error.message.includes('403')) {
            alert('No tienes permiso para cargar la lista de cuentas contables. Contacta a un administrador.');
        }
    }
}

async function loadBases() {
    try {
        const response = await fetch('index.php?controller=base&action=listBases', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            let errorData;
            try {
                errorData = JSON.parse(text);
            } catch (e) {
                throw new Error(`Error HTTP: ${response.status} - Respuesta no es JSON válida: ${text}`);
            }
            throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
        }
        const bases = await response.json();
        const baseFormSelect = document.querySelector('#base_id');
        if (baseFormSelect) {
            baseFormSelect.innerHTML = '<option value="">Seleccione una base</option>';
            bases.forEach(base => {
                const option = document.createElement('option');
                option.value = base.id;
                option.textContent = base.nombre;
                baseFormSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar bases:', error);
        const baseFormSelect = document.querySelector('#base_id');
        if (baseFormSelect) {
            baseFormSelect.innerHTML = '<option value="">Error al cargar bases</option>';
        }
        if (error.message.includes('403')) {
            alert('No tienes permiso para cargar la lista de bases. Contacta a un administrador.');
        }
    }
}

async function showCreateForm() {
    try {
        const response = await fetch('index.php?controller=factura&action=showForm', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${text}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        modalContent.innerHTML = html;
        modal.classList.add('active');
        await Promise.all([loadCuentas(), loadBases()]);
        addCreateValidations();
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addCreateValidations() {
    const form = document.querySelector('#modalContent #facturaFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="facturaFormInner" dentro de #modalContent');
        return;
    }

    const fields = {
        cuenta_id: { required: true },
        base_id: { required: true },
        numero_factura: { required: true, minLength: 2 },
        fecha: { required: true },
        proveedor: { required: true, minLength: 2 },
        monto: { required: true, minValue: 0.01 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value.trim();
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
            if (fields[fieldName].minLength && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'monto' && fields[fieldName].minValue && (isNaN(value) || parseFloat(value) < fields[fieldName].minValue)) {
                errorElement.textContent = `El monto debe ser mayor o igual a ${fields[fieldName].minValue}.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'numero_factura' && value) {
                const exists = await checkNumeroFacturaExists(value);
                if (exists) {
                    errorElement.textContent = `El número de factura "${value}" ya está registrado.`;
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
            const url = form.dataset.id ? 'index.php?controller=factura&action=updateFactura' : 'index.php?controller=factura&action=createFactura';
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const text = await response.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (err) {
                    throw new Error('Respuesta no es JSON válida: ' + text);
                }
                if (!response.ok) {
                    throw new Error(result.error || 'Error al procesar la factura');
                }
                alert(result.message || (form.dataset.id ? 'Factura actualizada exitosamente' : 'Factura creada exitosamente'));
                closeModal();
                loadFacturas();
            } catch (error) {
                console.error('Error al procesar:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al procesar la factura. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
            }
        }
    });
}

async function checkNumeroFacturaExists(numeroFactura) {
    try {
        let url = `index.php?controller=factura&action=checkNumeroFactura&numero_factura=${encodeURIComponent(numeroFactura)}`;
        if (currentFacturaId) {
            url += `&exclude_id=${currentFacturaId}`;
        }
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al verificar el número de factura');
            } catch (parseError) {
                throw new Error('Respuesta no es JSON válida: ' + text);
            }
        }
        const data = await response.json();
        return data.exists;
    } catch (error) {
        console.error('Error al verificar el número de factura:', error);
        return false;
    }
}

async function showViewForm(id) {
    try {
        const response = await fetch('../views/factura/view.html', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} - No se pudo cargar la plantilla`);
        }
        const html = await response.text();

        const facturaResponse = await fetch(`index.php?controller=factura&action=getFactura&id=${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!facturaResponse.ok) {
            const text = await facturaResponse.text();
            throw new Error(`Error HTTP: ${facturaResponse.status} - ${text}`);
        }
        const factura = await facturaResponse.json();

        modalContent.innerHTML = html;
        modal.classList.add('active');

        document.getElementById('view_id').textContent = factura.id || 'N/A';
        document.getElementById('view_numero_factura').textContent = factura.numero_factura || 'N/A';
        document.getElementById('view_cuenta_nombre').textContent = factura.cuenta_nombre || 'Sin cuenta';
        document.getElementById('view_base_nombre').textContent = factura.base_nombre || 'Sin base';
        document.getElementById('view_proveedor').textContent = factura.proveedor || 'N/A';
        const fecha = factura.fecha ? new Date(factura.fecha).toLocaleDateString('es-ES') : 'N/A';
        document.getElementById('view_fecha').textContent = fecha;
        const monto = parseFloat(factura.monto);
        document.getElementById('view_monto').textContent = isNaN(monto) ? 'N/A' : '$' + monto.toFixed(2);
        document.getElementById('view_estado').textContent = factura.estado || 'N/A';
        const createdAt = factura.created_at ? new Date(factura.created_at).toLocaleString('es-ES') : 'N/A';
        document.getElementById('view_created_at').textContent = createdAt;

        // Guardar el ID de la factura para usarlo en showEditForm
        currentFacturaId = id;
    } catch (error) {
        console.error('Error al mostrar los detalles de la factura:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function showEditForm() {
    try {
        if (!currentFacturaId) {
            throw new Error('No se ha seleccionado una factura para editar');
        }

        const response = await fetch('index.php?controller=factura&action=showForm', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${text}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }

        const facturaResponse = await fetch(`index.php?controller=factura&action=getFactura&id=${currentFacturaId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!facturaResponse.ok) {
            const text = await facturaResponse.text();
            throw new Error(`Error HTTP: ${facturaResponse.status} - ${text}`);
        }
        const factura = await facturaResponse.json();

        modalContent.innerHTML = html;
        modal.classList.add('active');

        const form = document.querySelector('#facturaFormInner');
        if (!form) {
            throw new Error('No se encontró el formulario en el modal');
        }

        // Cambiar el título a "Editar Factura"
        const formTitle = document.querySelector('#formTitle');
        if (formTitle) {
            formTitle.textContent = 'Editar Factura';
        }

        // Rellenar el formulario con los datos de la factura
        form.querySelector('[name="id"]').value = factura.id;
        form.querySelector('[name="cuenta_id"]').value = factura.cuenta_id || '';
        form.querySelector('[name="base_id"]').value = factura.base_id || '';
        form.querySelector('[name="numero_factura"]').value = factura.numero_factura || '';
        form.querySelector('[name="fecha"]').value = factura.fecha || '';
        form.querySelector('[name="proveedor"]').value = factura.proveedor || '';
        form.querySelector('[name="monto"]').value = factura.monto || '';
        form.querySelector('[name="estado"]').value = factura.estado || 'PENDIENTE';

        // Marcar el formulario como edición
        form.dataset.id = factura.id;

        await Promise.all([loadCuentas(), loadBases()]);
        addCreateValidations();
    } catch (error) {
        console.error('Error al cargar el formulario de edición:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function deleteFactura(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta factura?')) {
        return;
    }
    try {
        const response = await fetch(`index.php?controller=factura&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            throw new Error('Respuesta no es JSON válida: ' + text);
        }
        if (!response.ok) {
            throw new Error(result.error || 'Error al eliminar la factura');
        }
        alert(result.message || 'Factura eliminada');
        loadFacturas();
    } catch (error) {
        console.error('Error al eliminar:', error);
        alert(error.message || 'Error al eliminar la factura');
    }
}

async function showAuthorizeForm(id) {
    try {
        modalContent.innerHTML = `
            <h3>Autorizar Factura</h3>
            <form id="authorizeForm" data-id="${id}">
                <input type="hidden" name="id" value="${id}">
                <label for="accion">Acción:</label>
                <select name="accion" required>
                    <option value="">Seleccione una acción</option>
                    <option value="APROBADO">Aprobado</option>
                    <option value="RECHAZADO">Rechazado</option>
                </select>
                <label for="comentario">Comentario:</label>
                <textarea name="comentario" placeholder="Escribe un comentario (opcional)"></textarea>
                <div class="buttons">
                    <button type="submit">Enviar</button>
                    <button type="button" onclick="closeModal()">Cancelar</button>
                </div>
            </form>
        `;
        modal.classList.add('active');

        const form = document.querySelector('#authorizeForm');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const response = await fetch(`index.php?controller=factura&action=autorizarFactura&id=${id}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const text = await response.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (err) {
                    throw new Error('Respuesta no es JSON válida: ' + text);
                }
                if (!response.ok) {
                    throw new Error(result.error || 'Error al autorizar la factura');
                }
                alert(result.message || 'Factura autorizada exitosamente');
                closeModal();
                loadFacturas();
            } catch (error) {
                console.error('Error al autorizar:', error);
                const errorElement = document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al autorizar la factura';
                form.appendChild(errorElement);
            }
        });
    } catch (error) {
        console.error('Error al cargar el formulario de autorización:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function showReviewForm(id) {
    try {
        modalContent.innerHTML = `
            <h3>Revisar Factura</h3>
            <form id="reviewForm" data-id="${id}">
                <input type="hidden" name="id" value="${id}">
                <label for="accion">Acción:</label>
                <select name="accion" required>
                    <option value="">Seleccione una acción</option>
                    <option value="PAGADA">Pagada</option>
                    <option value="RECHAZADO">Rechazado</option>
                </select>
                <label for="comentario">Comentario:</label>
                <textarea name="comentario" placeholder="Escribe un comentario (opcional)"></textarea>
                <div class="buttons">
                    <button type="submit">Enviar</button>
                    <button type="button" onclick="closeModal()">Cancelar</button>
                </div>
            </form>
        `;
        modal.classList.add('active');

        const form = document.querySelector('#reviewForm');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const response = await fetch(`index.php?controller=factura&action=revisarFactura&id=${id}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const text = await response.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (err) {
                    throw new Error('Respuesta no es JSON válida: ' + text);
                }
                if (!response.ok) {
                    throw new Error(result.error || 'Error al revisar la factura');
                }
                alert(result.message || 'Factura revisada exitosamente');
                closeModal();
                loadFacturas();
            } catch (error) {
                console.error('Error al revisar:', error);
                const errorElement = document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al revisar la factura';
                form.appendChild(errorElement);
            }
        });
    } catch (error) {
        console.error('Error al cargar el formulario de revisión:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalContent.innerHTML = '';
        currentFacturaId = null; // Limpiar el ID al cerrar el modal
    }
}