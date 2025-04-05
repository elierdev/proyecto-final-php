<?php
require_once('../configs/db_configs.php');
session_start();
if (empty($_SESSION['userId'])) {
    header("Location: ../index.php");
    exit;
}

// Procesar formulario de publicación de vacantes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $ubicacion = $_POST['ubicacion'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $salario = $_POST['salario'];
    $requisitos = $_POST['requisitos'];
    $empresa_id = $_SESSION['userId']; // Asume que el ID de la empresa está almacenado en la sesión

    // Verificar si el empresa_id existe en la tabla empresas
    $sql_check_empresa = "SELECT id FROM empresas WHERE id = ?";
    $stmt_check = $conexion->prepare($sql_check_empresa);
    if (!$stmt_check) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    $stmt_check->bind_param("i", $empresa_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Insertar la oferta si el empresa_id es válido
        $sql = "INSERT INTO ofertas (titulo, descripcion, ubicacion, tipo_contrato, salario, requisitos, empresa_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("ssssdis", $titulo, $descripcion, $ubicacion, $tipo_contrato, $salario, $requisitos, $empresa_id);

        if ($stmt->execute()) {
            echo "<p class='notification is-success'>Vacante publicada con éxito.</p>";
        } else {
            echo "<p class='notification is-danger'>Error al publicar la vacante: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='notification is-danger'>Error: El ID de empresa no es válido.</p>";
    }
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Publicar Vacantes</title>
    <link rel="stylesheet" href="../assets/css/empresa.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
</head>

<body>
    <!-- Navbar -->
    <?php include_once("../views/templates/navbar.php"); ?>

    <section class="section">
        <div class="container">
            <h1 class="title">Publicar Vacante</h1>
            <form method="POST">
                <div class="field">
                    <label class="label">Título del Puesto</label>
                    <div class="control">
                        <input class="input" type="text" name="titulo" placeholder="Ej: Desarrollador Web" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Descripción</label>
                    <div class="control">
                        <textarea class="textarea" name="descripcion" placeholder="Describe el puesto" required></textarea>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Ubicación</label>
                    <div class="control">
                        <input class="input" type="text" name="ubicacion" placeholder="Ej: Santo Domingo">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Tipo de Contrato</label>
                    <div class="control">
                        <input class="input" type="text" name="tipo_contrato" placeholder="Ej: Tiempo completo">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Salario</label>
                    <div class="control">
                        <input class="input" type="number" step="0.01" name="salario" placeholder="Ej: 45000">
                    </div>
                </div>
                <div class="field">
                    <label la="label">Requisitos</label>
                    <div class="control">
                        <textarea class="textarea" name="requisitos" placeholder="Lista de habilidades y requisitos"></textarea>
                    </div>
                </div>
                <div class="control">
                    <button class="button is-link" type="submit">Publicar Vacante</button>
                </div>
            </form>
        </div>
    </section>
</body>

</html>