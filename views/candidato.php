<?php
require_once('../configs/db_configs.php');
session_start();

// Verificar si el usuario está logueado
if ($_SESSION['userRole'] !== "candidato") {
    header("Location: ../index.php");
    exit;
}

$userId = $_SESSION['userId'];

// Verificar la conexión a la base de datos
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
                    <h2 class="title">Mi Currículum</h2>
                    <div class="box" style="background-color:rgb(39, 59, 210);">
                        <?php
                        // Consulta a la base de datos, usando usuario_id para obtener el currículum del usuario actual
                        $sql = "SELECT * FROM curriculum WHERE id = $userId";
                        $result = $conexion->query($sql);
                        if ($result && $row = $result->fetch_assoc()) {
                            // Mostrar la foto si existe
                            $fotoRuta = $row['foto'];  // Asumiendo que 'foto' contiene la ruta de la imagen
                            if (file_exists($fotoRuta)) {
                                echo '<figure class="image is-128x128">';
                                echo '<img class="is-rounded" src="' . htmlspecialchars($fotoRuta) . '" alt="Foto del candidato" style="width: 128px; height: 128px; object-fit: cover;">';
                                echo '</figure>';
                            } else {
                                // Imagen predeterminada si no existe la foto
                                echo '<figure class="image is-128x128 is-rounded">';
                                echo '<img class="is-rounded" src="../uploads/default.png" alt="Foto del candidato" style="width: 128px; height: 128px; object-fit: cover;">';
                                echo '</figure>';
                            }

                            // Mostrar los demás campos del currículum
                            echo '<h3 class="title is-4">' . htmlspecialchars($row['nombre']) . ' ' . htmlspecialchars($row['apellido']) . '</h3>';
                            echo '<p><strong>Correo Electrónico:</strong> ' . htmlspecialchars($row['correo_electronico']) . '</p>';
                            echo '<p><strong>Teléfono:</strong> ' . htmlspecialchars($row['telefono']) . '</p>';
                            echo '<p><strong>Dirección:</strong> ' . htmlspecialchars($row['direccion']) . ', ' . htmlspecialchars($row['ciudad_provincia']) . '</p>';
                            echo '<p><strong>Formación Académica:</strong> ' . nl2br(htmlspecialchars($row['formacion_academica'])) . '</p>';
                            echo '<p><strong>Experiencia Laboral:</strong> ' . nl2br(htmlspecialchars($row['experiencia_laboral'])) . '</p>';
                            echo '<p><strong>Habilidades Clave:</strong> ' . nl2br(htmlspecialchars($row['habilidades_clave'])) . '</p>';
                            echo '<p><strong>Idiomas:</strong> ' . nl2br(htmlspecialchars($row['idiomas'])) . '</p>';
                            echo '<p><strong>Objetivo Profesional:</strong> ' . nl2br(htmlspecialchars($row['objetivo_profesional'])) . '</p>';
                            echo '<p><strong>Logros y Proyectos:</strong> ' . nl2br(htmlspecialchars($row['logros_proyectos'])) . '</p>';
                            echo '<p><strong>Disponibilidad:</strong> ' . htmlspecialchars($row['disponibilidad']) . '</p>';
                            echo '<p><strong>Redes Profesionales:</strong> ' . nl2br(htmlspecialchars($row['redes_profesionales'])) . '</p>';
                            echo '<p><strong>Referencias:</strong> ' . nl2br(htmlspecialchars($row['referencias'])) . '</p>';
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
                                echo "<h3 class='title is-5 p-0 m-0'>" . htmlspecialchars($row['titulo']) . "</h3>";
                                echo "<p>" . htmlspecialchars($row['descripcion']) . "</p>";
                                echo "<hr>";
                                echo "<h4 class='text'>" . htmlspecialchars($row['fecha_publicacion']) . "</h4>";
                                echo "<h4 class='button'>" . htmlspecialchars($row['salario']) . "</h4>";
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
