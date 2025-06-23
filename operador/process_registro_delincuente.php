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
  'fecha_nacimiento'=> $_POST['fecha_nacimiento'],
  'delitos'         => trim($_POST['delitos']),
  'estado'          => $_POST['estado'],
  'latitud'         => trim($_POST['latitud']),
  'longitud'        => trim($_POST['longitud']),
];

$stmt=$pdo->prepare("
  SELECT id FROM delincuente WHERE rut=:rut
");
$stmt->execute(['rut'=>$datos['rut']]);
if ($stmt->rowCount()>0) {
  header("Location: registro_delincuente.php?msg=Ya existe"); exit();
}

$sql="INSERT INTO delincuente
    (rut,apellidos_nombres,apodo,domicilio,ultimo_lugar_visto,
     fono_fijo,celular,email,fecha_nacimiento,delitos,estado,
     latitud,longitud)
  VALUES
    (:rut,:nombre,:apodo,:domicilio,:ultimo_lugar,
     :fono,:celular,:email,:fecha_nacimiento,:delitos,:estado,
     :latitud,:longitud)
";
$insert=$pdo->prepare($sql);
if ($insert->execute($datos)) {
  header("Location: registro_delincuente.php?msg=Registrado"); exit();
} else {
  header("Location: registro_delincuente.php?msg=Error"); exit();
}
