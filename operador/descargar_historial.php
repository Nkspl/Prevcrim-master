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
$sql = "SELECT fecha, td.nombre AS tipo, descripcion, comuna, sector, latitud, longitud
        FROM delito dl
        LEFT JOIN tipo_delito td ON dl.tipo_id = td.id
        WHERE dl.delincuente_id = ? ORDER BY fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$persona['id']]);
$delitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'html') {
    include '../inc/header.php';
    echo '<div class="wrapper"><div class="content">';
    echo '<h2>Historial de Delincuente</h2>';
    echo '<button onclick="window.print()" class="print-hide">Imprimir</button>';
    echo '<h3>Datos Personales</h3>';
    echo '<table><tbody>';
    foreach ([
        'RUT' => $persona['rut'],
        'Nombre' => $persona['apellidos_nombres'],
        'Apodo' => $persona['apodo'],
        'Domicilio' => $persona['domicilio'],
        'Fono' => $persona['fono_fijo'],
        'Celular' => $persona['celular'],
        'Email' => $persona['email'],
        'Fecha Nac.' => $persona['fecha_nacimiento'],
        'Delitos' => $persona['delitos'],
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
    } else {
        echo '<p>No hay delitos registrados.</p>';
    }
    echo '</div>';
    include '../inc/footer.php';
    echo '</div>';
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
    $persona['delitos'],
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
