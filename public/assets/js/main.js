document.querySelector("#loginForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    let email = document.querySelector("#email").value;
    let password = document.querySelector("#password").value;

    let response = await fetch("http://localhost:8080/agrocaja-chica/public/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
    });

    try {
        let data = await response.json();
        if (data.success) {
            localStorage.setItem("token", data.token);
            alert(`Bienvenido ${data.nombre}`);
            window.location.href = "dashboard.php";
        } else {
            alert(data.error);
        }
    } catch (error) {
        console.error("Error en fetch:", error);
        alert("Error al procesar la respuesta");
    }
});
