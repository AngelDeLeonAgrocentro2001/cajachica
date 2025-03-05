const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');
const reportesOutput = document.querySelector('#reportesOutput');

document.addEventListener('DOMContentLoaded', () => {
    // Cargar inicialmente la vista básica
});

async function showResumenForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=reportes&action=generarResumen', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        console.log('HTML devuelto (resumen):', html);
        modalForm.innerHTML = html;
        modal.style.display = 'flex';
        const form = modalForm.querySelector('#resumenFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="resumenFormInner" dentro de modalForm');
            modalForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addResumenValidations();
    } catch (error) {
        console.error('Error al cargar el formulario (resumen):', error);
        if (modalForm) {
            modalForm.innerHTML = `<div class="error">${error.message}</div>`;
            modal.style.display = 'flex';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    }
}

async function showDetalleForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=reportes&action=generarDetalle', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        console.log('HTML devuelto (detalle):', html);
        modalForm.innerHTML = html;
        modal.style.display = 'flex';
        const form = modalForm.querySelector('#detalleFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="detalleFormInner" dentro de modalForm');
            modalForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addDetalleValidations();
    } catch (error) {
        console.error('Error al cargar el formulario (detalle):', error);
        if (modalForm) {
            modalForm.innerHTML = `<div class="error">${error.message}</div>`;
            modal.style.display = 'flex';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    }
}

function closeModal() {
    if (modal) {
        modal.style.display = 'none';
        modalForm.innerHTML = '';
    }
}

function addResumenValidations() {
    const form = document.querySelector('#modalForm #resumenFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="resumenFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        fecha_inicio: { required: true },
        fecha_fin: { required: true },
        id_caja_chica: { required: false }
    };

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value.trim();
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`);
        if (!errorElement) return true;

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'fecha_inicio' && fieldName === 'fecha_fin') {
                const fechaInicio = new Date(form.querySelector('[name="fecha_inicio"]').value);
                const fechaFin = new Date(form.querySelector('[name="fecha_fin"]').value);
                if (fechaInicio > fechaFin) {
                    errorElement.textContent = 'La fecha de inicio no puede ser mayor que la fecha de fin.';
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
            return true;
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
                const response = await fetch('index.php?controller=reportes&action=generarResumen', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Error al generar el reporte');
                }
                const result = await response.text();
                reportesOutput.innerHTML = result;
                closeModal();
            } catch (error) {
                console.error('Error al generar reporte:', error);
                const errorElement = form.querySelector('.error');
                errorElement.textContent = error.message || 'Error al generar el reporte. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}

function addDetalleValidations() {
    const form = document.querySelector('#modalForm #detalleFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="detalleFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        fecha_inicio: { required: true },
        fecha_fin: { required: true },
        id_caja_chica: { required: false }
    };

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value.trim();
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`);
        if (!errorElement) return true;

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'fecha_inicio' && fieldName === 'fecha_fin') {
                const fechaInicio = new Date(form.querySelector('[name="fecha_inicio"]').value);
                const fechaFin = new Date(form.querySelector('[name="fecha_fin"]').value);
                if (fechaInicio > fechaFin) {
                    errorElement.textContent = 'La fecha de inicio no puede ser mayor que la fecha de fin.';
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
            return true;
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
                const response = await fetch('index.php?controller=reportes&action=generarDetalle', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Error al generar el reporte');
                }
                const result = await response.text();
                reportesOutput.innerHTML = result;
                closeModal();
            } catch (error) {
                console.error('Error al generar reporte:', error);
                const errorElement = form.querySelector('.error');
                errorElement.textContent = error.message || 'Error al generar el reporte. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}