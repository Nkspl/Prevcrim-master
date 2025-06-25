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
  'delitos'         => isset($_POST['delitos']) ? implode(',', $_POST['delitos']) : '',
  'estado'          => $_POST['estado'],
  'latitud'         => trim($_POST['latitud']),
  'longitud'        => trim($_POST['longitud']),
];

$stmt=$pdo->prepare("SELECT id, delitos FROM delincuente WHERE rut=:rut LIMIT 1");
$stmt->execute(['rut'=>$datos['rut']]);
$existe=$stmt->fetch();

if ($existe) {
  $existentes = array_filter(array_map('trim', explode(',', $existe['delitos'])));
  $nuevos = isset($_POST['delitos']) ? array_map('trim', $_POST['delitos']) : [];
  $datos['delitos'] = implode(',', array_unique(array_merge($existentes, $nuevos)));
  $datos['id'] = $existe['id'];

  $sql="UPDATE delincuente SET
          apellidos_nombres=:nombre,
          apodo=:apodo,
          domicilio=:domicilio,
          ultimo_lugar_visto=:ultimo_lugar,
          fono_fijo=:fono,
          celular=:celular,
          email=:email,
          fecha_nacimiento=:fecha_nacimiento,
          delitos=:delitos,
          estado=:estado,
          latitud=:latitud,
          longitud=:longitud
        WHERE id=:id";
  $stmtUp=$pdo->prepare($sql);
  if ($stmtUp->execute($datos)) {
    header("Location: registro_delincuente.php?msg=Actualizado"); exit();
  } else {
    header("Location: registro_delincuente.php?msg=Error"); exit();
  }
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
