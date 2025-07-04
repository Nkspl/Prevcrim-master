<?php
// operador/ver_controles.php
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['operador', 'admin', 'jefe_zona'])) {
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

$tipo   = $_GET['tipo']   ?? '';
$buscar = trim($_GET['buscar'] ?? '');

$params = [];
$sql = "SELECT * FROM control_policial WHERE 1";

if ($tipo !== '') {
    $sql .= " AND tipo = :tipo";
    $params['tipo'] = $tipo;
}

if ($buscar !== '') {
    // MySQL PDO no permite reutilizar el mismo nombre de placeholder,
    // por lo que usamos dos placeholders distintos
    $sql .= " AND (rut LIKE :buscar_rut OR nombre LIKE :buscar_nom OR apellido LIKE :buscar_ape)";
    $params['buscar_rut'] = '%' . $buscar . '%';
    $params['buscar_nom'] = '%' . $buscar . '%';
    $params['buscar_ape'] = '%' . $buscar . '%';
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$controles = $stmt->fetchAll();

if ($tipo !== '' && isset($colsByType[$tipo])) {
    $columns = array_intersect_key($allColumns, array_flip($colsByType[$tipo]));
} else {
    $columns = $allColumns;
}
?>
<?php include __DIR__ . '/../inc/header.php'; ?>

<main style="padding-left:16%; padding-right:5%;">
  <h1>Controles Policiales</h1>
  <form method="get" action="" style="margin-top:20px;">
    <label for="tipo">Tipo:</label>
    <select name="tipo" id="tipo">
      <option value="">Todos</option>
      <option value="identidad"  <?= $tipo==='identidad'?'selected':'' ?>>Control de Identidad</option>
      <option value="vehicular"  <?= $tipo==='vehicular'?'selected':'' ?>>Control Vehicular</option>
      <option value="armas_drogas" <?= $tipo==='armas_drogas'?'selected':'' ?>>Control de Armas o Drogas</option>
      <option value="transito"   <?= $tipo==='transito'?'selected':'' ?>>Control de Tránsito</option>
    </select>
    <input type="text" name="buscar" placeholder="Buscar por RUT o nombre" value="<?= htmlspecialchars($buscar) ?>">
    <button type="submit">Filtrar</button>
    <button type="button" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>'">Mostrar todos</button>
    <?php $printUrl = '/operador/imprimir_controles.php?'.http_build_query(['tipo'=>$tipo,'buscar'=>$buscar]); ?>
    <a href="<?= $printUrl ?>" target="_blank">Imprimir</a>
  </form>

  <table id="controlesTable">
    <thead>
      <tr>
        <?php foreach ($columns as $label): ?>
          <th><?= htmlspecialchars($label) ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php if ($controles): ?>
        <?php foreach ($controles as $c): ?>
          <tr>
            <?php foreach (array_keys($columns) as $col): ?>
              <td><?= htmlspecialchars($c[$col]) ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="<?= count($columns) ?>">No hay controles registrados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>
