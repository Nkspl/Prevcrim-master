<?php
// operador/process_registro_delito.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'operador') {
  header("Location: ../index.php");
  exit();
}
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: registro_delito.php');
  exit();
}

$codigo = trim($_POST['codigo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$comuna = trim($_POST['comuna'] ?? '');
$sector = trim($_POST['sector'] ?? '');
$fecha = $_POST['fecha'] ?? null;
$latitud = $_POST['latitud'] !== '' ? $_POST['latitud'] : null;
$longitud = $_POST['longitud'] !== '' ? $_POST['longitud'] : null;
$tipo_id = $_POST['tipo_id'] !== '' ? $_POST['tipo_id'] : null;
$delincuente_id = $_POST['delincuente_id'] !== '' ? $_POST['delincuente_id'] : null;

if ($codigo === '' || $descripcion === '' || $comuna === '' || !$fecha) {
  header('Location: registro_delito.php?msg=Datos%20incompletos');
  exit();
}

$check = $pdo->prepare('SELECT id FROM delito WHERE codigo = ? LIMIT 1');
$check->execute([$codigo]);
if ($check->fetch()) {
  header('Location: registro_delito.php?msg=Código%20ya%20registrado');
  exit();
}

$sql = "INSERT INTO delito (codigo, descripcion, comuna, sector, fecha, latitud, longitud, tipo_id, delincuente_id)
        VALUES (:codigo, :descripcion, :comuna, :sector, :fecha, :latitud, :longitud, :tipo_id, :delincuente_id)";
$stmt = $pdo->prepare($sql);
$result = $stmt->execute([
  'codigo' => $codigo,
  'descripcion' => $descripcion,
  'comuna' => $comuna,
  'sector' => $sector,
  'fecha' => $fecha,
  'latitud' => $latitud,
  'longitud' => $longitud,
  'tipo_id' => $tipo_id,
  'delincuente_id' => $delincuente_id
]);

if ($result) {
  header('Location: registro_delito.php?msg=Delito%20registrado');
  exit();
} else {
  header('Location: registro_delito.php?msg=Error');
  exit();
}
