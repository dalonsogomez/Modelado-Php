<?php
/**
 * ARCHIVO: confirmacion_inscripcion.php
 * PROPÓSITO: Procesar inscripción y mostrar confirmación con historial
 *
 * FLUJO:
 * 1. Validar datos recibidos
 * 2. Generar precio mensual aleatorio entre 100 y 1000 euros
 * 3. Insertar inscripción en la base de datos
 * 4. Mostrar confirmación con todos los datos
 * 5. Mostrar historial de inscripciones del socio
 */

session_start();
include 'conexion.php';

// ===== VALIDACIÓN DE DATOS =====
$errores = array();

$socioID = isset($_POST['socioID']) ? intval($_POST['socioID']) : 0;
$actividadID = isset($_POST['actividadID']) ? intval($_POST['actividadID']) : 0;
$monitorID = isset($_POST['monitorID']) ? intval($_POST['monitorID']) : 0;
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';

if ($socioID <= 0) {
    $errores[] = "No se ha especificado el socio.";
}

if ($actividadID <= 0) {
    $errores[] = "No se ha seleccionado ninguna actividad.";
}

if ($monitorID <= 0) {
    $errores[] = "No se ha seleccionado ningún monitor.";
}

if (empty($fecha_inicio)) {
    $errores[] = "No se ha especificado la fecha de inicio.";
}

// Validar que la fecha sea igual o posterior a hoy
$fecha_actual = date('Y-m-d');
if ($fecha_inicio < $fecha_actual) {
    $errores[] = "La fecha de inicio debe ser igual o posterior a hoy.";
}

if (count($errores) > 0) {
    echo "<h2>Errores de validación:</h2><ul>";
    foreach ($errores as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo '<a href="index.php">Volver al inicio</a>';
    exit();
}

// ===== OBTENER INFORMACIÓN DEL SOCIO =====
$query_socio = "SELECT nombre, nif, telefono, email FROM Socio WHERE socioID = $socioID";
$resultado_socio = mysqli_query($conex, $query_socio);
$socio = mysqli_fetch_array($resultado_socio);

// ===== OBTENER INFORMACIÓN DE LA ACTIVIDAD =====
$query_actividad = "SELECT nombre, descripcion, fechaInicio, fechaFin
                    FROM Actividad
                    WHERE actividadID = $actividadID";
$resultado_actividad = mysqli_query($conex, $query_actividad);
$actividad = mysqli_fetch_array($resultado_actividad);

// ===== OBTENER INFORMACIÓN DEL MONITOR =====
$query_monitor = "SELECT nombre, descripcion FROM Monitor WHERE monitorID = $monitorID";
$resultado_monitor = mysqli_query($conex, $query_monitor);
$monitor = mysqli_fetch_array($resultado_monitor);

// ===== GENERAR PRECIO MENSUAL ALEATORIO (entre 100 y 1000 euros) =====
$precio_mensual = rand(10000, 100000) / 100; // Genera entre 100.00 y 1000.00

// La fecha de inscripción es la fecha actual
$fecha_inscripcion = date('Y-m-d');

// ===== INSERTAR INSCRIPCIÓN EN LA BASE DE DATOS =====
$query_insert = "INSERT INTO Inscripciones (actividadID, socioID, monitorID, fechaInscripcion, precioMensual)
                 VALUES ($actividadID, $socioID, $monitorID, '$fecha_inscripcion', $precio_mensual)";

$resultado_insert = mysqli_query($conex, $query_insert);

if (!$resultado_insert) {
    die("Error al registrar la inscripción: " . mysqli_error($conex));
}

$inscripcionID = mysqli_insert_id($conex);

// ===== OBTENER HISTORIAL DE INSCRIPCIONES DEL SOCIO =====
$query_historial = "SELECT i.inscripcionID, a.nombre as actividad, m.descripcion as monitor,
                    i.fechaInscripcion, i.precioMensual
                    FROM Inscripciones i
                    INNER JOIN Actividad a ON i.actividadID = a.actividadID
                    INNER JOIN Monitor m ON i.monitorID = m.monitorID
                    WHERE i.socioID = $socioID AND i.inscripcionID != $inscripcionID
                    ORDER BY i.fechaInscripcion DESC";
$resultado_historial = mysqli_query($conex, $query_historial);
$num_inscripciones_anteriores = mysqli_num_rows($resultado_historial);

// Calcular precio anual (la duración es de un año)
$precio_anual = $precio_mensual * 12;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Inscripción - Gimnasio</title>
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
            max-width: 900px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }

        .success-icon {
            text-align: center;
            font-size: 60px;
            margin-bottom: 20px;
        }

        h1 {
            color: #27ae60;
            text-align: center;
            margin-bottom: 10px;
            font-size: 32px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .inscription-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .detail-section {
            margin-bottom: 25px;
        }

        .detail-section h3 {
            color: #333;
            margin-bottom: 12px;
            font-size: 18px;
            border-bottom: 2px solid #f5576c;
            padding-bottom: 8px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 600;
        }

        .detail-value {
            color: #333;
            text-align: right;
        }

        .price-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .price-total {
            font-size: 24px;
            font-weight: bold;
            border-top: 2px solid rgba(255,255,255,0.3);
            padding-top: 15px;
            margin-top: 10px;
        }

        h2 {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
            font-size: 22px;
            border-left: 4px solid #f5576c;
            padding-left: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            font-weight: 600;
            font-size: 14px;
        }

        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .no-inscriptions {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
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

        .info-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        @media print {
            .btn-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>¡Inscripción Confirmada!</h1>
        <p class="subtitle">Su inscripción ha sido registrada exitosamente</p>

        <div class="inscription-details">
            <div class="detail-section">
                <h3>👤 Datos del Socio</h3>
                <div class="detail-row">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value"><?php echo $socio['nombre']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">NIF:</span>
                    <span class="detail-value"><?php echo $socio['nif']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value"><?php echo $socio['telefono']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo $socio['email']; ?></span>
                </div>
            </div>

            <div class="detail-section">
                <h3>🏋️ Datos de la Actividad</h3>
                <div class="detail-row">
                    <span class="detail-label">Actividad:</span>
                    <span class="detail-value"><?php echo $actividad['nombre']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Descripción:</span>
                    <span class="detail-value"><?php echo $actividad['descripcion']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Período de actividad:</span>
                    <span class="detail-value">
                        <?php echo date('d/m/Y', strtotime($actividad['fechaInicio'])); ?> -
                        <?php echo date('d/m/Y', strtotime($actividad['fechaFin'])); ?>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h3>👨‍🏫 Datos del Monitor</h3>
                <div class="detail-row">
                    <span class="detail-label">Monitor asignado:</span>
                    <span class="detail-value"><?php echo $monitor['descripcion']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value"><?php echo $monitor['nombre']; ?></span>
                </div>
            </div>

            <div class="detail-section">
                <h3>📋 Detalles de la Inscripción</h3>
                <div class="detail-row">
                    <span class="detail-label">Nº de Inscripción:</span>
                    <span class="detail-value">#<?php echo $inscripcionID; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha de inscripción:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($fecha_inscripcion)); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha de inicio:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($fecha_inicio)); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duración:</span>
                    <span class="detail-value">
                        1 año <span class="info-badge">12 meses</span>
                    </span>
                </div>
            </div>

            <div class="price-box">
                <div class="price-row">
                    <span>Precio mensual:</span>
                    <span><?php echo number_format($precio_mensual, 2); ?>€</span>
                </div>
                <div class="price-row">
                    <span>Duración:</span>
                    <span>12 meses</span>
                </div>
                <div class="price-row price-total">
                    <span>PRECIO TOTAL ANUAL:</span>
                    <span><?php echo number_format($precio_anual, 2); ?>€</span>
                </div>
            </div>
        </div>

        <h2>📊 Historial de Inscripciones del Socio</h2>
        <?php if ($num_inscripciones_anteriores > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Actividad</th>
                        <th>Monitor</th>
                        <th>Fecha Inscripción</th>
                        <th>Precio Mensual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($inscripcion = mysqli_fetch_array($resultado_historial)): ?>
                        <tr>
                            <td>#<?php echo $inscripcion['inscripcionID']; ?></td>
                            <td><?php echo $inscripcion['actividad']; ?></td>
                            <td><?php echo $inscripcion['monitor']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($inscripcion['fechaInscripcion'])); ?></td>
                            <td><?php echo number_format($inscripcion['precioMensual'], 2); ?>€</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-inscriptions">
                <p>Esta es la primera inscripción del socio. ¡Bienvenido al gimnasio!</p>
            </div>
        <?php endif; ?>

        <div class="btn-container">
            <a href="index.php" class="btn btn-primary">🏋️ Nueva Inscripción</a>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir Confirmación</button>
        </div>
    </div>
</body>
</html>

<?php
// Limpiar la sesión
session_destroy();
mysqli_close($conex);
?>
