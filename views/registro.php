<?php
require_once('../configs/db_configs.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['RegistrarseCandidato'])) {
        $email = trim($_POST['email']);
        $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
        $name = trim($_POST['name']);

        $stmt = $conexion->prepare("SELECT id FROM usuarioscandidatos WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = '
                <div class="notification is-danger is-small p-1">
                    <button class="delete p-0 m-0"></button>
                    El correo ya está registrado como candidato.
                </div>';
        } else {
            $stmt = $conexion->prepare("INSERT INTO usuarioscandidatos (nombre, correo, contrasena) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password);

            if ($stmt->execute()) {
                echo "<script>alert('Registro exitoso.'); window.location.href = 'crear_cv.php';</script>";
                exit;
            } else {
                $error = '
                    <div class="notification is-danger is-small p-1">
                        <button class="delete p-0 m-0"></button>
                        Error al registrar candidato. Intenta de nuevo.
                    </div>';
            }
        }

        $stmt->close();
    }

    if (isset($_POST['RegistrarseEmpresa'])) {
        $email = trim($_POST['email']);
        $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
        $companyName = trim($_POST['companyName']);
        $contact = trim($_POST['contact']);

        $stmt = $conexion->prepare("SELECT id FROM empresas WHERE correo = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = '
                <div class="notification is-danger is-small p-1">
                    <button class="delete p-0 m-0"></button>
                    El correo ya está registrado como empresa.
                </div>';
        } else {
            $stmt = $conexion->prepare("INSERT INTO empresas (nombre_empresa, direccion, correo, contrasena) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $companyName, $contact, $email, $password);

            if ($stmt->execute()) {
                echo "<script>alert('Registro exitoso.'); window.location.href = '../index.php';</script>";
                exit;
            } else {
                $error = '
                    <div class="notification is-danger is-small p-1">
                        <button class="delete p-0 m-0"></button>
                        Error al registrar empresa. Intenta de nuevo.
                    </div>';
            }
        }

        $stmt->close();
    }

    $conexion->close();
}
?>


<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro</title>
    <link rel="stylesheet" href="../assets/css/registro.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    
</head>


<body style="max-height: 100%; background-image: url(https://i.imgur.com/lum3Ci8.jpeg); background-size: cover;"
    class="is-flex is-flex-direction-column is-justify-content-center is-align-items-center">
    <div class=" box login-container is-flex is-flex-direction-column mt-30" style="max-width: 400px;">
        <h2 class="title is-3">Registro</h2>
        <?php echo @$error; ?>
        <form id="formCandidato" method="post" action="registro.php" style="display:none;">
            <label class="label">Nombre:</label>
            <input class="input is-small" type="text" id="name" name="name" required />

            <label class="label" for="email">Correo electrónico:</label>
            <input class="input is-small" type="email" id="email" name="email" required />

            <label class="label" for="password">Contraseña:</label>
            <input class="input is-small" type="password" id="password" name="password" required />

            <input class="button is-primary mt-2" style="width: 360px;" type="submit" name="RegistrarseCandidato" value="Registrarse"></input>
        </form>

        <form id="formEmpresa" method="post" action="registro.php" style="display:none;">
            <label class="label">Nombre de la empresa:</label>
            <input class="input is-small" type="text" id="companyName" name="companyName" required />

            <label class="label">Dirección:</label>
            <input class="input is-small" type="text" id="contact" name="contact" required />

            <label class="label" for="email">Correo electrónico:</label>
            <input class="input is-small" type="email" id="email" name="email" required />

            <label class="label" for="password">Contraseña:</label>
            <input class="input is-small" type="password" id="password" name="password" required />

            <button class="button is-primary mt-2" style="width: 360px;" type="submit" name="RegistrarseEmpresa" value="Registrarse">Registrarse</button>
        </form>

        <label class="label">Tipo de registro:</label>
        <div class="control">
            <div class="select">
                <select id="userType" name="userType">
                    <option value="candidato">Candidato</option>
                    <option value="empresa">Empresa</option>
                </select>
            </div>
        </div>

        <p class="m-3">¿Ya tienes una cuenta?</p>
        <button class="button is-primary is-outlined" id="btnLogin" type="button">Iniciar Sesión</button>
    </div>

    <script>
        // Manejar la visibilidad de los formularios según el tipo de usuario seleccionado
        document.getElementById('userType').addEventListener('change', function () {
            const userType = this.value;
            if (userType === 'empresa') {
                document.getElementById('formEmpresa').style.display = 'block';
                document.getElementById('formCandidato').style.display = 'none';
            } else {
                document.getElementById('formEmpresa').style.display = 'none';
                document.getElementById('formCandidato').style.display = 'block';
            }
        });

        // Asegurarnos de que se muestre el formulario adecuado al cargar la página
        window.onload = function () {
            const userType = document.getElementById('userType').value;
            if (userType === 'empresa') {
                document.getElementById('formEmpresa').style.display = 'block';
                document.getElementById('formCandidato').style.display = 'none';
            } else {
                document.getElementById('formEmpresa').style.display = 'none';
                document.getElementById('formCandidato').style.display = 'block';
            }
        };

        document.getElementById("btnLogin").addEventListener("click", function () {
            window.location.href = "../index.php";
        });

        document.addEventListener('DOMContentLoaded', () => {
            (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
                const $notification = $delete.parentNode;

                $delete.addEventListener('click', () => {
                    $notification.parentNode.removeChild($notification);
                });
            });
        });
    </script>
</body>

</html>
