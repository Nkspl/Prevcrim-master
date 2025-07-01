<?php
require_once '../config.php';
header('Content-Type: application/json');
$rut = trim($_GET['rut'] ?? '');
if ($rut === '') { echo json_encode([]); exit; }
$sql = 'SELECT d.nombres, d.apellidos, d.apellidos_nombres, d.apodo, d.fecha_nacimiento,
               EXISTS(SELECT 1 FROM delito di WHERE di.delincuente_id = d.id LIMIT 1) AS tiene_delitos
        FROM delincuente d WHERE d.rut = ? LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->execute([$rut]);
$data = $stmt->fetch();
if (!$data) $data = [];
echo json_encode($data);
