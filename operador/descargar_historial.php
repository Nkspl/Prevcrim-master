<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}
require_once '../config.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    exit('ID inválido');
}
$stmt = $pdo->prepare("SELECT d.fecha, td.nombre AS tipo, d.descripcion, d.direccion, d.comuna, d.sector FROM delito d LEFT JOIN tipo_delito td ON d.tipo_id = td.id WHERE d.delincuente_id = ? ORDER BY d.fecha DESC");
$stmt->execute([$id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="historial_'.$id.'.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Fecha','Tipo','Descripcion','Direccion','Comuna','Sector']);
foreach ($rows as $r) {
    fputcsv($out, $r);
}
fclose($out);
exit;
?>
