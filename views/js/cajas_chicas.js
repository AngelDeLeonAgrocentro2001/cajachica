async function loadCajasChicas() {
    const tbody = document.querySelector('#cajasChicasTable tbody');
    if (!tbody) {
        console.log('Tabla #cajasChicasTable no encontrada, omitiendo carga de cajas chicas');
        return;
    }

    try {
        const response = await fetch('index.php?controller=cajachica&action=list', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
            } catch (parseError) {
                throw new Error(`Error HTTP: ${response.status} - Respuesta no es JSON válida: ${text}`);
            }
        }
        const cajasChicas = await response.json();
        tbody.innerHTML = '';
        if (cajasChicas.length > 0) {
            cajasChicas.forEach(caja => {
                tbody.innerHTML += `
                    <tr>
                        <td>${caja.id}</td>
                        <td>${caja.nombre}</td>
                        <td>${caja.monto_asignado}</td>
                        <td>${caja.monto_disponible}</td>
                        <td>${caja.estado || 'Sin estado'}</td>
                        <td>
                            <button onclick="showEditForm(${caja.id})">Editar</button>
                            <button onclick="deleteCajaChica(${caja.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6">No hay cajas chicas registradas.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar cajas chicas:', error.message);
        alert('No se pudo cargar la lista de cajas chicas: ' + error.message + '. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createCajaChica(data) {
    const response = await fetch('index.php?controller=cajachica&action=create', {
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

async function updateCajaChica(id, data) {
    const response = await fetch(`index.php?controller=cajachica&action=update&id=${id}`, {
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

async function deleteCajaChica(id) {
    const response = await fetch(`index.php?controller=cajachica&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || await response.text());
    }
    if (response.ok) loadCajasChicas();
}

function showCreateForm() {
    fetch('index.php?controller=cajachica&action=create')
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Error al cargar formulario: ${response.status} - ${text}`);
                });
            }
            return response.text();
        })
        .then(html => {
            const formContainer = document.getElementById('cajaChicaForm');
            formContainer.innerHTML = html;
            formContainer.style.display = 'block';
            console.log('HTML cargado:', html); // Depuración
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar formulario:', error.message);
            alert('No se pudo cargar el formulario: ' + error.message + '. Por favor, intenta de nuevo.');
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=cajachica&action=update&id=${id}`)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Error al cargar formulario: ${response.status} - ${text}`);
                });
            }
            return response.text();
        })
        .then(html => {
            const formContainer = document.getElementById('cajaChicaForm');
            formContainer.innerHTML = html;
            formContainer.style.display = 'block';
            console.log('HTML cargado:', html); // Depuración
            addValidations();
        })
        .catch(error => {
            console.error('Error al cargar formulario:', error.message);
            alert('No se pudo cargar el formulario: ' + error.message + '. Por favor, intenta de nuevo.');
        });
}

function cancelForm() {
    document.getElementById('cajaChicaForm').style.display = 'none';
    document.getElementById('cajaChicaForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('cajaChicaFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> dentro de #cajaChicaFormInner');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

    const fields = {
        nombre: { required: true },
        monto_asignado: { required: true, type: 'number', min: 0 },
        id_usuario_encargado: { required: true },
        id_supervisor: { required: true }
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
            try {
                if (id) {
                    const result = await updateCajaChica(id, formData);
                    if (result.message) {
                        window.location.reload();
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                } else {
                    const result = await createCajaChica(formData);
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

if (document.querySelector('#cajasChicasTable')) {
    loadCajasChicas();
}