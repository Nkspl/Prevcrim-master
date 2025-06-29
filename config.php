<?php
// config.php
$charset = 'utf8mb4';

function getEnvOrMessage($var, $message) {
    $val = getenv($var);
    if ($val === false || $val === '') {
        trigger_error($message, E_USER_WARNING);
        return '';
    }
    return $val;
}

// --- Sección de valores por defecto ---
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'sipc2';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'Hola.,123';
// ---------------------------------------

// Si tienes una variable DSN completa, la usas; si no, construyes:
$dsnEnv = getenv('DB_DSN');
if ($dsnEnv) {
    $dsn  = $dsnEnv;
    // Si DSN incluye usuario/clave, déjalos nulos para que PDO los ignore
    $user = getenv('DB_USER') ?: null;
    $pass = getenv('DB_PASS') ?: null;
} else {
    // Ya tienes $host, $db, $user, $pass definidos arriba
    $dsn  = "mysql:host=$host;dbname=$db;charset=$charset";
}

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}