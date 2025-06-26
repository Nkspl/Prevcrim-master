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
