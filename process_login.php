<?php
// process_login.php
session_start();
require_once 'config.php';
require_once 'inc/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$rut = trim($_POST['rut']);
if (!validarRut($rut)) {
    header("Location: index.php?error=RUT inválido");
    exit();
}
$password = $_POST['password'];

$sql  = "SELECT * FROM usuario WHERE rut = :rut";
$stmt = $pdo->prepare($sql);
$stmt->execute(['rut' => $rut]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id']        = $user['id'];
    $_SESSION['nombre']         = $user['nombre'];
    $_SESSION['rol']            = $user['rol'];
    $_SESSION['institucion_id'] = $user['institucion_id'];
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: index.php?error=Credenciales inválidas");
    exit();
}
