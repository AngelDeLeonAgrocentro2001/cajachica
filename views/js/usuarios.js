const usuarioForm = document.querySelector('#usuarioForm');

document.addEventListener('DOMContentLoaded', () => {
    loadUsuarios();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && usuarioForm) {
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
        return usuarios;
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        alert('No se pudo cargar la lista de usuarios. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function checkEmailExists(email, excludeId = null) {
    const usuarios = await loadUsuarios();
    const excludeIdNum = excludeId ? Number(excludeId) : null;
    return usuarios.some(usuario => usuario.email === email && (excludeIdNum === null || usuario.id !== excludeIdNum));
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
        throw new Error(errorData.error || 'Error al crear usuario');
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
            throw new Error(errorData.error || 'Error al actualizar usuario');
        } catch (parseError) {
            throw new Error(`Respuesta no es JSON válida: ${text}`);
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
        throw new Error(errorData.error || 'Error al eliminar usuario');
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
            return response.text().then(errorText => {
                throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
            });
        }
        return response.text();
    })
    .then(html => {
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
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
            return response.text().then(errorText => {
                throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
            });
        }
        return response.text();
    })
    .then(html => {
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
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
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    });
}

function cancelForm() {
    const formContainer = document.getElementById('usuarioForm');
    if (formContainer) {
        const urlParams = new URLSearchParams(window.location.search);
        const idFromUrl = urlParams.get('id');
        formContainer.style.display = 'none';
        formContainer.innerHTML = '';
        if (idFromUrl) {
            // Preservamos el id en la URL si estamos en modo edición
            window.history.pushState({}, '', `index.php?controller=usuario&action=update&id=${idFromUrl}`);
        } else {
            window.history.pushState({}, '', 'index.php?controller=usuario&action=list');
        }
    }
}

function addValidations() {
    const form = document.querySelector('#usuarioForm #usuarioFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="usuarioFormInner" dentro de #usuarioForm');
        return;
    }

    const fields = {
        nombre: { required: true, minLength: 2 },
        email: { required: true },
        password: { required: false, minLength: 6 },
        id_rol: { required: true }
    };

    let formMode = 'create';
    const urlParams = new URLSearchParams(window.location.search);
    const idFromUrl = urlParams.get('id');
    const formIdInput = form.querySelector('input[name="id"]');
    const formId = formIdInput ? formIdInput.value : null;

    // Determinamos el modo combinando idFromUrl y formId
    if (idFromUrl || formId) {
        formMode = 'update';
        fields.password.required = false;
    } else {
        fields.password.required = true;
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
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`);
        if (!errorElement) return true;

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].minLength && value && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
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
                // Verificar si el email ya existe
                const emailExists = await checkEmailExists(value, formId);
                if (emailExists) {
                    errorElement.textContent = `El email "${value}" ya está registrado. Por favor, usa un email diferente.`;
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
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
                    result = await updateUsuario(idToUse, formData);
                } else {
                    result = await createUsuario(formData);
                }

                if (result.message) {
                    const successElement = form.querySelector('.success');
                    successElement.textContent = result.message;
                    successElement.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = 'index.php?controller=usuario&action=list';
                    }, 1000);
                } else if (result.error) {
                    const errorElement = form.querySelector('.error');
                    errorElement.textContent = result.error;
                    errorElement.style.display = 'block';
                }
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error');
                errorElement.textContent = error.message || 'Error al procesar la solicitud. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}