<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';

$sql = "UPDATE delincuente SET
            rut = :rut,
            apellidos_nombres = :nombre,
            apodo = :apodo,
            domicilio = :domicilio,
            ultimo_lugar_visto = :ultimo_lugar,
            fono_fijo = :fono,
            celular = :celular,
            email = :email,
            fecha_nacimiento = :fecha_nacimiento,
            delitos = :delitos,
            estado = :estado,
            latitud = :latitud,
            longitud = :longitud
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'rut' => $_POST['rut'],
    'nombre' => $_POST['nombre'],
    'apodo' => $_POST['apodo'],
    'domicilio' => $_POST['domicilio'],
    'ultimo_lugar' => $_POST['ultimo_lugar'],
    'fono' => $_POST['fono'],
    'celular' => $_POST['celular'],
    'email' => $_POST['email'],
    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
    'delitos' => isset($_POST['delitos']) ? implode(',', $_POST['delitos']) : '',
    'estado' => $_POST['estado'],
    'latitud' => $_POST['latitud'],
    'longitud' => $_POST['longitud'],
    'id' => $_POST['id']
]);

header('Location: listado_delincuentes.php?msg=Delincuente actualizado');
exit;