<?php
// jefe/eliminar_sector.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='jefe_zona'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
$iid=$_SESSION['institucion_id'];
$id=intval($_GET['id']??0);
$pdo->prepare("
  DELETE FROM sector WHERE id=:id AND institucion_id=:iid
")->execute(['id'=>$id,'iid'=>$iid]);
header("Location: gestion_sectores.php");
exit();
