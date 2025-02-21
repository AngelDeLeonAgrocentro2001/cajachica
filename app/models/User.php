
<?php
require_once __DIR__ . '/../../config/database.php';

class User {
    public static function authenticate($email, $password) {
        $db = conectar();
        $stmt = $db->prepare("SELECT id_usuario, nombre, email FROM adm_usuario WHERE email = :email AND password = :password LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}