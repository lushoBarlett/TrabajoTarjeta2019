<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {

    const viajes = array(Tipos::Libre => 0, Tipos::Medio => 13.75, Tipos::Normal => 27.50);
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
        $this->assertEquals($boleto->obtenerTipoTarj(), Tipos::Normal);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Normal);

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Plus);

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
     * 
     * ARREGLAR ESTE TEST, NO CHEQUEA LO QUE DICE
     */
    public function testBoletoMedio() {
        $gestor = new GestorDeMontos(BoletoTest::viajes,BoletoTest::recargas);
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedio($tiempo);
        $tarjeta->recargar(30, $gestor);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(300);

        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipoTarj(), Tipos::Medios);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Normal);

        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Plus);
    }

    /**
     * Comprueba que el tipo de boleto sea medio al utilizar una tarjeta de tipo medio universitario.
     * Ademas comprueba que funcione el limite de dos boletos medio por dia.
     */
    public function testBoletoMedioUni() {
        $gestor = new GestorDeMontos(BoletoTest::viajes,BoletoTest::recargas);
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedioUni($tiempo);
        $tarjeta->recargar(100, $gestor);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(86400);

        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipoTarj(), Tipos::Medio);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Normal);
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes[Tipos::Medio]);

        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Normal);
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes[Tipos::Medio]);
        
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Completo);
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes[Tipos::Normal]);

        $tarjeta->avanzarTiempo(86400);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerValor(), BoletoTest::viajes[Tipos::Medio]);

        $tarjeta2 = new TarjetaMedioUni($tiempo);
        $tarjeta2->recargar(100, $gestor);
        $tarjeta2->pagarBoleto($colectivo);
        $tarjeta2->avanzarTiempo(86600);
        $this->assertEquals($tarjeta2->pagarBoleto($colectivo), Pasajes::Normal);
        $tarjeta2->pagarBoleto($colectivo);
        $this->assertEquals($tarjeta2->pagarBoleto($colectivo), Pasajes::Completo);

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
        $this->assertEquals($boleto->obtenerTipoTarj(), Tipos::Libre);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Normal);
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
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo, Pasajes::Completo);
    }
}
