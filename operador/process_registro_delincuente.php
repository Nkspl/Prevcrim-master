<?php
// operador/process_registro_delincuente.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='operador'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
require_once '../inc/funciones.php';

if ($_SERVER['REQUEST_METHOD']!=='POST') {
  header("Location: registro_delincuente.php"); exit();
}

$rut = trim($_POST['rut']);
if (!validarRut($rut)) {
  header("Location: registro_delincuente.php?msg=RUT inválido"); exit();
}
// Verificar si ya existe un delincuente con ese RUT
$check=$pdo->prepare("SELECT id FROM delincuente WHERE rut=:rut LIMIT 1");
$check->execute(['rut'=>$rut]);
if ($check->fetch()) {
  header("Location: registro_delincuente.php?msg=RUT ya registrado"); exit();
}
$datos = [
  'rut'             => $rut,
  'nombre'          => trim($_POST['nombre']),
  'apodo'           => trim($_POST['apodo']),
  'domicilio'       => trim($_POST['domicilio']),
  'ultimo_lugar'    => trim($_POST['ultimo_lugar']),
  'fono'            => trim($_POST['fono']),
  'celular'         => trim($_POST['celular']),
  'email'           => trim($_POST['email']),
  'imagen'          => null,
  'fecha_nacimiento'=> $_POST['fecha_nacimiento'],
  'delitos'         => isset($_POST['delitos']) ? implode(',', $_POST['delitos']) : '',
  'estado'          => $_POST['estado'],
  'latitud'         => trim($_POST['latitud']),
  'longitud'        => trim($_POST['longitud']),
];

// Validaciones de formato y longitud
if ($datos['fono'] !== '' && !ctype_digit($datos['fono'])) {
  header("Location: registro_delincuente.php?msg=Fono debe ser numérico"); exit();
}
if ($datos['celular'] !== '' && !ctype_digit($datos['celular'])) {
  header("Location: registro_delincuente.php?msg=Celular debe ser numérico"); exit();
}
if (!is_numeric($datos['latitud']) || !is_numeric($datos['longitud'])) {
  header("Location: registro_delincuente.php?msg=Coordenadas inválidas"); exit();
}

if (mb_strlen($datos['nombre']) > 150) {
  header("Location: registro_delincuente.php?msg=Nombre muy largo"); exit();
}
if (mb_strlen($datos['apodo']) > 50) {
  header("Location: registro_delincuente.php?msg=Apodo muy largo"); exit();
}
if (mb_strlen($datos['domicilio']) > 200) {
  header("Location: registro_delincuente.php?msg=Domicilio muy largo"); exit();
}
if (mb_strlen($datos['ultimo_lugar']) > 200) {
  header("Location: registro_delincuente.php?msg=Último lugar muy largo"); exit();
}
if (mb_strlen($datos['email']) > 100) {
  header("Location: registro_delincuente.php?msg=Email muy largo"); exit();
}

// Procesar la imagen subida
if (!empty($_FILES['imagen']['name'])) {
  $dir = __DIR__ . '/../img/delincuentes/';
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
  }
  $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
  $nombreArchivo = uniqid('delinc_', true) . '.' . $ext;
  if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $nombreArchivo)) {
    $datos['imagen'] = 'img/delincuentes/' . $nombreArchivo;
  }
}


$sql="INSERT INTO delincuente
    (rut,apellidos_nombres,apodo,domicilio,ultimo_lugar_visto,
     fono_fijo,celular,email,imagen,fecha_nacimiento,delitos,estado,
     latitud,longitud)
  VALUES
    (:rut,:nombre,:apodo,:domicilio,:ultimo_lugar,
     :fono,:celular,:email,:imagen,:fecha_nacimiento,:delitos,:estado,
     :latitud,:longitud)
";
$insert=$pdo->prepare($sql);
if ($insert->execute($datos)) {
  header("Location: registro_delincuente.php?msg=Registrado"); exit();
} else {
  header("Location: registro_delincuente.php?msg=Error"); exit();
}
