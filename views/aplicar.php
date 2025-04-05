<?php
require_once('../configs/db_configs.php');
session_start();

// Verificamos que el usuario esté autenticado
if (empty($_SESSION['userId'])) {
    header("Location: ../index.php");
    exit;
}

$userId = $_SESSION['userId'];

// Verificamos si llegó el ID de la oferta por POST
if (!isset($_POST['oferta_id'])) {
    die('Error: No se ha especificado una oferta.');
}

$ofertaId = intval($_POST['oferta_id']);

// Verificamos que la oferta exista y obtenemos el ID de la empresa que la publicó
$sqlOferta = "SELECT empresa_id FROM ofertas WHERE id = ?";
$stmtOferta = $conexion->prepare($sqlOferta);

if (!$stmtOferta) {
    die('Error en la preparación de la consulta: ' . $conexion->error);
}

$stmtOferta->bind_param("i", $ofertaId);
$stmtOferta->execute();
$resultado = $stmtOferta->get_result();

if ($resultado->num_rows === 0) {
    die('Error: La oferta no existe.');
}

$oferta = $resultado->fetch_assoc();
$empresaId = $oferta['empresa_id'];

// Verificamos si el candidato ya se ha postulado a esta oferta
$sqlVerificar = "SELECT id FROM postulaciones WHERE candidato_id = ? AND oferta_id = ?";
$stmtVerificar = $conexion->prepare($sqlVerificar);
$stmtVerificar->bind_param("ii", $userId, $ofertaId);
$stmtVerificar->execute();
$resultadoVerificar = $stmtVerificar->get_result();

if ($resultadoVerificar->num_rows > 0) {
    echo "Ya te has postulado a esta oferta.";
    exit;
}

// Insertamos la postulación en la base de datos
$sqlPostulacion = "INSERT INTO postulaciones (candidato_id, oferta_id, empresa_id) VALUES (?, ?, ?)";
$stmtPostulacion = $conexion->prepare($sqlPostulacion);

if (!$stmtPostulacion) {
    die('Error en la preparación de la consulta de postulación: ' . $conexion->error);
}

$stmtPostulacion->bind_param("iii", $userId, $ofertaId, $empresaId);

if ($stmtPostulacion->execute()) {
    
    header("Location: candidato.php?postulacion=exitosa");
    exit;
} else {
    echo "Error al postularte: " . $conexion->error;
}

// Cerramos las consultas y conexión
$stmtOferta->close();
$stmtVerificar->close();
$stmtPostulacion->close();
$conexion->close();
?>
