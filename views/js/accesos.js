const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

document.addEventListener('DOMContentLoaded', () => {
    loadUsuarios();
});

async function loadUsuarios() {
    try {
        const response = await fetch('index.php?controller=acceso&action=list', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al cargar usuarios');
        }
        const usuarios = await response.json();
        const tbody = document.querySelector('#accesosTable tbody');
        tbody.innerHTML = '';
        if (usuarios.length > 0) {
            usuarios.forEach(usuario => {
                tbody.innerHTML += `
                    <tr>
                        <td data-label="Correo">${usuario.email}</td>
                        <td data-label="Rol">${usuario.rol}</td>
                        <td data-label="Acciones">
                            <button onclick="showManageModules(${usuario.id})">Asignar Módulos</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3">No hay usuarios registrados.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        alert(error.message || 'Error al cargar usuarios');
    }
}

async function showManageModules(userId) {
    try {
        const response = await fetch(`index.php?controller=acceso&action=manageModules&user_id=${userId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        modalForm.innerHTML = html;
        modal.classList.add('active');

        const form = modalForm.querySelector('form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                console.log('Datos del formulario antes de enviar:');
                for (let pair of formData.entries()) {
                    console.log('Dato enviado:', pair[0], pair[1]);
                }

                try {
                    const response = await fetch(`index.php?controller=acceso&action=manageModules&user_id=${userId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || 'Error al guardar módulos');
                    }
                    const result = await response.json();
                    console.log('Respuesta del servidor:', result);
                    alert(result.message || 'Módulos asignados correctamente');
                    closeModal();
                    loadUsuarios();
                } catch (error) {
                    console.error('Error al guardar módulos:', error);
                    alert(error.message || 'Error al guardar módulos');
                }
            });
        } else {
            console.error('No se encontró el formulario en el modal');
        }
    } catch (error) {
        console.error('Error al cargar formulario de módulos:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function closeModal() {
    modal.classList.remove('active');
    modalForm.innerHTML = '';
    loadUsuarios();
}