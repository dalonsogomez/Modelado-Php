<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Sistema de Reservas para Evento</title>
</head>

<body>

<?php

// Variables de formulario
$nombre_completo = "";
$email = "";
$num_entradas = "";
$tipo_entrada = "";
$metodo_pago = "";
$error = "";

// Arrays con opciones disponibles
$tipos_entrada = array("General", "VIP");
$metodos_pago = array("Tarjeta", "PayPal", "Transferencia bancaria", "Bizum");
$extensiones_permitidas = array('pdf', 'jpg', 'jpeg', 'png');

if (empty($_POST)) {
    $nombre_completo = "";
    $email = "";
    $num_entradas = "";
    $tipo_entrada = "";
    $metodo_pago = "";
} else {
    $nombre_completo = isset($_POST["nombre_completo"]) ? $_POST["nombre_completo"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $num_entradas = isset($_POST["num_entradas"]) ? $_POST["num_entradas"] : "";
    $tipo_entrada = isset($_POST["tipo_entrada"]) ? $_POST["tipo_entrada"] : "";
    $metodo_pago = isset($_POST["metodo_pago"]) ? $_POST["metodo_pago"] : "";
}

function validar_carga_archivo()
{
    if ($_FILES['comprobante']['error'] > 0) {
        echo "Problema en la carga de archivo!";
        return false;
    } else {
        if (!is_dir('./comprobantes/')) {
            mkdir("comprobantes", 0777);
        }
        $timestamp = time();
        
        $ruta_fich = './comprobantes/' . $timestamp . "-" . $_FILES['comprobante']['name'];
        
        if (is_uploaded_file($_FILES['comprobante']['tmp_name'])) {
            if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $ruta_fich)) {
                echo 'Problema al copiarlo en el directorio';
                return false;
            } else {
                echo 'Descarga completada';
                return true;
            }
        } else {
            echo 'Fichero no descargado';
            return false;
        }
    }
}

function validar(&$nombre_completo, &$email, &$num_entradas, &$tipo_entrada, &$metodo_pago, &$error, $tipos_entrada, $metodos_pago, $extensiones_permitidas)
{
    $ok = true;
    
    // Expresión regular para nombre: solo letras y espacios, 2-30 caracteres
    $exp_nombre = '/^[a-zA-Z\s]{2,30}$/';
    
    // Expresión regular para email
    $exp_email = '/^[a-zA-Z0-9_%+-.]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
    // Validar nombre completo
    if (($nombre_completo == "") || (!preg_match($exp_nombre, $nombre_completo))) {
        $nombre_completo = "";
        $error = $error . "Nombre completo inválido (solo letras y espacios, 2-30 caracteres)";
        $ok = false;
    }
    
    // Validar email
    if (($email == "") || (!preg_match($exp_email, $email))) {
        $email = "";
        $error = $error . " / Email inválido";
        $ok = false;
    }
    
    // Validar número de entradas (entero positivo)
    if (!is_numeric($num_entradas) || $num_entradas <= 0 || $num_entradas != intval($num_entradas)) {
        $error = $error . " / Número de entradas inválido (debe ser un entero positivo)";
        $ok = false;
    }
    
    // Validar tipo de entrada
    if (!in_array($tipo_entrada, $tipos_entrada)) {
        $error = $error . " / Tipo de entrada inválido";
        $ok = false;
    }
    
    // Validar método de pago
    if (!in_array($metodo_pago, $metodos_pago)) {
        $error = $error . " / Método de pago inválido";
        $ok = false;
    }
    
    // Validar archivo
    if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['name'] == "") {
        $error = $error . " / Debe subir un comprobante de pago";
        $ok = false;
    } else {
        // Validar extensión usando explode
        $nombre_archivo = $_FILES['comprobante']['name'];
        $partes = explode('.', $nombre_archivo);
        $extension = strtolower(end($partes));
        
        if (!in_array($extension, $extensiones_permitidas)) {
            $error = $error . " / El comprobante debe ser PDF, JPG o PNG";
            $ok = false;
        } else {
            // Solo cargar si la extensión es válida
            if (!validar_carga_archivo()) {
                $error = $error . " / Error al cargar el comprobante";
                $ok = false;
            }
        }
    }
    
    return $ok;
}

function pintar_formulario($nombre_completo, $email, $num_entradas, $tipo_entrada, $metodo_pago, $tipos_entrada, $metodos_pago)
{
    $formulario1 = <<<FORMULARIO1
<form action="reservas.php" method="POST" enctype="multipart/form-data">
    <div>
        <p>SISTEMA DE RESERVAS PARA EVENTO</p>
        <br/>
        <p>Nombre Completo:
            <input name="nombre_completo" type="text" size="40" maxlength="30" value="$nombre_completo">
        </p>
        <p>Correo Electrónico:
            <input name="email" type="text" size="40" maxlength="50" value="$email">
        </p>
        <p>Número de Entradas:
            <input name="num_entradas" type="number" min="1" value="$num_entradas">
        </p>
FORMULARIO1;

    // Generar opciones de tipo de entrada
    $opciones_tipo = "";
    foreach ($tipos_entrada as $tipo) {
        if ($tipo_entrada == $tipo || ($tipo_entrada == "" && $tipo == "General")) {
            $opciones_tipo = $opciones_tipo . "<p><input name=\"tipo_entrada\" type=\"radio\" value=\"$tipo\" checked>$tipo</p>\n";
        } else {
            $opciones_tipo = $opciones_tipo . "<p><input name=\"tipo_entrada\" type=\"radio\" value=\"$tipo\">$tipo</p>\n";
        }
    }
    
    $formulario2 = <<<FORMULARIO2
        <p>Tipo de Entrada:
            $opciones_tipo
        </p>
FORMULARIO2;

    $formulario = $formulario1 . $formulario2;
    print $formulario;
    
    // Lista de métodos de pago
    print("<p>");
    print("Método de Pago: ");
    print("<select name='metodo_pago'>");
    print("<option value=''>-- Seleccione --</option>");
    
    foreach ($metodos_pago as $metodo) {
        if ($metodo_pago == $metodo) {
            print("<option value='$metodo' selected>$metodo</option>");
        } else {
            print("<option value='$metodo'>$metodo</option>");
        }
    }
    print("</select>");
    print("</p>");
    
    $formulario3 = <<<FORMULARIO3
        <p>Comprobante de Pago (PDF, JPG, PNG):
            <input type="file" name="comprobante" id="comprobante" accept=".pdf,.jpg,.jpeg,.png">
        </p>
        <p>
            <input type="submit" name="Enviar" value="Enviar Reserva">
        </p>
    </div>
</form>
FORMULARIO3;

    print $formulario3;
}

// Procesamiento principal
if (empty($_POST)) {
    pintar_formulario($nombre_completo, $email, $num_entradas, $tipo_entrada, $metodo_pago, $tipos_entrada, $metodos_pago);
} else {
    $error = "";
    if (!validar($nombre_completo, $email, $num_entradas, $tipo_entrada, $metodo_pago, $error, $tipos_entrada, $metodos_pago, $extensiones_permitidas)) {
        print("Errores: " . $error);
        pintar_formulario($nombre_completo, $email, $num_entradas, $tipo_entrada, $metodo_pago, $tipos_entrada, $metodos_pago);
    } else {
        // Obtener datos del archivo
        $timestamp = time();
        $nombre_archivo_final = $timestamp . "-" . $_FILES['comprobante']['name'];
        $tipo_mime = $_FILES['comprobante']['type'];
        $tamanio = $_FILES['comprobante']['size'];
        
        // Mensaje según tipo de entrada
        if ($tipo_entrada == "VIP") {
            $mensaje = "Gracias por tu reserva VIP. ¡Disfruta de la experiencia exclusiva del evento!";
        } else {
            $mensaje = "Gracias por tu reserva. ¡Te esperamos en el evento!";
        }
        
        $info = <<<DATOS
            <h1>$mensaje</h1>
            <h2>RESUMEN DE LA RESERVA</h2>
            <p>Nombre Completo: $nombre_completo</p>
            <p>Correo Electrónico: $email</p>
            <p>Número de Entradas: $num_entradas</p>
            <p>Tipo de Entrada: $tipo_entrada</p>
            <p>Método de Pago: $metodo_pago</p>
            <h3>Datos del Comprobante:</h3>
            <p>Nombre de archivo: $nombre_archivo_final</p>
            <p>Tipo MIME: $tipo_mime</p>
            <p>Tamaño: $tamanio bytes</p>
DATOS;
        print $info;
    }
}
?>

</body>
</html>
