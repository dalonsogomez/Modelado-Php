<?php
/**
 * ARCHIVO: procesar_valoracion.php
 * PROPÓSITO: Procesar y guardar la valoración del cliente sobre la atención recibida
 *
 * FLUJO:
 * 1. Recibir datos del formulario de valoración
 * 2. Validar puntuación (0-10) y comentario (máx 200 caracteres)
 * 3. Insertar comentario en la base de datos
 * 4. Mostrar confirmación
 */

include 'conexion.php';

// ===== VALIDACIÓN DE DATOS =====
$errores = array();

// Validar ID de reserva
if (!isset($_POST['ID_reserva']) || empty($_POST['ID_reserva'])) {
    $errores[] = "No se ha especificado el ID de la reserva.";
}
$ID_reserva = isset($_POST['ID_reserva']) ? intval($_POST['ID_reserva']) : 0;

// Validar puntuación
if (!isset($_POST['puntuacion']) || $_POST['puntuacion'] === '') {
    $errores[] = "Debe ingresar una puntuación.";
}
$puntuacion = isset($_POST['puntuacion']) ? floatval($_POST['puntuacion']) : 0;

if ($puntuacion < 0 || $puntuacion > 10) {
    $errores[] = "La puntuación debe estar entre 0 y 10.";
}

// Validar comentario (opcional, pero si existe debe tener máximo 200 caracteres)
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
if (strlen($comentario) > 200) {
    $errores[] = "El comentario no puede exceder los 200 caracteres.";
}

// Escapar caracteres especiales para evitar inyección SQL
$comentario = mysqli_real_escape_string($conex, $comentario);

// Si hay errores, mostrarlos
if (count($errores) > 0) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Error</title></head><body>";
    echo "<h2>Errores de validación:</h2><ul>";
    foreach ($errores as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo '<a href="index.php">Volver al inicio</a>';
    echo "</body></html>";
    exit();
}

// ===== INSERTAR COMENTARIO EN LA BASE DE DATOS =====
$query_insert = "INSERT INTO Comentario (ID_reserva, puntuacion, comentario)
                 VALUES ($ID_reserva, $puntuacion, '$comentario')";

$resultado = mysqli_query($conex, $query_insert);

if (!$resultado) {
    die("Error al guardar la valoración: " . mysqli_error($conex));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valoración Guardada</title>
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
            padding: 60px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-in-out;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }

        h1 {
            color: #27ae60;
            margin-bottom: 15px;
            font-size: 32px;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .rating-display {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .rating-score {
            font-size: 48px;
            color: #ffc107;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .rating-comment {
            color: #555;
            font-style: italic;
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
        }

        .btn {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            margin: 5px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">⭐</div>
        <h1>¡Gracias por su valoración!</h1>
        <p>Su opinión ha sido registrada exitosamente y nos ayudará a mejorar nuestros servicios.</p>

        <div class="rating-display">
            <div class="rating-score"><?php echo number_format($puntuacion, 1); ?>/10</div>
            <p style="color: #333; margin: 0;">Puntuación otorgada</p>

            <?php if (!empty($comentario)): ?>
                <div class="rating-comment">
                    <strong>Su comentario:</strong><br>
                    "<?php echo htmlspecialchars($comentario); ?>"
                </div>
            <?php endif; ?>
        </div>

        <a href="index.php" class="btn">🏠 Realizar Nueva Reserva</a>
    </div>
</body>
</html>

<?php
mysqli_close($conex);
?>
