const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');
const cuentaId = new URLSearchParams(window.location.search).get('cuenta_id');

document.addEventListener('DOMContentLoaded', () => {
    loadAccesos();
});

async function loadAccesos() {
    try {
        const response = await fetch(`index.php?controller=acceso&action=list&cuenta_id=${cuentaId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al cargar accesos');
        }
        const accesos = await response.json();
        const tbody = document.querySelector('#accesosTable tbody');
        tbody.innerHTML = '';
        if (accesos.length > 0) {
            accesos.forEach(acceso => {
                tbody.innerHTML += `
                    <tr>
                        <td>${acceso.email}</td>
                        <td>
                            <button onclick="removeUsuario(${acceso.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="2">No hay usuarios asignados.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar accesos:', error);
        alert(error.message || 'Error al cargar accesos');
    }
}

async function showAssignForm() {
    try {
        const response = await fetch(`index.php?controller=acceso&action=assignForm&cuenta_id=${cuentaId}`, {
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
        addAssignValidations();
    } catch (error) {
        console.error('Error al cargar formulario de asignación:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addAssignValidations() {
    const form = document.querySelector('#modalForm #assignFormInner');
    if (!form) {
        console.error('No se encontró el formulario de asignación');
        return;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch(`index.php?controller=acceso&action=assign&cuenta_id=${cuentaId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Error al asignar usuario');
            }
            const result = await response.json();
            alert(result.message || 'Usuario asignado');
            closeModal();
            loadAccesos();
        } catch (error) {
            console.error('Error al asignar usuario:', error);
            const errorElement = form.querySelector('.error[data-field="email"]');
            errorElement.textContent = error.message;
            errorElement.style.display = 'block';
        }
    });
}

async function removeUsuario(usuarioId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) return;
    try {
        const response = await fetch(`index.php?controller=acceso&action=remove&cuenta_id=${cuentaId}&usuario_id=${usuarioId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al eliminar usuario');
        }
        const result = await response.json();
        alert(result.message || 'Usuario eliminado');
        loadAccesos();
    } catch (error) {
        console.error('Error al eliminar usuario:', error);
        alert(error.message || 'Error al eliminar usuario');
    }
}

function closeModal() {
    modal.classList.remove('active');
    modalForm.innerHTML = '';
}