<?php
// api/getRutDelincuentes.php
require_once '../config.php';

header('Content-Type: application/json');

$sql = "SELECT rut, apellidos_nombres, apodo FROM delincuente ORDER BY apellidos_nombres";
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll();

echo json_encode($result);