<?php
require_once '../config.php';
header('Content-Type: application/json');

$rut = trim($_GET['rut'] ?? '');
if ($rut === '') {
    echo json_encode(['count' => 0]);
    exit;
}

// Obtener el id del delincuente para contar los delitos asociados
$stmt = $pdo->prepare('SELECT id FROM delincuente WHERE rut = ? LIMIT 1');
$stmt->execute([$rut]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo json_encode(['count' => 0]);
    exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM delito WHERE delincuente_id = ?');
$stmt->execute([$row['id']]);
$count = (int)$stmt->fetchColumn();

echo json_encode(['count' => $count]);
