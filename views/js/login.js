document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#loginForm');
    if (form) {
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

                const text = await response.text();
                let data;

                try {
                    data = JSON.parse(text);
                } catch (jsonError) {
                    throw new Error(`Respuesta no es JSON válido: ${text}`);
                }

                if (!response.ok) {
                    throw new Error(data.error || `Error HTTP: ${response.status}`);
                }

                if (data.message === 'Inicio de sesión exitoso' && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.error || 'Error al iniciar sesión');
                }
            } catch (error) {
                console.error('Error detallado:', error);
                const errorElement = form.querySelector('.error') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al procesar la solicitud. Intenta de nuevo.';
                errorElement.style.display = 'block';
                form.appendChild(errorElement);
            }
        });
    }
});