# PRÁCTICA 04 — Gestor de tareas con Symfony

**Duración estimada:** 2 días  
**Nivel:** Básico  
**Tecnologías:** PHP, Symfony, Twig y HTML/CSS

## 1. Objetivo

Desarrollar una pequeña aplicación web que permita visualizar y gestionar tareas.

El objetivo de esta práctica es familiarizarse con la estructura de un proyecto Symfony y entender cómo se relacionan sus componentes: **rutas, controladores, variables y vistas**.

No se proporcionará el código de la solución. Deberéis investigar cómo implementar cada requisito utilizando la documentación de Symfony.

## 2. Requisitos funcionales

### RF01 — Página principal

Crear una página de inicio accesible mediante la ruta `/tareas`.

La página deberá contener:

- Un título: «Gestor de tareas».
- Un pequeño texto de bienvenida.
- Un listado de tareas.
- Un enlace o botón para acceder a la creación de tareas.

### RF02 — Listado de tareas

Crear un listado con un mínimo de **5 tareas ficticias**.

Cada tarea deberá tener:

- ID.
- Título.
- Descripción.
- Estado: Pendiente o Completada.

**Importante:** inicialmente no será necesario utilizar una base de datos. Los datos podrán definirse en un array de PHP.

El listado deberá generarse dinámicamente desde Twig, sin escribir manualmente cada tarea en HTML.

### RF03 — Detalle de una tarea

Al hacer clic sobre una tarea, deberá abrirse una página con su información completa.

La URL deberá seguir esta estructura:

`/tareas/1`

El controlador deberá recibir el identificador de la tarea y buscar su información.

Si la tarea no existe, se deberá mostrar un error adecuado.

### RF04 — Crear una nueva tarea

Crear una página con un formulario que permita introducir:

- Título.
- Descripción.
- Estado.

Al enviar el formulario, Symfony deberá recibir los datos y procesarlos.

Por ahora no es necesario guardar las tareas en una base de datos. Será suficiente con mostrar una página de confirmación con los datos recibidos.

**Validaciones mínimas:**

- El título no puede estar vacío.
- La descripción no puede estar vacía.
- El estado debe ser Pendiente o Completada.

### RF05 — Organización de las vistas

Utilizar Twig para construir las páginas.

Todas las páginas deberán compartir una estructura común con:

- Cabecera.
- Menú de navegación.
- Contenido principal.

Se deberá evitar duplicar el mismo HTML en diferentes plantillas.

## 3. Requisitos técnicos

- Utilizar Symfony y PHP.
- Crear al menos un controlador propio.
- Definir rutas para cada página.
- Utilizar variables PHP y arrays.
- Utilizar estructuras `if` y `foreach`.
- Pasar información desde el controlador a las plantillas Twig.
- Recuperar los datos enviados mediante un formulario.
- Ejecutar el proyecto en el entorno local.

**No se permite utilizar una base de datos en esta fase.**

## 4. Preguntas de investigación

Durante la práctica deberéis averiguar:

1. ¿Qué es un controlador en Symfony?
2. ¿Cómo se define una ruta?
3. ¿Qué diferencia existe entre GET y POST?
4. ¿Cómo se pasa una variable PHP a Twig?
5. ¿Cómo se recorre un array en Twig?
6. ¿Qué función tiene `base.html.twig`?
7. ¿Cómo puede un controlador recuperar un parámetro de una URL?
8. ¿Qué sucede cuando Symfony recibe una petición a una ruta que no existe?

## 5. Entrega

Se deberá entregar:

- Código fuente del proyecto.
- Capturas de las páginas funcionando.
- Un archivo `README.md` con instrucciones para ejecutar la aplicación.
- Respuestas breves a las preguntas de investigación.

## 6. Ampliación opcional

Si termináis todos los requisitos, podéis incorporar:

- Un filtro para mostrar tareas pendientes o completadas.
- Un contador de tareas por estado.
- Estilos CSS para distinguir los estados.
- Una página personalizada para cuando una tarea no existe.

**Resultado esperado:** una pequeña aplicación Symfony funcional en la que se entienda el recorrido de los datos desde una petición HTTP hasta el controlador y la plantilla Twig.
