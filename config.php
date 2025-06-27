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

$host = getEnvOrMessage('DB_HOST', 'Environment variable DB_HOST is missing.');
$db   = getEnvOrMessage('DB_NAME', 'Environment variable DB_NAME is missing.');
$user = getEnvOrMessage('DB_USER', 'Environment variable DB_USER is missing.');
$pass = getEnvOrMessage('DB_PASS', 'Environment variable DB_PASS is missing.');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
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
