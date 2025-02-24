async function loadCajasChicas() {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=cajachica&action=list', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    const cajas = await response.json();
    const tbody = document.querySelector('#cajasChicasTable tbody');
    tbody.innerHTML = '';
    cajas.forEach(caja => {
        tbody.innerHTML += `
            <tr>
                <td>${caja.id}</td>
                <td>${caja.nombre}</td>
                <td>${caja.monto_asignado}</td>
                <td>${caja.monto_disponible}</td>
                <td>${caja.estado}</td>
                <td>
                    <button onclick="showEditForm(${caja.id})">Editar</button>
                    <button onclick="deleteCajaChica(${caja.id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
}

async function createCajaChica(data) {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=cajachica&action=create', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function updateCajaChica(id, data) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=cajachica&action=update&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function deleteCajaChica(id) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=cajachica&action=delete&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (response.ok) loadCajasChicas();
}

function showCreateForm() {
    fetch('index.php?controller=cajachica&action=create')
        .then(response => response.text())
        .then(html => {
            document.getElementById('cajaChicaForm').innerHTML = html;
            document.getElementById('cajaChicaForm').style.display = 'block';
            addValidations();
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=cajachica&action=update&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('cajaChicaForm').innerHTML = html;
            document.getElementById('cajaChicaForm').style.display = 'block';
            addValidations();
        });
}

function cancelForm() {
    document.getElementById('cajaChicaForm').style.display = 'none';
    document.getElementById('cajaChicaForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('cajaChicaForm');
    const fields = {
        nombre: { required: true, minLength: 2 },
        monto_asignado: { required: true, type: 'number', min: 0 },
        monto_disponible: { required: true, type: 'number', min: 0 },
        id_usuario_encargado: { required: true },
        id_supervisor: { required: true },
        estado: { required: true }
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
            if (fields[fieldName].type === 'number' && isNaN(value)) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} debe ser un número.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].min && value < fields[fieldName].min) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} debe ser mayor o igual a ${fields[fieldName].min}.`;
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
                const result = await updateCajaChica(id, formData);
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
                const result = await createCajaChica(formData);
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
            loadCajasChicas();
            cancelForm();
        }
    });
}

loadCajasChicas();