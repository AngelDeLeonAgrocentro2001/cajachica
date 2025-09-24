async function loadAuditoria() {
    const form = document.getElementById('auditoriaFilterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    
    console.log('Parámetros enviados:', params);
    try {
        const response = await fetch(`index.php?controller=auditoria&action=getAuditoria&${params}`, {
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
        const auditoria = await response.json();
        // console.log('Registros recibidos:', auditoria);
        const tbody = document.querySelector('#auditoriaTable tbody');
        tbody.innerHTML = '';
        if (auditoria.length > 0) {
            auditoria.forEach(entry => {
                let detallesHtml = '-';
                try {
                    const detalles = JSON.parse(entry.detalles || '{}');
                    detallesHtml = '<table class="details-table">';
                    detallesHtml += '<thead><tr><th>Campo</th><th>Valor</th></tr></thead>';
                    detallesHtml += '<tbody>';
                    for (const [key, value] of Object.entries(detalles)) {
                        detallesHtml += `<tr><td>${key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ')}</td><td>${value || 'N/A'}</td></tr>`;
                    }
                    detallesHtml += '</tbody></table>';
                } catch (e) {
                    // console.error('Error al parsear detalles para entrada ID ' + entry.id + ':', e);
                    detallesHtml = entry.detalles || '-';
                }

                const fecha = new Date(entry.fecha).toLocaleString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${entry.id}</td>
                        <td data-label="Liquidación">${entry.id_liquidacion || '-'}</td>
                        <td data-label="Detalle">${entry.id_detalle_liquidacion || '-'}</td>
                        <td data-label="Usuario">${entry.usuario_nombre}</td>
                        <td data-label="Tipo de Acción"><span>${entry.tipo_accion}</span></td>
                        <td data-label="Detalles"><span>${detallesHtml}</span></td>
                        <td data-label="Fecha">${fecha}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7">No hay registros de auditoría.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar auditoría:', error.message);
        alert('No se pudo cargar el historial de auditoría: ' + error.message);
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

async function updateAuditoria(id, data) {
    const response = await fetch(`index.php?controller=Auditoria&action=update&id=${id}`, {
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

const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=auditoria&action=list');
    }
}

function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch('index.php?controller=Auditoria&action=create', {
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
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addValidations();
    })
    .catch(error => {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    });
}

function showEditForm(id) {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    fetch(`index.php?controller=Auditoria&action=update&id=${id}`, {
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
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addValidations(id);
    })
    .catch(error => {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    });
}

function addValidations(id = null) {
    const form = document.querySelector('#modalForm #AuditoriaFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="AuditoriaFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        id_liquidacion: { required: true },
        id_usuario: { required: true },
        accion: { required: true },
        comentario: { required: false }
    };

    form.querySelectorAll('select, input').forEach(field => {
        field.addEventListener('input', validateField);
    });

    function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value.trim();
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`) || document.createElement('div');
        errorElement.className = 'error';
        errorElement.setAttribute('data-field', fieldName);
        if (!form.contains(errorElement)) {
            e.target.parentNode.appendChild(errorElement);
        }

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        const validations = await Promise.all(
            Array.from(form.querySelectorAll('select, input')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            const formId = formData.get('id') || id;

            try {
                const action = formId ? updateAuditoria(formId, formData) : createAuditoria(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadAuditoria();
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al enviar el formulario. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    loadAuditoria();
});