const form = document.getElementById('loginForm');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch('index.php?controller=login&action=login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Error desconocido');
            }
            const result = await response.json();
            if (result.message && result.redirect) {
                window.location.href = result.redirect; // Redirigir desde el cliente
            } else if (result.error) {
                const errorElement = form.querySelector('.error');
                errorElement.textContent = result.error;
                errorElement.style.display = 'block';
            }
        } catch (error) {
            console.error('Error detallado:', error);
            const errorElement = form.querySelector('.error') || document.createElement('div');
            errorElement.className = 'error';
            errorElement.textContent = 'Error al iniciar sesión: ' + error.message;
            errorElement.style.display = 'block';
        }
    });