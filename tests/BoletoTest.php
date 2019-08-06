<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase {

    /**
     * Comprueba que es posible tener saldo cero.
     */
    public function testSaldoCero() {
        $tarjeta = new Tarjeta;
        $colectivo = new Colectivo;

        $boleto = new Boleto($tarjeta->obtenerCosto(), $colectivo, $tarjeta, 0);
        $this->assertEquals($boleto->obtenerValor(), $tarjeta->obtenerCosto());
    }

    /**
     * Comprueba que el tipo de boleto es normal cuando se tiene el saldo suficiente.
     * Ademas de que cada funcion para obtener la informacion del boleto funcione correctamente.
     */
    public function testBoletoNormal() {
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(30);
        $colectivo = new Colectivo(142, "Metrobus", 3541);

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'normal');
        
        $tarjeta->avanzarTiempo(5400);        
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), 'normal');

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta);
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
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedio($tiempo);
        $tarjeta->recargar(10);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(300);

        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'medio');
        $this->assertEquals($boleto->obtenerTipo(), 'normal');

        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), 'plus');
    }

    /**
     * Comprueba que el tipo de boleto sea medio al utilizar una tarjeta de tipo medio universitario.
     * Ademas comprueba que funcione el limite de dos boletos medio por dia.
     */
    public function testBoletoMedioUni() {
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedioUni($tiempo);
        $tarjeta->recargar(50);
        $colectivo = new Colectivo;
        $tarjeta->avanzarTiempo(86400);

        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'medio');
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
        $this->assertEquals($boleto->obtenerValor(), 7.40);

        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
        $this->assertEquals($boleto->obtenerValor(), 7.40);

        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
        $this->assertEquals($boleto->obtenerValor(), 14.80);

        $tarjeta->avanzarTiempo(86400);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerValor(), 7.40);

        $tarjeta2 = new TarjetaMedioUni($tiempo);
        $tarjeta2->recargar(10);
        $tarjeta2->restarViaje($colectivo);
        $tarjeta2->avanzarTiempo(86600);
        $this->assertEquals($tarjeta2->restarViaje($colectivo), 1);
        $tarjeta2->avanzarTiempo(86600);
        $tarjeta2->restarViaje($colectivo);
        $this->assertFalse($tarjeta2->restarViaje($colectivo));

    }

    /**
     * Comprueba que el tipo del boleto sea libre al utilizar una tarjeta del tipo libre.
     */
    public function testBoletoLibre() {
        $tarjeta = new TarjetaLibre;
        $tarjeta->recargar(30);
        $colectivo = new Colectivo;
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipoTarj(), 'libre');
        $this->assertEquals($boleto->obtenerTipo(), 'normal');
    }

    /**
     * Comprueba que funcione el limite de un boleto medio cada 5 minutos
     */
    public function testLimiteCinco() {
        $tiempo = new TiempoFalso;
        $tarjeta = new TarjetaMedio($tiempo);
        $tarjeta->recargar(50);
        $colectivo = new Colectivo;
        $boleto = $colectivo->pagarCon($tarjeta);
        // $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertFalse($boleto);
    }
}
