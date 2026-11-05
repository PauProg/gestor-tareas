<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TareaControllerTest extends WebTestCase
{
    public function testListadoMuestraElTitulo(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Gestor de tareas');
    }

    public function testListadoMuestraLasCincoTareas(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tareas');

        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $crawler->filter('li.tarea'));
        $this->assertSelectorTextContains('body', 'Montar el listado');
        $this->assertSelectorExists('a[href="/tareas/1"]');
    }

    public function testDetalleMuestraLaTarea(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1','Preparar el entorno');
        $this->assertSelectorTextContains('body', 'Completada');
    }

    public function testDetalleDevuelve404SiLaTareaNoExiste(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas/999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDetalleDevuelve404SiElIdNoEsNumerico(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas/abc');

        $this->assertResponseStatusCodeSame(404);
    }
}
