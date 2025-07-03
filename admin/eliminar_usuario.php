<?php
// admin/eliminar_usuario.php
session_start();
if (!isset($_SESSION['user_id'])||$_SESSION['rol']!=='admin'){
  header("Location: ../index.php");exit();
}
require_once '../config.php';
require_once '../inc/funciones.php';
$id=intval($_GET['id']??0);
$stmt=$pdo->prepare("SELECT rut,nombre,rol FROM usuario WHERE id=:id");
$stmt->execute(['id'=>$id]);
$info=$stmt->fetch();
$pdo->prepare("DELETE FROM usuario WHERE id=:id")
    ->execute(['id'=>$id]);
logActividadUsuario($pdo,[
  'usuario_id'=>$id,
  'rut'=>$info['rut']??null,
  'nombre'=>$info['nombre']??null,
  'rol'=>$info['rol']??null,
  'accion'=>'eliminado',
  'datos'=>'',
  'autor_id'=>$_SESSION['user_id']
]);
header("Location: gestion_usuarios.php");
exit();
