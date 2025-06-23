<?php
// admin/eliminar_usuario.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='admin'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
$id=intval($_GET['id']??0);
$pdo->prepare("DELETE FROM usuario WHERE id=:id")
    ->execute(['id'=>$id]);
header("Location: gestion_usuarios.php");
exit();
