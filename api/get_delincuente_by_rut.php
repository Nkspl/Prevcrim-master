<?php
require_once '../config.php';
header('Content-Type: application/json');
$rut = trim($_GET['rut'] ?? '');
if ($rut === '') { echo json_encode([]); exit; }
$stmt = $pdo->prepare('SELECT nombres, apellidos, apellidos_nombres, apodo, fecha_nacimiento FROM delincuente WHERE rut = ? LIMIT 1');
$stmt->execute([$rut]);
$data = $stmt->fetch();
if (!$data) $data = [];
echo json_encode($data);
