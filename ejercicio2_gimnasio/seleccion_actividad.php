<?php
/**
 * ARCHIVO: seleccion_actividad.php
 * PROPÓSITO: Selección de actividad, monitor y fecha de inicio
 *
 * FLUJO:
 * 1. Verificar que se tiene socioID en sesión
 * 2. Mostrar solo actividades activas (fechaFin >= CURDATE())
 * 3. Mostrar lista de monitores
 * 4. Validar fecha de inscripción (debe ser >= hoy)
 * 5. Procesar inscripción
 */

session_start();
include 'conexion.php';

// Verificar que existe el socioID en sesión
if (!isset($_SESSION['socioID'])) {
    header("Location: index.php");
    exit();
}

$socioID = $_SESSION['socioID'];

// Obtener datos del socio
$query_socio = "SELECT nombre, nif FROM Socio WHERE socioID = $socioID";
$resultado_socio = mysqli_query($conex, $query_socio);
$datos_socio = mysqli_fetch_array($resultado_socio);

// Obtener fecha actual
$fecha_actual = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Actividad - Gimnasio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            border-color: #f5576c;
            background-color: white;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button, .btn {
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.4);
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
    </style>
    <script>
        function validarFormulario() {
            var actividad = document.getElementById('actividad').value;
            var monitor = document.getElementById('monitor').value;
            var fechaInicio = document.getElementById('fecha_inicio').value;

            if (!actividad) {
                alert('Por favor, seleccione una actividad.');
                return false;
            }

            if (!monitor) {
                alert('Por favor, seleccione un monitor.');
                return false;
            }

            if (!fechaInicio) {
                alert('Por favor, seleccione la fecha de inicio.');
                return false;
            }

            // Validar que la fecha sea igual o posterior a hoy
            var hoy = new Date().toISOString().split('T')[0];
            if (fechaInicio < hoy) {
                alert('La fecha de inicio debe ser igual o posterior a hoy.');
                return false;
            }

            return true;
        }

        window.onload = function() {
            // Establecer fecha mínima como hoy
            var hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fecha_inicio').min = hoy;
        };
    </script>
</head>
<body>
    <div class="container">
        <h1>🏋️ Inscripción en Actividad</h1>
        <p class="subtitle">Paso 2 de 2: Seleccione la actividad y monitor</p>

        <div class="user-info">
            <h3>👤 Socio seleccionado:</h3>
            <p><strong><?php echo $datos_socio['nombre']; ?></strong> (NIF: <?php echo $datos_socio['nif']; ?>)</p>
        </div>

        <div class="info-box">
            <p>
                <strong>ℹ️ Información importante:</strong><br>
                • Se mostrarán solo las actividades activas<br>
                • La fecha de inicio debe ser igual o posterior a hoy<br>
                • El precio mensual se generará automáticamente (100€ - 1000€)<br>
                • La duración de la inscripción es de un año
            </p>
        </div>

        <form action="confirmacion_inscripcion.php" method="POST" onsubmit="return validarFormulario()">
            <input type="hidden" name="socioID" value="<?php echo $socioID; ?>">

            <div class="form-group">
                <label for="actividad">
                    <span class="required">*</span> Actividad:
                </label>
                <select name="actividadID" id="actividad" required>
                    <option value="">-- Seleccione una actividad --</option>
                    <?php
                    // Obtener solo actividades activas (fechaFin >= CURDATE())
                    $query_actividades = "SELECT actividadID, nombre, fechaFin
                                         FROM Actividad
                                         WHERE fechaFin >= CURDATE()
                                         ORDER BY nombre";
                    $resultado_actividades = mysqli_query($conex, $query_actividades);

                    while ($actividad = mysqli_fetch_array($resultado_actividades)) {
                        echo '<option value="' . $actividad['actividadID'] . '">';
                        echo $actividad['nombre'];
                        echo ' (' . date('d/m/Y', strtotime($actividad['fechaFin'])) . ')';
                        echo '</option>';
                    }
                    ?>
                </select>
                <small style="color: #666; font-size: 12px;">Se muestra la fecha de finalización entre paréntesis</small>
            </div>

            <div class="form-group">
                <label for="monitor">
                    <span class="required">*</span> Monitor:
                </label>
                <select name="monitorID" id="monitor" required>
                    <option value="">-- Seleccione un monitor --</option>
                    <?php
                    // Obtener todos los monitores
                    $query_monitores = "SELECT monitorID, descripcion FROM Monitor ORDER BY descripcion";
                    $resultado_monitores = mysqli_query($conex, $query_monitores);

                    while ($monitor = mysqli_fetch_array($resultado_monitores)) {
                        echo '<option value="' . $monitor['monitorID'] . '">';
                        echo $monitor['descripcion'];
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_inicio">
                    <span class="required">*</span> Fecha de inicio de inscripción:
                </label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" required>
                <small style="color: #666; font-size: 12px;">Debe ser igual o posterior a hoy</small>
            </div>

            <div class="btn-container">
                <a href="index.php" class="btn btn-secondary">← Volver</a>
                <button type="submit" class="btn-primary">Confirmar Inscripción →</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
mysqli_close($conex);
?>
