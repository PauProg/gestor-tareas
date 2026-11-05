<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TareaControllerTest extends WebTestCase {
  public function testListadoMuestraElTitulo(): void {
    $client = static::createClient();
    $client->request('GET', '/tareas');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h1', 'Gestor de tareas');
  }

  public function testListadoMuestraLasCincoTareas(): void {
    $client = static::createClient();
    $crawler = $client-> request('GET', '/tareas');

    $this->assertResponseIsSuccessful();
    $this->assertCount(5, $crawler->filter('li.tarea'));
    $this->assertSelectorTextContains('body', 'Montar el listado');
  }
}