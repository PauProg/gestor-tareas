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

    public function testLayoutCargaLaHojaDeEstilos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas');

        $this->assertSelectorExists('link[rel="stylesheet"][href*="css/app.css"]');
        $this->assertSelectorExists('nav a.activo[href="/tareas"]');
    }

    public function testDetalleMuestraLaTarea(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Preparar el entorno');
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

    public function testFormularioNuevoMuestraLosCampos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas/nueva');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[method="post"]');
        $this->assertSelectorExists('input[name="titulo"]');
        $this->assertSelectorExists('textarea[name="descripcion"]');
        $this->assertSelectorExists('select[name="estado"]');
    }

    public function testCrearTareaMuestraConfirmacion(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tareas/nueva', [
            'titulo' => 'Comprar pan',
            'descripcion' => 'Ir a la panadería',
            'estado' => 'pendiente',
        ]);

        $this->assertResponseRedirects('/tareas/confirmacion');
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Tarea recibida');
        $this->assertSelectorTextContains('body', 'Comprar pan');
        $this->assertSelectorTextContains('body', 'Pendiente');
    }

    public function testConfirmacionSinDatosRedirigeAlFormulario(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas/confirmacion');

        $this->assertResponseRedirects('/tareas/nueva');
    }

    public function testCrearTareaSinTituloMuestraError(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tareas/nueva', [
            'titulo' => '   ',
            'descripcion' => 'Descripción válida',
            'estado' => 'pendiente',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'El título no puede estar vacío');
    }

    public function testCrearTareaSinDescripcionMuestraError(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tareas/nueva', [
            'titulo' => 'Tarea válida',
            'descripcion' => '',
            'estado' => 'pendiente',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'La descripción no puede estar vacía');
    }

    public function testCrearTareaConEstadoInvalidoMuestraError(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tareas/nueva', [
            'titulo' => 'Tarea válida',
            'descripcion' => 'Descripción válida',
            'estado' => 'inventado',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'El estado debe ser Pendiente o Completada');
    }

    public function testListadoMuestraElResumenPorEstado(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tareas');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('.resumen__todas', '5');
        $this->assertSelectorTextSame('.resumen__pendiente', '3');
        $this->assertSelectorTextSame('.resumen__completada', '2');
    }
}
