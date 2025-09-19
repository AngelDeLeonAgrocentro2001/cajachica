<?php
$hash = '$2y$10$rC5Rti3ZXtL7b/Ksa7e4wuH8XEwmcNce90ktaY02n64zs6lUudp96'; // Reemplaza con el hash de la base de datos
$password = 'Elian20'; // La contraseña que ingresaste
 var_dump(password_verify($password, $hash));
 