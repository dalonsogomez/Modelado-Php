<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reserva - Apartamentos Turísticos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 8px;
        }
        .confirmacion {
            background-color: #e8f5e9;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .info-reserva {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .info-reserva p {
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th {
            background-color: #2196F3;
            color: white;
            padding: 12px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .form-valoracion {
            background-color: #fff3e0;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #FF9800;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        .btn {
            background-color: #FF9800;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #F57C00;
        }
        .btn-primary {
            background-color: #4CAF50;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .success {
            color: #2e7d32;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .precio-destacado {
            font-size: 24px;
            color: #4CAF50;
            font-weight: bold;
        }
        small {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Paso 3: Confirmación de Reserva</h1>

        <?php
        // Incluir configuración
        require_once 'config.php';

        // Obtener ID de la reserva desde GET o POST
        $id_reserva = isset($_GET["id_reserva"]) ? $_GET["id_reserva"] : (isset($_POST["id_reserva"]) ? $_POST["id_reserva"] : "");
        
        // Variables del formulario de valoración
        $puntuacion = isset($_POST["puntuacion"]) ? $_POST["puntuacion"] : "";
        $comentario = isset($_POST["comentario"]) ? $_POST["comentario"] : "";
        $errores = "";

        /**
         * Función para mostrar información completa de la reserva
         */
        function mostrar_info_reserva($id_reserva, $conex) {
            $query = "SELECT 
                        r.ID_Reserva,
                        r.fecha_entrada,
                        r.fecha_salida,
                        r.precio_total,
                        DATEDIFF(r.fecha_salida, r.fecha_entrada) AS num_noches,
                        u.nombre AS nombre_usuario,
                        u.NIF,
                        u.telefono,
                        u.direccion AS direccion_usuario,
                        i.nombre AS nombre_inmueble,
                        i.direccion AS direccion_inmueble,
                        i.num_habitaciones,
                        i.precio AS precio_noche
                      FROM Reserva r
                      INNER JOIN Usuario u ON r.ID_usuario = u.ID_Usuario
                      INNER JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
                      WHERE r.ID_Reserva = " . intval($id_reserva);
            
            $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
            
            if ($fila = mysqli_fetch_assoc($resultado)) {
                echo '<div class="confirmacion">';
                echo '<h2>🎉 ¡Reserva Confirmada!</h2>';
                echo '<p><strong>Número de Reserva:</strong> #' . $fila['ID_Reserva'] . '</p>';
                echo '</div>';
                
                echo '<div class="info-reserva">';
                echo '<h3>👤 Información del Cliente</h3>';
                echo '<p><strong>Nombre:</strong> ' . htmlspecialchars($fila['nombre_usuario']) . '</p>';
                echo '<p><strong>NIF:</strong> ' . htmlspecialchars($fila['NIF']) . '</p>';
                echo '<p><strong>Teléfono:</strong> ' . htmlspecialchars($fila['telefono']) . '</p>';
                echo '<p><strong>Dirección:</strong> ' . htmlspecialchars($fila['direccion_usuario']) . '</p>';
                echo '</div>';
                
                echo '<div class="info-reserva">';
                echo '<h3>🏠 Información del Inmueble</h3>';
                echo '<p><strong>Nombre:</strong> ' . htmlspecialchars($fila['nombre_inmueble']) . '</p>';
                echo '<p><strong>Dirección:</strong> ' . htmlspecialchars($fila['direccion_inmueble']) . '</p>';
                echo '<p><strong>Habitaciones:</strong> ' . $fila['num_habitaciones'] . '</p>';
                echo '<p><strong>Precio por noche:</strong> ' . number_format($fila['precio_noche'], 2) . '€</p>';
                echo '</div>';
                
                echo '<div class="info-reserva">';
                echo '<h3>📅 Detalles de la Reserva</h3>';
                echo '<p><strong>Fecha de Entrada:</strong> ' . date('d/m/Y', strtotime($fila['fecha_entrada'])) . '</p>';
                echo '<p><strong>Fecha de Salida:</strong> ' . date('d/m/Y', strtotime($fila['fecha_salida'])) . '</p>';
                echo '<p><strong>Número de Noches:</strong> ' . $fila['num_noches'] . '</p>';
                echo '<p><strong>Precio Total:</strong> <span class="precio-destacado">' . number_format($fila['precio_total'], 2) . '€</span></p>';
                echo '</div>';
                
                return $fila['NIF']; // Devolver NIF para consultar reservas anteriores
            } else {
                echo '<div class="error">❌ No se encontró la reserva especificada.</div>';
                return null;
            }
        }

        /**
         * Función para mostrar reservas anteriores del cliente
         */
        function mostrar_reservas_anteriores($nif_cliente, $id_reserva_actual, $conex) {
            $query = "SELECT 
                        r.ID_Reserva,
                        r.fecha_entrada,
                        r.fecha_salida,
                        r.precio_total,
                        DATEDIFF(r.fecha_salida, r.fecha_entrada) AS num_noches,
                        i.nombre AS nombre_inmueble,
                        i.direccion AS direccion_inmueble
                      FROM Reserva r
                      INNER JOIN Usuario u ON r.ID_usuario = u.ID_Usuario
                      INNER JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
                      WHERE u.NIF = '" . mysqli_real_escape_string($conex, $nif_cliente) . "'
                      AND r.ID_Reserva != " . intval($id_reserva_actual) . "
                      ORDER BY r.fecha_entrada DESC";
            
            $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
            
            echo '<h2>📋 Reservas Anteriores del Cliente</h2>';
            
            if (mysqli_num_rows($resultado) > 0) {
                echo '<table>';
                echo '<tr>';
                echo '<th>Nº Reserva</th>';
                echo '<th>Inmueble</th>';
                echo '<th>Fecha Entrada</th>';
                echo '<th>Fecha Salida</th>';
                echo '<th>Noches</th>';
                echo '<th>Precio Total</th>';
                echo '</tr>';
                
                while ($fila = mysqli_fetch_assoc($resultado)) {
                    echo '<tr>';
                    echo '<td>#' . $fila['ID_Reserva'] . '</td>';
                    echo '<td>' . htmlspecialchars($fila['nombre_inmueble']) . '<br><small>' . htmlspecialchars($fila['direccion_inmueble']) . '</small></td>';
                    echo '<td>' . date('d/m/Y', strtotime($fila['fecha_entrada'])) . '</td>';
                    echo '<td>' . date('d/m/Y', strtotime($fila['fecha_salida'])) . '</td>';
                    echo '<td>' . $fila['num_noches'] . '</td>';
                    echo '<td>' . number_format($fila['precio_total'], 2) . '€</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<p>Este cliente no tiene reservas anteriores.</p>';
            }
        }

        /**
         * Función para mostrar formulario de valoración
         */
        function mostrar_formulario_valoracion($id_reserva, $puntuacion, $comentario) {
            echo '<div class="form-valoracion">';
            echo '<h2>⭐ Valorar la Atención Recibida</h2>';
            echo '<p>Por favor, valore la atención recibida durante el proceso de reserva:</p>';
            
            echo '<form action="paso3_confirmacion.php" method="post">';
            echo '<input type="hidden" name="id_reserva" value="' . $id_reserva . '">';
            
            echo '<div class="form-group">';
            echo '<label for="puntuacion">Puntuación (0-10):</label>';
            echo '<input type="number" name="puntuacion" id="puntuacion" min="0" max="10" step="0.1" value="' . $puntuacion . '" required>';
            echo '<small>Introduzca una puntuación entre 0 y 10</small>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="comentario">Comentario:</label>';
            echo '<textarea name="comentario" id="comentario" maxlength="200" required>' . htmlspecialchars($comentario) . '</textarea>';
            echo '<small>Máximo 200 caracteres</small>';
            echo '</div>';
            
            echo '<button type="submit" class="btn">Enviar Valoración</button>';
            echo '</form>';
            echo '</div>';
        }

        /**
         * Función para validar y guardar la valoración
         */
        function validar_guardar_valoracion($id_reserva, &$puntuacion, &$comentario, &$errores, $conex) {
            $flag = true;
            
            // Validar puntuación
            if ($puntuacion === "" || !is_numeric($puntuacion)) {
                $errores .= "Debe introducir una puntuación válida. ";
                $puntuacion = "";
                $flag = false;
            } else {
                $puntuacion = floatval($puntuacion);
                if ($puntuacion < 0 || $puntuacion > 10) {
                    $errores .= "La puntuación debe estar entre 0 y 10. ";
                    $puntuacion = "";
                    $flag = false;
                }
            }
            
            // Validar comentario
            if ($comentario == "") {
                $errores .= "Debe introducir un comentario. ";
                $flag = false;
            } else {
                if (strlen($comentario) > 200) {
                    $errores .= "El comentario no puede superar los 200 caracteres. ";
                    $comentario = substr($comentario, 0, 200);
                    $flag = false;
                }
                $comentario = mysqli_real_escape_string($conex, $comentario);
            }
            
            // Si todo es válido, guardar en la base de datos
            if ($flag) {
                $query = "INSERT INTO Comentario (ID_reserva, puntuacion, comentario) 
                          VALUES (" . intval($id_reserva) . ", " . $puntuacion . ", '" . $comentario . "')";
                
                $resultado = mysqli_query($conex, $query) or die("Error al guardar comentario: " . mysqli_error($conex));
                
                if (!$resultado) {
                    $errores .= "Error al guardar la valoración en la base de datos. ";
                    $flag = false;
                }
            }
            
            return $flag;
        }

        // Conectar a la base de datos
        $conex = conectar_bd();

        // Verificar que tenemos un ID de reserva válido
        if ($id_reserva == "" || !is_numeric($id_reserva)) {
            echo '<div class="error">❌ Error: No se ha especificado una reserva válida.</div>';
            echo '<a href="paso1_seleccionar_cliente.php" class="btn btn-primary">Nueva Reserva</a>';
            mysqli_close($conex);
            exit();
        }

        // Mostrar información de la reserva
        $nif_cliente = mostrar_info_reserva($id_reserva, $conex);

        if ($nif_cliente !== null) {
            // Mostrar reservas anteriores
            mostrar_reservas_anteriores($nif_cliente, $id_reserva, $conex);
            
            // Verificar si ya existe una valoración para esta reserva
            $query_check = "SELECT ID_resena FROM Comentario WHERE ID_reserva = " . intval($id_reserva);
            $resultado_check = mysqli_query($conex, $query_check);
            
            if (mysqli_num_rows($resultado_check) > 0) {
                // Ya existe una valoración
                echo '<div class="success">✅ Ya ha valorado esta reserva. ¡Gracias por su opinión!</div>';
            } else {
                // Procesar formulario de valoración
                if (isset($_POST["puntuacion"]) && isset($_POST["comentario"])) {
                    // Validar y guardar valoración
                    if (validar_guardar_valoracion($id_reserva, $puntuacion, $comentario, $errores, $conex)) {
                        echo '<div class="success">✅ ¡Gracias por su valoración! Su opinión ha sido registrada correctamente.</div>';
                    } else {
                        echo '<div class="error">❌ ' . htmlspecialchars($errores) . '</div>';
                        mostrar_formulario_valoracion($id_reserva, $puntuacion, $comentario);
                    }
                } else {
                    // Mostrar formulario de valoración
                    mostrar_formulario_valoracion($id_reserva, $puntuacion, $comentario);
                }
            }
            
            echo '<br><br>';
            echo '<a href="paso1_seleccionar_cliente.php" class="btn btn-primary">🏠 Nueva Reserva</a>';
        }

        // Cerrar conexión
        mysqli_close($conex);
        ?>
    </div>
</body>
</html>
