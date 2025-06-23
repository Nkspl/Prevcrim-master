<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM delincuente WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: listado_delincuentes.php?msg=Delincuente eliminado');
exit;