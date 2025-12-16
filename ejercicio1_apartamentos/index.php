<?php
/**
 * ARCHIVO: index.php
 * PROPÓSITO: Pantalla inicial - Selección de usuario para realizar reserva
 *
 * FLUJO:
 * 1. Muestra un formulario con lista desplegable de todos los usuarios
 * 2. Al seleccionar usuario, redirige a seleccion_inmueble.php
 */

// Incluir conexión a base de datos
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reservas - Apartamentos Turísticos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        select:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }

        .info-box p {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }

        .error {
            background: #fee;
            border-left-color: #e74c3c;
            color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏠 Sistema de Reservas</h1>
        <p class="subtitle">Apartamentos Turísticos</p>

        <div class="info-box">
            <p>Seleccione el usuario que desea realizar una reserva de apartamento.</p>
        </div>

        <?php
        // Consulta para obtener todos los usuarios
        $query_usuarios = "SELECT ID_Usuario, nombre, NIF FROM Usuario ORDER BY nombre";
        $resultado_usuarios = mysqli_query($conex, $query_usuarios) or die("Error en consulta: " . mysqli_error($conex));

        $num_usuarios = mysqli_num_rows($resultado_usuarios);

        if ($num_usuarios == 0) {
            echo '<div class="info-box error">';
            echo '<p><strong>⚠️ No hay usuarios registrados en el sistema.</strong></p>';
            echo '<p>Por favor, agregue usuarios a la base de datos antes de continuar.</p>';
            echo '</div>';
        }
        ?>

        <form action="seleccion_inmueble.php" method="POST">
            <div class="form-group">
                <label for="usuario">
                    <span style="color: #e74c3c;">*</span> Cliente:
                </label>
                <select name="ID_usuario" id="usuario" required <?php if($num_usuarios == 0) echo 'disabled'; ?>>
                    <option value="">-- Seleccione un cliente --</option>
                    <?php
                    // Generar opciones del select con todos los usuarios
                    while ($usuario = mysqli_fetch_array($resultado_usuarios)) {
                        echo '<option value="' . $usuario['ID_Usuario'] . '">';
                        echo $usuario['nombre'] . ' (' . $usuario['NIF'] . ')';
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn-primary" <?php if($num_usuarios == 0) echo 'disabled'; ?>>
                    Continuar →
                </button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Cerrar conexión
mysqli_close($conex);
?>
