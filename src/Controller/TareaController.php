<?php

namespace App\Controller;

use App\Repository\TareaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            throw $this->createNotFoundException(sprintf('Noe xiste la tarea con id %d.', $id));
        }

        return $this->render('tarea/detalle.html.twig', [
            'tarea' => $tarea,
        ]);
    }
}
