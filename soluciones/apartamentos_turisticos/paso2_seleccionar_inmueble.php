<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Inmueble y Fechas - Apartamentos Turísticos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
            border-bottom: 3px solid #2196F3;
            padding-bottom: 10px;
        }
        .info-cliente {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        select, input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn {
            background-color: #2196F3;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #1976D2;
        }
        .btn-secondary {
            background-color: #757575;
        }
        .btn-secondary:hover {
            background-color: #616161;
        }
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        small {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏠 Paso 2: Seleccionar Inmueble y Fechas</h1>

        <?php
        // Incluir configuración
        require_once 'config.php';

        // Obtener ID del usuario desde GET
        $id_usuario = isset($_GET["id_usuario"]) ? $_GET["id_usuario"] : (isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : "");
        
        // Variables del formulario
        $id_inmueble = isset($_POST["id_inmueble"]) ? $_POST["id_inmueble"] : "";
        $fecha_entrada = isset($_POST["fecha_entrada"]) ? $_POST["fecha_entrada"] : "";
        $fecha_salida = isset($_POST["fecha_salida"]) ? $_POST["fecha_salida"] : "";
        $errores = "";

        /**
         * Función para mostrar información del cliente
         */
        function mostrar_info_cliente($id_usuario, $conex) {
            $query = "SELECT nombre, NIF, telefono, direccion FROM Usuario WHERE ID_Usuario = " . intval($id_usuario);
            $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
            
            if ($fila = mysqli_fetch_assoc($resultado)) {
                echo '<div class="info-cliente">';
                echo '<h3>👤 Cliente Seleccionado:</h3>';
                echo '<p><strong>Nombre:</strong> ' . htmlspecialchars($fila['nombre']) . '</p>';
                echo '<p><strong>NIF:</strong> ' . htmlspecialchars($fila['NIF']) . '</p>';
                echo '<p><strong>Teléfono:</strong> ' . htmlspecialchars($fila['telefono']) . '</p>';
                echo '<p><strong>Dirección:</strong> ' . htmlspecialchars($fila['direccion']) . '</p>';
                echo '</div>';
            }
        }

        /**
         * Función para mostrar el formulario de selección de inmueble y fechas
         */
        function mostrar_formulario_inmueble($id_usuario, $id_inmueble, $fecha_entrada, $fecha_salida, $conex) {
            echo '<form action="paso2_seleccionar_inmueble.php" method="post">';
            echo '<input type="hidden" name="id_usuario" value="' . $id_usuario . '">';
            
            // Selector de inmueble
            echo '<div class="form-group">';
            echo '<label for="id_inmueble">Inmueble:</label>';
            echo '<select name="id_inmueble" id="id_inmueble" required>';
            echo '<option value="">-- Seleccione un inmueble --</option>';
            
            // Consulta para obtener todos los inmuebles
            $query = "SELECT ID_Inmueble, nombre, direccion, num_habitaciones, precio 
                      FROM Inmueble 
                      ORDER BY nombre ASC";
            $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
            
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $selected = ($id_inmueble == $fila['ID_Inmueble']) ? 'selected' : '';
                echo '<option value="' . $fila['ID_Inmueble'] . '" ' . $selected . '>';
                echo htmlspecialchars($fila['nombre']) . ' (' . $fila['ID_Inmueble'] . ') - ';
                echo $fila['num_habitaciones'] . ' hab. - ' . number_format($fila['precio'], 2) . '€/noche';
                echo '</option>';
            }
            
            echo '</select>';
            echo '</div>';
            
            // Fecha de entrada
            echo '<div class="form-group">';
            echo '<label for="fecha_entrada">Fecha de Entrada:</label>';
            echo '<input type="date" name="fecha_entrada" id="fecha_entrada" value="' . $fecha_entrada . '" required>';
            echo '<small>Debe ser posterior a la fecha actual</small>';
            echo '</div>';
            
            // Fecha de salida
            echo '<div class="form-group">';
            echo '<label for="fecha_salida">Fecha de Salida:</label>';
            echo '<input type="date" name="fecha_salida" id="fecha_salida" value="' . $fecha_salida . '" required>';
            echo '<small>Debe ser posterior a la fecha de entrada</small>';
            echo '</div>';
            
            echo '<button type="submit" class="btn">Continuar ➜</button>';
            echo '<a href="paso1_seleccionar_cliente.php" class="btn btn-secondary">⬅ Volver</a>';
            echo '</form>';
        }

        /**
         * Función para validar inmueble y fechas
         */
        function validar_reserva(&$id_usuario, &$id_inmueble, &$fecha_entrada, &$fecha_salida, &$errores, $conex) {
            $flag = true;
            
            // Validar usuario
            if ($id_usuario == "" || !is_numeric($id_usuario)) {
                $errores .= "Usuario inválido. ";
                $flag = false;
            }
            
            // Validar inmueble
            if ($id_inmueble == "" || !is_numeric($id_inmueble)) {
                $errores .= "Debe seleccionar un inmueble válido. ";
                $id_inmueble = "";
                $flag = false;
            } else {
                // Verificar que el inmueble existe
                $query = "SELECT ID_Inmueble FROM Inmueble WHERE ID_Inmueble = " . intval($id_inmueble);
                $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
                
                if (mysqli_num_rows($resultado) == 0) {
                    $errores .= "El inmueble seleccionado no existe. ";
                    $id_inmueble = "";
                    $flag = false;
                }
            }
            
            // Validar fecha de entrada
            if ($fecha_entrada == "") {
                $errores .= "Debe introducir la fecha de entrada. ";
                $flag = false;
            } else {
                // Verificar formato de fecha
                $partes = explode('-', $fecha_entrada);
                if (count($partes) != 3 || !checkdate($partes[1], $partes[2], $partes[0])) {
                    $errores .= "Fecha de entrada inválida. ";
                    $fecha_entrada = "";
                    $flag = false;
                } else {
                    // Verificar que sea posterior a hoy
                    $hoy = date('Y-m-d');
                    if ($fecha_entrada <= $hoy) {
                        $errores .= "La fecha de entrada debe ser posterior a la fecha actual. ";
                        $fecha_entrada = "";
                        $flag = false;
                    }
                }
            }
            
            // Validar fecha de salida
            if ($fecha_salida == "") {
                $errores .= "Debe introducir la fecha de salida. ";
                $flag = false;
            } else {
                // Verificar formato de fecha
                $partes = explode('-', $fecha_salida);
                if (count($partes) != 3 || !checkdate($partes[1], $partes[2], $partes[0])) {
                    $errores .= "Fecha de salida inválida. ";
                    $fecha_salida = "";
                    $flag = false;
                } else {
                    // Verificar que sea posterior a la fecha de entrada
                    if ($fecha_entrada != "" && $fecha_salida <= $fecha_entrada) {
                        $errores .= "La fecha de salida debe ser posterior a la fecha de entrada. ";
                        $fecha_salida = "";
                        $flag = false;
                    }
                }
            }
            
            return $flag;
        }

        // Conectar a la base de datos
        $conex = conectar_bd();

        // Verificar que tenemos un ID de usuario válido
        if ($id_usuario == "" || !is_numeric($id_usuario)) {
            echo '<div class="error">❌ Error: No se ha seleccionado un cliente válido.</div>';
            echo '<a href="paso1_seleccionar_cliente.php" class="btn">⬅ Volver al Paso 1</a>';
            mysqli_close($conex);
            exit();
        }

        // Mostrar información del cliente
        mostrar_info_cliente($id_usuario, $conex);

        // Procesar formulario
        if (empty($_POST)) {
            // Mostrar formulario inicial
            mostrar_formulario_inmueble($id_usuario, $id_inmueble, $fecha_entrada, $fecha_salida, $conex);
        } else {
            // Validar datos
            if (validar_reserva($id_usuario, $id_inmueble, $fecha_entrada, $fecha_salida, $errores, $conex)) {
                // Calcular precio total
                $query = "SELECT precio FROM Inmueble WHERE ID_Inmueble = " . intval($id_inmueble);
                $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
                $fila = mysqli_fetch_assoc($resultado);
                $precio_noche = $fila['precio'];
                
                // Calcular número de noches
                $fecha1 = new DateTime($fecha_entrada);
                $fecha2 = new DateTime($fecha_salida);
                $diferencia = $fecha1->diff($fecha2);
                $num_noches = $diferencia->days;
                $precio_total = $precio_noche * $num_noches;
                
                // Insertar reserva en la base de datos
                $query_insert = "INSERT INTO Reserva (ID_inmueble, ID_usuario, fecha_entrada, fecha_salida, precio_total) 
                                VALUES (" . intval($id_inmueble) . ", " . intval($id_usuario) . ", 
                                        '" . mysqli_real_escape_string($conex, $fecha_entrada) . "', 
                                        '" . mysqli_real_escape_string($conex, $fecha_salida) . "', 
                                        " . $precio_total . ")";
                
                $resultado_insert = mysqli_query($conex, $query_insert) or die("Error al insertar reserva: " . mysqli_error($conex));
                
                if ($resultado_insert) {
                    // Obtener el ID de la reserva recién creada
                    $id_reserva = mysqli_insert_id($conex);
                    
                    // Redirigir al paso 3 con el ID de la reserva
                    header("Location: paso3_confirmacion.php?id_reserva=" . $id_reserva);
                    exit();
                } else {
                    echo '<div class="error">❌ Error al guardar la reserva en la base de datos.</div>';
                    mostrar_formulario_inmueble($id_usuario, $id_inmueble, $fecha_entrada, $fecha_salida, $conex);
                }
            } else {
                // Mostrar errores y volver a mostrar el formulario
                echo '<div class="error">❌ ' . htmlspecialchars($errores) . '</div>';
                mostrar_formulario_inmueble($id_usuario, $id_inmueble, $fecha_entrada, $fecha_salida, $conex);
            }
        }

        // Cerrar conexión
        mysqli_close($conex);
        ?>
    </div>
</body>
</html>
