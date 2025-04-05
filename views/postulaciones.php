<?php
require_once('../configs/db_configs.php');


if (empty($_SESSION['userId']) || $_SESSION['userRole'] !== "empresa") {
    header("Location: ../index.php");
    exit;
}

$empresaId = $_SESSION['userId'];

// Ayudame a poner la foto en esta consulta 

$sql = "SELECT 
            postulaciones.id AS postulacion_id,
            curriculum.nombre AS candidato_nombre,
            curriculum.apellido AS candidato_apellido,
            curriculum.correo_electronico AS candidato_email,
            curriculum.telefono AS candidato_telefono,
            ofertas.titulo AS oferta_titulo,
            postulaciones.fecha_postulacion
        FROM postulaciones
        JOIN curriculum ON postulaciones.candidato_id = curriculum.id
        JOIN ofertas ON postulaciones.oferta_id = ofertas.id
        WHERE postulaciones.empresa_id = ?
        ORDER BY postulaciones.fecha_postulacion DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $empresaId);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Postulaciones Recibidas</title>
    <link rel="stylesheet" href="../assets/css/empresa.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
</head>

<body>
    <!-- Navbar -->
    <?php include_once("../views/templates/navbar.php"); ?>

    <section class="section">
        <div class="container">
            <h1 class="title">Postulaciones Recibidas</h1>

            <?php if ($resultado->num_rows > 0): ?>
                <table class="table is-fullwidth is-striped">
                    <thead>
                        <tr>
                            <th>Candidato</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Oferta</th>
                            <th>Fecha de Postulación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['candidato_nombre'] . ' ' . $fila['candidato_apellido']) ?></td>
                                <td><?= htmlspecialchars($fila['candidato_email']) ?></td>
                                <td><?= htmlspecialchars($fila['candidato_telefono']) ?></td>
                                <td><?= htmlspecialchars($fila['oferta_titulo']) ?></td>
                                <td><?= htmlspecialchars($fila['fecha_postulacion']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="notification is-info">Aún no has recibido postulaciones.</p>
            <?php endif; ?>

            
        </div>
    </section>
</body>

</html>

<?php
$stmt->close();
$conexion->close();
?>
