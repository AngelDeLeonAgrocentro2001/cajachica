async function loadTiposGastos() {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=tipogasto&action=list', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    const tipos = await response.json();
    const tbody = document.querySelector('#tiposGastosTable tbody');
    tbody.innerHTML = '';
    tipos.forEach(tipo => {
        tbody.innerHTML += `
            <tr>
                <td>${tipo.id}</td>
                <td>${tipo.name}</td>
                <td>${tipo.description}</td>
                <td>
                    <button onclick="showEditForm(${tipo.id})">Editar</button>
                    <button onclick="deleteTipoGasto(${tipo.id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
}

async function createTipoGasto(data) {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=tipogasto&action=create', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function updateTipoGasto(id, data) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=tipogasto&action=update&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function deleteTipoGasto(id) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=tipogasto&action=delete&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (response.ok) loadTiposGastos();
}

function showCreateForm() {
    fetch('index.php?controller=tipogasto&action=create')
        .then(response => response.text())
        .then(html => {
            document.getElementById('tipoGastoForm').innerHTML = html;
            document.getElementById('tipoGastoForm').style.display = 'block';
            addValidations();
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=tipogasto&action=update&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('tipoGastoForm').innerHTML = html;
            document.getElementById('tipoGastoForm').style.display = 'block';
            addValidations();
        });
}

function cancelForm() {
    document.getElementById('tipoGastoForm').style.display = 'none';
    document.getElementById('tipoGastoForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('tipoGastoForm');
    const fields = {
        name: { required: true, minLength: 2 },
        description: { required: true, minLength: 2 }
    };

    form.querySelectorAll('input, textarea').forEach(field => {
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
        form.querySelectorAll('input, textarea').forEach(field => {
            if (!validateField({ target: field })) isValid = false;
        });

        if (isValid) {
            const formData = new FormData(form);
            const id = formData.get('id');
            if (id) {
                const result = await updateTipoGasto(id, formData);
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
                const result = await createTipoGasto(formData);
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
            loadTiposGastos();
            cancelForm();
        }
    });
}

loadTiposGastos();