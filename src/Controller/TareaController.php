<?php

namespace App\Controller;

use App\Repository\TareaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TareaController extends AbstractController
{
    public function __construct(private readonly TareaRepository $tareaRepository)
    {
    }

    #[Route('/tareas', name: 'tarea_listado', methods: ['GET'])]
    public function listado(): Response
    {
        $bienvenida = 'Bienvenido al gestor de tareas con Symfony, desde aqui puedes consultar y gestionar tus tareas.';

        return $this->render('tarea/index.html.twig', [
            'bienvenida' => $bienvenida,
            'tareas' => $this->tareaRepository->findAll(),
        ]);
    }

    #[Route('/tareas/{id}', name: 'tarea_detalle', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detalle(int $id): Response
    {
        $tarea = $this->tareaRepository->find($id);

        if (null === $tarea) {
            throw $this->createNotFoundException(sprintf('No existe la tarea con id %d.', $id));
        }

        return $this->render('tarea/detalle.html.twig', [
            'tarea' => $tarea,
        ]);
    }

    #[Route('/tareas/nueva', name: 'tarea_nueva', methods: ['GET'])]
    public function nueva(): Response
    {
        return $this->render('tarea/nueva.html.twig', [
            'estados' => TareaRepository::ESTADOS,
            'datos' => [
                'titulo' => '',
                'descripcion' => '',
                'estado' => 'pendiente',
            ],
            'errores' => [],
        ]);
    }

    #[Route('/tareas/nueva', name: 'tarea_crear', methods: ['POST'])]
    public function crear(Request $request): Response
    {
        $datos = [
            'titulo' => trim((string) $request->request->get('titulo', '')),
            'descripcion' => trim((string) $request->request->get('descripcion', '')),
            'estado' => trim((string) $request->request->get('estado', '')),
        ];

        $errores = [];

        if ('' === $datos['titulo']) {
            $errores[] = 'El título no puede estar vacío.';
        }

        if ('' === $datos['descripcion']) {
            $errores[] = 'La descripción no puede estar vacía.';
        }

        if (!in_array($datos['estado'], TareaRepository::ESTADOS, true)) {
            $errores[] = 'El estado debe ser Pendiente o Completada.';
        }

        if ([] !== $errores) {
            return $this->render('tarea/nueva.html.twig', [
                'estados' => TareaRepository::ESTADOS,
                'datos' => $datos,
                'errores' => $errores,
            ]);
        }

        $request->getSession()->set('tarea_creada', $datos);

        return $this->redirectToRoute('tarea_confirmacion');
    }

    #[Route('/tareas/confirmacion', name: 'tarea_confirmacion', methods: ['GET'])]
    public function confirmacion(Request $request): Response
    {
        $datos = $request->getSession()->get('tarea_creada');

        if (null === $datos) {
            return $this->redirectToRoute('tarea_nueva');
        }

        $request->getSession()->remove('tarea_creada');

        return $this->render('tarea/confirmacion.html.twig', [
            'datos' => $datos,
        ]);
    }
}
