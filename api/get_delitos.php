<?php
require_once '../config.php';
header('Content-Type: application/json');
$where = ['latitud IS NOT NULL','longitud IS NOT NULL'];
$params = [];
if (!empty($_GET['inicio'])) { $where[] = 'fecha >= :inicio'; $params['inicio'] = $_GET['inicio']; }
if (!empty($_GET['fin'])) { $where[] = 'fecha <= :fin'; $params['fin'] = $_GET['fin']; }
if (!empty($_GET['comuna'])) { $where[] = 'comuna = :comuna'; $params['comuna'] = $_GET['comuna']; }
if (!empty($_GET['sector'])) { $where[] = 'sector = :sector'; $params['sector'] = $_GET['sector']; }
$sql = 'SELECT descripcion, fecha, comuna, sector, latitud, longitud FROM delito';
if ($where) $sql .= ' WHERE '.implode(' AND ', $where);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
