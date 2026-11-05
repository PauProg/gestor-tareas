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
}
