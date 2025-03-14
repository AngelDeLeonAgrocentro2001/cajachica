let debounceTimeout;
const modal = document.getElementById('modal');
const modalContent = document.getElementById('modalContent');

document.addEventListener('DOMContentLoaded', () => {
    loadCuentasContables();
    loadBases();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && modal) {
        showEditForm(id);
    }

    // Agregar el evento de búsqueda con debounce
    const searchInput = document.querySelector('#search');
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            loadCuentasContables();
        }, 300); // Retardo de 300ms
    });
});
//ya funciona
let cuentasContables = [];

async function loadCuentasContables() {
    try {
        const searchTerm = document.querySelector('#search').value.trim();
        const selectedBaseId = document.querySelector('#baseSelect').value;

        // Construir la URL con los parámetros de búsqueda
        let url = 'index.php?controller=cuentacontable&action=list';
        if (searchTerm) {
            url += `&search=${encodeURIComponent(searchTerm)}`;
        }
        if (selectedBaseId) {
            url += `&base_id=${encodeURIComponent(selectedBaseId)}`;
        }

        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            if (response.status === 401) {
                throw new Error(errorData.error || 'No autorizado');
            }
            throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
        }
        const cuentas = await response.json();
        if (!Array.isArray(cuentas)) {
            throw new Error('La respuesta no es un array válido');
        }
        cuentasContables = cuentas;

        const tbody = document.querySelector('#cuentasContablesTable tbody');
        tbody.innerHTML = '';
        if (cuentas.length > 0) {
            cuentas.forEach(cuenta => {
                const estado = cuenta.estado === 'ACTIVO' ? 'Y' : 'N';
                tbody.innerHTML += `
                    <tr>
                        <td>${cuenta.codigo}</td>
                        <td>${cuenta.nombre}</td>
                        <td>${cuenta.tipo || '5'}</td>
                        <td>${estado}</td>
                        <td>
                            <button class="access-btn" onclick="window.location.href='index.php?controller=acceso&action=list&cuenta_id=${cuenta.id}'">Accesos</button>
                            <button class="update-btn" onclick="showUpdateForm(${cuenta.id})">Actualizar</button>
                            <button class="delete-btn" onclick="deleteCuentaContable(${cuenta.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">No se encontraron cuentas contables con el nombre "' + searchTerm + '".</td></tr>';
        }
        return cuentas;
    } catch (error) {
        console.error('Error al cargar cuentas contables:', error);
        alert('No se pudo cargar la lista de cuentas contables. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
        return [];
    }
}
//ya funciona
//ya funciona

async function checkCodigoExists(codigo) {
    try {
        console.log('Verificando código:', codigo); // Depuración
        const response = await fetch(`index.php?controller=cuentacontable&action=checkCodigo&codigo=${encodeURIComponent(codigo)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        console.log('Respuesta del servidor (status):', response.status); // Depuración
        if (!response.ok) {
            const text = await response.text();
            console.log('Respuesta del servidor (texto):', text); // Depuración
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al verificar el código');
            } catch (parseError) {
                throw new Error('Respuesta no es JSON válida: ' + text);
            }
        }
        const data = await response.json();
        console.log('Resultado de checkCodigoExists:', data); // Depuración
        return data.exists;
    } catch (error) {
        console.error('Error al verificar el código:', error);
        return false; // En caso de error, asumimos que el código no existe
    }
}

async function createCuentaContable(formData) {
    try {
        const response = await fetch('index.php?controller=cuentacontable&action=create', {
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
        } catch (e) {
            throw new Error('Respuesta no es JSON válida: ' + text);
        }
        if (!response.ok) {
            throw new Error(result.error || 'Error al crear la cuenta contable');
        }
        return result;
    } catch (error) {
        console.error('Error al enviar formulario:', error);
        throw error;
    }
}

async function updateCuentaContable(id, formData) {
    try {
        console.log('Actualizando cuenta con ID:', id); // Depuración
        console.log('Datos del formulario:', Object.fromEntries(formData)); // Depuración
        const response = await fetch(`index.php?controller=cuentacontable&action=update&id=${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        console.log('Respuesta del servidor (status):', response.status); // Depuración
        const text = await response.text();
        console.log('Respuesta del servidor (texto):', text); // Depuración
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            throw new Error('Respuesta no es JSON válida: ' + text);
        }
        if (!response.ok) {
            throw new Error(result.error || 'Error al actualizar la cuenta contable');
        }
        return result;
    } catch (error) {
        console.error('Error al actualizar:', error);
        throw error;
    }
}

async function deleteCuentaContable(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta cuenta contable?')) return;

    try {
        const response = await fetch(`index.php?controller=cuentacontable&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar cuenta contable');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Cuenta contable eliminada');
        loadCuentasContables();
    } catch (error) {
        console.error('Error al eliminar cuenta contable:', error);
        alert(error.message || 'Error al eliminar cuenta contable. Intenta de nuevo.');
    }
}

async function deleteCuenta(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta cuenta contable?')) return;
    try {
        const response = await fetch(`index.php?controller=cuentacontable&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al eliminar la cuenta contable');
        }
        const result = await response.json();
        alert(result.message || 'Cuenta contable eliminada');
        loadCuentasContables();
    } catch (error) {
        console.error('Error al eliminar:', error);
        alert(error.message || 'Error al eliminar la cuenta contable');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalContent.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=cuentacontable&action=list');
    }
}

async function showCreateForm() {
    try {
        const response = await fetch('index.php?controller=cuentacontable&action=createForm', {
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

        // Cargar las bases en el select
        const baseSelect = document.querySelector('#base_id');
        if (baseSelect) {
            const basesResponse = await fetch('index.php?controller=base&action=listBases', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!basesResponse.ok) {
                throw new Error('Error al cargar las bases');
            }
            const bases = await basesResponse.json();
            bases.forEach(base => {
                const option = document.createElement('option');
                option.value = base.id;
                option.textContent = base.nombre;
                baseSelect.appendChild(option);
            });
        }

        addCreateValidations();
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function showUpdateForm(id) {
    try {
        const response = await fetch(`index.php?controller=cuentacontable&action=updateForm&id=${id}`, {
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
        addValidations(id);
    } catch (error) {
        console.error('Error al cargar el formulario de actualización:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addValidations(id = null) {
    const form = document.querySelector('#modalContent #cuentaContableFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="cuentaContableFormInner" dentro de #modalContent');
        return;
    }

    const fields = {
        codigo: { required: true, minLength: 2 },
        nombre: { required: true, minLength: 2 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
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
            if (fieldName === 'codigo' && value && !id) {
                const exists = await checkCodigoExists(value);
                if (exists) {
                    errorElement.textContent = `El código "${value}" ya está registrado. Por favor, usa un código diferente.`;
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
            const formId = formData.get('id') || id;

            try {
                const action = formId ? updateCuentaContable(formId, formData) : createCuentaContable(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadCuentasContables();
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

async function loadBases() {
    try {
        const response = await fetch('index.php?controller=base&action=listBases', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            console.error('Respuesta del servidor:', text);
            throw new Error(`Error HTTP: ${response.status} - ${text.replace(/<[^>]+>/g, '')}`);
        }
        const bases = await response.json();
        if (!Array.isArray(bases)) {
            throw new Error('La respuesta no es un array válido');
        }
        const baseSelect = document.querySelector('#baseSelect');
        baseSelect.innerHTML = '<option value="">Seleccione la Base</option>';
        bases.forEach(base => {
            const option = document.createElement('option');
            option.value = base.id;
            option.textContent = base.nombre;
            baseSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar bases:', error);
        const baseSelect = document.querySelector('#baseSelect');
        const errorMessage = error.message.replace(/<[^>]+>/g, '').trim() || 'Error desconocido';
        baseSelect.innerHTML = `<option value="">${errorMessage}</option>`;
    }
}

async function showUpdateForm(id) {
    try {
        const response = await fetch(`index.php?controller=cuentacontable&action=updateForm&id=${id}`, {
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
        addValidations(id);
    } catch (error) {
        console.error('Error al cargar el formulario de actualización:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addUpdateValidations(id) {
    const form = document.querySelector('#modalContent #updateCuentaContableFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="updateCuentaContableFormInner" dentro de #modalContent');
        return;
    }

    const fields = {
        nombre: { required: true, minLength: 2 },
        estado: { required: true },
        tipo: { required: true, minLength: 1 }
    };

    form.querySelectorAll('input, select').forEach(field => {
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
                const result = await updateCuentaContable(id, formData);
                alert(result.message || 'Cuenta contable actualizada');
                closeModal();
                loadCuentasContables();
            } catch (error) {
                console.error('Error al actualizar:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al actualizar. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
            }
        }
    });
}

function addCreateValidations() {
    const form = document.querySelector('#modalContent #cuentaContableFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="cuentaContableFormInner" dentro de #modalContent');
        return;
    }

    const fields = {
        codigo: { required: true, minLength: 2 },
        nombre: { required: true, minLength: 2 },
        tipo: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
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
            if (fieldName === 'codigo') {
                const exists = await checkCodigoExists(value);
                if (exists) {
                    errorElement.textContent = 'El código ya existe. Por favor, usa un código diferente.';
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
                const result = await createCuentaContable(formData);
                alert(result.message || 'Cuenta contable creada');
                closeModal();
                loadCuentasContables();
            } catch (error) {
                console.error('Error al crear:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al crear. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
            }
        }
    });
}

