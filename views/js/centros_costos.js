let debounceTimeout;
const modal = document.getElementById('modal');
const modalContent = document.getElementById('modalContent');

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded disparado');
    loadCentrosCostos();
    loadBases();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && modal) {
        showEditForm(id);
    }

    const searchInput = document.querySelector('#search');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                loadCentrosCostos();
            }, 300);
        });
    } else {
        console.error('Elemento #search no encontrado');
    }
});

let centrosCostos = [];

async function loadCentrosCostos() {
    try {
        console.log('Iniciando loadCentrosCostos');
        const searchTerm = document.querySelector('#search')?.value.trim() || '';
        const selectedBaseId = document.querySelector('#baseSelect')?.value || '';

        let url = 'index.php?controller=centrocosto&action=list'; // Cambia 'listCentrosCostos' por 'list'
        if (searchTerm) {
            url += `&search=${encodeURIComponent(searchTerm)}`;
        }
        if (selectedBaseId) {
            url += `&base_id=${encodeURIComponent(selectedBaseId)}`;
        }

        console.log('URL de la solicitud:', url);
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        console.log('Respuesta del servidor (status):', response.status);
        if (!response.ok) {
            const errorData = await response.json();
            if (response.status === 401) {
                throw new Error(errorData.error || 'No autorizado');
            }
            throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
        }

        const centros = await response.json();
        console.log('Datos recibidos:', centros);
        if (!Array.isArray(centros)) {
            throw new Error('La respuesta no es un array válido');
        }
        centrosCostos = centros;

        const tbody = document.querySelector('#centrosCostosTable tbody');
        if (!tbody) {
            throw new Error('Elemento #centrosCostosTable tbody no encontrado');
        }

        tbody.innerHTML = '';
        if (centros.length > 0) {
            centros.forEach(centro => {
                const estado = centro.estado === 'ACTIVO' ? 'Y' : 'N';
                tbody.innerHTML += `
                    <tr>
                        <td data-label="Código">${centro.codigo}</td>
                        <td data-label="Nombre">${centro.nombre}</td>
                        <td data-label="Tipo">${centro.tipo || '5'}</td>
                        <td data-label="Estado">${estado}</td>
                        <td data-label="Acciones">
                            <button class="update-btn" onclick="showUpdateForm(${centro.id})">Actualizar</button>
                            <button class="delete-btn" onclick="deleteCentroCosto(${centro.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="5">No se encontraron centros de costos con el nombre "${searchTerm}".</td></tr>`;
        }
        return centros;
    } catch (error) {
        console.error('Error al cargar centros de costos:', error);
        const tbody = document.querySelector('#centrosCostosTable tbody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="5">Error al cargar los centros de costos: ${error.message}</td></tr>`;
        }
        return [];
    }
}

async function checkCodigoExists(codigo) {
    try {
        console.log('Verificando código:', codigo);
        const response = await fetch(`index.php?controller=centrocosto&action=checkCodigo&codigo=${encodeURIComponent(codigo)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        console.log('Respuesta del servidor (status):', response.status);
        if (!response.ok) {
            const text = await response.text();
            console.log('Respuesta del servidor (texto):', text);
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al verificar el código');
            } catch (parseError) {
                throw new Error('Respuesta no es JSON válida: ' + text);
            }
        }
        const data = await response.json();
        console.log('Resultado de checkCodigoExists:', data);
        return data.exists;
    } catch (error) {
        console.error('Error al verificar el código:', error);
        return false;
    }
}

async function createCentroCosto(formData) {
    try {
        const response = await fetch('index.php?controller=centrocosto&action=create', { // Cambia 'createCentroCosto' por 'create'
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            throw new Error('Respuesta no es JSON válida: ' + text);
        }
        if (!response.ok) {
            throw new Error(result.error || 'Error al crear el centro de costos');
        }
        return result;
    } catch (error) {
        console.error('Error al enviar formulario:', error);
        throw error;
    }
}

async function updateCentroCosto(id, formData) {
    try {
        console.log('Actualizando centro de costos con ID:', id);
        console.log('Datos del formulario:', Object.fromEntries(formData));
        const response = await fetch(`index.php?controller=centrocosto&action=update&id=${id}`, { // Cambia 'updateCentroCosto' por 'update'
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        console.log('Respuesta del servidor (status):', response.status);
        const text = await response.text();
        console.log('Respuesta del servidor (texto):', text);
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            throw new Error('Respuesta no es JSON válida: ' + text);
        }
        if (!response.ok) {
            throw new Error(result.error || 'Error al actualizar el centro de costos');
        }
        return result;
    } catch (error) {
        console.error('Error al actualizar:', error);
        throw error;
    }
}

async function deleteCentroCosto(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este centro de costos?')) return;

    try {
        const response = await fetch(`index.php?controller=centrocosto&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json(); // Parsear la respuesta como JSON

        if (!response.ok) {
            // Si la respuesta no es exitosa, lanzar el error devuelto por el servidor
            throw new Error(result.error || 'Error al eliminar centro de costos');
        }

        // Si la eliminación es exitosa, mostrar el mensaje de éxito
        alert(result.message || 'Centro de costos eliminado');
        loadCentrosCostos();
    } catch (error) {
        console.error('Error al eliminar centro de costos:', error.message);
        alert(error.message || 'Error al eliminar centro de costos. Intenta de nuevo.');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalContent.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=centrocosto&action=list'); // Cambia 'listCentrosCostos' por 'list'
    }
}

async function showCreateForm() {
    try {
        const response = await fetch('index.php?controller=centrocosto&action=createForm', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${text}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        modalContent.innerHTML = html;
        modal.classList.add('active');

        const baseSelect = document.querySelector('#base_id');
        if (baseSelect) {
            const basesResponse = await fetch('index.php?controller=base&action=listBases', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!basesResponse.ok) {
                throw new Error('Error al cargar las bases');
            }
            const bases = await basesResponse.json();
            bases.forEach(base => {
                const option = document.createElement('option');
                option.value = base.id;
                option.textContent = base.nombre;
                baseSelect.appendChild(option);
            });
        }

        addCreateValidations();
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function showUpdateForm(id) {
    try {
        const response = await fetch(`index.php?controller=centrocosto&action=updateForm&id=${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${text}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        modalContent.innerHTML = html;
        modal.classList.add('active');
        addValidations(id);
    } catch (error) {
        console.error('Error al cargar el formulario de actualización:', error);
        modalContent.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addValidations(id = null) {
    const form = document.querySelector('#modalContent #centroCostoFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="centroCostoFormInner" dentro de #modalContent');
        return;
    }

    const fields = {
        codigo: { required: true, minLength: 2 },
        nombre: { required: true, minLength: 2 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
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
            if (fields[fieldName].minLength && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'codigo' && value && !id) {
                const exists = await checkCodigoExists(value);
                if (exists) {
                    errorElement.textContent = `El código "${value}" ya está registrado. Por favor, usa un código diferente.`;
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        const validations = await Promise.all(
            Array.from(form.querySelectorAll('input, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            const formId = formData.get('id') || id;

            try {
                const action = formId ? updateCentroCosto(formId, formData) : createCentroCosto(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadCentrosCostos();
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

async function loadBases() {
    try {
        const response = await fetch('index.php?controller=base&action=listBases', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            console.error('Respuesta del servidor:', text);
            throw new Error(`Error HTTP: ${response.status} - ${text.replace(/<[^>]+>/g, '')}`);
        }
        const bases = await response.json();
        if (!Array.isArray(bases)) {
            throw new Error('La respuesta no es un array válido');
        }
        const baseSelect = document.querySelector('#baseSelect');
        baseSelect.innerHTML = '<option value="">Seleccione la Base</option>';
        bases.forEach(base => {
            const option = document.createElement('option');
            option.value = base.id;
            option.textContent = base.nombre;
            baseSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar bases:', error);
        const baseSelect = document.querySelector('#baseSelect');
        const errorMessage = error.message.replace(/<[^>]+>/g, '').trim() || 'Error desconocido';
        baseSelect.innerHTML = `<option value="">${errorMessage}</option>`;
    }
}

function addCreateValidations() {
    const form = document.querySelector('#modalContent #centroCostoFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="centroCostoFormInner" dentro de #modalContent');
        return;
    }

    const fields = {
        codigo: { required: true, minLength: 2 },
        nombre: { required: true, minLength: 2 },
        tipo: { required: true, minLength: 1 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value.trim();
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`);
        if (!errorElement) {
            console.error(`No se encontró un elemento .error para el campo ${fieldName}`);
            return false;
        }

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

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
            if (fieldName === 'codigo') {
                const exists = await checkCodigoExists(value);
                if (exists) {
                    errorElement.textContent = 'El código ya existe. Por favor, usa un código diferente.';
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        const validations = await Promise.all(
            Array.from(form.querySelectorAll('input, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            try {
                const result = await createCentroCosto(formData);
                alert(result.message || 'Centro de costos creado');
                closeModal();
                loadCentrosCostos();
            } catch (error) {
                console.error('Error al crear:', error);
                const errorElement = form.querySelector('.error:not([data-field])');
                if (errorElement) {
                    errorElement.textContent = error.message || 'Error al crear. Intenta de nuevo.';
                    errorElement.style.display = 'block';
                } else {
                    console.error('No se encontró un elemento .error para mostrar el mensaje de error');
                }
            }
        }
    });
}