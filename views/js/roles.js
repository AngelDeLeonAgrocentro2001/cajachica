async function loadRoles() {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=role&action=list', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    const roles = await response.json();
    const tbody = document.querySelector('#rolesTable tbody');
    tbody.innerHTML = '';
    roles.forEach(role => {
        tbody.innerHTML += `
            <tr>
                <td>${role.id}</td>
                <td>${role.name}</td>
                <td>${role.description}</td>
                <td>
                    <button onclick="showEditForm(${role.id})">Editar</button>
                    <button onclick="deleteRole(${role.id})">Eliminar</button>
                </td>
            </tr>
        `;
    });
}

async function createRole(data) {
    const token = localStorage.getItem('token');
    const response = await fetch('index.php?controller=role&action=create', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function updateRole(id, data) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=role&action=update&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: data
    });
    return response.json();
}

async function deleteRole(id) {
    const token = localStorage.getItem('token');
    const response = await fetch(`index.php?controller=role&action=delete&id=${id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (response.ok) loadRoles();
}

function showCreateForm() {
    fetch('index.php?controller=role&action=create')
        .then(response => response.text())
        .then(html => {
            document.getElementById('roleForm').innerHTML = html;
            document.getElementById('roleForm').style.display = 'block';
        });
}

function showEditForm(id) {
    fetch(`index.php?controller=role&action=update&id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('roleForm').innerHTML = html;
            document.getElementById('roleForm').style.display = 'block';
        });
}

function cancelForm() {
    document.getElementById('roleForm').style.display = 'none';
    document.getElementById('roleForm').innerHTML = '';
}

document.getElementById('roleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const id = formData.get('id');
    if (id) {
        const result = await updateRole(id, formData);
        if (result.message) alert(result.message);
    } else {
        const result = await createRole(formData);
        if (result.message) alert(result.message);
    }
    loadRoles();
    cancelForm();
});

loadRoles();