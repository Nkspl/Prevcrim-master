<?php
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin','jefe_zona','operador'])) {
    header('Location: /login.php');
    exit;
}
require_once '../config.php';

$rut = $_GET['rut'] ?? '';
$format = $_GET['format'] ?? 'csv';
if (!$rut) {
    exit('RUT inválido');
}
$stmt = $pdo->prepare("SELECT * FROM delincuente WHERE rut = ? LIMIT 1");
$stmt->execute([$rut]);
$persona = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$persona) {
    exit('RUT inválido');
}
$sql = "SELECT fecha, td.nombre AS tipo, dl.descripcion, comuna, sector, latitud, longitud
        FROM delito dl
        LEFT JOIN tipo_delito td ON dl.tipo_id = td.id
        WHERE dl.delincuente_id = ? ORDER BY fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$persona['id']]);
$delitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT created_at, tipo, ubicacion, observacion
        FROM control_policial
        WHERE rut = ?
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$rut]);
$controles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'html') {
    include '../inc/header.php';
    echo '<div class="wrapper"><div class="content">';
    echo '<h2>Historial de Delincuente</h2>';
    echo '<button onclick="window.print()" class="print-hide">Imprimir</button>';
    echo '<h3>Datos Personales</h3>';
    echo '<table><tbody>';
    if (!empty($persona['imagen'])) {
        echo '<tr><th>Imagen</th><td><img src="/'.htmlspecialchars($persona['imagen']).'" style="max-width:150px;"></td></tr>';
    }
    foreach ([
        'RUT' => $persona['rut'],
        'Nombre' => $persona['apellidos_nombres'],
        'Apodo' => $persona['apodo'],
        'Domicilio' => $persona['domicilio'],
        'Fono' => $persona['fono_fijo'],
        'Celular' => $persona['celular'],
        'Email' => $persona['email'],
        'Fecha Nac.' => $persona['fecha_nacimiento'],
        'Delitos' => (function($c){
            return $c > 0 ? $c : 'sin registros aun';
        })(count($delitos)),
        'Estado' => $persona['estado'],
        'Último Lugar Visto' => $persona['ultimo_lugar_visto'],
        'Latitud' => $persona['latitud'],
        'Longitud' => $persona['longitud']
    ] as $label => $value) {
        echo '<tr><th>'.htmlspecialchars($label).'</th><td>'.htmlspecialchars($value).'</td></tr>';
    }
    echo '</tbody></table>';
    echo '<h3>Historial de Delitos</h3>';
    if ($delitos) {
        echo '<table><thead><tr><th>Fecha</th><th>Tipo</th><th>Descripción</th><th>Comuna</th><th>Sector</th><th>Latitud</th><th>Longitud</th></tr></thead><tbody>';
        foreach ($delitos as $d) {
            echo '<tr>';
            foreach ([$d['fecha'],$d['tipo'],$d['descripcion'],$d['comuna'],$d['sector'],$d['latitud'],$d['longitud']] as $val) {
                echo '<td>'.htmlspecialchars($val).'</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<div id="map" style="height:400px;margin-top:20px;"></div>';
    } else {
        echo '<p>No hay delitos registrados.</p>';
    }

    echo '<h3>Historial de Controles</h3>';
    if ($controles) {
        echo '<table><thead><tr><th>Fecha</th><th>Tipo</th><th>Ubicación</th><th>Observación</th></tr></thead><tbody>';
        foreach ($controles as $c) {
            echo '<tr>';
            foreach ([$c['created_at'],$c['tipo'],$c['ubicacion'],$c['observacion']] as $val) {
                echo '<td>'.htmlspecialchars($val).'</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No hay controles registrados.</p>';
    }

    echo '</div>';
    include '../inc/footer.php';
    echo '</div>';
?>
<script>
function initMap(){
  const map = new google.maps.Map(
    document.getElementById('map'),
    { zoom: 6, center: { lat: -33.45, lng: -70.66 } }
  );
  const bounds = new google.maps.LatLngBounds();
  <?php foreach ($delitos as $d): if ($d['latitud'] !== null && $d['longitud'] !== null): ?>
    const m = new google.maps.Marker({
      position: {
        lat: parseFloat('<?= $d['latitud'] ?>'),
        lng: parseFloat('<?= $d['longitud'] ?>')
      },
      map,
      title: "Lugar: <?= htmlspecialchars($d['comuna'], ENT_QUOTES) ?>"
    });
    bounds.extend(m.getPosition());
  <?php endif; endforeach; ?>
  if(!bounds.isEmpty()) map.fitBounds(bounds);
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaYoejG_5UXM_POLcQ47plW0tDytSmHqQ&callback=initMap"></script>
<?php
    exit;
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="historial_'.$rut.'.csv"');

$out = fopen('php://output', 'w');
$personalHeaders = ['RUT','Nombre','Apodo','Domicilio','Fono','Celular','Email','FechaNacimiento','Delitos','Estado','UltimoLugar','Latitud','Longitud'];
fputcsv($out, $personalHeaders);
fputcsv($out, [
    $persona['rut'],
    $persona['apellidos_nombres'],
    $persona['apodo'],
    $persona['domicilio'],
    $persona['fono_fijo'],
    $persona['celular'],
    $persona['email'],
    $persona['fecha_nacimiento'],
    count($delitos),
    $persona['estado'],
    $persona['ultimo_lugar_visto'],
    $persona['latitud'],
    $persona['longitud']
]);
fputcsv($out, []);
fputcsv($out, ['Fecha','Tipo','Descripción','Comuna','Sector','Latitud','Longitud']);
foreach ($delitos as $d) {
    fputcsv($out, [$d['fecha'],$d['tipo'],$d['descripcion'],$d['comuna'],$d['sector'],$d['latitud'],$d['longitud']]);
}
fclose($out);
exit;
?>
