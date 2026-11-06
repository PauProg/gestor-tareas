<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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

    public function testPagina404Personalizada(): void
    {
        // Con debug desactivado Symfony usa templates/bundles/TwigBundle/Exception/error404.html.twig.
        $client = static::createClient(['debug' => false]);
        $client->request('GET', '/tareas/999');

        $this->assertResponseStatusCodeSame(404);
        $this->assertSelectorTextContains('h1', 'Página no encontrada');
        $this->assertSelectorTextContains('body', 'Volver al listado');
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

        $this->enviarFormulario($client, [
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

        $this->enviarFormulario($client, [
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

        $this->enviarFormulario($client, [
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

        // Un <select> solo admite sus opciones, así que para simular una
        // manipulación del formulario cargamos el token válido y enviamos
        // el estado inventado con un POST directo.
        $crawler = $client->request('GET', '/tareas/nueva');
        $token = $crawler->filter('input[name="_token"]')->attr('value');

        $client->request('POST', '/tareas/nueva', [
            '_token' => $token,
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

    public function testFiltroPendienteMuestraSoloLasPendientes(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tareas?estado=pendiente');

        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('li.tarea'));
        $this->assertSelectorTextNotContains('body', 'Preparar el entorno');
        $this->assertSelectorExists('.filtros a[href="/tareas?estado=pendiente"].activo');
    }

    public function testFiltroCompletadaMuestraSoloLasCompletadas(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tareas?estado=completada');

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('li.tarea'));
        $this->assertSelectorTextContains('body', 'Preparar el entorno');
        $this->assertSelectorExists('.filtros a[href="/tareas?estado=completada"].activo');
    }

    public function testFiltroInvalidoVuelveAMostrarTodas(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tareas?estado=inventado');

        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $crawler->filter('li.tarea'));
        $this->assertSelectorExists('.filtros a[href="/tareas"].activo');
    }

    public function testCrearTareaConTokenInvalidoMuestraError(): void
    {
        $client = static::createClient();
        $client->request('POST', '/tareas/nueva', [
            '_token' => 'token-falso',
            'titulo' => 'Tarea',
            'descripcion' => 'Descripción',
            'estado' => 'pendiente',
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('body', 'La sesión ha caducado');
    }

    /**
     * Carga el formulario real y lo envía con su token CSRF incluido.
     *
     * @param array<string, string> $datos
     */
    private function enviarFormulario(KernelBrowser $client, array $datos): void
    {
        $crawler = $client->request('GET', '/tareas/nueva');
        $client->submit($crawler->filter('form')->form(), $datos);
    }
}
