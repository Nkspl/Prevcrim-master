<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'operador') {
    header('Location: /login.php');
    exit;
}

require_once '../config.php';
require_once '../inc/funciones.php';

$imagen = $_POST['imagen_actual'] ?? null;
if (!empty($_FILES['imagen']['name'])) {
    $dir = __DIR__ . '/../img/delincuentes/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid('delinc_', true) . '.' . $ext;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $nombreArchivo)) {
        $imagen = 'img/delincuentes/' . $nombreArchivo;
    }
}

$sql = "UPDATE delincuente SET
            rut = :rut,
            nombres = :nombres,
            apellidos = :apellidos,
            apellidos_nombres = :apellidos_nombres,
            apodo = :apodo,
            domicilio = :domicilio,
            ultimo_lugar_visto = :ultimo_lugar,
            fono_fijo = :fono,
            celular = :celular,
            email = :email,
            imagen = :imagen,
            fecha_nacimiento = :fecha_nacimiento,
            delitos = :delitos,
            estado = :estado,
            latitud = :latitud,
            longitud = :longitud
        WHERE id = :id";

$stmt = $pdo->prepare($sql);

// Validar que el estado sea uno de los permitidos
$estado = $_POST['estado'];
$permitidos = ['Preso', 'Libre', 'Orden de arresto'];
if (!in_array($estado, $permitidos, true)) {
    header('Location: editar_delincuente.php?id=' . urlencode($_POST['id']) . '&msg=Estado inválido');
    exit;
}

$id=$_POST['id'];
$stmt->execute([
    'rut' => $_POST['rut'],
    'nombres' => $_POST['nombres'],
    'apellidos' => $_POST['apellidos'],
    'apellidos_nombres' => trim($_POST['apellidos']) . ' ' . trim($_POST['nombres']),
    'apodo' => $_POST['apodo'],
    'domicilio' => $_POST['domicilio'],
    'ultimo_lugar' => $_POST['ultimo_lugar'],
    'fono' => $_POST['fono'],
    'celular' => $_POST['celular'],
    'email' => $_POST['email'],
    'imagen' => $imagen,
    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
    'delitos' => trim($_POST['delitos'] ?? ''),
    'estado' => $estado,
    'latitud' => $_POST['latitud'],
    'longitud' => $_POST['longitud'],
    'id' => $id
]);
// Registrar actividad
logActividadDelincuente($pdo,[
    'delincuente_id'=>$id,
    'rut'=>$_POST['rut'],
    'nombre'=>trim($_POST['apellidos']).' '.trim($_POST['nombres']),
    'accion'=>'actualizado',
    'datos'=>'',
    'autor_id'=>$_SESSION['user_id']
]);

header('Location: listado_delincuentes.php?msg=Delincuente actualizado');
exit;