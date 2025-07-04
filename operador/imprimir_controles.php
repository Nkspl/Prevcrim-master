<?php
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['operador','admin','jefe_zona'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/../config.php';

$allColumns = [
    'id' => 'ID',
    'tipo' => 'Tipo',
    'rut' => 'RUT',
    'nombre' => 'Nombre',
    'apellido' => 'Apellido',
    'motivo_desplazamiento' => 'Motivo Desplazamiento',
    'ubicacion' => 'Ubicación',
    'latitud' => 'Latitud',
    'longitud' => 'Longitud',
    'observacion' => 'Observación',
    'licencia_conducir' => 'Licencia Conducir',
    'padron_vehiculo' => 'Padrón Vehículo',
    'revision_seguro' => 'Revisión/Seguro',
    'rut_conductor' => 'RUT Conductor',
    'nombre_conductor' => 'Nombre Conductor',
    'pertenencias' => 'Pertenencias',
    'permisos_arma' => 'Permisos Arma',
    'revision_mochila' => 'Revisión Mochila',
    'test_alcoholemia' => 'Test Alcoholemia',
    'doc_vehicular' => 'Doc. Vehicular',
    'created_at' => 'Fecha'
];

$colsByType = [
    'identidad' => ['id','tipo','rut','nombre','apellido','motivo_desplazamiento','ubicacion','latitud','longitud','observacion','created_at'],
    'vehicular' => ['id','tipo','rut','nombre','apellido','ubicacion','latitud','longitud','observacion','licencia_conducir','padron_vehiculo','revision_seguro','rut_conductor','nombre_conductor','created_at'],
    'armas_drogas' => ['id','tipo','rut','nombre','apellido','ubicacion','latitud','longitud','observacion','pertenencias','permisos_arma','revision_mochila','created_at'],
    'transito' => ['id','tipo','rut','nombre','apellido','ubicacion','latitud','longitud','observacion','test_alcoholemia','doc_vehicular','created_at']
];

$tipo = $_GET['tipo'] ?? '';
$buscar = trim($_GET['buscar'] ?? '');

$params = [];
$sql = "SELECT * FROM control_policial WHERE 1";
if ($tipo !== '') { $sql .= " AND tipo = :tipo"; $params['tipo'] = $tipo; }
if ($buscar !== '') {
    $sql .= " AND (rut LIKE :br OR nombre LIKE :bn OR apellido LIKE :ba)";
    $params['br'] = "%$buscar%";
    $params['bn'] = "%$buscar%";
    $params['ba'] = "%$buscar%";
}
$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$controles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($tipo !== '' && isset($colsByType[$tipo])) {
    $columns = array_intersect_key($allColumns, array_flip($colsByType[$tipo]));
} else {
    $columns = $allColumns;
}

include '../inc/header.php';
?>
<div class="wrapper">
  <div class="content">
    <h2>Reporte de Controles</h2>
    <button onclick="window.print()" class="print-hide">Imprimir</button>
    <table>
      <thead>
        <tr>
          <?php foreach ($columns as $label): ?>
            <th><?= htmlspecialchars($label) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if ($controles): foreach ($controles as $c): ?>
        <tr>
          <?php foreach (array_keys($columns) as $col): ?>
            <td><?= htmlspecialchars($c[$col]) ?></td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="<?= count($columns) ?>">No hay controles registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php include '../inc/footer.php'; ?>
</div>
