<?php

namespace App\Repository;

class TareaRepository
{
    public const ESTADOS = ['pendiente', 'completada'];

    private const TAREAS = [
        [
            'id' => 1,
            'titulo' => 'Preparar el entorno',
            'descripcion' => 'Instalar PHP, Composer y Symfony CLI, y comprobar las extensiones.',
            'estado' => 'completada',
        ],
        [
            'id' => 2,
            'titulo' => 'Crear el controlador',
            'descripcion' => 'Añadir la ruta /tareas y renderizar la primera plantilla.',
            'estado' => 'completada',
        ],
        [
            'id' => 3,
            'titulo' => 'Montar el listado',
            'descripcion' => 'Recorrer el array de tareas con un bucle en Twig.',
            'estado' => 'pendiente',
        ],
        [
            'id' => 4,
            'titulo' => 'Añadir el detalle',
            'descripcion' => 'Crear /tareas/{id} y gestionar las tareas que no existen.',
            'estado' => 'pendiente',
        ],
        [
            'id' => 5,
            'titulo' => 'Documentar el proyecto',
            'descripcion' => 'Escribir el README y responder a las preguntas de investigación.',
            'estado' => 'pendiente',
        ],
    ];

    /**
     * @return list<array{id: int, titulo: string, descripcion: string, estado: string}>
     */
    public function findAll(): array
    {
        return self::TAREAS;
    }

    /**
     * @return array{id: int, titulo: string, descripcion: string, estado: string}|null
     */
    public function find(int $id): ?array
    {
        foreach (self::TAREAS as $tarea) {
            if ($tarea['id'] === $id) {
                return $tarea;
            }
        }

        return null;
    }

    public function countByEstado(string $estado): int
    {
        return count(array_filter(
            self::TAREAS,
            static fn (array $tarea): bool => $estado === $tarea['estado']
        ));
    }

    /**
     * @return list<array{id: int, titulo: string, descripcion: string, estado: string}>
     */
    public function findByEstado(?string $estado = null): array
    {
        if (null === $estado) {
            return self::TAREAS;
        }

        return array_values(array_filter(
            self::TAREAS,
            static fn (array $tarea): bool => $estado === $tarea['estado']
        ));
    }
}
