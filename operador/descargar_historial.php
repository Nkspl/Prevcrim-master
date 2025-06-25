<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}
require_once '../config.php';

$rut = $_GET['rut'] ?? '';
if (!$rut) {
    exit('RUT inválido');
}
$stmt = $pdo->prepare("SELECT created_at AS fecha, ultimo_lugar_visto, latitud, longitud FROM delincuente WHERE rut = ? ORDER BY created_at DESC");
$stmt->execute([$rut]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="historial_'.$rut.'.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Fecha','UltimoLugar','Latitud','Longitud']);
foreach ($rows as $r) {
    fputcsv($out, $r);
}
fclose($out);
exit;
?>
