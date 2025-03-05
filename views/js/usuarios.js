const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

document.addEventListener('DOMContentLoaded', () => {
    loadUsuarios();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && modal) {
        showEditForm(id);
    }
});

async function loadUsuarios() {
    try {
        const response = await fetch('index.php?controller=usuario&action=list', {
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
        const usuarios = await response.json();
        const tbody = document.querySelector('#usuariosTable tbody');
        tbody.innerHTML = '';
        if (usuarios.length > 0) {
            usuarios.forEach(usuario => {
                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${usuario.id}</td>
                        <td data-label="Nombre">${usuario.nombre}</td>
                        <td data-label="Email">${usuario.email}</td>
                        <td data-label="Rol">${usuario.rol}</td>
                        <td data-label="Acciones">
                            <button class="edit-btn" onclick="showEditForm(${usuario.id}); window.history.pushState({}, '', 'index.php?controller=usuario&action=update&id=${usuario.id}')">Editar</button>
                            <button class="delete-btn" onclick="deleteUsuario(${usuario.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">No hay usuarios registrados.</td></tr>';
        }
        return usuarios;
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        alert('No se pudo cargar la lista de usuarios. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function checkEmailExists(email, excludeId = null) {
    try {
        const usuarios = await loadUsuarios();
        const excludeIdNum = excludeId ? Number(excludeId) : null;
        return usuarios.some(usuario => usuario.email.toLowerCase() === email.toLowerCase() && (excludeIdNum === null || Number(usuario.id) !== excludeIdNum));
    } catch (error) {
        console.error('Error al verificar duplicados de email:', error);
        return false;
    }
}

async function createUsuario(data) {
    const response = await fetch('index.php?controller=usuario&action=create', {
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

async function updateUsuario(id, data) {
    const response = await fetch(`index.php?controller=usuario&action=update&id=${id}`, {
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

async function deleteUsuario(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) return;

    try {
        const response = await fetch(`index.php?controller=usuario&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar usuario');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Usuario eliminado');
        loadUsuarios();
    } catch (error) {
        console.error('Error al eliminar usuario:', error);
        alert(error.message || 'Error al eliminar usuario. Intenta de nuevo.');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=usuario&action=list');
    }
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=usuario&action=create', {
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
        const response = await fetch(`index.php?controller=usuario&action=update&id=${id}`, {
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
    const form = document.querySelector('#modalForm #usuarioFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="usuarioFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        nombre: { required: true, minLength: 2 },
        email: { required: true },
        password: { required: false, minLength: 6 },
        id_rol: { required: true }
    };

    // Determinar el modo (crear o actualizar)
    const formMode = id ? 'update' : 'create';
    if (formMode === 'update') {
        fields.password.required = false; // Contraseña no requerida al editar
    } else {
        fields.password.required = true; // Contraseña requerida al crear
    }

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
            if (fields[fieldName].minLength && value && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    errorElement.textContent = 'Por favor, ingresa un email válido.';
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
                const emailExists = await checkEmailExists(value, id);
                if (emailExists) {
                    errorElement.textContent = `El email "${value}" ya está registrado. Por favor, usa un email diferente.`;
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
                const action = formId ? updateUsuario(formId, formData) : createUsuario(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadUsuarios();
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