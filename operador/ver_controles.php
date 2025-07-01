<?php
// operador/ver_controles.php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

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
    $sql .= " AND (rut LIKE :buscar_rut OR nombre LIKE :buscar_nom)";
    $params['buscar_rut'] = '%' . $buscar . '%';
    $params['buscar_nom'] = '%' . $buscar . '%';
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$controles = $stmt->fetchAll();
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
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>RUT</th>
        <th>Nombre</th>
        <th>Motivo Desplazamiento</th>
        <th>Ubicación</th>
        <th>Latitud</th>
        <th>Longitud</th>
        <th>Observación</th>
        <th>Licencia Conducir</th>
        <th>Padrón Vehículo</th>
        <th>Revisión/Seguro</th>
        <th>RUT Conductor</th>
        <th>Nombre Conductor</th>
        <th>Pertenencias</th>
        <th>Permisos Arma</th>
        <th>Revisión Mochila</th>
        <th>Test Alcoholemia</th>
        <th>Doc. Vehicular</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($controles): ?>
        <?php foreach ($controles as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['id']) ?></td>
            <td><?= htmlspecialchars($c['tipo']) ?></td>
            <td><?= htmlspecialchars($c['rut']) ?></td>
            <td><?= htmlspecialchars($c['nombre']) ?></td>
            <td><?= htmlspecialchars($c['motivo_desplazamiento']) ?></td>
            <td><?= htmlspecialchars($c['ubicacion']) ?></td>
            <td><?= htmlspecialchars($c['latitud']) ?></td>
            <td><?= htmlspecialchars($c['longitud']) ?></td>
            <td><?= htmlspecialchars($c['observacion']) ?></td>
            <td><?= htmlspecialchars($c['licencia_conducir']) ?></td>
            <td><?= htmlspecialchars($c['padron_vehiculo']) ?></td>
            <td><?= htmlspecialchars($c['revision_seguro']) ?></td>
            <td><?= htmlspecialchars($c['rut_conductor']) ?></td>
            <td><?= htmlspecialchars($c['nombre_conductor']) ?></td>
            <td><?= htmlspecialchars($c['pertenencias']) ?></td>
            <td><?= htmlspecialchars($c['permisos_arma']) ?></td>
            <td><?= htmlspecialchars($c['revision_mochila']) ?></td>
            <td><?= htmlspecialchars($c['test_alcoholemia']) ?></td>
            <td><?= htmlspecialchars($c['doc_vehicular']) ?></td>
            <td><?= htmlspecialchars($c['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="20">No hay controles registrados.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>
