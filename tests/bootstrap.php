<?php
require __DIR__ . '/../vendor/autoload.php';

// Use a temporary SQLite database for tests
putenv('DB_DSN=sqlite:' . __DIR__ . '/test.db');
putenv('DB_USER=');
putenv('DB_PASS=');

require __DIR__ . '/../config.php';
$GLOBALS['pdo'] = $pdo;
?>
