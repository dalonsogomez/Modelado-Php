<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Cliente - Apartamentos Turísticos</title>
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
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
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
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Paso 1: Seleccionar Cliente</h1>
        <p>Seleccione el cliente que desea realizar una reserva:</p>

        <?php
        // Incluir configuración
        require_once 'config.php';

        // Variables
        $id_usuario = isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : "";
        $errores = "";

        /**
         * Función para mostrar el formulario de selección de cliente
         */
        function mostrar_formulario_cliente($id_usuario, $conex) {
            echo '<form action="paso1_seleccionar_cliente.php" method="post">';
            echo '<div class="form-group">';
            echo '<label for="id_usuario">Cliente:</label>';
            echo '<select name="id_usuario" id="id_usuario" required>';
            echo '<option value="">-- Seleccione un cliente --</option>';
            
            // Consulta para obtener todos los usuarios
            $query = "SELECT ID_Usuario, nombre, NIF FROM Usuario ORDER BY nombre ASC";
            $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
            
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $selected = ($id_usuario == $fila['ID_Usuario']) ? 'selected' : '';
                echo '<option value="' . $fila['ID_Usuario'] . '" ' . $selected . '>';
                echo htmlspecialchars($fila['nombre']) . ' (NIF: ' . $fila['NIF'] . ')';
                echo '</option>';
            }
            
            echo '</select>';
            echo '</div>';
            echo '<button type="submit" class="btn">Continuar ➜</button>';
            echo '</form>';
        }

        /**
         * Función para validar el cliente seleccionado
         */
        function validar_cliente(&$id_usuario, &$errores, $conex) {
            $flag = true;
            
            // Validar que se haya seleccionado un cliente
            if ($id_usuario == "" || !is_numeric($id_usuario)) {
                $errores .= "Debe seleccionar un cliente válido. ";
                $id_usuario = "";
                $flag = false;
            } else {
                // Verificar que el cliente existe en la base de datos
                $query = "SELECT ID_Usuario FROM Usuario WHERE ID_Usuario = " . intval($id_usuario);
                $resultado = mysqli_query($conex, $query) or die("Error en consulta: " . mysqli_error($conex));
                
                if (mysqli_num_rows($resultado) == 0) {
                    $errores .= "El cliente seleccionado no existe. ";
                    $id_usuario = "";
                    $flag = false;
                }
            }
            
            return $flag;
        }

        // Conectar a la base de datos
        $conex = conectar_bd();

        // Procesar formulario
        if (empty($_POST)) {
            // Mostrar formulario inicial
            mostrar_formulario_cliente($id_usuario, $conex);
        } else {
            // Validar datos
            if (validar_cliente($id_usuario, $errores, $conex)) {
                // Redirigir al paso 2 con el ID del usuario
                header("Location: paso2_seleccionar_inmueble.php?id_usuario=" . $id_usuario);
                exit();
            } else {
                // Mostrar errores y volver a mostrar el formulario
                echo '<div class="error">❌ ' . htmlspecialchars($errores) . '</div>';
                mostrar_formulario_cliente($id_usuario, $conex);
            }
        }

        // Cerrar conexión
        mysqli_close($conex);
        ?>
    </div>
</body>
</html>
