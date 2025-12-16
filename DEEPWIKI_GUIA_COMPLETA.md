# DEEPWIKI: Guía Definitiva para Exámenes de PHP + MySQL
## Ingeniería del Software Web - Universidad Pontificia de Salamanca

---

# INTRODUCCION: PATRON COMUN DE TODOS LOS EXAMENES

Tras analizar múltiples exámenes de años anteriores, he identificado un **patrón común** que se repite en TODOS los enunciados. Entender este patrón te permitirá resolver cualquier examen de forma sistemática.

## Estructura Universal de los Exámenes

Todos los exámenes siguen esta estructura:

1. **Contexto/Temática:** Un sistema de gestión (hotel, gimnasio, apartamentos, pistas deportivas, etc.)
2. **4 Tablas de Base de Datos:** Siempre hay exactamente 4 tablas con relaciones entre ellas
3. **Flujo de 4 Pantallas PHP:**
   - Pantalla 1: Login/Registro de usuario
   - Pantalla 2: Selección de opciones (desplegables dinámicos)
   - Pantalla 3: Filtrado/Disponibilidad
   - Pantalla 4: Confirmación + Historial
4. **Puntuación típica:**
   - Diseño BD en phpMyAdmin: 1-1.5 puntos
   - Implementación PHP proceso principal: 5.5-6.5 puntos
   - Confirmación + historial: 2.5-3.5 puntos

---

# ANALISIS DETALLADO DE EXAMENES

---

## EXAMEN 1: GESTION HOTELERA (Enero 2025)

### Enunciado Resumido:
Sistema para gestionar reservas de habitaciones de hotel. Almacena información de tipos de habitación, habitaciones, clientes y reservas.

### Tablas Identificadas:

**1. TiposHabitacion** (Catálogo de tipos)
```
- tipoHabitacionID (PK, INT, AUTO_INCREMENT)
- descripcion (VARCHAR 50)
- precioBase (DECIMAL 10,2)
```

**2. Habitaciones** (Inventario de habitaciones)
```
- habitacionID (PK, INT, AUTO_INCREMENT)
- numeroHabitacion (INT)
- tipoHabitacionID (FK → TiposHabitacion)
```

**3. Clientes** (Usuarios del sistema)
```
- clienteID (PK, INT, AUTO_INCREMENT)
- nif (VARCHAR 9, UNIQUE)
- nombre (VARCHAR 50)
- telefono (VARCHAR 15)
- email (VARCHAR 100)
- direccion (VARCHAR 50)
- tarjetaCredito (VARCHAR 20)
- contrasena (VARCHAR 50)
```

**4. Reservas** (Tabla relacional)
```
- reservaID (PK, INT, AUTO_INCREMENT)
- habitacionID (FK → Habitaciones)
- clienteID (FK → Clientes)
- fechaReserva (DATE)
- fechaEntrada (DATE)
- fechaSalida (DATE)
- importeTotal (DECIMAL 10,2)
```

### Flujo de Pantallas:

**Pantalla 1 - Identificación:**
- Formulario de login (NIF + contraseña)
- Opción de registro de nuevo cliente
- Validar credenciales contra BD

**Pantalla 2 - Selección de Reserva:**
- Introducir fechas de entrada y salida
- Validaciones:
  - fecha_entrada >= fecha_actual
  - fecha_salida > fecha_entrada
- Desplegable de tipo de habitación (desde TiposHabitacion, mostrando descripción)

**Pantalla 3 - Habitaciones Disponibles:**
- Mostrar desplegable con habitaciones disponibles para esas fechas
- Formato: "Hab. 101 (Doble)"
- Consulta SQL clave:
```sql
SELECT h.habitacionID, h.numeroHabitacion, t.descripcion
FROM Habitaciones h
JOIN TiposHabitacion t ON h.tipoHabitacionID = t.tipoHabitacionID
WHERE h.tipoHabitacionID = [tipo_seleccionado]
AND h.habitacionID NOT IN (
    SELECT habitacionID FROM Reservas
    WHERE (fechaEntrada <= '[fecha_salida]' AND fechaSalida >= '[fecha_entrada]')
)
```

**Pantalla 4 - Confirmación:**
- Calcular precio: precioBase × número_noches
- Aplicar descuento aleatorio entre 10% y 30%
- Guardar en importeTotal
- Mostrar datos del cliente, habitación y reserva
- Mostrar historial de reservas del cliente (fecha actual en adelante)

### Cálculos Específicos:
```php
// Número de noches
$fecha1 = new DateTime($fecha_entrada);
$fecha2 = new DateTime($fecha_salida);
$diferencia = $fecha1->diff($fecha2);
$noches = $diferencia->days;

// Precio con descuento
$descuento = mt_rand(10, 30) / 100;
$precio_total = $precioBase * $noches;
$precio_final = $precio_total * (1 - $descuento);
```

---

## EXAMEN 2: APARTAMENTOS TURISTICOS (Diciembre 2023)

### Enunciado Resumido:
Sistema de alquiler de apartamentos turísticos con usuarios, inmuebles, reservas y comentarios.

### Tablas Identificadas:

**1. Usuario** (Clientes)
```
- ID_Usuario (PK, INT, AUTO_INCREMENT)
- NIF (VARCHAR 9, UNIQUE)
- nombre (VARCHAR 50)
- direccion (VARCHAR 50)
- telefono (VARCHAR 15)
```

**2. Inmueble** (Propiedades)
```
- ID_Inmueble (PK, INT, AUTO_INCREMENT)
- nombre (VARCHAR 50)
- direccion (VARCHAR 50)
- num_habitaciones (INT)
- precio (DECIMAL 10,2)
```

**3. Reserva** (Tabla relacional)
```
- ID_Reserva (PK, INT, AUTO_INCREMENT)
- ID_Inmueble (FK → Inmueble)
- ID_Usuario (FK → Usuario)
- fecha_entrada (DATE)
- fecha_salida (DATE)
```

**4. Comentario** (Valoraciones)
```
- ID_resena (PK, INT, AUTO_INCREMENT)
- ID_reserva (FK → Reserva)
- puntuacion (INT) -- entre 0 y 10
- comentario (VARCHAR 200)
```

### Flujo de Pantallas:

**Pantalla 1 - Selección de Cliente:**
- Desplegable con todos los clientes (nombre)
- Al seleccionar, pasar a siguiente pantalla

**Pantalla 2 - Selección de Inmueble:**
- Desplegable con inmuebles disponibles
- Formato: "Caserón en Salamanca (1234)"
- Introducir fechas de entrada y salida

**Pantalla 3 - Confirmación:**
- Calcular precio total
- Mostrar lista de reservas anteriores del cliente
- Formulario para valorar la atención recibida

### Consulta de Disponibilidad:
```sql
SELECT * FROM Inmueble
WHERE ID_Inmueble NOT IN (
    SELECT ID_Inmueble FROM Reserva
    WHERE (fecha_entrada <= '[fecha_salida_solicitada]' 
    AND fecha_salida >= '[fecha_entrada_solicitada]')
)
```

---

## EXAMEN 3: PISTAS DE PADEL (Junio 2023)

### Enunciado Resumido:
Sistema de reserva de pistas de pádel para socios de un club.

### Tablas Identificadas:

**1. SOCIO** (Usuarios)
```
- NIF (PK, VARCHAR 9)
- nombre (VARCHAR 50)
- telefono (VARCHAR 15)
```

**2. PISTA** (Instalaciones)
```
- id_pista (PK, INT, AUTO_INCREMENT)
- descripcion (VARCHAR 40)
- precio (DECIMAL 10,2) -- precio por persona
```

**3. ALQUILER** (Reservas)
```
- NIF (FK → SOCIO)
- id_pista (FK → PISTA)
- num_personas (INT)
- fecha_alquiler (DATE)
- hora_alquiler (TIME)
- importe (DECIMAL 10,2) -- calculado
- PRIMARY KEY (NIF, id_pista, fecha_alquiler, hora_alquiler)
```

### Flujo de Pantallas:

**Pantalla 1 - Formulario de Reserva:**
- Desplegable con todos los socios (nombre)
- Desplegable con pistas disponibles (descripción)
- Campos: fecha, hora, número de personas
- Validaciones:
  - fecha >= fecha_actual
  - hora > hora_actual (si es hoy)
  - num_personas > 0

**Pantalla 2 - Verificación:**
- Comprobar si ya existe reserva para esa pista, fecha y hora
- Si existe: mostrar mensaje de error y volver al formulario
- Si no existe: mostrar confirmación

**Pantalla 3 - Confirmación:**
- Calcular importe: precio_pista × num_personas
- Mostrar datos de la reserva
- Guardar en BD

### Cálculo del Importe:
```php
$importe = $precio_pista * $num_personas;
```

### Consulta de Disponibilidad:
```sql
SELECT * FROM ALQUILER
WHERE id_pista = [pista_seleccionada]
AND fecha_alquiler = '[fecha]'
AND hora_alquiler = '[hora]'
```

---

## EXAMEN 4: GIMNASIO (Diciembre 2024)

### Enunciado Resumido:
Sistema de gestión de un gimnasio con socios, actividades, monitores e inscripciones.

### Tablas Identificadas:

**1. Socios** (Clientes)
```
- id_socio (PK, INT, AUTO_INCREMENT)
- nif (VARCHAR 9, UNIQUE)
- nombre (VARCHAR 50)
- usuario (VARCHAR 20, UNIQUE)
- clave (VARCHAR 50)
- f_nac (DATE)
- email (VARCHAR 100)
- telefono (VARCHAR 15)
```

**2. Actividades** (Servicios)
```
- id_actividad (PK, INT, AUTO_INCREMENT)
- nombre (VARCHAR 50)
- descripcion (TEXT)
- fecha_inicio (DATE)
- fecha_fin (DATE)
```

**3. Monitores** (Personal)
```
- id_monitor (PK, INT, AUTO_INCREMENT)
- nif (VARCHAR 9, UNIQUE)
- nombre (VARCHAR 50)
- especialidad (VARCHAR 50)
- telefono (VARCHAR 15)
```

**4. Inscripciones** (Tabla relacional)
```
- id_socio (FK → Socios)
- id_actividad (FK → Actividades)
- id_monitor (FK → Monitores)
- fecha_inscripcion (DATE)
- fecha_fin (DATE)
- precio (DECIMAL 10,2)
- PRIMARY KEY (id_socio, id_actividad)
```

### Flujo de Pantallas:

**Pantalla 1 - Login/Registro:**
- Login con usuario y contraseña
- Opción de registrar nuevo socio

**Pantalla 2 - Selección de Actividad:**
- Desplegable con actividades activas (fecha_fin > CURDATE())
- Desplegable con monitores
- Formato actividad: "Spinning (hasta 2025-12-31)"

**Pantalla 3 - Confirmación:**
- Precio aleatorio entre 100€ y 1000€
- Duración: 1 año desde fecha actual
- Guardar inscripción

**Pantalla 4 - Historial:**
- Mostrar todas las inscripciones del socio
- Incluir: actividad, monitor, fechas, precio

---

# GUIA PASO A PASO: COMO RESOLVER CUALQUIER EXAMEN

## FASE 1: LECTURA Y ANALISIS DEL ENUNCIADO (5 minutos)

### Paso 1.1: Identificar las 4 Tablas
Lee el enunciado y subraya:
- Nombres de tablas (aparecen en **negrita** o subrayados)
- Campos de cada tabla (aparecen entre paréntesis)
- Claves primarias (subrayadas)
- Claves foráneas (en negrita)

### Paso 1.2: Dibujar el Modelo E-R
En un papel, dibuja rápidamente:
```
[Tabla1] --1:N-- [TablaRelacional] --N:1-- [Tabla2]
                        |
                       N:1
                        |
                    [Tabla3]
```

### Paso 1.3: Identificar el Flujo de Pantallas
Busca en el enunciado frases como:
- "En una primera pantalla..."
- "Una vez identificado el cliente..."
- "Se pasará a la siguiente pantalla..."
- "Una vez almacenados los datos..."

### Paso 1.4: Identificar Cálculos Especiales
Busca:
- "precio aleatorio entre X e Y"
- "descuento entre X% e Y%"
- "duración de 1 año"
- "precio total = precio × cantidad"

---

## FASE 2: CREAR LA BASE DE DATOS EN PHPMYADMIN (15-20 minutos)

### Paso 2.1: Crear la Base de Datos
1. Abrir phpMyAdmin: `http://localhost/phpmyadmin`
2. Clic en "Nueva"
3. Nombre: `bd_[tema]` (ej: bd_hotel, bd_gimnasio)
4. Cotejamiento: `utf8mb4_spanish_ci`
5. Crear

### Paso 2.2: Crear Tablas en Orden Correcto

**IMPORTANTE:** Crear primero las tablas que NO tienen claves foráneas.

**Orden típico:**
1. Tablas de catálogo (TiposHabitacion, Actividades, Pistas)
2. Tablas de usuarios (Clientes, Socios, Usuarios)
3. Tablas de recursos (Habitaciones, Inmuebles, Monitores)
4. Tabla relacional (Reservas, Inscripciones, Alquileres)

### Paso 2.3: Definir Campos Correctamente

**Reglas de tipos de datos:**

| Campo | Tipo | Longitud | Notas |
|-------|------|----------|-------|
| ID (PK) | INT | - | AUTO_INCREMENT |
| NIF/DNI | VARCHAR | 9 | UNIQUE |
| Nombre/Descripción | VARCHAR | 50 | - |
| Email | VARCHAR | 100 | - |
| Teléfono | VARCHAR | 15 | - |
| Dirección | VARCHAR | 50-100 | - |
| Precio | DECIMAL | 10,2 | NUNCA FLOAT |
| Fecha | DATE | - | - |
| Hora | TIME | - | - |
| Contraseña | VARCHAR | 50 | - |
| Comentario largo | VARCHAR/TEXT | 200+ | - |
| Cantidad/Número | INT | - | - |
| Puntuación | INT | - | CHECK entre 0-10 |

### Paso 2.4: Configurar Claves Primarias

**Para tablas normales:**
- Seleccionar campo ID
- Marcar como PRIMARY
- Marcar AUTO_INCREMENT

**Para tablas relacionales con PK compuesta:**
- Seleccionar múltiples campos (Ctrl+clic)
- Marcar como PRIMARY
- NO marcar AUTO_INCREMENT

### Paso 2.5: Configurar Claves Foráneas

1. Ir a la tabla que tiene la FK
2. Pestaña "Estructura"
3. Clic en "Vista de relaciones"
4. Para cada FK:
   - Columna: [campo_fk]
   - Tabla referenciada: [tabla_padre]
   - Columna referenciada: [pk_padre]
   - ON DELETE: RESTRICT
   - ON UPDATE: CASCADE

### Paso 2.6: Insertar Datos de Prueba

**Mínimo 3 registros por tabla:**

```sql
-- Ejemplo para tabla de tipos
INSERT INTO TiposHabitacion (descripcion, precioBase) VALUES
('Individual', 50.00),
('Doble', 80.00),
('Suite', 150.00);

-- Ejemplo para tabla de usuarios
INSERT INTO Clientes (nif, nombre, telefono, email, contrasena) VALUES
('12345678A', 'Juan García', '666111222', 'juan@email.com', '1234'),
('87654321B', 'María López', '666333444', 'maria@email.com', '1234');
```

### Paso 2.7: Exportar la Base de Datos

1. Seleccionar la BD
2. Pestaña "Exportar"
3. Método: Personalizado
4. Marcar: CREATE DATABASE, CREATE TABLE, INSERT
5. Ejecutar y guardar como `[nombre].sql`

---

## FASE 3: CREAR PROYECTO EN NETBEANS (5 minutos)

### Paso 3.1: Nuevo Proyecto
1. File → New Project
2. PHP → PHP Application
3. Nombre: `[NombreProyecto]`
4. Ubicación: `C:\xampp\htdocs\[NombreProyecto]`
5. Finish

### Paso 3.2: Estructura de Archivos
```
[Proyecto]/
├── conexion_bd.php      ← Conexión a BD
├── index.php            ← Pantalla 1 (Login)
├── pantalla2.php        ← Pantalla 2 (Selección)
├── pantalla3.php        ← Pantalla 3 (Disponibilidad)
├── confirmacion.php     ← Pantalla 4 (Confirmación)
└── [nombre].sql         ← Backup BD
```

---

## FASE 4: IMPLEMENTAR PHP (60-90 minutos)

### ARCHIVO 1: conexion_bd.php

```php
<?php
// conexion_bd.php - SIEMPRE IGUAL EN TODOS LOS EXAMENES

$servidor = "localhost";
$usuario = "root";
$password = "";
$bd = "bd_[tema]";  // Cambiar según el examen

$conex = mysqli_connect($servidor, $usuario, $password, $bd)
    or die("Error de conexión: " . mysqli_connect_error());

mysqli_set_charset($conex, "utf8mb4");
?>
```

---

### ARCHIVO 2: index.php (Pantalla 1 - Login/Registro)

```php
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión</title>
</head>
<body>
<?php

// Función para mostrar formulario de login
function pintar_formulario_login() {
    $formulario = <<<FORMULARIO
    <h1>Iniciar Sesión</h1>
    <form action="index.php" method="post">
        <p>NIF: <input type="text" name="nif" size="9" maxlength="9" required></p>
        <p>Contraseña: <input type="password" name="pwd" required></p>
        <p><input type="submit" value="Entrar"></p>
    </form>
    <p><a href="registro.php">Registrar nuevo usuario</a></p>
FORMULARIO;
    print $formulario;
}

// Lógica principal
if (empty($_POST)) {
    pintar_formulario_login();
} else {
    include 'conexion_bd.php';
    
    $nif = isset($_POST['nif']) ? $_POST['nif'] : "";
    $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : "";
    
    // Escapar para prevenir SQL injection
    $nif = mysqli_real_escape_string($conex, $nif);
    $pwd = mysqli_real_escape_string($conex, $pwd);
    
    // Consulta de validación
    $query = "SELECT * FROM [TablaUsuarios] 
              WHERE nif = '$nif' AND contrasena = '$pwd'";
    
    $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
    
    if (mysqli_num_rows($resultado) == 0) {
        echo "<p style='color:red;'>Credenciales incorrectas</p>";
        pintar_formulario_login();
    } else {
        // Login correcto
        $usuario = mysqli_fetch_array($resultado);
        
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['nif'] = $usuario['nif'];
        
        header("Location: pantalla2.php");
        exit();
    }
    
    mysqli_close($conex);
}
?>
</body>
</html>
```

---

### ARCHIVO 3: pantalla2.php (Selección con Desplegables)

```php
<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre = $_SESSION['nombre'];

include 'conexion_bd.php';

// Función para mostrar formulario de selección
function mostrar_formulario($conex, $id_usuario, $nombre) {
    
    // Consulta para llenar desplegable 1 (ej: actividades activas)
    $query1 = "SELECT id, nombre, fecha_fin FROM [Tabla1] 
               WHERE fecha_fin > CURDATE() 
               ORDER BY nombre";
    $res1 = mysqli_query($conex, $query1) or die(mysqli_error($conex));
    
    // Consulta para llenar desplegable 2 (ej: monitores)
    $query2 = "SELECT id, nombre FROM [Tabla2] ORDER BY nombre";
    $res2 = mysqli_query($conex, $query2) or die(mysqli_error($conex));
    
    echo "<h1>Selección - Usuario: $nombre</h1>";
    echo "<form action='pantalla2.php' method='post'>";
    
    // Desplegable 1
    echo "<p>Seleccione opción 1: <select name='opcion1' required>";
    echo "<option value=''>-- Seleccione --</option>";
    while ($reg = mysqli_fetch_array($res1)) {
        $id = $reg['id'];
        $nom = $reg['nombre'];
        $fecha = $reg['fecha_fin'];
        echo "<option value='$id'>$nom (hasta $fecha)</option>";
    }
    echo "</select></p>";
    
    // Desplegable 2
    echo "<p>Seleccione opción 2: <select name='opcion2' required>";
    echo "<option value=''>-- Seleccione --</option>";
    while ($reg = mysqli_fetch_array($res2)) {
        $id = $reg['id'];
        $nom = $reg['nombre'];
        echo "<option value='$id'>$nom</option>";
    }
    echo "</select></p>";
    
    // Campos de fecha (si aplica)
    echo "<p>Fecha entrada: <input type='date' name='fecha_entrada' required></p>";
    echo "<p>Fecha salida: <input type='date' name='fecha_salida' required></p>";
    
    echo "<p><input type='submit' value='Continuar'></p>";
    echo "</form>";
}

// Lógica principal
if (empty($_POST)) {
    mostrar_formulario($conex, $id_usuario, $nombre);
} else {
    // Obtener datos del formulario
    $opcion1 = isset($_POST['opcion1']) ? intval($_POST['opcion1']) : 0;
    $opcion2 = isset($_POST['opcion2']) ? intval($_POST['opcion2']) : 0;
    $fecha_entrada = isset($_POST['fecha_entrada']) ? $_POST['fecha_entrada'] : "";
    $fecha_salida = isset($_POST['fecha_salida']) ? $_POST['fecha_salida'] : "";
    
    // Validaciones
    $errores = "";
    $hoy = date('Y-m-d');
    
    if ($opcion1 == 0) {
        $errores .= " / Debe seleccionar opción 1";
    }
    if ($opcion2 == 0) {
        $errores .= " / Debe seleccionar opción 2";
    }
    if ($fecha_entrada < $hoy) {
        $errores .= " / Fecha entrada debe ser >= hoy";
    }
    if ($fecha_salida <= $fecha_entrada) {
        $errores .= " / Fecha salida debe ser > fecha entrada";
    }
    
    if ($errores != "") {
        echo "<p style='color:red;'>Errores: $errores</p>";
        mostrar_formulario($conex, $id_usuario, $nombre);
    } else {
        // Guardar en sesión y continuar
        $_SESSION['opcion1'] = $opcion1;
        $_SESSION['opcion2'] = $opcion2;
        $_SESSION['fecha_entrada'] = $fecha_entrada;
        $_SESSION['fecha_salida'] = $fecha_salida;
        
        header("Location: pantalla3.php");
        exit();
    }
}

mysqli_close($conex);
?>
```

---

### ARCHIVO 4: pantalla3.php (Disponibilidad)

```php
<?php
session_start();

// Verificar sesión completa
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['opcion1'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$opcion1 = $_SESSION['opcion1'];
$fecha_entrada = $_SESSION['fecha_entrada'];
$fecha_salida = $_SESSION['fecha_salida'];

include 'conexion_bd.php';

// Consulta de disponibilidad (CLAVE DEL EXAMEN)
$query = "SELECT r.id, r.nombre, r.descripcion
          FROM [TablaRecursos] r
          WHERE r.tipo_id = $opcion1
          AND r.id NOT IN (
              SELECT recurso_id FROM [TablaReservas]
              WHERE (fecha_entrada <= '$fecha_salida' 
              AND fecha_salida >= '$fecha_entrada')
          )
          ORDER BY r.nombre";

$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

echo "<h1>Recursos Disponibles</h1>";

if (mysqli_num_rows($resultado) == 0) {
    echo "<p>No hay recursos disponibles para las fechas seleccionadas.</p>";
    echo "<p><a href='pantalla2.php'>Volver a seleccionar</a></p>";
} else {
    echo "<form action='pantalla3.php' method='post'>";
    echo "<p>Seleccione recurso: <select name='recurso' required>";
    echo "<option value=''>-- Seleccione --</option>";
    
    while ($reg = mysqli_fetch_array($resultado)) {
        $id = $reg['id'];
        $nom = $reg['nombre'];
        $desc = $reg['descripcion'];
        echo "<option value='$id'>$nom ($desc)</option>";
    }
    
    echo "</select></p>";
    echo "<p><input type='submit' value='Reservar'></p>";
    echo "</form>";
}

// Procesar selección
if (!empty($_POST)) {
    $recurso = isset($_POST['recurso']) ? intval($_POST['recurso']) : 0;
    
    if ($recurso > 0) {
        $_SESSION['recurso'] = $recurso;
        header("Location: confirmacion.php");
        exit();
    }
}

mysqli_close($conex);
?>
```

---

### ARCHIVO 5: confirmacion.php (Confirmación + Historial)

```php
<?php
session_start();

// Verificar sesión completa
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['recurso'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre'];
$recurso = $_SESSION['recurso'];
$fecha_entrada = $_SESSION['fecha_entrada'];
$fecha_salida = $_SESSION['fecha_salida'];

include 'conexion_bd.php';

// Obtener datos del recurso
$query_recurso = "SELECT r.*, t.precio 
                  FROM [TablaRecursos] r
                  JOIN [TablaTipos] t ON r.tipo_id = t.id
                  WHERE r.id = $recurso";
$res_recurso = mysqli_query($conex, $query_recurso) or die(mysqli_error($conex));
$datos_recurso = mysqli_fetch_array($res_recurso);

// CALCULOS (adaptar según enunciado)

// Calcular número de días/noches
$fecha1 = new DateTime($fecha_entrada);
$fecha2 = new DateTime($fecha_salida);
$diferencia = $fecha1->diff($fecha2);
$dias = $diferencia->days;

// Precio base
$precio_base = $datos_recurso['precio'];

// Precio total
$precio_total = $precio_base * $dias;

// Descuento aleatorio (si aplica)
$descuento_porcentaje = mt_rand(10, 30);
$descuento = $precio_total * ($descuento_porcentaje / 100);
$precio_final = $precio_total - $descuento;

// O precio aleatorio directo (si aplica)
// $precio_final = mt_rand(100, 1000);

// Fecha fin (si es inscripción de 1 año)
$fecha_fin = date('Y-m-d', strtotime('+1 year'));

// Función para mostrar confirmación
function mostrar_confirmacion($datos, $precio_final, $descuento_porcentaje) {
    echo "<h1>Confirmación de Reserva</h1>";
    echo "<h2>Datos de la reserva:</h2>";
    echo "<p><strong>Recurso:</strong> " . $datos['nombre'] . "</p>";
    echo "<p><strong>Fecha entrada:</strong> " . $_SESSION['fecha_entrada'] . "</p>";
    echo "<p><strong>Fecha salida:</strong> " . $_SESSION['fecha_salida'] . "</p>";
    echo "<p><strong>Descuento aplicado:</strong> $descuento_porcentaje%</p>";
    echo "<p><strong>Precio final:</strong> " . number_format($precio_final, 2) . " &euro;</p>";
    
    echo "<form action='confirmacion.php' method='post'>";
    echo "<input type='hidden' name='confirmar' value='1'>";
    echo "<input type='hidden' name='precio' value='$precio_final'>";
    echo "<p><input type='submit' value='Confirmar Reserva'></p>";
    echo "</form>";
}

// Función para mostrar historial
function mostrar_historial($conex, $id_usuario) {
    $query = "SELECT r.*, rec.nombre as recurso_nombre
              FROM [TablaReservas] r
              JOIN [TablaRecursos] rec ON r.recurso_id = rec.id
              WHERE r.usuario_id = $id_usuario
              AND r.fecha_entrada >= CURDATE()
              ORDER BY r.fecha_entrada";
    
    $resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));
    
    if (mysqli_num_rows($resultado) > 0) {
        echo "<h2>Historial de Reservas</h2>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Recurso</th><th>Entrada</th><th>Salida</th><th>Precio</th></tr>";
        
        while ($reg = mysqli_fetch_array($resultado)) {
            echo "<tr>";
            echo "<td>" . $reg['recurso_nombre'] . "</td>";
            echo "<td>" . $reg['fecha_entrada'] . "</td>";
            echo "<td>" . $reg['fecha_salida'] . "</td>";
            echo "<td>" . number_format($reg['precio'], 2) . " &euro;</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Lógica principal
if (empty($_POST)) {
    mostrar_confirmacion($datos_recurso, $precio_final, $descuento_porcentaje);
} else {
    // Guardar reserva en BD
    $precio = floatval($_POST['precio']);
    
    $query_insert = "INSERT INTO [TablaReservas] 
                     (usuario_id, recurso_id, fecha_entrada, fecha_salida, precio)
                     VALUES ($id_usuario, $recurso, '$fecha_entrada', '$fecha_salida', $precio)";
    
    $resultado = mysqli_query($conex, $query_insert);
    
    if ($resultado) {
        echo "<h1>Reserva Confirmada</h1>";
        echo "<p style='color:green;'>La reserva se ha registrado correctamente.</p>";
        
        // Mostrar historial
        mostrar_historial($conex, $id_usuario);
        
        // Limpiar sesión de reserva
        unset($_SESSION['opcion1']);
        unset($_SESSION['opcion2']);
        unset($_SESSION['recurso']);
        unset($_SESSION['fecha_entrada']);
        unset($_SESSION['fecha_salida']);
        
    } else {
        echo "<p style='color:red;'>Error: " . mysqli_error($conex) . "</p>";
    }
}

echo "<p><a href='pantalla2.php'>Nueva reserva</a></p>";

mysqli_close($conex);
?>
```

---

# CODIGO PHP QUE SIEMPRE SE REPITE

## 1. Conexión a Base de Datos
```php
<?php
$conex = mysqli_connect('localhost', 'root', '', 'bd_nombre')
    or die("Error: " . mysqli_connect_error());
mysqli_set_charset($conex, "utf8mb4");
?>
```

## 2. Inicio de Sesión
```php
<?php
session_start();
?>
```

## 3. Verificación de Sesión
```php
<?php
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}
?>
```

## 4. Obtener Datos del POST
```php
<?php
$campo = isset($_POST['campo']) ? $_POST['campo'] : "";
$numero = isset($_POST['numero']) ? intval($_POST['numero']) : 0;
?>
```

## 5. Escapar Datos (Seguridad)
```php
<?php
$dato = mysqli_real_escape_string($conex, $dato);
// O para strings simples:
$dato = addslashes($dato);
?>
```

## 6. Ejecutar Consulta SELECT
```php
<?php
$query = "SELECT * FROM tabla WHERE condicion";
$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

if (mysqli_num_rows($resultado) > 0) {
    while ($reg = mysqli_fetch_array($resultado)) {
        $campo = $reg['nombre_campo'];
    }
}
?>
```

## 7. Ejecutar INSERT
```php
<?php
$query = "INSERT INTO tabla (campo1, campo2) VALUES ('$valor1', '$valor2')";
$resultado = mysqli_query($conex, $query) or die(mysqli_error($conex));

if ($resultado) {
    echo "Insertado correctamente";
    $nuevo_id = mysqli_insert_id($conex);
}
?>
```

## 8. Generar Desplegable Dinámico
```php
<?php
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

## 9. Validar Fecha
```php
<?php
$partes = explode('-', $fecha);
if (count($partes) == 3) {
    if (checkdate($partes[1], $partes[2], $partes[0])) {
        // Fecha válida
    }
}
?>
```

## 10. Calcular Diferencia de Fechas
```php
<?php
$fecha1 = new DateTime($fecha_inicio);
$fecha2 = new DateTime($fecha_fin);
$diferencia = $fecha1->diff($fecha2);
$dias = $diferencia->days;
?>
```

## 11. Precio Aleatorio
```php
<?php
$precio = mt_rand(100, 1000);
// O con decimales:
$precio = mt_rand(10000, 100000) / 100;
?>
```

## 12. Descuento Aleatorio
```php
<?php
$descuento_porcentaje = mt_rand(10, 30);
$descuento = $precio * ($descuento_porcentaje / 100);
$precio_final = $precio - $descuento;
?>
```

## 13. Fecha Actual + 1 Año
```php
<?php
$hoy = date('Y-m-d');
$dentro_un_ano = date('Y-m-d', strtotime('+1 year'));
?>
```

## 14. Redirección
```php
<?php
header("Location: pagina.php");
exit();
?>
```

## 15. Heredoc para HTML
```php
<?php
$html = <<<HTML
<form action="archivo.php" method="post">
    <p>Campo: <input type="text" name="campo"></p>
    <p><input type="submit" value="Enviar"></p>
</form>
HTML;
print $html;
?>
```

---

# CONSULTAS SQL CLAVE

## 1. Disponibilidad (Excluir Reservados)
```sql
SELECT * FROM Recursos
WHERE id NOT IN (
    SELECT recurso_id FROM Reservas
    WHERE (fecha_entrada <= '[fecha_fin_solicitada]' 
    AND fecha_salida >= '[fecha_inicio_solicitada]')
)
```

## 2. Actividades/Recursos Activos
```sql
SELECT * FROM Actividades
WHERE fecha_fin > CURDATE()
ORDER BY nombre
```

## 3. Opciones No Seleccionadas por Usuario
```sql
SELECT * FROM Opciones
WHERE id NOT IN (
    SELECT opcion_id FROM Selecciones
    WHERE usuario_id = [id_usuario]
)
```

## 4. JOIN para Obtener Datos Relacionados
```sql
SELECT r.*, u.nombre as usuario, rec.nombre as recurso
FROM Reservas r
JOIN Usuarios u ON r.usuario_id = u.id
JOIN Recursos rec ON r.recurso_id = rec.id
WHERE r.usuario_id = [id]
```

## 5. Historial desde Fecha Actual
```sql
SELECT * FROM Reservas
WHERE usuario_id = [id]
AND fecha_entrada >= CURDATE()
ORDER BY fecha_entrada
```

---

# VALIDACIONES COMUNES

## 1. Campo No Vacío
```php
if ($campo == "") {
    $errores .= " / Campo requerido";
}
```

## 2. NIF Español (8 números + 1 letra)
```php
if (!preg_match("/^[0-9]{8}[A-Za-z]$/", $nif)) {
    $errores .= " / NIF inválido";
}
```

## 3. Email
```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores .= " / Email inválido";
}
```

## 4. Teléfono Español
```php
if (!preg_match("/^[6-9][0-9]{8}$/", $telefono)) {
    $errores .= " / Teléfono inválido";
}
```

## 5. Fecha >= Hoy
```php
if ($fecha < date('Y-m-d')) {
    $errores .= " / Fecha debe ser >= hoy";
}
```

## 6. Fecha Salida > Fecha Entrada
```php
if ($fecha_salida <= $fecha_entrada) {
    $errores .= " / Fecha salida debe ser > fecha entrada";
}
```

## 7. Número Positivo
```php
if ($numero <= 0) {
    $errores .= " / Número debe ser > 0";
}
```

## 8. Contraseñas Coinciden
```php
if ($pwd != $pwd2) {
    $errores .= " / Las contraseñas no coinciden";
}
```

## 9. Opción en Lista Válida
```php
$opciones_validas = array("op1", "op2", "op3");
if (!in_array($opcion, $opciones_validas)) {
    $errores .= " / Opción inválida";
}
```

---

# CHECKLIST RAPIDO PARA EL EXAMEN

## Antes de Empezar:
- [ ] Leer TODO el enunciado
- [ ] Identificar las 4 tablas
- [ ] Identificar PKs y FKs
- [ ] Identificar el flujo de pantallas
- [ ] Identificar cálculos especiales

## Base de Datos:
- [ ] Crear BD con cotejamiento utf8mb4_spanish_ci
- [ ] Crear tablas en orden correcto (sin FK primero)
- [ ] Definir PKs (AUTO_INCREMENT si corresponde)
- [ ] Definir FKs con RESTRICT/CASCADE
- [ ] Insertar datos de prueba (mínimo 3 por tabla)
- [ ] Exportar archivo .sql

## PHP:
- [ ] conexion_bd.php creado
- [ ] session_start() en TODOS los archivos
- [ ] Verificación de sesión en páginas protegidas
- [ ] Formularios con method="post"
- [ ] Validación de TODOS los campos
- [ ] Escapar datos antes de usar en SQL
- [ ] Desplegables dinámicos funcionando
- [ ] Consulta de disponibilidad correcta
- [ ] Cálculos implementados según enunciado
- [ ] INSERT en tabla relacional
- [ ] Historial mostrado correctamente

## Entrega:
- [ ] Proyecto funciona sin errores
- [ ] Archivo .sql incluido
- [ ] ZIP nombrado correctamente: [INICIAL_APELLIDO]_IWPHPFecha.zip

---

# ERRORES COMUNES Y SOLUCIONES

## Error: "Headers already sent"
**Causa:** Hay salida HTML antes de header()
**Solución:** Poner session_start() y header() ANTES de cualquier HTML

## Error: "Undefined index"
**Causa:** Acceder a $_POST['campo'] que no existe
**Solución:** Usar isset(): `$campo = isset($_POST['campo']) ? $_POST['campo'] : "";`

## Error: "Cannot add foreign key constraint"
**Causa:** Tipos de datos no coinciden entre PK y FK
**Solución:** Asegurar que ambos campos sean exactamente del mismo tipo (INT, VARCHAR, etc.)

## Error: "Duplicate entry for key PRIMARY"
**Causa:** Intentar insertar un registro con PK que ya existe
**Solución:** Verificar que el campo PK sea AUTO_INCREMENT o usar valores únicos

## Error: Caracteres extraños (ñ, acentos)
**Causa:** Charset incorrecto
**Solución:** Usar utf8mb4 en BD y mysqli_set_charset($conex, "utf8mb4")

## Error: Fechas no se comparan correctamente
**Causa:** Formato de fecha incorrecto
**Solución:** Usar formato 'YYYY-MM-DD' y funciones DATE de MySQL

---

**FIN DE LA DEEPWIKI**

*Documento generado como guía de estudio para exámenes de Ingeniería del Software Web (PHP + MySQL)*
