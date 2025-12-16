# Modelado-Php

Repositorio de materiales educativos para la asignatura **Ingenieria del Software Web** de la Universidad Pontificia de Salamanca. Contiene ejemplos de proyectos PHP + MySQL, enunciados de examenes y documentacion completa para aprender a desarrollar aplicaciones web.

## Contenido del Repositorio

### Documentacion
- **[ENUNCIADOS.md](ENUNCIADOS.md)** - Recopilacion de enunciados de examenes anteriores
- **[DEEPWIKI_GUIA_COMPLETA.md](DEEPWIKI_GUIA_COMPLETA.md)** - Guia definitiva para resolver cualquier examen PHP+MySQL
- **[MANUAL_PASO_A_PASO.md](MANUAL_PASO_A_PASO.md)** - Manual detallado con codigo reutilizable

### Archivos PHP de Ejemplo
- `conexion_bd.php` - Conexion a base de datos MySQL
- `index.php` - Pagina de inicio/login
- `form_alta.php` - Formulario de registro con validaciones
- `menu_principal.php` - Menu principal de navegacion
- `registro.php` - Sistema de registro de usuarios
- `inicio.php` - Gestion de libros (sistema libreria)
- `reservas (2).php` - Sistema de reservas

### Material Teorico (PDFs)
- Tema 1: Introduccion e Instalacion
- Tema 2: PHP Basico
- Tema 3: Funciones
- Tema 4: Matrices/Arrays
- Tema 5: Formularios
- Tema 6: Sesiones
- Tema 7: Base de Datos con mysqli

## Herramientas Necesarias

- **XAMPP** (Apache + MySQL + PHP)
- **NetBeans IDE** (version 8 o superior)
- **phpMyAdmin** (incluido en XAMPP)
- Navegador web

## Como Empezar

1. Instalar XAMPP y activar Apache + MySQL
2. Clonar este repositorio en `C:\xampp\htdocs\`
3. Importar la base de datos usando phpMyAdmin
4. Abrir el proyecto en NetBeans
5. Acceder via `http://localhost/Modelado-Php/`

## Estructura de un Examen Tipico

Todos los examenes siguen un patron similar:
1. **4 Tablas de BD** con relaciones (PKs y FKs)
2. **4 Pantallas PHP**: Login -> Seleccion -> Disponibilidad -> Confirmacion
3. **Validaciones** de todos los campos de formulario
4. **Calculos** (precios aleatorios, descuentos, fechas)

Consulta la [Guia Completa](DEEPWIKI_GUIA_COMPLETA.md) para mas detalles.

## Autor

Materiales recopilados para estudio de la asignatura Ingenieria del Software Web.
