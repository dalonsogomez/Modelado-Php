<?php
/**
 * ARCHIVO: seleccion_inmueble.php
 * PROPÓSITO: Pantalla de selección de inmueble y fechas de reserva
 *
 * FLUJO:
 * 1. Recibe ID_usuario del formulario anterior
 * 2. Muestra lista desplegable con todos los inmuebles disponibles
 * 3. Permite seleccionar fechas de entrada y salida
 * 4. Valida que las fechas sean correctas
 * 5. Redirige a confirmacion_reserva.php
 */

session_start();
include 'conexion.php';

// Validar que se recibió el ID de usuario
if (!isset($_POST['ID_usuario']) || empty($_POST['ID_usuario'])) {
    die("Error: No se ha seleccionado ningún usuario.");
}

$ID_usuario = $_POST['ID_usuario'];

// Guardar ID de usuario en sesión para uso posterior
$_SESSION['ID_usuario'] = $ID_usuario;

// Obtener datos del usuario seleccionado
$query_usuario = "SELECT nombre, NIF FROM Usuario WHERE ID_Usuario = $ID_usuario";
$resultado_usuario = mysqli_query($conex, $query_usuario);
$datos_usuario = mysqli_fetch_array($resultado_usuario);

// Obtener la fecha actual para validaciones
$fecha_actual = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Inmueble - Reservas</title>
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
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .user-info h3 {
            margin-bottom: 8px;
            font-size: 18px;
        }

        .user-info p {
            font-size: 14px;
            opacity: 0.9;
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

        select, input[type="date"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        select:focus, input[type="date"]:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
        }

        .date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }

        .info-box p {
            color: #856404;
            font-size: 13px;
            line-height: 1.5;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button, .btn-back {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .required {
            color: #e74c3c;
        }

        @media (max-width: 600px) {
            .date-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        // Validación del formulario en el cliente
        function validarFormulario() {
            var inmueble = document.getElementById('inmueble').value;
            var fechaEntrada = document.getElementById('fecha_entrada').value;
            var fechaSalida = document.getElementById('fecha_salida').value;

            if (!inmueble) {
                alert('Por favor, seleccione un inmueble.');
                return false;
            }

            if (!fechaEntrada || !fechaSalida) {
                alert('Por favor, seleccione las fechas de entrada y salida.');
                return false;
            }

            // Validar que fecha de entrada sea menor que fecha de salida
            if (fechaEntrada >= fechaSalida) {
                alert('La fecha de salida debe ser posterior a la fecha de entrada.');
                return false;
            }

            // Validar que las fechas sean futuras
            var hoy = new Date().toISOString().split('T')[0];
            if (fechaEntrada < hoy) {
                alert('La fecha de entrada debe ser igual o posterior a hoy.');
                return false;
            }

            return true;
        }

        // Establecer fecha mínima para los inputs de fecha
        window.onload = function() {
            var hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fecha_entrada').min = hoy;
            document.getElementById('fecha_salida').min = hoy;

            // Cuando cambia la fecha de entrada, actualizar el mínimo de la fecha de salida
            document.getElementById('fecha_entrada').addEventListener('change', function() {
                var fechaEntrada = this.value;
                if (fechaEntrada) {
                    // La fecha de salida debe ser al menos un día después de la entrada
                    var fechaMinSalida = new Date(fechaEntrada);
                    fechaMinSalida.setDate(fechaMinSalida.getDate() + 1);
                    document.getElementById('fecha_salida').min = fechaMinSalida.toISOString().split('T')[0];
                }
            });
        };
    </script>
</head>
<body>
    <div class="container">
        <h1>🏢 Selección de Inmueble</h1>
        <p class="subtitle">Paso 2 de 3: Elija el apartamento y las fechas</p>

        <div class="user-info">
            <h3>👤 Cliente seleccionado:</h3>
            <p><strong><?php echo $datos_usuario['nombre']; ?></strong> (NIF: <?php echo $datos_usuario['NIF']; ?>)</p>
        </div>

        <div class="info-box">
            <p>
                <strong>ℹ️ Instrucciones:</strong><br>
                • Seleccione el inmueble que desea reservar<br>
                • Las fechas deben ser iguales o posteriores a hoy<br>
                • La fecha de salida debe ser posterior a la fecha de entrada
            </p>
        </div>

        <form action="confirmacion_reserva.php" method="POST" onsubmit="return validarFormulario()">
            <!-- Campo oculto con el ID del usuario -->
            <input type="hidden" name="ID_usuario" value="<?php echo $ID_usuario; ?>">

            <div class="form-group">
                <label for="inmueble">
                    <span class="required">*</span> Inmueble:
                </label>
                <select name="ID_inmueble" id="inmueble" required>
                    <option value="">-- Seleccione un inmueble --</option>
                    <?php
                    // Obtener todos los inmuebles disponibles
                    $query_inmuebles = "SELECT ID_Inmueble, nombre, direccion, num_habitaciones, precio
                                        FROM Inmueble
                                        ORDER BY nombre";
                    $resultado_inmuebles = mysqli_query($conex, $query_inmuebles);

                    while ($inmueble = mysqli_fetch_array($resultado_inmuebles)) {
                        echo '<option value="' . $inmueble['ID_Inmueble'] . '">';
                        echo $inmueble['nombre'] . ' (' . $inmueble['ID_Inmueble'] . ') - ';
                        echo $inmueble['num_habitaciones'] . ' hab. - ';
                        echo number_format($inmueble['precio'], 2) . '€/noche';
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="date-grid">
                <div class="form-group">
                    <label for="fecha_entrada">
                        <span class="required">*</span> Fecha de entrada:
                    </label>
                    <input type="date" name="fecha_entrada" id="fecha_entrada" required>
                </div>

                <div class="form-group">
                    <label for="fecha_salida">
                        <span class="required">*</span> Fecha de salida:
                    </label>
                    <input type="date" name="fecha_salida" id="fecha_salida" required>
                </div>
            </div>

            <div class="btn-container">
                <a href="index.php" class="btn-secondary">← Volver</a>
                <button type="submit" class="btn-primary">Continuar →</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
mysqli_close($conex);
?>
