<?php
require_once '../config.php';
header('Content-Type: application/json');
$rut = trim($_GET['rut'] ?? '');
if ($rut === '') { echo json_encode(['count' => 0]); exit; }
$stmt = $pdo->prepare('SELECT delitos FROM delincuente WHERE rut = ? LIMIT 1');
$stmt->execute([$rut]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$count = 0;
if ($row && !empty($row['delitos'])) {
    $items = array_filter(array_map('trim', explode(',', $row['delitos'])));
    $count = count($items);
}
echo json_encode(['count' => $count]);
