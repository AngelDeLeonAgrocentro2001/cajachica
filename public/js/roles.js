const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

document.addEventListener('DOMContentLoaded', () => {
    loadRoles();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && modal) {
        showEditForm(id);
    }
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
        if (roles.length > 0) {
            roles.forEach(rol => {
                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${rol.id}</td>
                        <td data-label="Nombre">${rol.nombre}</td>
                        <td data-label="Descripción">${rol.descripcion || 'N/A'}</td>
                        <td data-label="Estado">${rol.estado}</td>
                        <td data-label="Acciones">
                            <button class="edit-btn" onclick="showEditForm(${rol.id}); window.history.pushState({}, '', 'index.php?controller=rol&action=update&id=${rol.id}')">Editar</button>
                            <button class="delete-btn" onclick="deleteRol(${rol.id})">Eliminar</button>
                            <button class="permissions-btn" onclick="showManagePermissions(${rol.id})">Permisos</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">No hay roles registrados.</td></tr>';
        }
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
        const text = await response.text();
        try {
            const errorData = JSON.parse(text);
            throw new Error(errorData.error || text);
        } catch (parseError) {
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }
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
            throw new Error(errorData.error || text);
        } catch (parseError) {
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function deleteRol(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este rol?')) return;

    try {
        const response = await fetch(`index.php?controller=rol&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar rol');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Rol eliminado con éxito');
        loadRoles();
    } catch (error) {
        console.error('Error al eliminar rol:', error);
        alert(error.message || 'Error al eliminar rol. Intenta de nuevo.');
    }
}

async function showManagePermissions(rolId) {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch(`index.php?controller=rol&action=managePermissions&rol_id=${rolId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        modalForm.innerHTML = html;
        modal.classList.add('active');

        const form = modalForm.querySelector('#permissionsForm');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                console.log('Datos del formulario antes de enviar:');
                for (let pair of formData.entries()) {
                    console.log('Dato enviado:', pair[0], pair[1]);
                }

                try {
                    const response = await fetch(`index.php?controller=rol&action=managePermissions&rol_id=${rolId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || 'Error al guardar permisos');
                    }
                    const result = await response.json();
                    console.log('Respuesta del servidor:', result);
                    alert(result.message || 'Permisos asignados correctamente');

                    // Recargar el formulario para reflejar los cambios
                    await showManagePermissions(rolId);
                    loadRoles();
                } catch (error) {
                    console.error('Error al guardar permisos:', error);
                    alert(error.message || 'Error al guardar permisos');
                }
            });
        } else {
            console.error('No se encontró el formulario en el modal');
        }
    } catch (error) {
        console.error('Error al cargar formulario de permisos:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function assignAdditionalPermissions() {
    const form = modalForm.querySelector('#permissionsForm');
    if (!form) {
        console.error('Formulario no encontrado');
        return;
    }

    const additionalPermissionsMessage = document.querySelector('#additionalPermissionsMessage');
    if (!additionalPermissionsMessage) {
        console.error('Mensaje de permisos adicionales no encontrado');
        return;
    }

    const additionalPermissions = [];
    additionalPermissionsMessage.querySelectorAll('li').forEach(li => {
        const permisoText = li.textContent;
        const inputs = form.querySelectorAll('input[name="permissions[]"]');
        inputs.forEach(input => {
            const label = input.parentElement;
            if (label.textContent.includes(permisoText)) {
                input.checked = true;
            }
        });
    });

    additionalPermissionsMessage.style.display = 'none';
}

function dismissAdditionalPermissions() {
    const additionalPermissionsMessage = document.querySelector('#additionalPermissionsMessage');
    if (additionalPermissionsMessage) {
        additionalPermissionsMessage.style.display = 'none';
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=rol&action=list');
    }
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=rol&action=create', {
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
        addValidations();
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
        const response = await fetch(`index.php?controller=rol&action=update&id=${id}`, {
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
        addValidations(id);
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addValidations(id = null) {
    const form = document.querySelector('#modalForm #rolFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="rolFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        nombre: { required: true, minLength: 2 },
        descripcion: { required: false },
        estado: { required: true }
    };

    form.querySelectorAll('input, textarea, select').forEach(field => {
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
            if (fields[fieldName].minLength && value && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
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
            Array.from(form.querySelectorAll('input, textarea, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            const formId = formData.get('id') || id;

            try {
                const action = formId ? updateRol(formId, formData) : createRol(formData);
                const result = await action;
                alert(result.message || (formId ? 'Rol actualizado con éxito' : 'Rol creado con éxito'));
                closeModal();
                loadRoles();
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