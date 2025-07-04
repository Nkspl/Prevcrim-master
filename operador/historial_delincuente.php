<?php
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin','jefe_zona','operador'])) {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';

// Obtener listado de delincuentes (agrupados por RUT)
$lista = $pdo->query("SELECT DISTINCT rut, apellidos_nombres FROM delincuente ORDER BY apellidos_nombres")->fetchAll();

$rut = $_GET['rut'] ?? '';
$persona = null;
$delitos = [];
$controles = [];
if ($rut) {
    $stmt = $pdo->prepare("SELECT d.*, (SELECT COUNT(*) FROM delito dl WHERE dl.delincuente_id = d.id) AS delitos_count FROM delincuente d WHERE d.rut = ? LIMIT 1");
    $stmt->execute([$rut]);
    $persona = $stmt->fetch();
    if ($persona) {
        $sql = "SELECT fecha, td.nombre AS tipo, dl.descripcion, comuna, sector, latitud, longitud
                FROM delito dl
                LEFT JOIN tipo_delito td ON dl.tipo_id = td.id
                WHERE dl.delincuente_id = ? ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$persona['id']]);
        $delitos = $stmt->fetchAll();

        $sql = "SELECT created_at, tipo, ubicacion, observacion
                FROM control_policial
                WHERE rut = ?
                ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rut]);
        $controles = $stmt->fetchAll();
    }
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

    <?php if ($rut && $persona): ?>
      <a href="descargar_historial.php?rut=<?= urlencode($rut) ?>&format=html" target="_blank">Imprimir</a>
      <h3>Datos Personales</h3>
      <table>
        <tbody>
          <tr><th>Imagen</th><td><?php if ($persona['imagen']): ?><img src="/<?= htmlspecialchars($persona['imagen']) ?>" style="width:50px;"><?php endif; ?></td></tr>
          <tr><th>RUT</th><td><?= htmlspecialchars($persona['rut']) ?></td></tr>
          <tr><th>Nombre</th><td><?= htmlspecialchars($persona['apellidos_nombres']) ?></td></tr>
          <tr><th>Apodo</th><td><?= htmlspecialchars($persona['apodo']) ?></td></tr>
          <tr><th>Domicilio</th><td><?= htmlspecialchars($persona['domicilio']) ?></td></tr>
          <tr><th>Fono</th><td><?= htmlspecialchars($persona['fono_fijo']) ?></td></tr>
          <tr><th>Celular</th><td><?= htmlspecialchars($persona['celular']) ?></td></tr>
          <tr><th>Email</th><td><?= htmlspecialchars($persona['email']) ?></td></tr>
          <tr><th>Fecha Nac.</th><td><?= htmlspecialchars($persona['fecha_nacimiento']) ?></td></tr>
          <?php $delitosCount = (int)$persona['delitos_count']; ?>
          <tr><th>Delitos</th><td><?= $delitosCount > 0 ? $delitosCount : 'sin registros aun' ?></td></tr>
          <tr><th>Estado</th><td><?= htmlspecialchars($persona['estado']) ?></td></tr>
          <tr><th>Último Lugar Visto</th><td><?= htmlspecialchars($persona['ultimo_lugar_visto']) ?></td></tr>
          <tr><th>Latitud</th><td><?= htmlspecialchars($persona['latitud']) ?></td></tr>
          <tr><th>Longitud</th><td><?= htmlspecialchars($persona['longitud']) ?></td></tr>
        </tbody>
      </table>

      <h3>Historial de Delitos</h3>
      <?php if ($delitos): ?>
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Comuna</th>
            <th>Sector</th>
            <th>Latitud</th>
            <th>Longitud</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($delitos as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['fecha']) ?></td>
              <td><?= htmlspecialchars($d['tipo']) ?></td>
              <td><?= htmlspecialchars($d['descripcion']) ?></td>
              <td><?= htmlspecialchars($d['comuna']) ?></td>
              <td><?= htmlspecialchars($d['sector']) ?></td>
              <td><?= htmlspecialchars($d['latitud']) ?></td>
              <td><?= htmlspecialchars($d['longitud']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div id="map" style="height:400px;margin-top:20px;"></div>
      <h3>Historial de Controles</h3>
      <?php if ($controles): ?>
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Ubicación</th>
            <th>Observación</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($controles as $c): ?>
            <tr>
              <td><?= htmlspecialchars($c['created_at']) ?></td>
              <td><?= htmlspecialchars($c['tipo']) ?></td>
              <td><?= htmlspecialchars($c['ubicacion']) ?></td>
              <td><?= htmlspecialchars($c['observacion']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p>No hay controles registrados.</p>
      <?php endif; ?>
      <?php else: ?>
        <p>No hay delitos registrados.</p>
      <?php endif; ?>
    <?php elseif ($rut): ?>
      <p>No hay registros en el historial para este delincuente.</p>
    <?php endif; ?>
  </div>
  <?php include('../inc/footer.php'); ?>
</div>
<?php if ($rut && $persona && $delitos): ?>
<script>
  function initMap() {
    const map = new google.maps.Map(
      document.getElementById('map'),
      { zoom: 6, center: { lat: -33.45, lng: -70.66 } }
    );
    const bounds = new google.maps.LatLngBounds();
    <?php foreach ($delitos as $d): if ($d['latitud'] !== null && $d['longitud'] !== null): ?>
      const marker = new google.maps.Marker({
        position: {
          lat: parseFloat('<?= $d['latitud'] ?>'),
          lng: parseFloat('<?= $d['longitud'] ?>')
        },
        map,
        title: "Lugar: <?= htmlspecialchars($d['comuna'], ENT_QUOTES) ?>"
      });
      bounds.extend(marker.getPosition());
    <?php endif; endforeach; ?>
    if (!bounds.isEmpty()) {
      map.fitBounds(bounds);
    }
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaYoejG_5UXM_POLcQ47plW0tDytSmHqQ&callback=initMap"></script>
<?php endif; ?>
