<?php
/**
 * ARCHIVO: confirmacion_reserva.php
 * PROPÓSITO: Procesar y confirmar la reserva, mostrar resumen y formulario de valoración
 *
 * FLUJO:
 * 1. Validar datos recibidos (ID_usuario, ID_inmueble, fechas)
 * 2. Calcular precio total basado en número de noches
 * 3. Insertar reserva en la base de datos
 * 4. Mostrar confirmación con todos los detalles
 * 5. Mostrar historial de reservas anteriores del cliente
 * 6. Proporcionar formulario para valorar la atención recibida
 */

session_start();
include 'conexion.php';

// ===== VALIDACIÓN DE DATOS RECIBIDOS =====
$errores = array();

// Validar ID de usuario
if (!isset($_POST['ID_usuario']) || empty($_POST['ID_usuario'])) {
    $errores[] = "No se ha especificado el usuario.";
}
$ID_usuario = isset($_POST['ID_usuario']) ? intval($_POST['ID_usuario']) : 0;

// Validar ID de inmueble
if (!isset($_POST['ID_inmueble']) || empty($_POST['ID_inmueble'])) {
    $errores[] = "No se ha seleccionado ningún inmueble.";
}
$ID_inmueble = isset($_POST['ID_inmueble']) ? intval($_POST['ID_inmueble']) : 0;

// Validar fecha de entrada
if (!isset($_POST['fecha_entrada']) || empty($_POST['fecha_entrada'])) {
    $errores[] = "No se ha especificado la fecha de entrada.";
}
$fecha_entrada = isset($_POST['fecha_entrada']) ? $_POST['fecha_entrada'] : '';

// Validar fecha de salida
if (!isset($_POST['fecha_salida']) || empty($_POST['fecha_salida'])) {
    $errores[] = "No se ha especificado la fecha de salida.";
}
$fecha_salida = isset($_POST['fecha_salida']) ? $_POST['fecha_salida'] : '';

// Validaciones adicionales de fechas
$fecha_actual = date('Y-m-d');

if ($fecha_entrada < $fecha_actual) {
    $errores[] = "La fecha de entrada debe ser igual o posterior a hoy.";
}

if ($fecha_salida <= $fecha_entrada) {
    $errores[] = "La fecha de salida debe ser posterior a la fecha de entrada.";
}

// Si hay errores, mostrarlos y detener ejecución
if (count($errores) > 0) {
    echo "<h2>Errores de validación:</h2><ul>";
    foreach ($errores as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo '<a href="index.php">Volver al inicio</a>';
    exit();
}

// ===== OBTENER INFORMACIÓN DEL USUARIO =====
$query_usuario = "SELECT nombre, NIF, direccion, telefono
                  FROM Usuario
                  WHERE ID_Usuario = $ID_usuario";
$resultado_usuario = mysqli_query($conex, $query_usuario);
$usuario = mysqli_fetch_array($resultado_usuario);

// ===== OBTENER INFORMACIÓN DEL INMUEBLE =====
$query_inmueble = "SELECT nombre, direccion, num_habitaciones, precio
                   FROM Inmueble
                   WHERE ID_Inmueble = $ID_inmueble";
$resultado_inmueble = mysqli_query($conex, $query_inmueble);
$inmueble = mysqli_fetch_array($resultado_inmueble);

// ===== CALCULAR PRECIO TOTAL =====
$fecha_entrada_obj = new DateTime($fecha_entrada);
$fecha_salida_obj = new DateTime($fecha_salida);
$diferencia = $fecha_entrada_obj->diff($fecha_salida_obj);
$num_noches = $diferencia->days;
$precio_noche = $inmueble['precio'];
$precio_total = $num_noches * $precio_noche;

// ===== INSERTAR RESERVA EN LA BASE DE DATOS =====
$query_insert = "INSERT INTO Reserva (ID_inmueble, ID_usuario, fecha_entrada, fecha_salida)
                 VALUES ($ID_inmueble, $ID_usuario, '$fecha_entrada', '$fecha_salida')";

$resultado_insert = mysqli_query($conex, $query_insert);

if (!$resultado_insert) {
    die("Error al insertar la reserva: " . mysqli_error($conex));
}

// Obtener el ID de la reserva recién creada
$ID_reserva_nueva = mysqli_insert_id($conex);

// Guardar ID de reserva en sesión para el formulario de valoración
$_SESSION['ID_reserva_nueva'] = $ID_reserva_nueva;

// ===== OBTENER HISTORIAL DE RESERVAS DEL CLIENTE =====
$query_historial = "SELECT r.ID_Reserva, i.nombre as nombre_inmueble, r.fecha_entrada, r.fecha_salida,
                    DATEDIFF(r.fecha_salida, r.fecha_entrada) as noches,
                    i.precio * DATEDIFF(r.fecha_salida, r.fecha_entrada) as precio_total
                    FROM Reserva r
                    INNER JOIN Inmueble i ON r.ID_inmueble = i.ID_Inmueble
                    WHERE r.ID_usuario = $ID_usuario AND r.ID_Reserva != $ID_reserva_nueva
                    ORDER BY r.fecha_entrada DESC";
$resultado_historial = mysqli_query($conex, $query_historial);
$num_reservas_anteriores = mysqli_num_rows($resultado_historial);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reserva</title>
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

        .reservation-details {
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
            border-bottom: 2px solid #667eea;
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

        .price-total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
        }

        .price-total h2 {
            font-size: 28px;
            margin: 0;
        }

        h2 {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
            font-size: 22px;
            border-left: 4px solid #667eea;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .no-reservations {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        .rating-form {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .rating-form h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="number"], textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
        }

        input[type="number"]:focus, textarea:focus {
            outline: none;
            border-color: #ffc107;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
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
            background: #ffc107;
            color: #333;
        }

        .btn-primary:hover {
            background: #ffb300;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #667eea;
            color: white;
        }

        .btn-secondary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        @media print {
            .rating-form, .btn-container {
                display: none;
            }
        }
    </style>
    <script>
        function validarValoracion() {
            var puntuacion = document.getElementById('puntuacion').value;

            if (puntuacion === '') {
                alert('Por favor, ingrese una puntuación.');
                return false;
            }

            var punt = parseFloat(puntuacion);
            if (punt < 0 || punt > 10) {
                alert('La puntuación debe estar entre 0 y 10.');
                return false;
            }

            return confirm('¿Está seguro de enviar esta valoración?');
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>¡Reserva Confirmada!</h1>
        <p class="subtitle">Su reserva ha sido registrada exitosamente en el sistema</p>

        <div class="reservation-details">
            <div class="detail-section">
                <h3>👤 Datos del Cliente</h3>
                <div class="detail-row">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value"><?php echo $usuario['nombre']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">NIF:</span>
                    <span class="detail-value"><?php echo $usuario['NIF']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value"><?php echo $usuario['telefono']; ?></span>
                </div>
            </div>

            <div class="detail-section">
                <h3>🏠 Datos del Inmueble</h3>
                <div class="detail-row">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value"><?php echo $inmueble['nombre']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Dirección:</span>
                    <span class="detail-value"><?php echo $inmueble['direccion']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Habitaciones:</span>
                    <span class="detail-value"><?php echo $inmueble['num_habitaciones']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Precio por noche:</span>
                    <span class="detail-value"><?php echo number_format($precio_noche, 2); ?>€</span>
                </div>
            </div>

            <div class="detail-section">
                <h3>📅 Detalles de la Reserva</h3>
                <div class="detail-row">
                    <span class="detail-label">Nº de Reserva:</span>
                    <span class="detail-value">#<?php echo $ID_reserva_nueva; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha de entrada:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($fecha_entrada)); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha de salida:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($fecha_salida)); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Número de noches:</span>
                    <span class="detail-value"><?php echo $num_noches; ?></span>
                </div>
            </div>

            <div class="price-total">
                <h2>PRECIO TOTAL: <?php echo number_format($precio_total, 2); ?>€</h2>
            </div>
        </div>

        <h2>📋 Historial de Reservas del Cliente</h2>
        <?php if ($num_reservas_anteriores > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nº Reserva</th>
                        <th>Inmueble</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Noches</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reserva = mysqli_fetch_array($resultado_historial)): ?>
                        <tr>
                            <td>#<?php echo $reserva['ID_Reserva']; ?></td>
                            <td><?php echo $reserva['nombre_inmueble']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reserva['fecha_entrada'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reserva['fecha_salida'])); ?></td>
                            <td><?php echo $reserva['noches']; ?></td>
                            <td><?php echo number_format($reserva['precio_total'], 2); ?>€</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-reservations">
                <p>Esta es la primera reserva del cliente. ¡Bienvenido!</p>
            </div>
        <?php endif; ?>

        <div class="rating-form">
            <h3>⭐ Valorar la atención recibida</h3>
            <p style="margin-bottom: 20px; color: #666;">Ayúdenos a mejorar nuestro servicio valorando la atención recibida durante el proceso de reserva.</p>

            <form action="procesar_valoracion.php" method="POST" onsubmit="return validarValoracion()">
                <input type="hidden" name="ID_reserva" value="<?php echo $ID_reserva_nueva; ?>">

                <div class="form-group">
                    <label for="puntuacion">
                        Puntuación (0-10): <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="number" name="puntuacion" id="puntuacion"
                           min="0" max="10" step="0.1" required
                           placeholder="Ej: 8.5">
                </div>

                <div class="form-group">
                    <label for="comentario">
                        Comentario (opcional):
                    </label>
                    <textarea name="comentario" id="comentario"
                              placeholder="Cuéntenos su experiencia..." maxlength="200"></textarea>
                    <small style="color: #666;">Máximo 200 caracteres</small>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn-primary">Enviar Valoración</button>
                </div>
            </form>
        </div>

        <div class="btn-container" style="margin-top: 30px;">
            <a href="index.php" class="btn btn-secondary">🏠 Nueva Reserva</a>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir Confirmación</button>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conex);
?>
