# Preguntas de investigación

Respuestas breves a las preguntas de la Práctica 04.

## 1. ¿Qué es un controlador en Symfony?

Es una clase PHP que recibe la petición HTTP, ejecuta la lógica necesaria y
devuelve una `Response`. En el proyecto, `TareaController` extiende
`AbstractController`, y cada método público con un atributo `#[Route]` atiende
una ruta: valida datos, consulta el repositorio y renderiza una plantilla Twig.

## 2. ¿Cómo se define una ruta?

Con el atributo `#[Route]` encima del método del controlador:

```php
#[Route('/tareas/{id}', name: 'tarea_detalle', requirements: ['id' => '\d+'], methods: ['GET'])]
```

`name` permite generar la URL con `path('tarea_detalle', {id: 1})`,
`requirements` restringe el formato del parámetro y `methods` limita el verbo
HTTP. `config/routes.yaml` importa los controladores para que el router
descubra estas rutas.

## 3. ¿Qué diferencia existe entre GET y POST?

GET recupera información y envía los datos en la URL (query string): son
visibles, tienen límite de longitud y la petición debe poder repetirse sin
efectos secundarios. POST envía los datos en el cuerpo de la petición y se usa
para crear o modificar información. En el proyecto, el listado y el detalle son
GET, mientras que el envío del formulario es POST; tras procesarlo se redirige
con GET (patrón PRG) para que recargar no lo reenvíe.

## 4. ¿Cómo se pasa una variable PHP a Twig?

Como segundo argumento de `render()`, mediante un array asociativo:

```php
return $this->render('tarea/index.html.twig', [
    'tareas' => $this->tareaRepository->findAll(),
    'estado' => $estado,
]);
```

Cada clave del array se convierte en una variable disponible en la plantilla:
`{{ estado }}` o `{{ tarea.titulo }}` dentro de un bucle.

## 5. ¿Cómo se recorre un array en Twig?

Con un bucle `for`, que además admite una rama `else` para el caso vacío:

```twig
{% for tarea in tareas %}
    <li>{{ tarea.titulo }}</li>
{% else %}
    <li>No hay tareas.</li>
{% endfor %}
```

Dentro del bucle se accede a las claves del array con notación de punto
(`tarea.titulo`, `tarea.estado`), y la variable `loop` ofrece información sobre
la iteración.

## 6. ¿Qué función tiene `base.html.twig`?

Es el layout común de todas las páginas: define el `<head>`, la hoja de estilos,
la cabecera, el menú de navegación y los bloques (`title`, `body`, etc.). Las
demás plantillas lo extienden con `{% extends 'base.html.twig' %}` y rellenan
solo los bloques que necesitan. Así se evita duplicar el mismo HTML y cualquier
cambio en la cabecera se aplica a toda la aplicación.

## 7. ¿Cómo puede un controlador recuperar un parámetro de una URL?

Declarando el placeholder en la ruta y un argumento con el mismo nombre en el
método:

```php
#[Route('/tareas/{id}', name: 'tarea_detalle', requirements: ['id' => '\d+'])]
public function detalle(int $id): Response
```

El router extrae el valor de la URL, lo convierte al tipo del argumento (`int`)
y Symfony lo inyecta al llamar al método. El `requirements` `\d+` hace que solo
coincidan valores numéricos.

## 8. ¿Qué sucede cuando Symfony recibe una petición a una ruta que no existe?

El router no encuentra ninguna ruta que coincida y lanza una
`NotFoundHttpException`, que Symfony convierte en una respuesta HTTP 404. En
`dev` se muestra la pantalla de debug con la traza; en `prod` se usa la
plantilla `templates/bundles/TwigBundle/Exception/error404.html.twig`. Algo
parecido ocurre cuando el id es válido pero la tarea no existe: el controlador
lanza `createNotFoundException()` y se obtiene el mismo 404 personalizado.
