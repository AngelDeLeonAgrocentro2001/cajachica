<?php
// config/env.php - Cargador SIMPLIFICADO y ROBUSTO

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        error_log("⚠️ Archivo .env no encontrado: " . $filePath);
        return;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Saltar comentarios y líneas vacías
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Validar que tenga formato clave=valor
        if (strpos($line, '=') === false) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Validar que no estén vacíos
        if (!empty($name) && !empty($value)) {
            // Poner en entorno (más confiable que $_ENV)
            putenv("$name=$value");
            $_SERVER[$name] = $value;
        }
    }
}

// Cargar variables de entorno
loadEnv(__DIR__ . '/../.env');