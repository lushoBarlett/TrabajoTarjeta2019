<?php

namespace TrabajoTarjeta;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase {

    const viajes = array(Pasajes::Transbordo => 0, Pasajes::Libre => 0, Pasajes::Medio => 1, Pasajes::Normal => 2);
    const recargas = array("1" => 1, "2" => 2, "3" => 3, "4" => 5, "5" => 10);

    /**
    * Comprueba que es posible pagar un viaje con y sin tener saldo
    */
    public function testPagarSaldo() {
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta($gestor);
        $colectivo = new Colectivo(145, "Metrobus", 4825);

        $tarjeta->recargar(30, $gestor);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Normal);

        $tarjeta = new Tarjeta;
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Plus);
    }

    /**
     * Comprueba que la tarjeta aumenta su saldo cuando se carga saldo válido.
     */
    public function testCargaSaldo() {
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta;

        $this->assertTrue($tarjeta->recargar(1, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 1);

        $this->assertTrue($tarjeta->recargar(2, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 2);

        $this->assertTrue($tarjeta->recargar(4, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 5);

        $this->assertTrue($tarjeta->recargar(5, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 10);
    }

    /**
     * Comprueba que al realizar una recarga luego de haber utilizado los viajes plus, estos vuelvan a su valor inicial.
     */
    public function testCargaPlus(){
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertFalse($tarjeta->restarPlus());
        $tarjeta->recargar(3, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->obtenerPlus(), 2);
    }

    /**
     * Comprueba que el monto abonado por la tarjeta es el correcto.
     */
    public function testAbonado(){
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $colectivo = new Colectivo;
        $tarjeta = new Tarjeta;

        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->recargar(3, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->abonado(), TarjetaTest::viajes[Tipos::Normal] * 3);
        
        $tarjeta = new Tarjeta;
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $tarjeta->recargar(3, $gestor);
        $tarjeta->pagarBoleto($colectivo, $gestor);
        $this->assertEquals($tarjeta->abonado(), TarjetaTest::viajes[Tipos::Normal] * 2);
    }

    /**
     * Comprueba que la tarjeta no puede cargar saldos invalidos.
     */
    public function testCargaSaldoInvalido() {
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta;

        $this->assertFalse($tarjeta->recargar(15, $gestor));
        $this->assertEquals($tarjeta->obtenerSaldo(), 0);
    }

    /**
     * Comprueba que es posible obtener el boleto de tipo trasbordo para cada tipo.
     */
    public function testTrasbordo() {
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new Tarjeta;
        $tarjeta->recargar(3, $gestor);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);

        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Transbordo);

        $tarjeta->avanzarTiempo(5400);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Normal);

    }

    public function testTrasbordoMedio() {
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new TarjetaMedio;
        $tarjeta->recargar(2, $gestor);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);

        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Transbordo);
    }

    public function testTrasbordoMedioUni() {
        $gestor = new Montos(TarjetaTest::viajes,TarjetaTest::recargas);
        $tarjeta = new TarjetaMedioUni;
        $tarjeta->recargar(2, $gestor);
        $colectivo = new Colectivo(145, "Metrobus", 4825);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);
        $colectivo = new Colectivo(456, "Rosariobus", 1234);
        $tarjeta->avanzarTiempo(300);
        $boleto = $colectivo->pagarCon($tarjeta, $gestor);

        $this->assertEquals($boleto->obtenerTipo(), Pasajes::Transbordo);
    }


}
