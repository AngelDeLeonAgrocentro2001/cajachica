async function loadTiposGastos() {
    try {
        const response = await fetch('index.php?controller=tipogasto&action=list', {
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
        const tipos = await response.json();
        const tbody = document.querySelector('#tiposGastosTable tbody');
        tbody.innerHTML = '';
        if (tipos.length > 0) {
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
        } else {
            tbody.innerHTML = '<tr><td colspan="4">No hay tipos de gastos registrados.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar tipos de gastos:', error);
        alert('No se pudo cargar la lista de tipos de gastos. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createTipoGasto(data) {
    const response = await fetch('index.php?controller=tipogasto&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al crear tipo de gasto: ${errorData.error || await response.text()}`);
    }
    return response.json();
}

async function updateTipoGasto(id, data) {
    const response = await fetch(`index.php?controller=tipogasto&action=update&id=${id}`, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al actualizar tipo de gasto: ${errorData.error || await response.text()}`);
    }
    return response.json();
}

async function deleteTipoGasto(id) {
    const response = await fetch(`index.php?controller=tipogasto&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al eliminar tipo de gasto: ${errorData.error || await response.text()}`);
    }
    if (response.ok) loadTiposGastos();
}

function showCreateForm() {
    fetch('index.php?controller=tipogasto&action=create')
        .then(response => {
            if (!response.ok) throw new Error(`Error al cargar formulario: ${response.status}`);
            return response.text();
        })
        .then(html => {
            document.getElementById('tipoGastoForm').innerHTML = html;
            document.getElementById('tipoGastoForm').style.display = 'block';
            addValidations();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('No se pudo cargar el formulario. Por favor, intenta de nuevo.');
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=tipogasto&action=update&id=${id}`)
        .then(response => {
            if (!response.ok) throw new Error(`Error al cargar formulario: ${response.status}`);
            return response.text();
        })
        .then(html => {
            document.getElementById('tipoGastoForm').innerHTML = html;
            document.getElementById('tipoGastoForm').style.display = 'block';
            addValidations();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('No se pudo cargar el formulario. Por favor, intenta de nuevo.');
        });
}

function cancelForm() {
    document.getElementById('tipoGastoForm').style.display = 'none';
    document.getElementById('tipoGastoForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('tipoGastoFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> con ID #tipoGastoFormInner. Verifica el HTML cargado:', document.getElementById('tipoGastoForm')?.innerHTML || 'No se encontró #tipoGastoForm');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

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
            try {
                if (id) {
                    const result = await updateTipoGasto(id, formData);
                    if (result.message) {
                        window.location.reload(); // Recargar la página automáticamente
                    } else if (result.error) {
                        const errorElement = form.querySelector('.error') || document.createElement('div');
                        errorElement.className = 'error';
                        errorElement.textContent = result.error;
                        errorElement.style.display = 'block';
                    }
                } else {
                    const result = await createTipoGasto(formData);
                    if (result.message) {
                        window.location.reload(); // Recargar la página automáticamente
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
                errorElement.textContent = 'Error al procesar la solicitud. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}

loadTiposGastos();