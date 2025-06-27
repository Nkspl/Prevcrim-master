<?php
// operador/process_registro_control.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'operador') {
  header("Location: ../index.php");
  exit();
}
require_once '../config.php';
require_once '../inc/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: registro_control.php');
  exit();
}

$tipo = $_POST['tipo'] ?? '';
if (!$tipo) {
  header('Location: registro_control.php?msg=Tipo%20requerido');
  exit();
}

$data = [
  'operador_id' => $_SESSION['user_id'],
  'tipo' => $tipo,
  'rut' => trim($_POST['rut'] ?? ''),
  'nombre' => trim($_POST['nombre'] ?? ''),
  'motivo_desplazamiento' => trim($_POST['motivo_desplazamiento'] ?? ''),
  'ubicacion' => trim($_POST['ubicacion'] ?? ''),
  'latitud' => $_POST['latitud'] !== '' ? $_POST['latitud'] : null,
  'longitud' => $_POST['longitud'] !== '' ? $_POST['longitud'] : null,
  'observacion' => trim($_POST['observacion'] ?? ''),
  'licencia_conducir' => trim($_POST['licencia_conducir'] ?? ''),
  'padron_vehiculo' => trim($_POST['padron_vehiculo'] ?? ''),
  'revision_seguro' => trim($_POST['revision_seguro'] ?? ''),
  'rut_conductor' => trim($_POST['rut_conductor'] ?? ''),
  'nombre_conductor' => trim($_POST['nombre_conductor'] ?? ''),
  'pertenencias' => trim($_POST['pertenencias'] ?? ''),
  'permisos_arma' => trim($_POST['permisos_arma'] ?? ''),
  'revision_mochila' => trim($_POST['revision_mochila'] ?? ''),
  'test_alcoholemia' => trim($_POST['test_alcoholemia'] ?? ''),
  'doc_vehicular' => trim($_POST['doc_vehicular'] ?? '')
];

// Validaciones
if ($data['latitud'] !== null && !is_numeric($data['latitud'])) {
  header('Location: registro_control.php?msg=Latitud%20inv\xE1lida');
  exit();
}
if ($data['longitud'] !== null && !is_numeric($data['longitud'])) {
  header('Location: registro_control.php?msg=Longitud%20inv\xE1lida');
  exit();
}

$limites = [
  'rut' => 12,
  'nombre' => 150,
  'ubicacion' => 200,
  'licencia_conducir' => 100,
  'padron_vehiculo' => 100,
  'revision_seguro' => 100,
  'rut_conductor' => 12,
  'nombre_conductor' => 150,
  'test_alcoholemia' => 100
];
foreach ($limites as $campo => $max) {
  if (!empty($data[$campo]) && mb_strlen($data[$campo]) > $max) {
    header('Location: registro_control.php?msg=' . $campo . '%20muy%20largo');
    exit();
  }
}

$sql = "INSERT INTO control_policial
          (operador_id,tipo,rut,nombre,motivo_desplazamiento,ubicacion,latitud,longitud,observacion,
           licencia_conducir,padron_vehiculo,revision_seguro,rut_conductor,nombre_conductor,
           pertenencias,permisos_arma,revision_mochila,test_alcoholemia,doc_vehicular)
        VALUES
          (:operador_id,:tipo,:rut,:nombre,:motivo_desplazamiento,:ubicacion,:latitud,:longitud,:observacion,
           :licencia_conducir,:padron_vehiculo,:revision_seguro,:rut_conductor,:nombre_conductor,
           :pertenencias,:permisos_arma,:revision_mochila,:test_alcoholemia,:doc_vehicular)";
$stmt = $pdo->prepare($sql);
if ($stmt->execute($data)) {
  header('Location: registro_control.php?msg=Control%20registrado');
  exit();
} else {
  header('Location: registro_control.php?msg=Error');
  exit();
}
