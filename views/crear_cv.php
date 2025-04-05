<?php
require_once('../configs/db_configs.php');
session_start();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y obtener los datos del formulario
    $nombre = $_POST['nombre'] ?? null;
    $apellido = $_POST['apellido'] ?? null;
    $correo_electronico = $_POST['correo_electronico'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $ciudad_provincia = $_POST['ciudad_provincia'] ?? null;
    $formacion_academica = $_POST['formacion_academica'] ?? null;
    $experiencia_laboral = $_POST['experiencia_laboral'] ?? null;
    $habilidades_clave = $_POST['habilidades_clave'] ?? null;
    $idiomas = $_POST['idiomas'] ?? null;
    $objetivo_profesional = $_POST['objetivo_profesional'] ?? null;
    $logros_proyectos = $_POST['logros_proyectos'] ?? null;
    $disponibilidad = $_POST['disponibilidad'] ?? null;
    $redes_profesionales = $_POST['redes_profesionales'] ?? null;
    $referencias = $_POST['referencias'] ?? null;

    // Subida de imagen
    $fotoRuta = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = $_FILES['foto']['name'];
        $temporal = $_FILES['foto']['tmp_name'];
        $carpetaDestino = '../uploads/';

        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        $fotoRuta = $carpetaDestino . uniqid() . "_" . basename($nombreArchivo);
        move_uploaded_file($temporal, $fotoRuta);
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO curriculum (
                nombre, apellido, correo_electronico, telefono, direccion, ciudad_provincia, 
                formacion_academica, experiencia_laboral, habilidades_clave, idiomas, 
                objetivo_profesional, logros_proyectos, disponibilidad, redes_profesionales, 
                referencias, foto
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    $stmt->bind_param(
        "ssssssssssssssss",
        $nombre,
        $apellido,
        $correo_electronico,
        $telefono,
        $direccion,
        $ciudad_provincia,
        $formacion_academica,
        $experiencia_laboral,
        $habilidades_clave,
        $idiomas,
        $objetivo_profesional,
        $logros_proyectos,
        $disponibilidad,
        $redes_profesionales,
        $referencias,
        $fotoRuta
    );

    if ($stmt->execute()) {
        echo "<script>window.location.href='candidato.php';</script>";
    } else {
        echo "Error al guardar el currículum: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Currículum</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <style>
        .textarea,
        .input {
            width: 600px;
        }
    </style>
</head>

<body>
    <form class="is-flex is-flex-direction-column is-align-items-center mt-5 mb-5" action="crear_cv.php" method="POST" enctype="multipart/form-data" style="width: 90%;;">
        <h1 class="title is-3">Crear Currículum</h1>
        <label class="label">Nombre(s): <br><input class="input" type="text" name="nombre" required></label>
        <label class="label">Apellido(s): <br><input class="input" type="text" name="apellido" required></label>
        <label class="label">Correo Electrónico: <br><input class="input" type="email" name="correo_electronico" required></label>
        <label class="label">Teléfono: <br><input class="input" type="text" name="telefono" required></label>
        <label class="label">Dirección: <br><input class="input" type="text" name="direccion"></label>
        <label class="label">Ciudad / Provincia: <br><input class="input" type="text" name="ciudad_provincia"></label>
        <label class="label">Formación Académica: <br><textarea class="textarea" style="resize: none" name="formacion_academica"></textarea class="textarea" style="resize: none"></label>
        <label class="label">Experiencia Laboral: <br><textarea class="textarea" style="resize: none" name="experiencia_laboral"></textarea class="textarea" style="resize: none"></label>
        <label class="label">Habilidades Clave: <br><textarea class="textarea" style="resize: none" name="habilidades_clave"></textarea class="textarea" style="resize: none"></label>
        <label class="label">Idiomas: <br><textarea class="textarea" style="resize: none" name="idiomas"></textarea class="textarea" style="resize: none"></label>
        <label class="label">Objetivo Profesional / Resumen: <br><textarea class="textarea" style="resize: none" name="objetivo_profesional"></textarea class="textarea" style="resize: none"></label>
        <label class="label">Logros o Proyectos Destacados: <br><textarea class="textarea" style="resize: none" name="logros_proyectos"></textarea class="textarea" style="resize: none"></label>
        <label class="label">Disponibilidad: <br><input class="input" type="text" name="disponibilidad"></label>
        <label class="label">Redes Profesionales: <br><textarea class="textarea" style="resize: none" name="redes_profesionales"></textarea class="textarea" style="resize: none"></label>
        <label class="label">Referencias: <br><textarea class="textarea" style="resize: none" name="referencias"></textarea class="textarea" style="resize: none"></label>
        <label for="cv_pdf" class="label">Suba su Currículum en PDF: <br></label>
        <!-- SUBIR UNA IMAGEN -->
        <label for="foto" class="label">Suba su foto: <br></label>
        <div class="file has-name" id="fotoUpload">
            <label class="file-label">
                <input class="file-input" type="file" name="foto" />
                    <span class="file-cta">
                        <span class="file-icon">
                            <i class="fas fa-upload"></i>
                        </span>
                    <span class="file-label"> Elija un archivo... </span>
                </span>
                <span class="file-name"> Suba una foto aqui en jpg o png </span>
            </label>
        </div>
        <button class="button is-primary"type="submit">Guardar Currículum</button>
    </form>
    <script>
        const fileInput = document.querySelector("#fotoUpload input[type=file]");
        fileInput.onchange = () => {
            if (fileInput.files.length > 0) {
            const fileName = document.querySelector("#fotoUpload .file-name");
            fileName.textContent = fileInput.files[0].name;
            }
        };
    </script>
</body>

</html>