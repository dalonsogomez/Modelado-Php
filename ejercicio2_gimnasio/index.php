<?php
/**
 * ARCHIVO: index.php
 * PROPÓSITO: Pantalla inicial - Identificación del socio
 *
 * FLUJO:
 * 1. Permite registrar un nuevo socio con formulario
 * 2. Permite seleccionar socio existente mediante desplegable
 * 3. Valida datos si es nuevo socio
 * 4. Redirige a seleccion_actividad.php
 */

session_start();
include 'conexion.php';

// Variable para controlar si mostrar formulario de registro o selección
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'seleccionar';

// Procesar formulario de registro de nuevo socio
if (isset($_POST['registrar_socio'])) {
    // Validar datos
    $errores = array();

    $nif = isset($_POST['nif']) ? trim($_POST['nif']) : '';
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Validaciones
    if (empty($nif)) {
        $errores[] = "El NIF es obligatorio.";
    } elseif (strlen($nif) != 9) {
        $errores[] = "El NIF debe tener 9 caracteres.";
    }

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }

    if (!empty($telefono) && strlen($telefono) != 9) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }

    // Verificar si el NIF ya existe
    if (empty($errores)) {
        $nif_escapado = mysqli_real_escape_string($conex, $nif);
        $query_check = "SELECT socioID FROM Socio WHERE nif = '$nif_escapado'";
        $result_check = mysqli_query($conex, $query_check);

        if (mysqli_num_rows($result_check) > 0) {
            $errores[] = "Ya existe un socio registrado con ese NIF.";
        }
    }

    // Si no hay errores, insertar nuevo socio
    if (empty($errores)) {
        $nombre_escapado = mysqli_real_escape_string($conex, $nombre);
        $telefono_escapado = mysqli_real_escape_string($conex, $telefono);
        $email_escapado = mysqli_real_escape_string($conex, $email);

        $query_insert = "INSERT INTO Socio (nif, nombre, telefono, email)
                         VALUES ('$nif_escapado', '$nombre_escapado', '$telefono_escapado', '$email_escapado')";

        if (mysqli_query($conex, $query_insert)) {
            $nuevo_socio_id = mysqli_insert_id($conex);
            $_SESSION['socioID'] = $nuevo_socio_id;
            header("Location: seleccion_actividad.php");
            exit();
        } else {
            $errores[] = "Error al registrar el socio: " . mysqli_error($conex);
        }
    }
}

// Procesar selección de socio existente
if (isset($_POST['seleccionar_socio'])) {
    $socioID = isset($_POST['socioID']) ? intval($_POST['socioID']) : 0;

    if ($socioID > 0) {
        $_SESSION['socioID'] = $socioID;
        header("Location: seleccion_actividad.php");
        exit();
    }
}

// Obtener todos los socios para el desplegable
$query_socios = "SELECT socioID, nombre, nif FROM Socio ORDER BY nombre";
$resultado_socios = mysqli_query($conex, $query_socios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inscripciones - Gimnasio</title>
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
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
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

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s;
        }

        .tab.active {
            color: #f5576c;
            border-bottom-color: #f5576c;
        }

        .tab:hover {
            color: #f5576c;
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

        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #f5576c;
            background-color: white;
        }

        .error-box {
            background: #fee;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .error-box ul {
            margin-left: 20px;
            color: #c0392b;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .info-box p {
            color: #0d47a1;
            font-size: 14px;
            line-height: 1.5;
        }

        button {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.4);
        }

        .required {
            color: #e74c3c;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script>
        function switchTab(tab) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.remove('active');
            });

            // Desactivar todas las pestañas
            document.querySelectorAll('.tab').forEach(function(t) {
                t.classList.remove('active');
            });

            // Activar la pestaña seleccionada
            if (tab === 'nuevo') {
                document.getElementById('tab-nuevo').classList.add('active');
                document.getElementById('content-nuevo').classList.add('active');
            } else {
                document.getElementById('tab-existente').classList.add('active');
                document.getElementById('content-existente').classList.add('active');
            }
        }

        function validarRegistro() {
            var nif = document.getElementById('nif').value;
            var nombre = document.getElementById('nombre').value;
            var telefono = document.getElementById('telefono').value;

            if (nif.length != 9) {
                alert('El NIF debe tener exactamente 9 caracteres.');
                return false;
            }

            if (nombre.trim() === '') {
                alert('El nombre es obligatorio.');
                return false;
            }

            if (telefono !== '' && telefono.length != 9) {
                alert('El teléfono debe tener exactamente 9 dígitos.');
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>💪 Sistema de Inscripciones</h1>
        <p class="subtitle">Gimnasio FitClub</p>

        <?php if (isset($errores) && count($errores) > 0): ?>
            <div class="error-box">
                <strong>⚠️ Errores encontrados:</strong>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab <?php echo ($accion == 'nuevo') ? 'active' : ''; ?>"
                    id="tab-nuevo" onclick="switchTab('nuevo')">
                📝 Nuevo Socio
            </button>
            <button class="tab <?php echo ($accion == 'seleccionar') ? 'active' : ''; ?>"
                    id="tab-existente" onclick="switchTab('existente')">
                👤 Socio Existente
            </button>
        </div>

        <!-- TAB: Nuevo Socio -->
        <div class="tab-content <?php echo ($accion == 'nuevo') ? 'active' : ''; ?>" id="content-nuevo">
            <div class="info-box">
                <p><strong>ℹ️ Registro de nuevo socio</strong><br>
                Complete el formulario para registrar un nuevo socio en el gimnasio.</p>
            </div>

            <form method="POST" action="index.php?accion=nuevo" onsubmit="return validarRegistro()">
                <div class="form-group">
                    <label for="nif"><span class="required">*</span> NIF:</label>
                    <input type="text" id="nif" name="nif" required
                           maxlength="9" placeholder="Ej: 12345678A"
                           value="<?php echo isset($_POST['nif']) ? htmlspecialchars($_POST['nif']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="nombre"><span class="required">*</span> Nombre completo:</label>
                    <input type="text" id="nombre" name="nombre" required
                           maxlength="50" placeholder="Ej: Juan García López"
                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono"
                           maxlength="9" placeholder="Ej: 612345678"
                           value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                           maxlength="50" placeholder="Ej: usuario@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <button type="submit" name="registrar_socio">Registrar y Continuar →</button>
            </form>
        </div>

        <!-- TAB: Socio Existente -->
        <div class="tab-content <?php echo ($accion == 'seleccionar') ? 'active' : ''; ?>" id="content-existente">
            <div class="info-box">
                <p><strong>ℹ️ Selección de socio</strong><br>
                Seleccione un socio existente para realizar una nueva inscripción.</p>
            </div>

            <form method="POST" action="index.php?accion=seleccionar">
                <div class="form-group">
                    <label for="socioID"><span class="required">*</span> Socio:</label>
                    <select name="socioID" id="socioID" required>
                        <option value="">-- Seleccione un socio --</option>
                        <?php
                        while ($socio = mysqli_fetch_array($resultado_socios)) {
                            echo '<option value="' . $socio['socioID'] . '">';
                            echo $socio['nombre'] . ' (' . $socio['nif'] . ')';
                            echo '</option>';
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="seleccionar_socio">Continuar →</button>
            </form>
        </div>
    </div>

    <script>
        // Mantener la pestaña activa según el parámetro de URL
        <?php if ($accion == 'nuevo'): ?>
        switchTab('nuevo');
        <?php endif; ?>
    </script>
</body>
</html>

<?php
mysqli_close($conex);
?>
