// Definir usuarioForm y agregar una verificación
const usuarioForm = document.querySelector('#usuarioFormInner');

// Cargar usuarios al iniciar
document.addEventListener('DOMContentLoaded', () => {
    loadUsuarios();
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
        usuarios.forEach(usuario => {
            tbody.innerHTML += `
                <tr>
                    <td>${usuario.id}</td>
                    <td>${usuario.nombre}</td>
                    <td>${usuario.email}</td>
                    <td>${usuario.rol}</td>
                    <td>
                        <button onclick="showEditForm(${usuario.id})">Editar</button>
                        <button onclick="deleteUsuario(${usuario.id})">Eliminar</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        alert('No se pudo cargar la lista de usuarios. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
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
        const errorData = await response.json();
        throw new Error(`Error al crear usuario: ${errorData.error || await response.text()}`);
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
            throw new Error(`Error al actualizar usuario: ${errorData.error || 'Error desconocido'}`);
        } catch (parseError) {
            throw new Error(`Error al actualizar usuario: Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function deleteUsuario(id) {
    const response = await fetch(`index.php?controller=usuario&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al eliminar usuario: ${errorData.error || await response.text()}`);
    }
    if (response.ok) loadUsuarios();
}

function showCreateForm() {
    if (!usuarioForm) {
        console.error('El elemento #usuarioForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch('index.php?controller=usuario&action=create', {
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
        usuarioForm.innerHTML = html;
        usuarioForm.style.display = 'block';
        const form = usuarioForm.querySelector('#usuarioFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="usuarioFormInner" dentro de #usuarioForm');
            usuarioForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    })
    .catch(error => {
        console.error('Error al cargar el formulario (create):', error);
        if (usuarioForm) {
            usuarioForm.innerHTML = `<div class="error">${error.message}</div>`;
            usuarioForm.style.display = 'block';
        } else {
            console.error('El elemento #usuarioForm no se encontró en el DOM');
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    });
}

function showEditForm(id) {
    if (!usuarioForm) {
        console.error('El elemento #usuarioForm no se encontró en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch(`index.php?controller=usuario&action=update&id=${id}`, {
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
        usuarioForm.innerHTML = html;
        usuarioForm.style.display = 'block';
        const form = usuarioForm.querySelector('#usuarioFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="usuarioFormInner" dentro de #usuarioForm');
            usuarioForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    })
    .catch(error => {
        console.error('Error al cargar el formulario (update):', error);
        if (usuarioForm) {
            usuarioForm.innerHTML = `<div class="error">${error.message}</div>`;
            usuarioForm.style.display = 'block';
        } else {
            console.error('El elemento #usuarioForm no se encontró en el DOM');
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    });
}

function cancelForm() {
    const formContainer = document.getElementById('usuarioFormInner');
    if (formContainer) {
        formContainer.style.display = 'none';
        formContainer.innerHTML = '';
    }
}

function addValidations() {
    const form = document.querySelector('#usuarioFormInner #usuarioFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="usuarioFormInner" dentro de #usuarioForm');
        return;
    }

    const fields = {
        nombre: { required: true, minLength: 2 },
        email: { required: true, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
        rol: { required: true },
        password: { required: form.querySelector('input[name="id"]').value === '', minLength: 6 }
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
            if (fields[fieldName].pattern && !fields[fieldName].pattern.test(value)) {
                errorElement.textContent = `El ${fieldName} no es válido.`;
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
                    const result = await updateUsuario(id, formData);
                    if (result.message) {
                        window.location.reload();
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                } else {
                    const result = await createUsuario(formData);
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