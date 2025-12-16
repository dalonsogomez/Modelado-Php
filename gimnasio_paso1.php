<html>
<head>
    <meta charset="UTF-8">
    <title>Inscripción en Gimnasio - Paso 1</title>
    <style>
        .container { display: flex; justify-content: space-around; }
        .form-section { border: 1px solid #ccc; padding: 20px; width: 45%; }
        h1, h2 { text-align: center; }
    </style>
</head>
<body>
    <h1>Paso 1: Identificación del Socio</h1>
    <div class="container">
        <div class="form-section">
            <h2>Registrar Nuevo Socio</h2>
            <form action="gimnasio_guardar_socio.php" method="post">
                <p>
                    NIF: 
                    <input type="text" name="nif" size="9" maxlength="9" required>
                </p>
                <p>
                    Nombre completo: 
                    <input type="text" name="nombre" size="30" maxlength="50" required>
                </p>
                <p>
                    Teléfono: 
                    <input type="tel" name="telefono" size="9" maxlength="9" required>
                </p>
                <p>
                    Email: 
                    <input type="email" name="email" size="30" maxlength="50" required>
                </p>
                <p>
                    <input type="submit" value="Registrar y Continuar">
                </p>
            </form>
        </div>

        <div class="form-section">
            <h2>Seleccionar Socio Existente</h2>
            <?php
            include 'conexion_gimnasio.php';
            $query_socios = "SELECT socioID, nombre, nif FROM Socio ORDER BY nombre ASC";
            $res_socios = mysqli_query($conex, $query_socios) or die(mysqli_error($conex));

            if (mysqli_num_rows($res_socios) == 0) {
                echo "<p>No hay socios existentes. Por favor, registre un nuevo socio.</p>";
            } else {
                $formulario = <<<FORM
                <form action="gimnasio_paso2.php" method="post">
                    <p>
                        Seleccione un socio:
                        <select name="socioID">
FORM;
                print $formulario;

                while ($socio = mysqli_fetch_assoc($res_socios)) {
                    echo "<option value=\"{$socio['socioID']}\">{$socio['nombre']} ({$socio['nif']})</option>";
                }

                $formulario_fin = <<<FORM_FIN
                        </select>
                    </p>
                    <p>
                        <input type="submit" value="Seleccionar y Continuar">
                    </p>
                </form>
FORM_FIN;
                print $formulario_fin;
            }
            ?>
        </div>
    </div>
</body>
</html>
