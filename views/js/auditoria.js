async function loadAuditoria() {
    try {
        const response = await fetch('index.php?controller=Auditoria&action=list', {
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
        const auditorias = await response.json();
        const tbody = document.querySelector('#AuditoriaesTable tbody');
        tbody.innerHTML = '';
        if (auditorias.length > 0) {
            auditorias.forEach(auditorial => {
                tbody.innerHTML += `
                    <tr>
                        <td>${auditorial.id}</td>
                        <td>${auditorial.id_caja_chica || 'General'}</td>
                        <td>${auditorial.no_factura || 'N/A'}</td>
                        <td>${auditorial.nombre_usuario}</td>
                        <td>${auditorial.accion}</td>
                        <td>${auditorial.fecha}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6">No hay auditorias de aprobaciones registrados.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar auditorial de aprobaciones:', error);
        alert('No se pudo cargar la lista de auditorial de aprobaciones. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createAuditoria(data) {
    const response = await fetch('index.php?controller=Auditoria&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(`Error al crear auditorial de aprobación: ${errorData.error || await response.text()}`);
    }
    return response.json();
}

function showCreateForm() {
    fetch('index.php?controller=Auditoria&action=create')
        .then(response => {
            if (!response.ok) throw new Error(`Error al cargar formulario: ${response.status}`);
            return response.text();
        })
        .then(html => {
            document.getElementById('AuditoriaForm').innerHTML = html;
            document.getElementById('AuditoriaForm').style.display = 'block';
            addValidations();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('No se pudo cargar el formulario. Por favor, intenta de nuevo.');
        });
}

function cancelForm() {
    document.getElementById('AuditoriaForm').style.display = 'none';
    document.getElementById('AuditoriaForm').innerHTML = '';
}

function addValidations() {
    const form = document.getElementById('AuditoriaFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> con ID #AuditoriaFormInner. Verifica el HTML cargado:', document.getElementById('AuditoriaForm')?.innerHTML || 'No se encontró #AuditoriaForm');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

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

    // Inspeccionar el HTML de los <select> para depuración
    form.querySelectorAll('select').forEach(select => {
        console.log(select.outerHTML);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        form.querySelectorAll('select').forEach(field => {
            if (!validateField({ target: field })) isValid = false;
        });

        if (isValid) {
            const formData = new FormData(form);
            try {
                const result = await createAuditoria(formData);
                if (result.message) {
                    window.location.reload(); // Recargar la página automáticamente
                } else if (result.error) {
                    const errorElement = form.querySelector('.error') || document.createElement('div');
                    errorElement.className = 'error';
                    errorElement.textContent = result.error;
                    errorElement.style.display = 'block';
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

loadAuditoria();