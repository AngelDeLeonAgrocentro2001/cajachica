const impuestoForm = document.querySelector('#impuestoForm');

document.addEventListener('DOMContentLoaded', () => {
    loadImpuestos();
});

async function loadImpuestos() {
    try {
        const response = await fetch('index.php?controller=impuesto&action=list', {
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
        const impuestos = await response.json();
        const tbody = document.querySelector('#impuestosTable tbody');
        tbody.innerHTML = '';
        impuestos.forEach(impuesto => {
            tbody.innerHTML += `
                <tr>
                    <td>${impuesto.id}</td>
                    <td>${impuesto.nombre}</td>
                    <td>${impuesto.porcentaje}</td>
                    <td>${impuesto.estado}</td>
                    <td>
                        <button onclick="showEditForm(${impuesto.id})">Editar</button>
                        <button onclick="deleteImpuesto(${impuesto.id})">Eliminar</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error al cargar impuestos:', error);
        alert('No se pudo cargar la lista de impuestos. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createImpuesto(data) {
    const response = await fetch('index.php?controller=impuesto&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al crear impuesto: ${errorData.error || await response.text()}`);
    }
    return response.json();
}

async function updateImpuesto(id, data) {
    const response = await fetch(`index.php?controller=impuesto&action=update&id=${id}`, {
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
            throw new Error(`Error al actualizar impuesto: ${errorData.error || 'Error desconocido'}`);
        } catch (parseError) {
            throw new Error(`Error al actualizar impuesto: Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function deleteImpuesto(id) {
    const response = await fetch(`index.php?controller=impuesto&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al eliminar impuesto: ${errorData.error || await response.text()}`);
    }
    if (response.ok) loadImpuestos();
}

function showCreateForm() {
    if (!impuestoForm) {
        console.error('El elemento #impuestoForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch('index.php?controller=impuesto&action=create', {
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
        impuestoForm.innerHTML = html;
        impuestoForm.style.display = 'block';
        const form = impuestoForm.querySelector('#impuestoFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="impuestoFormInner" dentro de #impuestoForm');
            impuestoForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    })
    .catch(error => {
        console.error('Error al cargar el formulario (create):', error);
        if (impuestoForm) {
            impuestoForm.innerHTML = `<div class="error">${error.message}</div>`;
            impuestoForm.style.display = 'block';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    });
}

function showEditForm(id) {
    if (!impuestoForm) {
        console.error('El elemento #impuestoForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch(`index.php?controller=impuesto&action=update&id=${id}`, {
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
        impuestoForm.innerHTML = html;
        impuestoForm.style.display = 'block';
        const form = impuestoForm.querySelector('#impuestoFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="impuestoFormInner" dentro de #impuestoForm');
            impuestoForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    })
    .catch(error => {
        console.error('Error al cargar el formulario (update):', error);
        if (impuestoForm) {
            impuestoForm.innerHTML = `<div class="error">${error.message}</div>`;
            impuestoForm.style.display = 'block';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    });
}

function cancelForm() {
    const formContainer = document.getElementById('impuestoForm');
    if (formContainer) {
        formContainer.style.display = 'none';
        formContainer.innerHTML = '';
    }
}

function addValidations() {
    const form = document.querySelector('#impuestoForm #impuestoFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="impuestoFormInner" dentro de #impuestoForm');
        return;
    }

    const fields = {
        nombre: { required: true, minLength: 2 },
        porcentaje: { required: true, min: 0.01, max: 100 }
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
            if (fieldName === 'porcentaje') {
                const numValue = parseFloat(value);
                if (isNaN(numValue) || numValue < fields[fieldName].min || numValue > fields[fieldName].max) {
                    errorElement.textContent = `El porcentaje debe estar entre ${fields[fieldName].min} y ${fields[fieldName].max}.`;
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
        form.querySelectorAll('input, select').forEach(field => {
            if (!validateField({ target: field })) isValid = false;
        });

        if (isValid) {
            const formData = new FormData(form);
            const id = formData.get('id');
            try {
                if (id) {
                    const result = await updateImpuesto(id, formData);
                    if (result.message) {
                        window.location.reload();
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                } else {
                    const result = await createImpuesto(formData);
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
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al procesar la solicitud. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}