<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {
    
    /**
    * Comprueba que es posible pagar un viaje con y sin tener saldo
    */
    
    public function testPagarSaldo() {
        $tarjeta = new Tarjeta;
        $colectivo = New Colectivo(145, "Metrobus", 4825);

        $tarjeta->recargar(20);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), "normal");

        $tarjeta = new Tarjeta;
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), "plus");
    }

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido.
     */
    public function testCargaSaldo() {
        $tarjeta = new Tarjeta;

        $this->assertTrue($tarjeta->recargar(10));
        $this->assertEquals($tarjeta->obtenerSaldo(), 10);

        $this->assertTrue($tarjeta->recargar(20));
        $this->assertEquals($tarjeta->obtenerSaldo(), 30);

        $this->assertTrue($tarjeta->recargar(510.15));
        $this->assertEquals($tarjeta->obtenerSaldo(), 622.08);

        $this->assertTrue($tarjeta->recargar(962.59));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1806.25);
    }

    /**
     * Comprueba que al realizar una recarga luego de haber utilizado los viajes plus, estos vuelvan a su valor inicial.
     */
    
    public function testCargaPlus(){
        $colectivo = New Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $this->assertFalse($tarjeta->restarPlus());
        $tarjeta->recargar(50);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);

        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $this->assertEquals($tarjeta->obtenerPlus(), 1);
        $tarjeta->recargar(50);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);
    }

    /**
     * Comprueba que el monto abonado por la tarjeta es el correcto.
     */
    public function testAbonado(){
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->restarViaje($colectivo);
        $tarjeta->restarViaje($colectivo);
        $tarjeta->recargar(50);
        $tarjeta->restarViaje($colectivo);
        $this->assertEquals($tarjeta->abonado(), 14.8/*($tarjeta->obtenerCosto() * 3)*/);

        $tarjeta->restarViaje($colectivo);
        $tarjeta->recargar(50);
      $this->assertEquals($tarjeta->abonado(), 14.8/*($tarjeta->obtenerCosto() * 2)*/);
    }

    /**
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido() {
      $tarjeta = new Tarjeta;

      $this->assertFalse($tarjeta->recargar(15));
      $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }

    /**
     * Comprueba que es posible obtener el boleto de tipo trasbordo para cada tipo.
     */
    public function testTrasbordo() {
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(50);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $boleto = $colectivo->pagarCon($tarjeta);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $boleto = $colectivo->pagarCon($tarjeta);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta);
        $this->assertEquals($boleto->obtenerTipo(), "normal");

    }

    public function testTrasbordoMedio() {
        $tarjeta = new TarjetaMedio;
        $tarjeta->recargar(50);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");
    }

    public function testTrasbordoMedioUni() {
        $tarjeta = new TarjetaMedioUni;
        $tarjeta->recargar(50);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta);

        $this->assertEquals($boleto->obtenerTipo(), "trasbordo");
    }


}
