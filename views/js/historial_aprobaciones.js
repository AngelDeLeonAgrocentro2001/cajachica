async function loadHistorialAprobaciones() {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=historialaprobacion&action=list', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    const historiales = await response.json();
    const tbody = document.querySelector('#historialAprobacionesTable tbody');
    tbody.innerHTML = '';
    historiales.forEach(historial => {
        tbody.innerHTML += `
            <tr>
                <td>${historial.id}</td>
                <td>${historial.id_caja_chica || 'General'}</td>
                <td>${historial.no_factura || 'N/A'}</td>
                <td>${historial.nombre_usuario}</td>
                <td>${historial.accion}</td>
                <td>${historial.fecha}</td>
            </tr>
        `;
    });
}

async function createHistorialAprobacion(data) {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=historialaprobacion&action=create', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

function showCreateForm() {
    fetch('index.php?controller=historialaprobacion&action=create')
        .then(response => response.text())
        .then(html => {
            document.getElementById('historialAprobacionForm').innerHTML = html;
            document.getElementById('historialAprobacionForm').style.display = 'block';
            addValidations();
        });
}

function cancelForm() {
    document.getElementById('historialAprobacionForm').style.display = 'none';
    document.getElementById('historialAprobacionForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('historialAprobacionForm');
    const fields = {
        id_liquidacion: { required: true },
        id_usuario: { required: true },
        accion: { required: true }
    };

    form.querySelectorAll('select').forEach(field => {
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
            errorElement.style.display = 'none';
            e.target.classList.remove('invalid');
            return true;
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        form.querySelectorAll('select').forEach(field => {
            if (!validateField({ target: field })) isValid = false;
        });

        if (isValid) {
            const formData = new FormData(form);
            const result = await createHistorialAprobacion(formData);
            if (result.message) {
                alert(result.message);
                document.querySelector('.success').textContent = result.message;
                document.querySelector('.success').style.display = 'block';
            } else if (result.error) {
                alert(result.error);
                document.querySelector('.error').textContent = result.error;
                document.querySelector('.error').style.display = 'block';
            }
            loadHistorialAprobaciones();
            cancelForm();
        }
    });
}

loadHistorialAprobaciones();