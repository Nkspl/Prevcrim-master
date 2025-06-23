<?php
// api/get_delincuentes.php
require_once '../config.php';
header('Content-Type: application/json');

$where = [];
$params = [];

// Filtros opcionales
if (!empty($_GET['rut'])) {
    $where[]        = 'rut = :rut';
    $params['rut']  = trim($_GET['rut']);
}
if (!empty($_GET['nombre'])) {
    $where[]          = 'apellidos_nombres LIKE :nombre';
    $params['nombre'] = '%'.trim($_GET['nombre']).'%';
}
if (!empty($_GET['apodo'])) {
    $where[]         = 'apodo LIKE :apodo';
    $params['apodo'] = '%'.trim($_GET['apodo']).'%';
}

// Siempre incluir sólo con coordenadas
$where[] = 'latitud IS NOT NULL AND longitud IS NOT NULL';

$sql = 'SELECT rut, apellidos_nombres, apodo, latitud, longitud, ultimo_lugar_visto
        FROM delincuente';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
