function previewImage(input) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewPDF(input) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const pdfUrl = URL.createObjectURL(input.files[0]);
        const link = document.createElement('a');
        link.href = pdfUrl;
        link.textContent = 'Previsualizar PDF';
        link.target = '_blank';
        preview.appendChild(link);
    }
}

async function loadDetallesLiquidacion() {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=detalleliquidacion&action=list', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    const detalles = await response.json();
    const tbody = document.querySelector('#detallesLiquidacionesTable tbody');
    tbody.innerHTML = '';
    detalles.forEach(detalle => {
        tbody.innerHTML += `
            <tr>
                <td>${detalle.id}</td>
                <td>${detalle.no_factura}</td>
                <td>${detalle.nombre_proveedor}</td>
                <td>${detalle.total_factura}</td>
                <td>${detalle.estado}</td>
                <td>
                    <button onclick="showEditForm(${detalle.id})">Editar</button>
                    <button onclick="deleteDetalleLiquidacion(${detalle.id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
}

async function createDetalleLiquidacion(data) {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=detalleliquidacion&action=create', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function updateDetalleLiquidacion(id, data) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function deleteDetalleLiquidacion(id) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=detalleliquidacion&action=delete&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (response.ok) loadDetallesLiquidacion();
}

function showCreateForm() {
    fetch('index.php?controller=detalleliquidacion&action=create')
        .then(response => response.text())
        .then(html => {
            document.getElementById('detalleLiquidacionForm').innerHTML = html;
            document.getElementById('detalleLiquidacionForm').style.display = 'block';
            addValidations();
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('detalleLiquidacionForm').innerHTML = html;
            document.getElementById('detalleLiquidacionForm').style.display = 'block';
            addValidations();
        });
}

function cancelForm() {
    document.getElementById('detalleLiquidacionForm').style.display = 'none';
    document.getElementById('detalleLiquidacionForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('detalleLiquidacionForm');
    const fields = {
        id_liquidacion: { required: true },
        no_factura: { required: true, minLength: 1 },
        nombre_proveedor: { required: true, minLength: 2 },
        fecha: { required: true },
        bien_servicio: { required: true, minLength: 2 },
        t_gasto: { required: true, minLength: 2 },
        p_unitario: { required: true, type: 'number', min: 0 },
        total_factura: { required: true, type: 'number', min: 0 },
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
                const result = await updateDetalleLiquidacion(id, formData);
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
                const result = await createDetalleLiquidacion(formData);
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
            loadDetallesLiquidacion();
            cancelForm();
        }
    });
}

loadDetallesLiquidacion();