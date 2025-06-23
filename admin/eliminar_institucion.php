<?php
// admin/eliminar_institucion.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='admin') {
  header("Location: ../index.php"); exit();
}
require_once '../config.php';
$id=intval($_GET['id']??0);
$pdo->prepare("DELETE FROM institucion WHERE id=:id")
    ->execute(['id'=>$id]);
header("Location: gestion_instituciones.php");
exit();
