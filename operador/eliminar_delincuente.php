<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';
require_once '../inc/funciones.php';

$id = $_POST['id'] ?? null;

if ($id) {
    $infoStmt=$pdo->prepare("SELECT rut,apellidos_nombres FROM delincuente WHERE id=?");
    $infoStmt->execute([$id]);
    $info=$infoStmt->fetch();
    $stmt = $pdo->prepare("DELETE FROM delincuente WHERE id = ?");
    $stmt->execute([$id]);
    logActividadDelincuente($pdo,[
      'delincuente_id'=>$id,
      'rut'=>$info['rut']??null,
      'nombre'=>$info['apellidos_nombres']??null,
      'accion'=>'eliminado',
      'datos'=>'',
      'autor_id'=>$_SESSION['user_id']
    ]);
}

header('Location: listado_delincuentes.php?msg=Delincuente eliminado');
exit;