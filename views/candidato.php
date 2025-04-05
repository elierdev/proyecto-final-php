<?php
require_once('../configs/db_configs.php');
session_start();

if (empty($_SESSION['userId'])) {
    header("Location: ../index.php");
    exit;
}

$userId = $_SESSION['userId'];

// Verifica si la conexión a la base de datos está establecida
if (!$conexion || $conexion->connect_error) {
    die("Error: No se pudo conectar a la base de datos. " . $conexion->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Empleo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
</head>

<body>
    <!-- Navbar -->
    <?php include_once("../views/templates/navbar.php"); ?>

    <section class="section">
        <div class="container">
            <div class="columns">
                <!-- Currículum -->
                <div class="column is-one-third">
                    <h2 class="title">Currículum del Candidato</h2>
                    <div class="box">
                        <?php
                        // Consulta a la base de datos
                        $sql = "SELECT * FROM curriculum WHERE id = $userId";
                        $result = $conexion->query($sql);
                        if ($result && $row = $result->fetch_assoc()) {
                            // Mostrar la foto
                            echo '<figure class="image is-128x128">';
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['foto']) . '" alt="Foto del candidato">';
                            echo '</figure>';

                            // Mostrar los demás campos
                            echo '<h3 class="title is-4">' . htmlspecialchars($row['nombre']) . ' ' . htmlspecialchars($row['apellido']) . '</h3>';
                            echo '<p><strong>Correo Electrónico:</strong> ' . htmlspecialchars($row['correo_electronico']) . '</p>';
                            echo '<p><strong>Teléfono:</strong> ' . htmlspecialchars($row['telefono']) . '</p>';
                            echo '<p><strong>Dirección:</strong> ' . htmlspecialchars($row['direccion']) . ', ' . htmlspecialchars($row['ciudad_provincia']) . '</p>';
                            echo '<p><strong>Formación Académica:</strong> ' . htmlspecialchars($row['formacion_academica']) . '</p>';
                            echo '<p><strong>Experiencia Laboral:</strong> ' . htmlspecialchars($row['experiencia_laboral']) . '</p>';
                            echo '<p><strong>Habilidades Clave:</strong> ' . htmlspecialchars($row['habilidades_clave']) . '</p>';
                            echo '<p><strong>Idiomas:</strong> ' . htmlspecialchars($row['idiomas']) . '</p>';
                            echo '<p><strong>Objetivo Profesional:</strong> ' . htmlspecialchars($row['objetivo_profesional']) . '</p>';
                            echo '<p><strong>Logros y Proyectos:</strong> ' . htmlspecialchars($row['logros_proyectos']) . '</p>';
                            echo '<p><strong>Disponibilidad:</strong> ' . htmlspecialchars($row['disponibilidad']) . '</p>';
                            echo '<p><strong>Redes Profesionales:</strong> ' . htmlspecialchars($row['redes_profesionales']) . '</p>';
                            echo '<p><strong>Referencias:</strong> ' . htmlspecialchars($row['referencias']) . '</p>';
                        } else {
                            echo '<p>No se encontró información del candidato.</p>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Ofertas de Trabajo -->
                <div class="column is-two-thirds">
                    <h2 class="title">Ofertas de Trabajo</h2>

                    <div id="resultados">
                        <?php
                        $sql = "SELECT * FROM ofertas ORDER BY id DESC";
                        $result = $conexion->query($sql);
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='box'>";
                                echo "<h3 class='title is-5'>" . htmlspecialchars($row['titulo']) . "</h3>";
                                echo "<p>" . htmlspecialchars($row['descripcion']) . "</p>";
                                echo "<form action='aplicar.php' method='POST'>";
                                echo "<input type='hidden' name='oferta_id' value='" . $row['id'] . "'>";
                                echo "<button class='button is-success mt-2' type='submit'>Postularme</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p>No hay ofertas disponibles en este momento.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>