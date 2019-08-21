<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {

    const viajes = array("libre" => 0, "medio" => 13.75, "normal" => 27.50);
    const recargas = array("10" => 10, "30" => 30, "50" => 50, "100" => 100, "200" => 200, "947.60" => 1100, "1788.80" => 2200);

    /**
     * Comprueba que es posible tener saldo cero.
     */
    public function testSaldoCero() {
        $tarjeta = new Tarjeta;
        $colectivo = new Colectivo;

        $boleto = new Boleto($tarjeta->abonado(), $colectivo, $tarjeta, 0);
        $this->assertEquals($boleto->obtenerValor(), $tarjeta->abonado());
    }

    /**
     * Comprueba que el tipo de boleto es normal cuando se tiene el saldo suficiente.
     * Ademas de que cada funcion para obtener la informacion del boleto funcione correctamente.
     */
    public function testBoletoNormal() {
        $gestor = new GestorDeMontos(BoletoTest::viajes,BoletoTest::recargas);
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(30, $gestor);
        $colectivo = new Colectivo(142, "Metrobus", 3541);

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'normal');
        
        $tarjeta->avanzarTiempo(5400);        
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), 'normal');

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), 'plus');

        $this->assertEquals($boleto->obtenerColectivo(), $colectivo);
        $this->assertEquals($boleto->obtenerLinea(), $colectivo->linea());
        $this->assertEquals($boleto->obtenerAbonado(), $tarjeta->abonado());
        $this->assertEquals($boleto->obtenerSaldo(), $tarjeta->obtenerSaldo());
        $this->assertEquals($boleto->obtenerIDTarj(), $tarjeta->obtenerID());
    }

    /**
     * Comprueba que tipo de boleto sea medio al utilizar una tarjeta del tipo medio.
     * Tambien verifica si el tiempo limite funciona correctamente, en cuyo caso que se haya pagado
     * otro boleto dentro de los 5 minutos, su tipo sera normal.
     */
    public function testBoletoMedio() {
        $gestor = new GestorDeMontos(BoletoTest::viajes,BoletoTest::recargas);
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedio($tiempo);
        $tarjeta->recargar(30, $gestor);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(300);

        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'medio');
        $this->assertEquals($boleto->obtenerTipo(), 'normal');

        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), 'plus');
    }

    /**
     * Comprueba que el tipo de boleto sea medio al utilizar una tarjeta de tipo medio universitario.
     * Ademas comprueba que funcione el limite de dos boletos medio por dia.
     */
    public function testBoletoMedioUni() {
        $gestor = new GestorDeMontos(BoletoTest::viajes,BoletoTest::recargas);
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedioUni($tiempo);
        $tarjeta->recargar(50, $gestor);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(86400);

        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'medio');
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes["medio"]);

        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes["medio"]);

        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes["normal"]);

        $tarjeta->avanzarTiempo(86400);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes["medio"]);

        $tarjeta2 = new TarjetaMedioUni($tiempo);
        $tarjeta2->recargar(30, $gestor);
        $tarjeta2->pagarBoleto($colectivo);
        $tarjeta2->avanzarTiempo(86600);
        $this->assertEquals($tarjeta2->pagarBoleto($colectivo), 1);
        $tarjeta2->avanzarTiempo(86600);
        $tarjeta2->pagarBoleto($colectivo);
        $this->assertFalse($tarjeta2->pagarBoleto($colectivo));

    }

    /**
     * Comprueba que el tipo del boleto sea libre al utilizar una tarjeta del tipo libre.
     */
    public function testBoletoLibre() {
        $gestor = new GestorDeMontos(BoletoTest::viajes,BoletoTest::recargas);
        $tarjeta = new TarjetaLibre;
        $tarjeta->recargar(30, $gestor);
        $colectivo = new Colectivo;
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'libre');
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
    }

    /**
     * Comprueba que funcione el limite de un boleto medio cada 5 minutos
     */
    public function testLimiteCinco() {
        $gestor = new GestorDeMontos(BoletoTest::viajes,BoletoTest::recargas);
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedio($tiempo);
        $tarjeta->recargar(50, $gestor);
        $colectivo = new Colectivo;
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        // $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertFalse($boleto);
    }
}
