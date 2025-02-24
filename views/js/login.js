document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    try {
        const response = await fetch('index.php?controller=usuario&action=login', {
            method: 'POST',
            body: formData
        });
        console.log('Estado de la respuesta:', response.status); // Depuración: estado HTTP
        const responseText = await response.text();
        console.log('Cuerpo de la respuesta (texto):', responseText);
        let data;
        try {
            data = responseText ? JSON.parse(responseText) : {};
        } catch (parseError) {
            throw new Error('Respuesta no es un JSON válido: ' + parseError.message);
        }
        if (response.ok) {
            if (data.token) {
                localStorage.setItem('token', data.token);
                window.location.href = 'index.php?controller=usuario&action=list';
            } else {
                throw new Error('Token no encontrado en la respuesta');
            }
        } else {
            document.getElementById('message').innerText = data.error || 'Error desconocido';
            document.getElementById('message').style.color = '#dc3545';
        }
    } catch (error) {
        document.getElementById('message').innerText = 'Error en la conexión: ' + error.message;
        document.getElementById('message').style.color = '#dc3545';
        console.error('Error detallado:', error);
    }
});