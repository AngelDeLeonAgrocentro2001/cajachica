<!-- <?php
require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

define('JWT_SECRET', 'tu_secreto_super_seguro'); 
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRY', 3600);

function generateJWT($payload) {
    $payload['exp'] = time() + JWT_EXPIRY;
    return JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);
}

function validateJWT($token) {
    try {
        return JWT::decode($token, new Key(JWT_SECRET, JWT_ALGORITHM));
    } catch (Exception $e) {
        return false;
    }
}
?> -->