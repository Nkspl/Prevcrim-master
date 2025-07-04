<?php
// reportes.php - Generación de múltiples reportes
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once 'config.php';

$reporte = $_GET['reporte'] ?? 'alfabetico';
$reporte_nombres = [
    'alfabetico'              => 'Delincuentes por orden alfabético',
    'por_delito'              => 'Delincuentes por Delito',
    'por_comuna_domicilio'    => 'Delincuentes por Comuna de Residencia',
    'por_comuna_visto'        => 'Delincuentes por Última Comuna Vista',
    'parentesco'              => 'Delincuentes con Parentesco',
    'delitos_por_sector_fecha'=> 'Delitos por Comuna/Sector y Fecha',
    'historico_delitos_sector'=> 'Histórico de Delitos por Sector',
    'busqueda_global'         => 'Búsqueda Global',
    'ranking_comunas'         => 'Ranking de Comunas',
    'mapa_delitos'            => 'Mapa de Delitos',
];
$inicio  = $_GET['inicio']  ?? '';
$fin     = $_GET['fin']     ?? '';
$comuna  = trim($_GET['comuna']  ?? '');
$sector  = trim($_GET['sector']  ?? '');
$busqueda = trim($_GET['q'] ?? '');

function fetchAll($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

$datos = [];
switch ($reporte) {
    case 'alfabetico':
        $sql = "SELECT d.rut, d.apellidos_nombres, d.estado, td.nombre AS tipo_delito, dl.descripcion, dl.fecha
                FROM delincuente d
                LEFT JOIN delito dl ON dl.delincuente_id = d.id
                LEFT JOIN tipo_delito td ON dl.tipo_id = td.id
                ORDER BY d.apellidos_nombres";
        $datos = fetchAll($pdo, $sql);
        break;
    case 'por_delito':
        $sql = "SELECT td.nombre AS delito, dl.descripcion, d.rut, d.apellidos_nombres, dl.fecha
                FROM delincuente d
                JOIN delito dl ON dl.delincuente_id = d.id
                JOIN tipo_delito td ON dl.tipo_id = td.id
                ORDER BY td.nombre, d.apellidos_nombres";
        $datos = fetchAll($pdo, $sql);
        break;
    case 'por_comuna_domicilio':
        $sql = "SELECT TRIM(SUBSTRING_INDEX(d.domicilio, ',', -1)) AS comuna, d.rut, d.apellidos_nombres,
                       td.nombre AS delito, dl.descripcion, dl.fecha
                FROM delincuente d
                LEFT JOIN delito dl ON dl.delincuente_id = d.id
                LEFT JOIN tipo_delito td ON dl.tipo_id = td.id
                ORDER BY comuna, d.apellidos_nombres";
        $datos = fetchAll($pdo, $sql);
        break;
    case 'por_comuna_visto':
        $sql = "SELECT TRIM(SUBSTRING_INDEX(d.ultimo_lugar_visto, ',', -1)) AS comuna_visto,
                       d.rut, d.apellidos_nombres, dl.fecha
                FROM delincuente d
                LEFT JOIN delito dl ON dl.delincuente_id = d.id
                ORDER BY comuna_visto, d.apellidos_nombres";
        $datos = fetchAll($pdo, $sql);
        break;
    case 'parentesco':
        $sql = "SELECT SUBSTRING_INDEX(apellidos_nombres, ' ', 1) AS apellido, GROUP_CONCAT(apellidos_nombres SEPARATOR ', ') AS miembros
                FROM delincuente
                GROUP BY apellido HAVING COUNT(*) > 1";
        $datos = fetchAll($pdo, $sql);
        break;
    case 'delitos_por_sector_fecha':
        $where = [];
        $params = [];
        if ($comuna !== '') { $where[] = 'comuna LIKE :comuna'; $params['comuna'] = "%$comuna%"; }
        if ($sector !== '') { $where[] = 'sector LIKE :sector'; $params['sector'] = "%$sector%"; }
        if ($inicio !== '') { $where[] = 'fecha >= :inicio'; $params['inicio'] = $inicio; }
        if ($fin !== '') { $where[] = 'fecha <= :fin'; $params['fin'] = $fin; }
        $sql = 'SELECT codigo, descripcion, comuna, sector, fecha FROM delito';
        if ($where) $sql .= ' WHERE '.implode(' AND ', $where);
        $sql .= ' ORDER BY fecha DESC';
        $datos = fetchAll($pdo, $sql, $params);
        break;
    case 'historico_delitos_sector':
        $sql = 'SELECT sector, COUNT(*) AS total FROM delito GROUP BY sector ORDER BY sector';
        $datos = fetchAll($pdo, $sql);
        break;
    case 'busqueda_global':
        $where = [];
        $params = [];
        if ($busqueda !== '') {
            $search = "%$busqueda%";
            $where[] = '(d.rut LIKE :search OR d.apellidos_nombres LIKE :search OR dl.descripcion LIKE :search OR dl.comuna LIKE :search OR dl.sector LIKE :search)';
            $params['search'] = $search;
        }
        $sql = "SELECT d.imagen, d.rut, d.apellidos_nombres, d.estado,
                       td.nombre AS tipo_delito, dl.descripcion,
                       CONCAT(dl.comuna, ' / ', dl.sector) AS Comuna_Sector, dl.fecha
                FROM delito dl
                LEFT JOIN delincuente d ON dl.delincuente_id = d.id
                LEFT JOIN tipo_delito td ON dl.tipo_id = td.id";
        if ($where) $sql .= ' WHERE '.implode(' AND ', $where);
        $sql .= ' ORDER BY dl.fecha DESC';
        $datos = fetchAll($pdo, $sql, $params);
        break;
    case 'ranking_comunas':
        $where = [];
        $params = [];
        if ($inicio !== '') { $where[] = 'fecha >= :inicio'; $params['inicio'] = $inicio; }
        if ($fin !== '') { $where[] = 'fecha <= :fin'; $params['fin'] = $fin; }
        $sql = 'SELECT comuna, COUNT(*) AS total FROM delito';
        if ($where) $sql .= ' WHERE '.implode(' AND ', $where);
        $sql .= ' GROUP BY comuna ORDER BY total DESC';
        $datos = fetchAll($pdo, $sql, $params);
        break;
    case 'mapa_delitos':
        // la data se cargará vía API
        $datos = [];
        break;
}
?>
<?php include 'inc/header.php'; ?>
<div class="wrapper">
  <div class="content">
    <h2>Reportes</h2>
    <h3><?= htmlspecialchars($reporte_nombres[$reporte] ?? '') ?></h3>
    <form method="get" action="" class="print-hide">
      <label for="reporte">Tipo de Reporte:</label>
      <select id="reporte" name="reporte" onchange="this.form.submit()">
        <option value="alfabetico"<?= $reporte==='alfabetico'?' selected':'' ?>>Delincuentes Alfabético</option>
        <option value="por_delito"<?= $reporte==='por_delito'?' selected':'' ?>>Delincuentes por Delito</option>
        <option value="por_comuna_domicilio"<?= $reporte==='por_comuna_domicilio'?' selected':'' ?>>Delincuentes por Comuna de Residencia</option>
        <option value="por_comuna_visto"<?= $reporte==='por_comuna_visto'?' selected':'' ?>>Delincuentes por Última Comuna Vista</option>
        <option value="parentesco"<?= $reporte==='parentesco'?' selected':'' ?>>Delincuentes con Parentesco</option>
        <option value="delitos_por_sector_fecha"<?= $reporte==='delitos_por_sector_fecha'?' selected':'' ?>>Delitos por Comuna/Sector y Fecha</option>
        <option value="historico_delitos_sector"<?= $reporte==='historico_delitos_sector'?' selected':'' ?>>Histórico de Delitos por Sector</option>
        <option value="busqueda_global"<?= $reporte==='busqueda_global'?' selected':'' ?>>Búsqueda Global</option>
        <option value="ranking_comunas"<?= $reporte==='ranking_comunas'?' selected':'' ?>>Ranking de Comunas</option>
        <option value="mapa_delitos"<?= $reporte==='mapa_delitos'?' selected':'' ?>>Mapa de Delitos</option>
      </select>
      <?php if (in_array($reporte, ['delitos_por_sector_fecha','ranking_comunas','mapa_delitos'])): ?>
        <label>Desde: <input type="date" name="inicio" value="<?= htmlspecialchars($inicio) ?>"></label>
        <label>Hasta: <input type="date" name="fin" value="<?= htmlspecialchars($fin) ?>"></label>
      <?php endif; ?>
      <?php if ($reporte==='delitos_por_sector_fecha' || $reporte==='mapa_delitos'): ?>
        <label>Comuna: <input type="text" name="comuna" value="<?= htmlspecialchars($comuna) ?>"></label>
        <label>Sector: <input type="text" name="sector" value="<?= htmlspecialchars($sector) ?>"></label>
      <?php endif; ?>
      <?php if ($reporte==='busqueda_global'): ?>
        <input type="text" name="q" placeholder="Buscar" value="<?= htmlspecialchars($busqueda) ?>">
      <?php endif; ?>
      <noscript><button type="submit">Ver</button></noscript>
    </form>
    <button onclick="window.print()" class="print-hide">Imprimir</button>
    <div class="reporte">
<?php if ($reporte==='mapa_delitos'): ?>
      <div id="map" style="height:500px;"></div>
<?php elseif ($datos): ?>
      <table>
        <thead>
<?php
  $first = $datos[0];
  echo '<tr>'; foreach(array_keys($first) as $h) echo '<th>'.htmlspecialchars($h).'</th>'; echo '</tr>';
?>
        </thead>
        <tbody>
<?php foreach ($datos as $row): ?>
          <tr>
<?php foreach ($row as $k => $val): ?>
            <?php if ($k === 'imagen'): ?>
            <td><img src="<?= htmlspecialchars($val) ?>" style="max-width:50px;"></td>
            <?php else: ?>
            <td><?= htmlspecialchars($val) ?></td>
            <?php endif; ?>
<?php endforeach; ?>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
<?php else: ?>
      <p>No hay datos.</p>
<?php endif; ?>
    </div>
  </div>
  <?php include 'inc/footer.php'; ?>
</div>
<?php if ($reporte==='mapa_delitos'): ?>
<script>
let map;
function initMap(){
  map=new google.maps.Map(document.getElementById('map'),{zoom:6,center:{lat:-33.4,lng:-70.6}});
  loadDelitos();
}
function loadDelitos(){
  const params=new URLSearchParams({inicio:'<?= $inicio ?>',fin:'<?= $fin ?>',comuna:'<?= $comuna ?>',sector:'<?= $sector ?>'});
  fetch('api/get_delitos.php?'+params.toString())
    .then(r=>r.json())
    .then(data=>{
      data.forEach(d=>{
        const pos={lat:parseFloat(d.latitud),lng:parseFloat(d.longitud)};
        new google.maps.Marker({position:pos,map,title:`${d.descripcion} - ${d.fecha}`});
      });
    });
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaYoejG_5UXM_POLcQ47plW0tDytSmHqQ&callback=initMap"></script>
<?php endif; ?>
