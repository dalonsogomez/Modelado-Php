<html>
<head>
    <meta charset="UTF-8">
    <title>Guardando Valoración</title>
</head>
<body>
    <?php
    include 'conexion_apartamentos.php';

    if (empty($_POST) || !isset($_POST['id_reserva']) || !isset($_POST['puntuacion'])) {
        echo "<h1>Error</h1>";
        echo "<p>No se han recibido los datos necesarios para guardar la valoración.</p>";
        echo "<p><a href='reservas_paso1.php'>Volver al inicio</a></p>";
    } else {
        $id_reserva = $_POST['id_reserva'];
        $puntuacion = $_POST['puntuacion'];
        $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : "";

        // Validación de datos
        $errores = "";
        if (!is_numeric($id_reserva)) {
            $errores .= " / ID de reserva inválido.";
        }
        if (!is_numeric($puntuacion) || $puntuacion < 0 || $puntuacion > 10) {
            $errores .= " / La puntuación debe ser un número entre 0 y 10.";
        }
        if (strlen($comentario) > 200) {
            $errores .= " / El comentario no puede exceder los 200 caracteres.";
        }

        if ($errores != "") {
            echo "<h1>Error al validar los datos</h1>";
            echo "<p>$errores</p>";
            echo "<p><a href='javascript:history.back()'>Volver atrás</a></p>";
        } else {
            // Escapar datos para la consulta
            $id_reserva_sql = mysqli_real_escape_string($conex, $id_reserva);
            $puntuacion_sql = mysqli_real_escape_string($conex, $puntuacion);
            $comentario_sql = mysqli_real_escape_string($conex, $comentario);

            // Insertar el comentario
            $query_insert = "INSERT INTO Comentario (ID_reserva, puntuacion, comentario) VALUES ('$id_reserva_sql', '$puntuacion_sql', '$comentario_sql')";
            $res_insert = mysqli_query($conex, $query_insert) or die(mysqli_error($conex));

            if ($res_insert) {
                echo "<h1>¡Gracias por su valoración!</h1>";
                echo "<p>Su comentario ha sido guardado correctamente.</p>";
                echo "<p><a href='reservas_paso1.php'>Realizar otra reserva</a></p>";
            } else {
                echo "<h1>Error</h1>";
                echo "<p>No se pudo guardar su valoración en la base de datos.</p>";
                echo "<p><a href='javascript:history.back()'>Volver a intentarlo</a></p>";
            }
        }
    }
    ?>
</body>
</html>
