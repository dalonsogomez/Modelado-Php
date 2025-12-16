<html>
<head>
    <meta charset="UTF-8">
    <title>Reserva de Apartamentos - Confirmación</title>
</head>
<body>
    <h1>Paso 3: Confirmación de la Reserva</h1>

    <?php
    include 'conexion_apartamentos.php';

    function validar_datos(&$id_usuario, &$id_inmueble, &$fecha_entrada, &$fecha_salida, &$errores) {
        $flag = true;
        
        // Validación de IDs
        if (!isset($id_usuario) || !is_numeric($id_usuario)) {
            $errores .= " / ID de usuario inválido";
            $flag = false;
        }
        if (!isset($id_inmueble) || !is_numeric($id_inmueble)) {
            $errores .= " / ID de inmueble inválido";
            $flag = false;
        }

        // Validación de fechas
        $fecha_actual = date('Y-m-d');
        if (empty($fecha_entrada) || $fecha_entrada < $fecha_actual) {
            $errores .= " / La fecha de entrada debe ser igual o posterior a la actual.";
            $flag = false;
        }
        if (empty($fecha_salida) || $fecha_salida <= $fecha_entrada) {
            $errores .= " / La fecha de salida debe ser posterior a la fecha de entrada.";
            $flag = false;
        }
        
        return $flag;
    }

    if (empty($_POST)) {
        echo "<p>No se han recibido datos. <a href='reservas_paso1.php'>Por favor, inicie el proceso de nuevo.</a></p>";
    } else {
        $id_usuario = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : "";
        $id_inmueble = isset($_POST['id_inmueble']) ? $_POST['id_inmueble'] : "";
        $fecha_entrada = isset($_POST['fecha_entrada']) ? $_POST['fecha_entrada'] : "";
        $fecha_salida = isset($_POST['fecha_salida']) ? $_POST['fecha_salida'] : "";
        $errores = "";

        if (!validar_datos($id_usuario, $id_inmueble, $fecha_entrada, $fecha_salida, $errores)) {
            echo "<h2>Error en los datos de la reserva:</h2>";
            echo "<p>$errores</p>";
            echo "<p><a href='javascript:history.back()'>Volver atrás</a></p>";
        } else {
            // Escapar datos para la consulta
            $id_usuario = mysqli_real_escape_string($conex, $id_usuario);
            $id_inmueble = mysqli_real_escape_string($conex, $id_inmueble);
            $fecha_entrada_sql = mysqli_real_escape_string($conex, $fecha_entrada);
            $fecha_salida_sql = mysqli_real_escape_string($conex, $fecha_salida);

            // Insertar la reserva
            $query_insert = "INSERT INTO Reserva (ID_inmueble, ID_usuario, fecha_entrada, fecha_salida) VALUES ('$id_inmueble', '$id_usuario', '$fecha_entrada_sql', '$fecha_salida_sql')";
            $res_insert = mysqli_query($conex, $query_insert) or die(mysqli_error($conex));
            $id_reserva_nueva = mysqli_insert_id($conex);

            if ($res_insert) {
                echo "<h2>¡Reserva realizada con éxito!</h2>";
                
                // Obtener datos para mostrar la confirmación
                $query_info = "SELECT u.nombre AS nombre_usuario, i.nombre AS nombre_inmueble, i.precio 
                               FROM Usuario u, Inmueble i 
                               WHERE u.ID_Usuario = $id_usuario AND i.ID_Inmueble = $id_inmueble";
                $res_info = mysqli_query($conex, $query_info) or die(mysqli_error($conex));
                $info = mysqli_fetch_assoc($res_info);
                
                // Calcular precio total
                $date1 = new DateTime($fecha_entrada);
                $date2 = new DateTime($fecha_salida);
                $diff = $date2->diff($date1)->format("%a");
                $noches = intval($diff);
                $precio_total = $noches * $info['precio'];
                
                echo "<h3>Detalles de la reserva:</h3>";
                echo "<ul>";
                echo "<li><b>Cliente:</b> {$info['nombre_usuario']}</li>";
                echo "<li><b>Inmueble:</b> {$info['nombre_inmueble']}</li>";
                echo "<li><b>Fecha de entrada:</b> $fecha_entrada</li>";
                echo "<li><b>Fecha de salida:</b> $fecha_salida</li>";
                echo "<li><b>Número de noches:</b> $noches</li>";
                echo "<li><b>Precio total:</b> " . number_format($precio_total, 2, ',', '.') . " €</li>";
                echo "</ul>";

                // Formulario para valorar la atención
                echo "<h3>Valora la atención recibida durante el proceso de reserva</h3>";
                $formulario_comentario = <<<FORM
                <form action="guardar_comentario.php" method="post">
                    <input type="hidden" name="id_reserva" value="$id_reserva_nueva">
                    <p>
                        Puntuación (0-10): 
                        <input type="number" name="puntuacion" min="0" max="10" step="0.1" required>
                    </p>
                    <p>
                        Comentario: <br>
                        <textarea name="comentario" rows="4" cols="50"></textarea>
                    </p>
                    <p><input type="submit" value="Enviar valoración"></p>
                </form>
FORM;
                print $formulario_comentario;


                // Listado de reservas anteriores del cliente
                echo "<h3>Historial de reservas del cliente:</h3>";
                $query_historial = "SELECT r.fecha_entrada, r.fecha_salida, i.nombre AS nombre_inmueble 
                                    FROM Reserva r
                                    JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
                                    WHERE r.ID_usuario = $id_usuario AND r.ID_Reserva != $id_reserva_nueva
                                    ORDER BY r.fecha_entrada DESC";
                $res_historial = mysqli_query($conex, $query_historial) or die(mysqli_error($conex));

                if (mysqli_num_rows($res_historial) > 0) {
                    echo "<table border='1' cellpadding='5' cellspacing='0'>";
                    echo "<tr><th>Inmueble</th><th>Fecha de Entrada</th><th>Fecha de Salida</th></tr>";
                    while ($reserva_anterior = mysqli_fetch_assoc($res_historial)) {
                        echo "<tr>";
                        echo "<td>{$reserva_anterior['nombre_inmueble']}</td>";
                        echo "<td>{$reserva_anterior['fecha_entrada']}</td>";
                        echo "<td>{$reserva_anterior['fecha_salida']}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Este cliente no tiene otras reservas registradas.</p>";
                }
            } else {
                echo "<p>Error al guardar la reserva en la base de datos.</p>";
                echo "<p><a href='javascript:history.back()'>Volver atrás</a></p>";
            }
        }
    }
    ?>
</body>
</html>
