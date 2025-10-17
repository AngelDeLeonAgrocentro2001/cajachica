const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');
const reportesOutput = document.querySelector('#reportesOutput');
const detallesModal = document.querySelector('#detallesModal');
const detallesModalForm = document.querySelector('#detallesModalForm');

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
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addResumenValidations();
    } catch (error) {
        console.error('Error al cargar el formulario (resumen):', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
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
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addDetalleValidations();
    } catch (error) {
        console.error('Error al cargar el formulario (detalle):', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
    }
}

function closeDetallesModal() {
    if (detallesModal) {
        detallesModal.classList.remove('active');
        detallesModalForm.innerHTML = '';
    }
}

async function showDetalles(idLiquidacion) {
    if (!detallesModal || !detallesModalForm) {
        console.error('DetallesModal o detallesModalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del modal de detalles. Intenta de nuevo.');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('id_liquidacion', idLiquidacion);

        const response = await fetch('index.php?controller=reportes&action=getDetallesByLiquidacion', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }

        const html = await response.text();
        detallesModalForm.innerHTML = html;
        detallesModal.classList.add('active');
    } catch (error) {
        console.error('Error al cargar los detalles:', error);
        detallesModalForm.innerHTML = `<div class="error">${error.message}</div>`;
        detallesModal.classList.add('active');
    }
}

function exportReport(tipoReporte, formato) {
    // Obtener los parámetros del formulario más reciente (almacenados en el DOM)
    const fechaInicio = localStorage.getItem('reporteFechaInicio') || '';
    const fechaFin = localStorage.getItem('reporteFechaFin') || '';
    const idCajaChica = localStorage.getItem('reporteIdCajaChica') || '';

    const formData = new FormData();
    formData.append('fecha_inicio', fechaInicio);
    formData.append('fecha_fin', fechaFin);
    formData.append('id_caja_chica', idCajaChica);
    formData.append('formato', formato);

    const action = tipoReporte === 'resumen' ? 'generarResumen' : 'generarDetalle';

    fetch(`index.php?controller=reportes&action=${action}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(response => {
        if (!response.ok) {
            throw new Error('Error al exportar el reporte');
        }
        return response.blob();
    }).then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_${tipoReporte}.${formato === 'pdf' ? 'pdf' : 'xlsx'}`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
    }).catch(error => {
        console.error('Error al exportar:', error);
        alert('Error al exportar el reporte: ' + error.message);
    });
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
            if (fieldName === 'fecha_inicio' || fieldName === 'fecha_fin') {
                const fechaInicio = new Date(form.querySelector('[name="fecha_inicio"]').value);
                const fechaFin = new Date(form.querySelector('[name="fecha_fin"]').value);
                if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                    errorElement.textContent = 'La fecha de inicio no puede ser mayor que la fecha de fin.';
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

                // Almacenar los parámetros para usarlos en la exportación
                localStorage.setItem('reporteFechaInicio', formData.get('fecha_inicio'));
                localStorage.setItem('reporteFechaFin', formData.get('fecha_fin'));
                localStorage.setItem('reporteIdCajaChica', formData.get('id_caja_chica'));

                closeModal();
            } catch (error) {
                console.error('Error al generar reporte:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al generar el reporte. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
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
            if (fieldName === 'fecha_inicio' || fieldName === 'fecha_fin') {
                const fechaInicio = new Date(form.querySelector('[name="fecha_inicio"]').value);
                const fechaFin = new Date(form.querySelector('[name="fecha_fin"]').value);
                if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                    errorElement.textContent = 'La fecha de inicio no puede ser mayor que la fecha de fin.';
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

                // Almacenar los parámetros para usarlos en la exportación
                localStorage.setItem('reporteFechaInicio', formData.get('fecha_inicio'));
                localStorage.setItem('reporteFechaFin', formData.get('fecha_fin'));
                localStorage.setItem('reporteIdCajaChica', formData.get('id_caja_chica'));

                closeModal();
            } catch (error) {
                console.error('Error al generar reporte:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al generar el reporte. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
            }
        }
    });
}

async function exportDetallesToPDF(idLiquidacion) {
    const formData = new FormData();
    formData.append('id_liquidacion', idLiquidacion);

    try {
        const response = await fetch('index.php?controller=reportes&action=exportDetallesToPDF', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Clonar la respuesta antes de leerla para verificar errores
        const responseClone = response.clone();

        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            let errorMessage = 'Error al exportar los detalles';
            try {
                const errorData = await responseClone.json();
                errorMessage = errorData.error || errorMessage;
            } catch (jsonError) {
                // Si JSON parsing falla, intentar como texto
                try {
                    const errorText = await responseClone.text();
                    errorMessage = errorText || 'Error desconocido del servidor';
                } catch (textError) {
                    errorMessage = `Error HTTP: ${response.status} ${response.statusText}`;
                }
            }
            throw new Error(errorMessage);
        }

        // Verificar el tipo de contenido
        const contentType = response.headers.get('Content-Type');
        if (!contentType || !contentType.includes('application/pdf')) {
            throw new Error('El servidor no devolvió un PDF válido');
        }

        // Obtener el blob de la respuesta original (no clonada)
        const blob = await response.blob();
        
        // Crear y descargar el archivo
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `detalles_liquidacion_${idLiquidacion}.pdf`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        
    } catch (error) {
        console.error('Error al exportar a PDF:', error);
        alert('Error al exportar los detalles a PDF: ' + error.message);
    }
}