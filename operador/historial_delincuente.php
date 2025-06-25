<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';

// Obtener listado de delincuentes
$lista = $pdo->query("SELECT id, apellidos_nombres FROM delincuente ORDER BY apellidos_nombres")->fetchAll();

$id = $_GET['id'] ?? '';
$historial = [];
if ($id) {
    $stmt = $pdo->prepare("SELECT d.fecha, td.nombre AS tipo, d.descripcion, d.direccion, d.comuna, d.sector, d.latitud, d.longitud FROM delito d LEFT JOIN tipo_delito td ON d.tipo_id = td.id WHERE d.delincuente_id = ? ORDER BY d.fecha DESC");
    $stmt->execute([$id]);
    $historial = $stmt->fetchAll();
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Historial de Delincuente</h2>
    <form method="get" action="">
      <label for="id">Delincuente:</label>
      <select name="id" id="id" onchange="this.form.submit()">
        <option value="">-- Selecciona --</option>
        <?php foreach ($lista as $d): ?>
          <option value="<?= $d['id'] ?>" <?= $id == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['apellidos_nombres']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php if ($id && $historial): ?>
      <a href="descargar_historial.php?id=<?= $id ?>">Descargar Reporte</a>
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Dirección</th>
            <th>Comuna</th>
            <th>Sector</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($historial as $h): ?>
            <tr>
              <td><?= htmlspecialchars($h['fecha']) ?></td>
              <td><?= htmlspecialchars($h['tipo']) ?></td>
              <td><?= htmlspecialchars($h['descripcion']) ?></td>
              <td><?= htmlspecialchars($h['direccion']) ?></td>
              <td><?= htmlspecialchars($h['comuna']) ?></td>
              <td><?= htmlspecialchars($h['sector']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div id="map" style="height:400px;margin-top:20px;"></div>
    <?php elseif ($id): ?>
      <p>No hay delitos registrados para este delincuente.</p>
    <?php endif; ?>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
<?php if ($id && $historial): ?>
<script>
  function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), { zoom: 6, center: {lat: -33.45, lng: -70.66}});
    const bounds = new google.maps.LatLngBounds();
    <?php foreach ($historial as $h): ?>
      const marker = new google.maps.Marker({
        position: {lat: parseFloat('<?= $h['latitud'] ?>'), lng: parseFloat('<?= $h['longitud'] ?>')},
        map,
        title: "<?= htmlspecialchars($h['descripcion'], ENT_QUOTES) ?>"
      });
      bounds.extend(marker.getPosition());
    <?php endforeach; ?>
    map.fitBounds(bounds);
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBS9ZZ_-SfCP-HnAOJcRRUVDJO5TqBs2gg&callback=initMap"></script>
<?php endif; ?>
