const rolForm = document.querySelector('#rolForm');

document.addEventListener('DOMContentLoaded', () => {
    loadRoles();
});

async function loadRoles() {
    try {
        const response = await fetch('index.php?controller=rol&action=list', {
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
        const roles = await response.json();
        const tbody = document.querySelector('#rolesTable tbody');
        tbody.innerHTML = '';
        roles.forEach(rol => {
            tbody.innerHTML += `
                <tr>
                    <td>${rol.id}</td>
                    <td>${rol.nombre}</td>
                    <td>${rol.descripcion || ''}</td>
                    <td>${rol.estado}</td>
                    <td>
                        <button onclick="showEditForm(${rol.id})">Editar</button>
                        <button onclick="deleteRol(${rol.id})">Eliminar</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error al cargar roles:', error);
        alert('No se pudo cargar la lista de roles. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createRol(data) {
    const response = await fetch('index.php?controller=rol&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al crear rol: ${errorData.error || await response.text()}`);
    }
    return response.json();
}

async function updateRol(id, data) {
    const response = await fetch(`index.php?controller=rol&action=update&id=${id}`, {
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
            throw new Error(`Error al actualizar rol: ${errorData.error || 'Error desconocido'}`);
        } catch (parseError) {
            throw new Error(`Error al actualizar rol: Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function deleteRol(id) {
    const response = await fetch(`index.php?controller=rol&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al eliminar rol: ${errorData.error || await response.text()}`);
    }
    if (response.ok) loadRoles();
}

function showCreateForm() {
    if (!rolForm) {
        console.error('El elemento #rolForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch('index.php?controller=rol&action=create', {
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
        rolForm.innerHTML = html;
        rolForm.style.display = 'block';
        const form = rolForm.querySelector('#rolFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="rolFormInner" dentro de #rolForm');
            rolForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    })
    .catch(error => {
        console.error('Error al cargar el formulario (create):', error);
        if (rolForm) {
            rolForm.innerHTML = `<div class="error">${error.message}</div>`;
            rolForm.style.display = 'block';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    });
}

function showEditForm(id) {
    if (!rolForm) {
        console.error('El elemento #rolForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch(`index.php?controller=rol&action=update&id=${id}`, {
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
        rolForm.innerHTML = html;
        rolForm.style.display = 'block';
        const form = rolForm.querySelector('#rolFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="rolFormInner" dentro de #rolForm');
            rolForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    })
    .catch(error => {
        console.error('Error al cargar el formulario (update):', error);
        if (rolForm) {
            rolForm.innerHTML = `<div class="error">${error.message}</div>`;
            rolForm.style.display = 'block';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    });
}

function cancelForm() {
    const formContainer = document.getElementById('rolForm');
    if (formContainer) {
        formContainer.style.display = 'none';
        formContainer.innerHTML = '';
    }
}

function addValidations() {
    const form = document.querySelector('#rolForm #rolFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="rolFormInner" dentro de #rolForm');
        return;
    }

    const fields = {
        nombre: { required: true, minLength: 2 }
    };

    form.querySelectorAll('input, textarea, select').forEach(field => {
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
            errorElement.style.display = 'none';
            e.target.classList.remove('invalid');
            return true;
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        form.querySelectorAll('input, textarea, select').forEach(field => {
            if (!validateField({ target: field })) isValid = false;
        });

        if (isValid) {
            const formData = new FormData(form);
            const id = formData.get('id');
            try {
                if (id) {
                    const result = await updateRol(id, formData);
                    if (result.message) {
                        window.location.reload();
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                } else {
                    const result = await createRol(formData);
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