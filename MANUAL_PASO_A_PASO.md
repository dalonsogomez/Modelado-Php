# Manual Paso a Paso: Codigo PHP Reutilizable

Este manual contiene todos los bloques de codigo que se repiten en cada examen. Puedes copiar y adaptar estos fragmentos para resolver cualquier ejercicio.

---

## 1. CONEXION A BASE DE DATOS

```php
<?php
// conexion_bd.php - SIEMPRE IGUAL EN TODOS LOS EXAMENES

$servidor = "localhost";
$usuario = "root";
$password = "";
$bd = "bd_nombre";  // Cambiar segun el examen

$conex = mysqli_connect($servidor, $usuario, $password, $bd)
    or die("Error de conexion: " . mysqli_connect_error());

mysqli_set_charset($conex, "utf8mb4");
?>
```

---

## 2. INICIO DE SESION

```php
<?php
// SIEMPRE al inicio de cada archivo PHP (excepto conexion_bd.php)
session_start();
?>
```

---

## 3. VERIFICACION DE SESION

```php
<?php
// Poner al inicio de paginas protegidas (despues de session_start)
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}
?>
```

---

## 4. OBTENER DATOS DEL POST

```php
<?php
// Para campos de texto
$campo = isset($_POST['campo']) ? $_POST['campo'] : "";

// Para numeros
$numero = isset($_POST['numero']) ? intval($_POST['numero']) : 0;

// Para decimales (precios)
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0.0;
?>
```

---

## 5. ESCAPAR DATOS (SEGURIDAD)

```php
<?php
// Antes de usar en consultas SQL
$dato = mysqli_real_escape_string($conex, $dato);

// O para strings simples
$dato = addslashes($dato);
?>
```

---

## 6. EJECUTAR CONSULTA SELECT

```php
<?php
$query = "SELECT * FROM tabla WHERE condicion = '$valor'";
$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

// Verificar si hay resultados
if (mysqli_num_rows($resultado) > 0) {
    // Recorrer resultados
    while ($reg = mysqli_fetch_array($resultado)) {
        $campo1 = $reg['nombre_campo1'];
        $campo2 = $reg['nombre_campo2'];
        // ... usar los datos
    }
} else {
    echo "No se encontraron resultados";
}
?>
```

---

## 7. EJECUTAR INSERT

```php
<?php
$query = "INSERT INTO tabla (campo1, campo2, campo3) 
          VALUES ('$valor1', '$valor2', $valor3)";

$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

if ($resultado) {
    echo "Registro insertado correctamente";
    // Obtener el ID del nuevo registro
    $nuevo_id = mysqli_insert_id($conex);
}
?>
```

---

## 8. GENERAR DESPLEGABLE DINAMICO

```php
<?php
// Consultar opciones de la BD
$query = "SELECT id, nombre FROM tabla ORDER BY nombre";
$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

// Generar el HTML del desplegable
echo "<select name='campo' required>";
echo "<option value=''>-- Seleccione --</option>";

while ($reg = mysqli_fetch_array($resultado)) {
    $id = $reg['id'];
    $nombre = $reg['nombre'];
    echo "<option value='$id'>$nombre</option>";
}

echo "</select>";
?>
```

### Desplegable con formato especial (nombre + ID)

```php
<?php
while ($reg = mysqli_fetch_array($resultado)) {
    $id = $reg['id'];
    $nombre = $reg['nombre'];
    // Formato: "Nombre (ID)"
    echo "<option value='$id'>$nombre ($id)</option>";
}
?>
```

### Desplegable con fecha

```php
<?php
while ($reg = mysqli_fetch_array($resultado)) {
    $id = $reg['id'];
    $nombre = $reg['nombre'];
    $fecha = $reg['fecha_fin'];
    // Formato: "Nombre (hasta fecha)"
    echo "<option value='$id'>$nombre (hasta $fecha)</option>";
}
?>
```

---

## 9. VALIDAR FECHA

```php
<?php
// Verificar formato de fecha (YYYY-MM-DD)
$partes = explode('-', $fecha);
if (count($partes) == 3) {
    $anio = $partes[0];
    $mes = $partes[1];
    $dia = $partes[2];
    
    if (checkdate($mes, $dia, $anio)) {
        // Fecha valida
    } else {
        $errores .= " / Fecha invalida";
    }
}
?>
```

### Comparar con fecha actual

```php
<?php
$hoy = date('Y-m-d');

// Fecha debe ser >= hoy
if ($fecha < $hoy) {
    $errores .= " / Fecha debe ser igual o mayor que hoy";
}

// Fecha salida > fecha entrada
if ($fecha_salida <= $fecha_entrada) {
    $errores .= " / Fecha salida debe ser mayor que fecha entrada";
}
?>
```

---

## 10. CALCULAR DIFERENCIA DE FECHAS

```php
<?php
$fecha1 = new DateTime($fecha_inicio);
$fecha2 = new DateTime($fecha_fin);
$diferencia = $fecha1->diff($fecha2);

// Obtener dias
$dias = $diferencia->days;

// Obtener meses
$meses = $diferencia->m + ($diferencia->y * 12);
?>
```

---

## 11. PRECIO ALEATORIO

```php
<?php
// Precio aleatorio entre 100 y 1000 (entero)
$precio = mt_rand(100, 1000);

// Precio aleatorio con decimales (entre 100.00 y 1000.00)
$precio = mt_rand(10000, 100000) / 100;
?>
```

---

## 12. DESCUENTO ALEATORIO

```php
<?php
// Descuento aleatorio entre 10% y 30%
$descuento_porcentaje = mt_rand(10, 30);

// Calcular descuento
$precio_original = 500; // ejemplo
$descuento = $precio_original * ($descuento_porcentaje / 100);
$precio_final = $precio_original - $descuento;

// O en una linea
$precio_final = $precio_original * (1 - $descuento_porcentaje / 100);
?>
```

---

## 13. FECHA ACTUAL + 1 ANO

```php
<?php
// Fecha de hoy
$hoy = date('Y-m-d');

// Fecha dentro de 1 ano
$dentro_un_ano = date('Y-m-d', strtotime('+1 year'));

// Fecha dentro de 6 meses
$dentro_seis_meses = date('Y-m-d', strtotime('+6 months'));
?>
```

---

## 14. REDIRECCION

```php
<?php
// Redirigir a otra pagina
header("Location: pagina.php");
exit();  // IMPORTANTE: siempre poner exit() despues de header()
?>
```

---

## 15. HEREDOC PARA HTML

```php
<?php
$html = <<<HTML
<form action="archivo.php" method="post">
    <p>Campo: <input type="text" name="campo" required></p>
    <p>Numero: <input type="number" name="numero" min="1"></p>
    <p>Fecha: <input type="date" name="fecha"></p>
    <p><input type="submit" value="Enviar"></p>
</form>
HTML;

print $html;
?>
```

---

## 16. FUNCION DE VALIDACION COMPLETA

```php
<?php
function validar_datos(&$campo1, &$campo2, &$fecha, &$errores) {
    $flag = true;
    
    // Validar campo no vacio
    if ($campo1 == "") {
        $campo1 = "";
        $errores .= " / Campo1 requerido";
        $flag = false;
    }
    
    // Validar NIF (8 numeros + 1 letra)
    if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $campo2)) {
        $campo2 = "";
        $errores .= " / NIF invalido";
        $flag = false;
    }
    
    // Validar fecha >= hoy
    $hoy = date('Y-m-d');
    if ($fecha < $hoy) {
        $fecha = "";
        $errores .= " / Fecha debe ser >= hoy";
        $flag = false;
    }
    
    return $flag;
}
?>
```

---

## 17. CONSULTA DE DISPONIBILIDAD

```php
<?php
// Recursos NO reservados en un rango de fechas
$query = "SELECT * FROM Recursos
          WHERE id NOT IN (
              SELECT recurso_id FROM Reservas
              WHERE (fecha_entrada <= '$fecha_salida' 
              AND fecha_salida >= '$fecha_entrada')
          )";

$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
?>
```

---

## 18. CONSULTA CON JOIN

```php
<?php
// Obtener datos de multiples tablas relacionadas
$query = "SELECT r.*, u.nombre as usuario, rec.nombre as recurso
          FROM Reservas r
          JOIN Usuarios u ON r.usuario_id = u.id
          JOIN Recursos rec ON r.recurso_id = rec.id
          WHERE r.usuario_id = $id_usuario
          ORDER BY r.fecha DESC";

$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
?>
```

---

## 19. MOSTRAR TABLA HTML CON RESULTADOS

```php
<?php
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Columna1</th><th>Columna2</th><th>Columna3</th></tr>";

while ($reg = mysqli_fetch_array($resultado)) {
    echo "<tr>";
    echo "<td>" . $reg['campo1'] . "</td>";
    echo "<td>" . $reg['campo2'] . "</td>";
    echo "<td>" . number_format($reg['precio'], 2) . " EUR</td>";
    echo "</tr>";
}

echo "</table>";
?>
```

---

## 20. ESTRUCTURA COMPLETA DE ARCHIVO PHP

```php
<?php
session_start();
include 'conexion_bd.php';

// Verificar sesion si es necesario
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtener datos de sesion
$id_usuario = $_SESSION['id_usuario'];
$nombre = $_SESSION['nombre'];

// Funcion para mostrar formulario
function mostrar_formulario($conex) {
    // Consultar datos para desplegables
    $query = "SELECT id, nombre FROM tabla ORDER BY nombre";
    $resultado = mysqli_query($conex, $query);
    
    echo "<h1>Titulo del Formulario</h1>";
    echo "<form action='' method='post'>";
    
    // Desplegable
    echo "<p>Seleccione: <select name='opcion' required>";
    echo "<option value=''>-- Seleccione --</option>";
    while ($reg = mysqli_fetch_array($resultado)) {
        echo "<option value='" . $reg['id'] . "'>" . $reg['nombre'] . "</option>";
    }
    echo "</select></p>";
    
    // Campo de fecha
    echo "<p>Fecha: <input type='date' name='fecha' required></p>";
    
    // Boton enviar
    echo "<p><input type='submit' value='Enviar'></p>";
    echo "</form>";
}

// Funcion para validar datos
function validar_datos(&$opcion, &$fecha, &$errores) {
    $flag = true;
    
    if ($opcion == 0) {
        $errores .= " / Debe seleccionar una opcion";
        $flag = false;
    }
    
    $hoy = date('Y-m-d');
    if ($fecha < $hoy) {
        $errores .= " / Fecha debe ser >= hoy";
        $flag = false;
    }
    
    return $flag;
}

// LOGICA PRINCIPAL
if (empty($_POST)) {
    // Primera carga: mostrar formulario
    mostrar_formulario($conex);
} else {
    // Procesar formulario enviado
    $opcion = isset($_POST['opcion']) ? intval($_POST['opcion']) : 0;
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : "";
    $errores = "";
    
    if (!validar_datos($opcion, $fecha, $errores)) {
        // Hay errores: mostrar mensaje y formulario
        echo "<p style='color:red;'>Errores: $errores</p>";
        mostrar_formulario($conex);
    } else {
        // Todo correcto: procesar
        // Guardar en sesion
        $_SESSION['opcion'] = $opcion;
        $_SESSION['fecha'] = $fecha;
        
        // Redirigir a siguiente pantalla
        header("Location: siguiente.php");
        exit();
    }
}

mysqli_close($conex);
?>
```

---

## VALIDACIONES COMUNES

### NIF Espanol (8 numeros + 1 letra)
```php
if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $nif)) {
    $errores .= " / NIF invalido";
}
```

### Email
```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores .= " / Email invalido";
}
```

### Telefono Espanol (9 digitos, empieza por 6, 7, 8 o 9)
```php
if (!preg_match("/^[6-9][0-9]{8}$/", $telefono)) {
    $errores .= " / Telefono invalido";
}
```

### Numero Positivo
```php
if ($numero <= 0) {
    $errores .= " / Numero debe ser > 0";
}
```

### Contrasenas Coinciden
```php
if ($pwd != $pwd2) {
    $errores .= " / Las contrasenas no coinciden";
}
```

---

## ERRORES COMUNES Y SOLUCIONES

### Error: "Headers already sent"
**Causa:** Hay salida HTML antes de header()
**Solucion:** Poner session_start() y header() ANTES de cualquier HTML o echo

### Error: "Undefined index"
**Causa:** Acceder a $_POST['campo'] que no existe
**Solucion:** Usar isset(): `$campo = isset($_POST['campo']) ? $_POST['campo'] : "";`

### Error: "Cannot add foreign key constraint"
**Causa:** Tipos de datos no coinciden entre PK y FK
**Solucion:** Asegurar que ambos campos sean exactamente del mismo tipo

### Error: Caracteres extranos (n, acentos)
**Causa:** Charset incorrecto
**Solucion:** Usar utf8mb4 en BD y mysqli_set_charset($conex, "utf8mb4")

---

## CHECKLIST RAPIDO PARA EL EXAMEN

### Base de Datos:
- [ ] Crear BD con cotejamiento utf8mb4_spanish_ci
- [ ] Crear tablas en orden correcto (sin FK primero)
- [ ] Definir PKs (AUTO_INCREMENT si corresponde)
- [ ] Definir FKs con RESTRICT/CASCADE
- [ ] Insertar datos de prueba (minimo 3 por tabla)
- [ ] Exportar archivo .sql

### PHP:
- [ ] conexion_bd.php creado
- [ ] session_start() en TODOS los archivos
- [ ] Verificacion de sesion en paginas protegidas
- [ ] Formularios con method="post"
- [ ] Validacion de TODOS los campos
- [ ] Escapar datos antes de usar en SQL
- [ ] Desplegables dinamicos funcionando
- [ ] Consulta de disponibilidad correcta
- [ ] Calculos implementados segun enunciado
- [ ] INSERT en tabla relacional
- [ ] Historial mostrado correctamente

### Entrega:
- [ ] Proyecto funciona sin errores
- [ ] Archivo .sql incluido
- [ ] ZIP nombrado correctamente
