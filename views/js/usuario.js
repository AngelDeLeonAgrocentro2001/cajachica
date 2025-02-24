async function loadUsuarios() {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=usuario&action=list', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
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
}

async function createUsuario(data) {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=usuario&action=create', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function updateUsuario(id, data) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=usuario&action=update&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function deleteUsuario(id) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=usuario&action=delete&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (response.ok) loadUsuarios();
}

function showCreateForm() {
    fetch('index.php?controller=usuario&action=create')
        .then(response => response.text())
        .then(html => {
            document.getElementById('usuarioForm').innerHTML = html;
            document.getElementById('usuarioForm').style.display = 'block';
            addValidations();
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=usuario&action=update&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('usuarioForm').innerHTML = html;
            document.getElementById('usuarioForm').style.display = 'block';
            addValidations();
        });
}

function cancelForm() {
    document.getElementById('usuarioForm').style.display = 'none';
    document.getElementById('usuarioForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('usuarioForm');
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
            if (id) {
                const result = await updateUsuario(id, formData);
                if (result.message) {
                    alert(result.message);
                    document.querySelector('.success').textContent = result.message;
                    document.querySelector('.success').style.display = 'block';
                } else if (result.error) {
                    alert(result.error);
                    document.querySelector('.error').textContent = result.error;
                    document.querySelector('.error').style.display = 'block';
                }
            } else {
                const result = await createUsuario(formData);
                if (result.message) {
                    alert(result.message);
                    document.querySelector('.success').textContent = result.message;
                    document.querySelector('.success').style.display = 'block';
                } else if (result.error) {
                    alert(result.error);
                    document.querySelector('.error').textContent = result.error;
                    document.querySelector('.error').style.display = 'block';
                }
            }
            loadUsuarios();
            cancelForm();
        }
    });
}

loadUsuarios();