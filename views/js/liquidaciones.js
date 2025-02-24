async function loadLiquidaciones() {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=liquidacion&action=list', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    const liquidaciones = await response.json();
    const tbody = document.querySelector('#liquidacionesTable tbody');
    tbody.innerHTML = '';
    liquidaciones.forEach(liquidacion => {
        tbody.innerHTML += `
            <tr>
                <td>${liquidacion.id}</td>
                <td>${liquidacion.nombre_caja_chica}</td>
                <td>${liquidacion.fecha_creacion}</td>
                <td>${liquidacion.monto_total}</td>
                <td>${liquidacion.estado}</td>
                <td>
                    <button onclick="showEditForm(${liquidacion.id})">Editar</button>
                    <button onclick="deleteLiquidacion(${liquidacion.id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
}

async function createLiquidacion(data) {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=liquidacion&action=create', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function updateLiquidacion(id, data) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=liquidacion&action=update&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function deleteLiquidacion(id) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=liquidacion&action=delete&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (response.ok) loadLiquidaciones();
}

function showCreateForm() {
    fetch('index.php?controller=liquidacion&action=create')
        .then(response => response.text())
        .then(html => {
            document.getElementById('liquidacionForm').innerHTML = html;
            document.getElementById('liquidacionForm').style.display = 'block';
            addValidations();
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=liquidacion&action=update&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('liquidacionForm').innerHTML = html;
            document.getElementById('liquidacionForm').style.display = 'block';
            addValidations();
        });
}

function cancelForm() {
    document.getElementById('liquidacionForm').style.display = 'none';
    document.getElementById('liquidacionForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('liquidacionForm');
    const fields = {
        id_caja_chica: { required: true },
        fecha_creacion: { required: true },
        monto_total: { required: true, type: 'number', min: 0 },
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
                const result = await updateLiquidacion(id, formData);
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
                const result = await createLiquidacion(formData);
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
            loadLiquidaciones();
            cancelForm();
        }
    });
}

loadLiquidaciones();