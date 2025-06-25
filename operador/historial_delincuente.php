<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';

// Obtener listado de delincuentes (agrupados por RUT)
$lista = $pdo->query("SELECT DISTINCT rut, apellidos_nombres FROM delincuente ORDER BY apellidos_nombres")->fetchAll();

$rut = $_GET['rut'] ?? '';
$historial = [];
if ($rut) {
    $stmt = $pdo->prepare("SELECT ultimo_lugar_visto, latitud, longitud, created_at AS fecha FROM delincuente WHERE rut = ? ORDER BY created_at DESC");
    $stmt->execute([$rut]);
    $historial = $stmt->fetchAll();
}
?>
<?php include('../inc/header.php'); ?>
<div class="wrapper">
  <div class="content">
    <h2>Historial de Delincuente</h2>
    <form method="get" action="">
      <label for="rut">Delincuente:</label>
      <select name="rut" id="rut" onchange="this.form.submit()">
        <option value="">-- Selecciona --</option>
        <?php foreach ($lista as $d): ?>
          <option value="<?= $d['rut'] ?>" <?= $rut == $d['rut'] ? 'selected' : '' ?>><?= htmlspecialchars($d['apellidos_nombres']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php if ($rut && $historial): ?>
      <a href="descargar_historial.php?rut=<?= urlencode($rut) ?>">Descargar Reporte</a>
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Último Lugar Visto</th>
            <th>Latitud</th>
            <th>Longitud</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($historial as $h): ?>
            <tr>
              <td><?= htmlspecialchars($h['fecha']) ?></td>
              <td><?= htmlspecialchars($h['ultimo_lugar_visto']) ?></td>
              <td><?= htmlspecialchars($h['latitud']) ?></td>
              <td><?= htmlspecialchars($h['longitud']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div id="map" style="height:400px;margin-top:20px;"></div>
    <?php elseif ($rut): ?>
      <p>No hay registros en el historial para este delincuente.</p>
    <?php endif; ?>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
<?php if ($rut && $historial): ?>
<script>
  function initMap() {
    const map = new google.maps.Map(document.getElementById('map'), { zoom: 6, center: {lat: -33.45, lng: -70.66}});
    const bounds = new google.maps.LatLngBounds();
    <?php foreach ($historial as $h): ?>
      const marker = new google.maps.Marker({
        position: {lat: parseFloat('<?= $h['latitud'] ?>'), lng: parseFloat('<?= $h['longitud'] ?>')},
        map,
        title: "Lugar: <?= htmlspecialchars($h['ultimo_lugar_visto'], ENT_QUOTES) ?>"
      });
      bounds.extend(marker.getPosition());
    <?php endforeach; ?>
    map.fitBounds(bounds);
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBS9ZZ_-SfCP-HnAOJcRRUVDJO5TqBs2gg&callback=initMap"></script>
<?php endif; ?>
