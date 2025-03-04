const cuentaContableForm = document.querySelector('#cuentaContableForm');

document.addEventListener('DOMContentLoaded', () => {
    loadCuentasContables();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && cuentaContableForm) {
        showEditForm(id);
    }
});

async function loadCuentasContables() {
    try {
        const response = await fetch('index.php?controller=cuentacontable&action=list', {
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
        const cuentasContables = await response.json();
        const tbody = document.querySelector('#cuentasContablesTable tbody');
        tbody.innerHTML = '';
        cuentasContables.forEach(cuenta => {
            tbody.innerHTML += `
                <tr>
                    <td>${cuenta.id}</td>
                    <td>${cuenta.codigo}</td>
                    <td>${cuenta.nombre}</td>
                    <td>${cuenta.estado}</td>
                    <td>
                        <button onclick="showEditForm(${cuenta.id})">Editar</button>
                        <button onclick="deleteCuentaContable(${cuenta.id})">Eliminar</button>
                    </td>
                </tr>
            `;
        });
        return cuentasContables;
    } catch (error) {
        console.error('Error al cargar cuentas contables:', error);
        alert('No se pudo cargar la lista de cuentas contables. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function checkCodigoExists(codigo, excludeId = null) {
    const cuentas = await loadCuentasContables();
    const excludeIdNum = excludeId ? Number(excludeId) : null;
    return cuentas.some(cuenta => cuenta.codigo === codigo && (excludeIdNum === null || cuenta.id !== excludeIdNum));
}

async function createCuentaContable(data) {
    const response = await fetch('index.php?controller=cuentacontable&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Error al crear cuenta contable');
    }
    return response.json();
}

async function updateCuentaContable(id, data) {
    const response = await fetch(`index.php?controller=cuentacontable&action=update&id=${id}`, {
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
            throw new Error(errorData.error || 'Error al actualizar cuenta contable');
        } catch (parseError) {
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function deleteCuentaContable(id) {
    const response = await fetch(`index.php?controller=cuentacontable&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Error al eliminar cuenta contable');
    }
    if (response.ok) loadCuentasContables();
}

function showCreateForm() {
    if (!cuentaContableForm) {
        console.error('El elemento #cuentaContableForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch('index.php?controller=cuentacontable&action=create', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.error || `Error HTTP: ${response.status} - ${response.statusText}`);
                });
            }
            return response.text();
        })
        .then(html => {
            console.log('HTML devuelto (create):', html);
            cuentaContableForm.innerHTML = html;
            cuentaContableForm.style.display = 'block';
            const form = cuentaContableForm.querySelector('#cuentaContableFormInner');
            if (!form) {
                console.error('No se encontró un elemento <form> con id="cuentaContableFormInner" dentro de #cuentaContableForm');
                cuentaContableForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
                return;
            }
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar el formulario (create):', error);
            if (cuentaContableForm) {
                cuentaContableForm.innerHTML = `<div class="error">${error.message}</div>`;
                cuentaContableForm.style.display = 'block';
            } else {
                alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
            }
        });
}

function showEditForm(id) {
    if (!cuentaContableForm) {
        console.error('El elemento #cuentaContableForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch(`index.php?controller=cuentacontable&action=update&id=${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.error || `Error HTTP: ${response.status} - ${response.statusText}`);
                });
            }
            return response.text();
        })
        .then(html => {
            console.log('HTML devuelto (update):', html);
            cuentaContableForm.innerHTML = html;
            cuentaContableForm.style.display = 'block';
            const form = cuentaContableForm.querySelector('#cuentaContableFormInner');
            if (!form) {
                console.error('No se encontró un elemento <form> con id="cuentaContableFormInner" dentro de #cuentaContableForm');
                cuentaContableForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
                return;
            }
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar el formulario (update):', error);
            if (cuentaContableForm) {
                cuentaContableForm.innerHTML = `<div class="error">${error.message}</div>`;
                cuentaContableForm.style.display = 'block';
            } else {
                alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
            }
        });
}

function cancelForm() {
    const formContainer = document.getElementById('cuentaContableForm');
    if (formContainer) {
        formContainer.style.display = 'none';
        formContainer.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=cuentacontable&action=list');
    }
}

function addValidations() {
    const form = document.querySelector('#cuentaContableForm #cuentaContableFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="cuentaContableFormInner" dentro de #cuentaContableForm');
        return;
    }

    const fields = {
        codigo: { required: true, minLength: 2 },
        nombre: { required: true, minLength: 2 }
    };

    // Determinar el modo usando tanto la URL como el campo id del formulario
    let formMode = 'create';
    const urlParams = new URLSearchParams(window.location.search);
    const idFromUrl = urlParams.get('id');
    const formIdInput = form.querySelector('input[name="id"]');
    const formId = formIdInput ? formIdInput.value : null;

    if (idFromUrl || formId) {
        formMode = 'update';
    }

    console.log('formMode (determined):', formMode);
    console.log('idFromUrl:', idFromUrl);
    console.log('formId:', formId);

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
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
            // Solo verificamos duplicidad si estamos en modo creación
            if (fieldName === 'codigo' && formMode === 'create') {
                const codigoExists = await checkCodigoExists(value, idFromUrl || formId);
                if (codigoExists) {
                    errorElement.textContent = `El código "${value}" ya está registrado. Por favor, usa un código diferente.`;
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
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
        const validations = await Promise.all(
            Array.from(form.querySelectorAll('input, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            const formIdInput = form.querySelector('input[name="id"]');
            const formId = formIdInput ? formIdInput.value : null;

            console.log('formMode (submit):', formMode);
            console.log('formId (submit):', formId);

            try {
                let result;
                if (formMode === 'update' && (idFromUrl || formId)) {
                    const idToUse = idFromUrl || formId;
                    result = await updateCuentaContable(idToUse, formData);
                } else {
                    result = await createCuentaContable(formData);
                }

                if (result.message) {
                    window.location.href = 'index.php?controller=cuentacontable&action=list';
                } else if (result.error) {
                    const errorElement = form.querySelector('.error') || document.createElement('div');
                    errorElement.className = 'error';
                    errorElement.textContent = result.error;
                    errorElement.style.display = 'block';
                }
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al procesar la solicitud. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}