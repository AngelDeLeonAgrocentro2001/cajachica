
<?php
function conectar(){
    try {
        $link = new PDO('mysql:host=192.168.1.12;dbname=agrosistemas', 'admin');
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link->exec("set names utf8");
        return $link;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}